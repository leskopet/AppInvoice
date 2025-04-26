<?php

require_once __DIR__ . '/classDb.php';

//error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

class Table {
    private $name;
    private $columns;
    private $foreignKeys = array();

    public function __construct($name, $columns, $foreignKeys) {
        $this->name = $name;
        $this->columns = $columns;
        // $this->$foreignKeys = $foreignKeys;
    }

    private function buildWhere($where, $operator = "") {
        if(empty($where)) { return array('sql_where' => '', 'params' => array()); }
        $where_text = '';
        $params = array();
        foreach($where as $column => $value) {
            if($column == 'and') { 
                $result = $this->buildWhere($value, 'and');
                $where_text .= ' ( ' . $result['sql_where'] . ' ) ' . strtoupper($operator) . ' ';
                $params = array_merge($params, $result['params']);
                continue;
            }
            if($column == 'or') { 
                $result = $this->buildWhere($value, 'or');
                $where_text .= ' ( ' . $result['sql_where'] . ' ) ' . strtoupper($operator) . ' ';
                $params = array_merge($params, $result['params']);
                continue;
            }
            if(is_null($value)) {
                $where_text .= $column . ' IS NULL ' . strtoupper($operator) . ' ';
                continue;
            }
            $where_text .= $column . ' = ? ' . strtoupper($operator) . ' ';
            $params[] = $value;
        }
        $where_text = substr($where_text, 0, -strlen($operator)-1);
        return array('sql_where' => $where_text, 'params' => $params);
    }

    public function buildSelect($data, $db) {
        $sql_select = '';
        $sql_join = '';
        foreach($data as $table => $columns) {
            if($table == $this->name) {
                foreach($columns as $column) {
                    $sql_select .= $this->name . '.' . $column . ', ';
                }
                continue;
            }

            // joining tables
            $result = $db->tables[$table]->buildSelect(array($table => $columns), $db);
            if(!empty($result['sql_select'])) {
                $sql_select .= $result['sql_select'] . ', ';
            }
            $sql_join .= $result['sql_join'] . ' ';
            $lowertable = strtolower($table);
            $lowername = strtolower($this->name);
            if(in_array(strtolower($lowertable), $this->columns)) {
                $sql_join .= <<<TEXT
                    LEFT JOIN $table ON $table.id = $this->name.$lowertable
                TEXT;
            }
            else {
                $sql_join .= <<<TEXT
                    LEFT JOIN $table ON $table.$lowername = $this->name.id
                TEXT;
            }
        }
        $sql_select = substr($sql_select, 0, -2);
        return array('sql_select' => $sql_select, 'sql_join' => $sql_join);
    }

    public function insert($data, $db) {
        // create sql for insert
        $columns = array_keys($data);
        $columns_text = implode(', ', $columns);
        $value_text = implode(', ', array_fill(0, count($columns), '?'));
        $sql = <<<TEXT
            INSERT INTO $this->name (
                $columns_text
            )
            VALUES (
                $value_text
            )
        TEXT;
        // asign data values to sql columns in correct order
        $params = array();
        foreach($columns as $column) {
            $params[] = $data[$column];
        }
        // execute sql
        $result = $db->querySQL($sql, $params);
        return $db->lastInsertId();
    }

    public function select($data, $db, $where = null) {
        $result = $this->buildSelect($data, $db);
        $sql_select = $result['sql_select'];
        $sql_join = $result['sql_join'];
        $sql = <<<TEXT
            SELECT $sql_select
            FROM $this->name
            $sql_join
        TEXT;
        if(!empty($where)) {        // build where clause
            $sql_where = '';
            $result = $this->buildWhere($where);
            $sql_where = $result['sql_where'];
            $params = $result['params'];
            $sql .= <<<TEXT
                WHERE $sql_where
            TEXT;
        }
        
        $result = $db->querySQL($sql, $params);
        return $result;
    }

    public function update($data, $db, $where = null) {
        $columns = array_keys($data);
        $columns_text = '';
        foreach($columns as $column) {
            if($column == 'id') { continue; }
            $columns_text .= $this->name . '.' . $column . ' = ?, ';
        }
        $columns_text = substr($columns_text, 0, -2);
        $sql = <<<TEXT
            UPDATE $this->name
            SET $columns_text
        TEXT;
        $params = array();
        foreach($columns as $column) {
            if($column == 'id') { continue; }
            $params[] = $data[$column];
        }
        if(!empty($where)) {        // build where clause
            $sql_where = '';
            $result = $this->buildWhere($where);
            $sql_where = $result['sql_where'];
            $params = array_merge($params, $result['params']);
            $sql .= <<<TEXT
                WHERE $sql_where
            TEXT;
        }
        else {
            $sql .= ' WHERE id = ?';
            $params[] = $data['id'];
        }
        $result = $db->querySQL($sql, $params);
        return array($where);
    }

    public function delete($db, $where) {
        if(empty($where)) { return false; }
        $sql = <<<TEXT
            DELETE FROM $this->name
        TEXT;
        $result = $this->buildWhere($where);
        $sql_where = $result['sql_where'];
        $sql .= <<<TEXT
            WHERE $sql_where
        TEXT;
        $params = $result['params'];
        $result = $db->querySQL($sql, $params);
        return true;
    }
}
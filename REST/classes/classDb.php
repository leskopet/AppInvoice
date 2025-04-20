<?php

// enable debugging
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// include_once ( __DIR__ . '/globals.php');
require_once __DIR__ . '/classTable.php';

class Database {
    private $type;
    private $host;
    private $username;
    private $password;
    private $database;
    private $charset;
    private $connection;

    private $sql;
    private $params = array();
    private $types = "";
    private $stmt;
    private $result;
    private $rows;

    public $tables = array();

    public function __construct($host, $username, $password, $database, $type = 'mysql', $charset = 'utf8mb4') {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->charset = $charset;
        $this->type = $type;
    }

    public function connect() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->connection->connect_error) {
            throw new Exception("Connection failed: " . $this->connection->connect_error);
        }
        $this->connection->set_charset($this->charset);
    }

    public function addTable($table, $columns) {
        $this->tables[$table] = new Table($table, $columns, array());
    }

    public function setSql($sql) { $this->sql = $sql; }

    public function setParams($params) { $this->params = $params; }

    public function setTypes($types) { $this->types = $types; }

    public function convertPsglToMysql($sql = null) {
        if (is_null($sql)) { $sql = $this->sql; }
        $sql = str_replace('`', '', $sql);
        $sql = str_replace('"', '', $sql);
        $sql = str_replace("'", '', $sql);
        $sql = preg_replace("/[\$\d]+/", "?", $sql);
        $this->setSql($sql);
        return $sql;
    }

    public function query($query = null) {
        if (is_null($query)) {
            $query = $this->sql;
        }
        if (!$this->connection) {
            throw new Exception("Not connected to the database");
        }
        $this->result = $this->connection->query($query);
        if (!$this->result) {
            throw new Exception("Query failed: " . $this->connection->error);
        }
        return $this->result;
    }

    public function prepare($query = null) {
        if (is_null($query)) { $query = $this->sql; }
        if (!$this->connection) {
            throw new Exception("Not connected to the database");
        }
        $this->stmt = $this->connection->prepare($query);
        if (!$this->stmt) {
            throw new Exception("Query failed: " . $this->connection->error);
        }
        return $this->stmt;
    }

    // binding parameters to the query
    public function bind_param($types = null, $params = null) {
        if (is_null($types)) { $types = $this->types; }
        if (is_null($params)) { $params = $this->params; }
        if (!$this->connection) {
            throw new Exception("Not connected to the database");
        }
        if (!$this->stmt) {
            throw new Exception("Query failed: " . $this->connection->error);
        }
        if(empty($types)) {
            $types = str_repeat('s', count($params));
        }
        $this->stmt->bind_param($types, ...$params);
        return $this->stmt;
    }

    public function execute() {
        if (!$this->connection) {
            throw new Exception("Not connected to the database");
        }
        if (!$this->stmt) {
            throw new Exception("Query failed: " . $this->connection->error);
        }
        $this->stmt->execute();
        return $this->stmt;
    }

    public function get_result() {
        if (!$this->connection) {
            throw new Exception("Not connected to the database");
        }
        if (!$this->stmt) {
            throw new Exception("Query failed: " . $this->connection->error);
        }
        $this->result = $this->stmt->get_result();
        return $this->result;
    }

    public function fetch_all($mode = MYSQLI_ASSOC) {
        if (!$this->connection) {
            throw new Exception("Not connected to the database");
        }
        if (empty($this->result) || !$this->result) {
            $this->rows = array();
        }
        else {
            $this->rows = $this->result->fetch_all($mode);
        }
        return $this->rows;
    }

    public function getRows() { return $this->rows; }

    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    public function querySQL($sql = null, $params = null, $types = null) {
        if (is_null($sql)) { $sql = $this->sql; }
        if (is_null($params)) { $params = $this->params; }
        if (is_null($types)) { $types = $this->types; }
        if($this->type == 'mysql') {
            $sql = $this->convertPsglToMysql($sql);
        }
        
        if(!$this->connection) {
            $this->connect();
        }
        $this->prepare($sql);
        $this->bind_param($types, $params);
        $this->execute();
        $this->get_result();
        $this->fetch_all();
        $this->reset();
        return $this->rows;
    }

    public function lastInsertId() {
        if (!$this->connection) {
            throw new Exception("Not connected to the database");
        }
        return $this->connection->insert_id;
    }

    // reset parameters
    public function reset() {
        $this->sql = "";
        $this->params = array();
        $this->types = "";
    }
}

<?php
include_once(__DIR__ . '/../functions.php');

trait db_renew_password {

    private function insert_renew_password($data, $db) {

        $allowed_keys = array('user', 'expiration');
    
        $data = filter_table_data($data, $allowed_keys);
        
        $params = array();
        
        // build sql
        $sql_header = <<<TEXT
            INSERT INTO "Renew_password"
        TEXT;
        
        // build table
        $sql_table = <<<TEXT
            ("user", "expiration")
        TEXT;
        
        // build condition and params
        if(!empty($data['user'])) {
            $sql_condition = <<<TEXT
                VALUES ($1, $2)
            TEXT;
            $params[] = $data['user'];
            $params[] = date("Y-m-d H:i:s", strtotime('+24 hours'));
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'invalid inputs';
            echo json_encode($response);
            exit;
        }

        $sql = $sql_header . $sql_table . $sql_condition;
        
        $result = $db->querySQL($sql, $params);
        
        return $result;
    }

    private function get_renew_password($data, $db) {

        $allowed_keys = array('user', 'id');
        $data = filter_table_data($data, $allowed_keys);

        $params = array();

        // build sql
        $sql_header = <<<TEXT
            SELECT  "Renew_password".id,
                    "Renew_password".user,
                    "User".email,
                    "User".fname,
                    "User".lname,
                    "Renew_password".expiration
        TEXT;

        // build table
        $sql_table = <<<TEXT
            FROM "Renew_password"
            INNER JOIN "User" ON "User".id = "Renew_password".user
        TEXT;

        // build condition
        if(!empty($data['id'])) {
            $sql_condition = <<<TEXT
                WHERE "Renew_password".id = $1
            TEXT;
            $params[] = $data['id'];
        }
        else if(!empty($data['user'])) {
            $sql_condition = <<<TEXT
                WHERE "Renew_password".user = $1
            TEXT;
            $params[] = $data['user'];
        }
        else {
            $response['status'] = 'error';
            $response['message'] = 'invalid inputs';
            echo json_encode($response);
            exit;
        }

        $sql = $sql_header . $sql_table . $sql_condition;
        
        $result = $db->querySQL($sql, $params);
        
        return $result;
    }

    private function delete_renew_password($data, $db) {

        $allowed_keys = array('user');
        $data = filter_table_data($data, $allowed_keys);

        $params = array();

        // build sql
        $sql_header = <<<TEXT
            DELETE FROM "Renew_password"
        TEXT;

        // build condition
        $sql_condition = <<<TEXT
            WHERE "Renew_password".user = $1
        TEXT;

        $sql = $sql_header . $sql_condition;
        
        $result = $db->querySQL($sql, $data);
        
        return $result;
    }

}

trait db_renew_password_guest {

    use db_renew_password {
        insert_renew_password as public;
    }
}
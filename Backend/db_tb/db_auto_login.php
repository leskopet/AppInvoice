<?php
include_once(__DIR__ . '/../functions.php');

trait db_auto_login {

    private function create_autologin_table($db) {
        $sql = <<<TEXT
            CREATE TABLE Auto_login (
                id SERIAL PRIMARY KEY,
                user BIGINT UNSIGNED NOT NULL,
                device VARCHAR(255) NOT NULL,
                FOREIGN KEY (user) references AppInvoice.User(id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (device) references Device(id) ON DELETE CASCADE ON UPDATE CASCADE
            )
        TEXT;
        return $db->querySQL($sql);
    }
    
    private function tab_user_authenticate($data, $db) {

        $allowed_keys = array(
            "email", "device"
        );
        $data = filter_table_data($data, $allowed_keys);
    
        $select = array(
            "Auto_login" => array(),
            "User" => array(
                "id", "fname", "lname", "role", "tag"
            ),
            "Device" => array()
        );
        
        if(empty($data['device'])) { 
            $response['status'] = 'error';
            $response['message'] = 'invalid inputs';
            echo json_encode($response);
            exit;
        }
        $where = array(
            'and' => array(
                "Device.id" => $data['device'],
                "User.email" => !empty($data['email']) ? $data['email'] : null
            )
        );
        
        $result = $db->tables['Auto_login']->select($select, $db, $where);
        return $result;
        
    }

    private function tab_insert_autologin($data, $db) {
        $allowed_keys = array(
            "user", "device"
        );
        $data = filter_table_data($data, $allowed_keys);
        $data['user'] = $this->id;
        $data['device'] = $this->device;

        $result = $this->tab_get_device($data, $db);
        if(empty($result)) {
            $result = $this->tab_insert_device($data, $db);
        }
        $data['device'] = $result[0]['id'];
        
        $insert = array(
            "user" => $data['user'],
            "device" => $data['device']
        );
        $result = $db->tables['Auto_login']->insert($insert, $db);
        
        return array(array("id" => $db->lastInsertId()));
    }

    private function tab_get_autologin($data, $db) {
        $allowed_keys = array(
            "email", "device"
        );
        if(empty($data['device'])) { $data['device'] = $this->device; }
        $data = filter_table_data($data, $allowed_keys);

        $select = array(
            "Auto_login" => array(),
            "User" => array( 'id' ),
            "Device" => array()
        );
        if(empty($data['email']) || empty($data['device'])) {
            $response['status'] = 'error';
            $response['message'] = 'invalid inputs';
            echo json_encode($response);
            exit;
        }
        $where = array(
            'and' => array(
                "User.email" => $data['email'],
                "Device.id" => $data['device']
            )
        );
        
        $result = $db->tables['Auto_login']->select($select, $db, $where);
        return $result;
    }

    private function tab_delete_autologin($data, $db) {
        $allowed_keys = array( "id" );
        $data = filter_table_data($data, $allowed_keys);

        $where = array(
            'id' => $data['id']
        );

        $result = $db->tables['Auto_login']->delete($db, $where);
        return true;
    }

}

trait db_auto_login_guest {

    use db_auto_login {
        tab_insert_autologin as public;
        tab_user_authenticate as public;
        tab_get_autologin as protected;
        tab_delete_autologin as protected;
    }

}

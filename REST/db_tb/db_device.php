<?php
include_once(__DIR__ . '/../functions.php');

trait db_device {
    
    private function create_device_table($db) {
        
        $sql = <<<TEXT
            CREATE TABLE Device (
                id VARCHAR(255) PRIMARY KEY NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        TEXT;
        return $db->querySQL($sql);
    }

    private function tab_insert_device($data, $db) {
        
        $data = array(
            "device" => bin2hex(random_bytes(32))
        );

        $insert = array(
            "id" => $data['device']
        );
        $result = $db->tables['Device']->insert($insert, $db);
        
        return array(array("id" => $data['device']));
    }


    public function tab_get_device($data, $db) {
        $allowed_keys = array(
            "device"
        );
        $data = filter_table_data($data, $allowed_keys);
        $data['device'] = $this->device;

        $select = array(
            "Device" => array( 'id' )
        );
        $where = array( "Device.id" => $data['device'] );

        $result = $db->tables['Device']->select($select, $db, $where);
        return $result;
    }

    public function tab_update_device($data, $db) {
        $allowed_keys = array( "device" );
        $data = filter_table_data($data, $allowed_keys);
        $data['device'] = $this->device;

        $update = array(
            "last_login" => date("Y-m-d H:i:s")
        );
        $where = array(
            "Device.id" => $data['device']
        );
        $result = $db->tables['Device']->update($update, $db, $where);
        
        return true;
    }
    
}

trait db_device_guest {

    use db_device {
        
    }

}
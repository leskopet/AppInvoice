<?php

include_once(__DIR__ . '/../functions.php');

trait db_address {

    private function create_address_table($db) {
        $sql = <<<TEXT
            CREATE TABLE Address (
                id SERIAL PRIMARY KEY,
                state VARCHAR(255),
                region VARCHAR(255),
                city VARCHAR(255),
                street VARCHAR(255),
                street_number VARCHAR(255),
                postal_code VARCHAR(255)
            )
        TEXT;
        return $db->querySQL($sql);
    }

    private function tab_insert_address($data, $db) {
        $allowed_keys = array(
            "state", "city", "street", "street_number", "postal_code"
        );
        $data = filter_table_data($data, $allowed_keys);

        $insert = array(
            'state' => empty($data['state']) ? 'Slovakia' : $data['state'],
            'region' => null,
            'city' => !empty($data['city']) ? $data['city'] : "",
            'street' => !empty($data['street']) ? $data['street'] : "",
            'street_number' => !empty($data['street_number']) ? $data['street_number'] : "",
            'postal_code' => !empty($data['postal_code']) ? $data['postal_code'] : ""
        );
        $db->tables['Address']->insert($insert, $db);
        return array(array("id" => $db->lastInsertId()));
    }

    private function tab_update_address($data, $db) {

        $allowed_keys = array(
            "id", "state", "city", "street", "street_number", "postal_code"
        );
        $data = filter_table_data($data, $allowed_keys);

        if(empty($data['id'])) {
            return $this->tab_insert_address($data, $db);
        }

        $insert = array(
            'state' => empty($data['state']) ? 'Slovakia' : $data['state'],
            'city' => !empty($data['city']) ? $data['city'] : "",
            'street' => !empty($data['street']) ? $data['street'] : "",
            'street_number' => !empty($data['street_number']) ? $data['street_number'] : "",
            'postal_code' => !empty($data['postal_code']) ? $data['postal_code'] : ""
        );
        $where = array('Address.id' => $data['id']);
        $db->tables['Address']->update($insert, $db, $where);
        return array(array("id" => $data['id']));
    }

}

trait db_address_user {

    use db_address {
        tab_insert_address as protected;
        tab_update_address as protected;
    }
}
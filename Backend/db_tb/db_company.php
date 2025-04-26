<?php
include_once(__DIR__ . '/../functions.php');

trait db_company {

    private function tab_insert_company($data, $db) {
        $allowed_keys = array(
            "user", "title", "description", "ico", "dic", "icdph", "iban", "swift", "bank", "address", "email", "phone"
        );
        $data['user'] = $this->id;
        $data = filter_table_data($data, $allowed_keys);
        
        $insert = array(
            "title" => $data['title'],
            "description" => !empty($data['description']) ? $data['description'] : "",
            "email" => !empty($data['email']) ? $data['email'] : "",
            "phone" => !empty($data['phone']) ? $data['phone'] : "",
            "ico" => $data['ico'],
            "dic" => !empty($data['dic']) ? $data['dic'] : null,
            "icdph" => !empty($data['icdph']) ? $data['icdph'] : null,
            "iban" => !empty($data['iban']) ? $data['iban'] : "",
            "swift" => !empty($data['swift']) ? $data['swift'] : "",
            "bank" => !empty($data['bank']) ? $data['bank'] : "",
            "address" => !empty($data['address']) ? $data['address'] : null,
            "user" => $data['user']
        );
        $db->tables['Company']->insert($insert, $db);
        return array(array("id" => $db->lastInsertId()));
    }

    private function tab_insert_customer($data, $db) {
        $allowed_keys = array(
            "user", "title", "description", "ico", "dic", "icdph", "iban", "swift", "bank", "address", "email", "phone"
        );
        $data = filter_table_data($data, $allowed_keys);
        $data['user'] = null;
        
        $insert = array(
            "title" => $data['title'],
            "description" => !empty($data['description']) ? $data['description'] : "",
            "email" => !empty($data['email']) ? $data['email'] : "",
            "phone" => !empty($data['phone']) ? $data['phone'] : "",
            "ico" => $data['ico'],
            "dic" => !empty($data['dic']) ? $data['dic'] : null,
            "icdph" => !empty($data['icdph']) ? $data['icdph'] : null,
            "iban" => !empty($data['iban']) ? $data['iban'] : "",
            "swift" => !empty($data['swift']) ? $data['swift'] : "",
            "bank" => !empty($data['bank']) ? $data['bank'] : "",
            "address" => !empty($data['address']) ? $data['address'] : null,
            "user" => $data['user']
        );
        $db->tables['Company']->insert($insert, $db);
        return array(array("id" => $db->lastInsertId()));
    }

    private function tab_get_company($data, $db) {
        $allowed_keys = array( "id", "user" );
        $data = filter_table_data($data, $allowed_keys);
        $data['user'] = $this->id;

        $select = array(
            "Company" => array( "id", "email", "phone", "title", "ico", "dic", "icdph", "address", "description", "iban", "swift", "bank", "user" ),
            "User" => array(),
            "Address" => array( "city", "street", "street_number", "postal_code", "state" )
        );
        $where = array( 
            "and" => array(
                "Company.id" => $data['id'],
                "Company.user" => $data['user']
            )
        );

        $result = $db->tables['Company']->select($select, $db, $where);
        return $result;
    }

    private function tab_get_customer_by_id($data, $db) {
        $allowed_keys = array( "id", "user" );
        $data = filter_table_data($data, $allowed_keys);
        $data['user'] = null;

        $select = array(
            "Company" => array( "id", "email", "phone", "title", "ico", "dic", "icdph", "address", "description", "iban", "swift", "bank", "user" ),
            "User" => array(),
            "Address" => array( "city", "street", "street_number", "postal_code", "state" )
        );
        $where = array( 
            "and" => array(
                "Company.id" => $data['id'],
                "Company.user" => $data['user']
            )
        );

        $result = $db->tables['Company']->select($select, $db, $where);
        return $result;
    }

    private function tab_get_companies_byUserInvoices($data, $db) {
        $allowed_keys = array( "user" );
        $data = filter_table_data($data, $allowed_keys);
        $data['user'] = $this->id;

        $sql = <<<TEXT
            SELECT Company.id, Company.title, Company.description, Company.email, Company.phone, Company.ico, Company.dic, Company.icdph, Company.iban, Company.swift, Company.bank,
                Address.city, Address.street, Address.street_number, Address.postal_code, Address.state
            FROM Company
            INNER JOIN Address ON Company.address = Address.id
            INNER JOIN Invoice ON Company.id = Invoice.customer
            INNER JOIN Company as Suplier ON Invoice.suplier = Suplier.id
            INNER JOIN User ON Suplier.user = User.id
            WHERE User.id = ?
            GROUP BY Company.id
        TEXT;
        $params = array(
            $data['user']
        );

        $result = $db->querySQL($sql, $params);
        return $result;
    }

    private function tab_get_customer($data, $db) {
        $allowed_keys = array( "user", "id" );
        $data = filter_table_data($data, $allowed_keys);
        $data['user'] = $this->id;

        $sql = <<<TEXT
            SELECT Company.id, Company.title, Company.description, Company.email, Company.phone, Company.ico, Company.dic, Company.icdph, Company.iban, Company.swift, Company.bank,
                Address.city, Address.street, Address.street_number, Address.postal_code, Address.state
            FROM Company
            INNER JOIN Address ON Company.address = Address.id
            INNER JOIN Invoice ON Company.id = Invoice.customer
            INNER JOIN Company as Suplier ON Invoice.suplier = Suplier.id
            INNER JOIN User ON Suplier.user = User.id
            WHERE User.id = ? AND Company.id = ?
            GROUP BY Company.id
        TEXT;
        $params = array(
            $data['user'],
            $data['id']
        );

        $result = $db->querySQL($sql, $params);
        return $result;
    }

    private function tab_update_company($data, $db) {
        $allowed_keys = array(
            "id", "title", "description", "ico", "dic", "icdph", "iban", "swift", "bank", "address", "email", "phone"
        );
        $data = filter_table_data($data, $allowed_keys);

        if(empty($data['id'])) {
            return $this->tab_insert_company($data, $db);
        }
        
        $insert = array(
            "title" => $data['title'],
            "description" => !empty($data['description']) ? $data['description'] : "",
            "email" => !empty($data['email']) ? $data['email'] : "",
            "phone" => !empty($data['phone']) ? $data['phone'] : "",
            "ico" => $data['ico'],
            "dic" => !empty($data['dic']) ? $data['dic'] : null,
            "icdph" => !empty($data['icdph']) ? $data['icdph'] : null,
            "iban" => !empty($data['iban']) ? $data['iban'] : "",
            "swift" => !empty($data['swift']) ? $data['swift'] : "",
            "bank" => !empty($data['bank']) ? $data['bank'] : "",
            "address" => $data['address']
        );
        $where = array( "id" => $data['id'] );

        $db->tables['Company']->update($insert, $db, $where);
        return array(array("id" => $data['id']));
    }
    
}

trait db_company_guest {
    
    use db_company {
        tab_get_company as public;
        tab_insert_company as protected;
        tab_insert_customer as protected;
        tab_update_company as protected;
        tab_get_customer_by_id as protected;
    }
}

trait db_company_user {
    
    use db_company_guest {
        tab_get_companies_byUserInvoices as public;
        tab_get_customer as public;
    }

    public function tabU_get_user_companies($data, $db) {
        $data['user'] = $this->id;

        $select = array(
            "Company" => array( "id", "title", "email", "phone", "ico", "dic", "icdph", "address", "description", "iban", "swift", "bank", "user" ),
            "User" => array(),
            "Address" => array( "city", "street", "street_number", "postal_code", "state" )
        );
        $where = array( 
            "Company.user" => $data['user']
        );

        $result = $db->tables['Company']->select($select, $db, $where);
        return $result;
    }

}
<?php
include_once(__DIR__ . '/../functions.php');

trait db_invoice {

    private function tab_insert_invoice($data, $db) {
        $data_keys = array(
            "customer", "suplier", "title", "description", "total", "vat", "total_vat", "created", "suplied", "due_date", "status"
        );
        $data = filter_table_data($data, $data_keys);
        $data['status'] = 0;  // open
        $data['created'] = !empty($data['created']) ? $data['created'] : date('Y-m-d H:i:s');
        $data['suplied'] = !empty($data['suplied']) ? $data['suplied'] : date('Y-m-d H:i:s');
        $data['due_date'] = !empty($data['due_date']) ? $data['due_date'] : date('Y-m-d H:i:s', strtotime("+14 days"));

        $insert = array(
            "customer" => $data['customer'],
            "suplier" => $data['suplier'],
            "title" => $data['title'],
            "description" => !empty($data['description']) ? $data['description'] : "",
            "total" => $data['total'],
            "vat" => !empty($data['vat']) ? $data['vat'] : 0,
            "total_vat" => !empty($data['total_vat']) ? $data['total_vat'] : $data['total'],
            "status" => $data['status'],
            "created" => $data['created'],
            "suplied" => $data['suplied'],
            "due_date" => $data['due_date']
        );

        $result = $db->tables['Invoice']->insert($insert, $db);
        return array(array("id" => $db->lastInsertId()));
    }

    private function tab_get_invoice($data, $db) {
        $data_keys = array( "id" );
        $data = filter_table_data($data, $data_keys);
        $data['user'] = $this->id;

        $sql = <<<TEXT
            SELECT Invoice.id, Invoice.customer, Invoice.suplier, Invoice.title, Invoice.total, Invoice.vat, Invoice.total_vat, Invoice.created, Invoice.suplied, Invoice.due_date, Invoice.status
            FROM Invoice
            INNER JOIN Company ON Invoice.suplier = Company.id
            INNER JOIN User ON Company.user = User.id
            WHERE Invoice.id = ? AND User.id = ?
        TEXT;
        $params = array(
            $data['id'],
            $data['user']
        );

        $result = $db->querySQL($sql, $params);
        return $result;
    }

    private function tab_get_user_invoices_byUser($data, $db) {
        $data_keys = array( "user" );
        $data = filter_table_data($data, $data_keys);
        $data['user'] = $this->id;

        $sql = <<<TEXT
            SELECT Invoice.id, Invoice.title, Invoice.total, Invoice.vat, Invoice.total_vat, Invoice.created, Invoice.suplied, Invoice.due_date, Invoice.status,
                Customer.title as customer_title
            FROM Invoice
            INNER JOIN Company as Customer ON Invoice.customer = Customer.id
            INNER JOIN Company ON Invoice.suplier = Company.id
            INNER JOIN User ON Company.user = User.id
            WHERE User.id = ?
        TEXT;
        $params = array(
            $data['user']
        );

        $result = $db->querySQL($sql, $params);
        return $result;
    }

    private function tab_update_invoice($data, $db) {
        $data_keys = array(
            "id", "customer", "suplier", "title", "description", "total", "vat", "total_vat", "created", "suplied", "due_date", "status"
        );
        $data = filter_table_data($data, $data_keys);
        if(empty($data['status'])) { $data['status'] = 0; }     // open
        $data['created'] = !empty($data['created']) ? $data['created'] : date('Y-m-d');
        $data['suplied'] = !empty($data['suplied']) ? $data['suplied'] : date('Y-m-d');
        $data['due_date'] = !empty($data['due_date']) ? $data['due_date'] : date('Y-m-d', strtotime("+14 days"));

        $update = array(
            "customer" => $data['customer'],
            "suplier" => $data['suplier'],
            "title" => $data['title'],
            "description" => !empty($data['description']) ? $data['description'] : "",
            "total" => $data['total'],
            "vat" => !empty($data['vat']) ? $data['vat'] : 0,
            "total_vat" => !empty($data['total_vat']) ? $data['total_vat'] : $data['total'],
            "status" => $data['status'],
            "created" => $data['created'],
            "suplied" => $data['suplied'],
            "due_date" => $data['due_date']
        );
        $where = array(
            "id" => $data['id']
        );

        $result = $db->tables['Invoice']->update($update, $db, $where);
        return array(array("id" => $data['id']));
    }

    private function tab_delete_invoice($data, $db) {
        $data_keys = array( "id" );
        $data = filter_table_data($data, $data_keys);

        $where = array(
            "id" => $data['id']
        );
        $result = $db->tables['Invoice']->delete($db, $where);
        return array(array("id" => $data['id']));
    }
    
}

trait db_invoice_guest {
    use db_invoice {
        tab_insert_invoice as protected;
        tab_get_invoice as protected;
        tab_update_invoice as protected;
        tab_delete_invoice as protected;
    }
}

trait db_invoice_user {
    use db_invoice_guest {
        tab_get_user_invoices_byUser as public;
    }
}

trait db_invoice_admin {
    use db_invoice_user;
}
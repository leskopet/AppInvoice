<?php
include_once(__DIR__ . '/../functions.php');

trait db_invoice_item {

    private function tab_insert_invoice_item($data, $db) {
        $data_keys = array( "invoice", "title", "quantity", "unit", "price" );
        $data = filter_table_data($data, $data_keys);

        $insert = array(
            "invoice" => $data['invoice'],
            "title" => $data['title'],
            "quantity" => $data['quantity'],
            "unit" => $data['unit'],
            "price" => $data['price']
        );

        $result = $db->tables['Invoice_item']->insert($insert, $db);
        return array(array("id" => $db->lastInsertId()));
    }

    private function set_invoice_items($data, $db) {
        $invoice_id = $data['invoice'];
        foreach($data['items'] as $item) {
            $item['invoice'] = $invoice_id;
            if(isset($item['id']) && !empty($item['id'])) {
                if(isset($item['title']) && !empty($item['title'])) {
                    $result = $this->tab_update_invoice_item($item, $db);
                    continue;
                }
                $result = $this->tab_delete_invoice_item($item, $db);
                continue;
            }
            $result = $this->tab_insert_invoice_item($item, $db);
        }
        return true;
    }

    private function tab_get_invoice_items_byInvoice($data, $db) {
        $data_keys = array( "invoice" );
        $data = filter_table_data($data, $data_keys);
        $data['user'] = $this->id;

        $sql = <<<TEXT
            SELECT Invoice_item.id, Invoice_item.title, Invoice_item.quantity, Invoice_item.unit, Invoice_item.price
            FROM Invoice_item
            INNER JOIN Invoice ON Invoice_item.invoice = Invoice.id
            INNER JOIN Company ON Invoice.suplier = Company.id
            INNER JOIN User ON Company.user = User.id
            WHERE Invoice_item.invoice = ? AND User.id = ?
        TEXT;
        $params = array( 
            $data['invoice'],
            $data['user']
        );

        $result = $db->querySQL($sql, $params);
        return $result;
    }

    private function tab_update_invoice_item($data, $db) {
        $data_keys = array( "id", "invoice", "title", "quantity", "unit", "price" );
        $data = filter_table_data($data, $data_keys);

        $update = array(
            "invoice" => $data['invoice'],
            "title" => $data['title'],
            "quantity" => $data['quantity'],
            "unit" => $data['unit'],
            "price" => $data['price']
        );
        $where = array(
            "id" => $data['id']
        );

        $result = $db->tables['Invoice_item']->update($update, $db, $where);
        return array(array("id" => $data['id']));
    }

    private function tab_delete_invoice_item($data, $db) {
        $data_keys = array( "id" );
        $data = filter_table_data($data, $data_keys);

        $where = array(
            "id" => $data['id']
        );

        $result = $db->tables['Invoice_item']->delete($db, $where);
        return array(array("id" => $data['id']));
    }
    
}

trait db_invoice_item_guest {
    use db_invoice_item {
        tab_get_invoice_items_byInvoice as protected;
        set_invoice_items as protected;
    }
}

trait db_invoice_item_user {
    use db_invoice_item_guest;
}

trait db_invoice_item_admin {
    use db_invoice_item_user;
}
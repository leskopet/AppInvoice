<?php

include_once(__DIR__ . '/../functions.php');

trait feat_invoice {

    private function create_invoice($data, $db) {

        if(empty($this->id)) {
            return $data;
        }

        $result = $this->update_company_profile($data['suplier'], $db);
        $data['suplier'] = $result[0]['id'];
        $result = $this->update_customer_profile($data['customer'], $db);
        $data['customer'] = $result[0]['id'];

        $result = $this->tab_insert_invoice($data, $db);
        $data['id'] = $result[0]['id'];

        $data['invoice'] = $data['id'];
        $result = $this->set_invoice_items($data, $db);

        $result = $this->get_invoice_alldata($data, $db);
        return $result;
    }

    private function get_invoice_alldata($data, $db) {
        $data_keys = array( "id" );
        $data = filter_table_data($data, $data_keys);

        $invoice = array();

        $result = $this->tab_get_invoice($data, $db);
        $invoice = $result[0];

        $result = $this->tab_get_invoice_items_byInvoice(array("invoice" => $invoice['id']), $db);
        $invoice['items'] = $result;

        $result = $this->tab_get_customer_by_id(array("id" => $invoice['customer']), $db);
        $invoice['customer'] = $result[0];

        $result = $this->tab_get_company(array("id" => $invoice['suplier']), $db);
        $invoice['suplier'] = $result[0];
        return array($invoice);
    }

    private function set_invoice($data, $db) {

        if(empty($this->id)) {
            return $data;
        }
        if(empty($data['id'])) {
            return $this->create_invoice($data, $db);
        }

        $result = $this->update_company_profile($data['suplier'], $db);
        $data['suplier'] = $result[0]['id'];
        $result = $this->update_customer_profile($data['customer'], $db);
        $data['customer'] = $result[0]['id'];

        $result = $this->tab_update_invoice($data, $db);
        $data['id'] = $result[0]['id'];

        $data['invoice'] = $data['id'];
        $result = $this->set_invoice_items($data, $db);

        $result = $this->get_invoice_alldata($data, $db);
        return $result;
    }

    private function delete_invoice($data, $db) {
        if(empty($this->id)) {
            $response['status'] = 'error';
            $response['message'] = 'Access denied';
            echo json_encode($response);
            exit;
        }

        $result = $this->tab_get_user_invoices_byUser($data, $db);
        if(empty($result)) {
            $response['status'] = 'error';
            $response['message'] = 'Invoice not found';
            echo json_encode($response);
            exit;
        }
        $result = $this->tab_delete_invoice($data, $db);
        return true;
    }

    private function get_statistics($data, $db) {
        if(empty($this->id)) {
            $response['status'] = 'error';
            $response['message'] = 'Access denied';
            echo json_encode($response);
            exit;
        }
        $statistics = array(
            'invoices' => array(
                'count_total' => 0,
                'count_open' => 0,
                'count_sent' => 0,
                'count_paid' => 0,
                'count_overdue' => 0,
                'sum_total' => 0,
                'sum_open' => 0,
                'sum_expected' => 0,
                'sum_paid' => 0,
                'sum_overdue' => 0
            ),
            'customers' => array(
                'count_total' => 0,
            ),
            'upcoming_deadlines' => array(),
            'overdue_invoices' => array()
        );

        $result = $this->tab_get_user_invoices_byUser($data, $db);
        $statistics['invoices']['count_total'] = count($result);
        $statistics['invoices']['sum_total'] = array_sum(array_column($result, 'total'));
        foreach($result as $invoice) {
            switch($invoice['status']) {
                case 0:
                    $statistics['invoices']['count_open']++;
                    $statistics['invoices']['sum_open'] += $invoice['total'];
                    break;
                case 1:
                    $statistics['invoices']['count_sent']++;
                    $statistics['invoices']['sum_expected'] += $invoice['total'];
                    $statistics['upcoming_deadlines'][] = array(
                        'id' => $invoice['id'],
                        'invoice' => $invoice['title'],
                        'customer' => $invoice['customer_title'],
                        'date' => $invoice['due_date']
                    );
                    break;
                case 2:
                    $statistics['invoices']['count_paid']++;
                    $statistics['invoices']['sum_paid'] += $invoice['total'];
                    break;
                case 3:
                    $statistics['invoices']['count_overdue']++;
                    $statistics['invoices']['sum_overdue'] += $invoice['total'];
                    $statistics['overdue_invoices'][] = array(
                        'id' => $invoice['id'],
                        'invoice' => $invoice['title'],
                        'customer' => $invoice['customer']['title'],
                        'date' => $invoice['due_date']
                    );
                    break;
            }
        }

        $result = $this->tab_get_companies_byUserInvoices($data, $db);
        $statistics['customers']['count_total'] = count($result);

        return $statistics;
    }

    private function set_invoice_status($data, $db) {
        if(empty($this->id)) {
            $response['status'] = 'error';
            $response['message'] = 'Access denied';
            echo json_encode($response);
            exit;
        }

        $result = $this->tab_get_invoice($data, $db);
        if(empty($result)) {
            $response['status'] = 'error';
            $response['message'] = 'Invoice not found';
            echo json_encode($response);
            exit;
        }

        $invoice = $result[0];
        if ($data['status'] < 0 || $data['status'] > 3) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid status';
            echo json_encode($response);
            exit;
        }
        $invoice['status'] = $data['status'];
        return $this->tab_update_invoice($invoice, $db);
    }

}

trait feat_invoice_guest {
    use feat_invoice {
        create_invoice as public;
        get_invoice_alldata as protected;
        delete_invoice as public;
    }
}

trait feat_invoice_user {
    use feat_invoice_guest {
        get_invoice_alldata as public;
        set_invoice as public;
        get_statistics as public;
        set_invoice_status as public;
    }
}

trait feat_invoice_admin {
    use feat_invoice_user;
}
<?php

include_once __DIR__ . '/../db_tb/db_device.php';

class System
{
    use db_device,
        db_auto_login;

    public $auth;
    public $data;
    public $action;
    public $db;
    public $user;

    public function __construct($params, $db) {
        // filter params
        $params = filter_data($params);

        // validate params
        if(!validation($params) && !TEST_MODE) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid data';
            echo json_encode($response);
            exit;
        }

        $this->auth = $params['auth'];
        $this->data = $params['data'];
        $this->action = $params['action'];
        $this->db = $db;

        // new device log on
        if(empty($this->auth['device'])) {
            $result = $this->tab_insert_device($this->data, $this->db);
            $this->auth['device'] = $result[0]['id'];
        }

    }

    public function authenticate_user() {
        $result = $this->tab_user_authenticate($this->auth, $this->db);
        return $result;
    }

    public function buildUserData($result) {
        $user_data = array();
        
        if(count($result) >= 2) {   // multiple users autologged on one device
            $response['status'] = 'error';
            $response['message'] = 'Database integrity error';
            echo json_encode($response);
            exit;
        }
        $user_data['role'] = 0;
        if(!empty($result)) {
            // user logged in
        
            $user_data['id'] = $result[0]['id'];
            $user_data['fname'] = $result[0]['fname'];
            $user_data['lname'] = $result[0]['lname'];
            $user_data['role'] = $result[0]['role'];
            $user_data['tag'] = $result[0]['tag'];
        }
        
        $user_data['email'] = $this->auth['email'];
        $user_data['device'] = $this->auth['device'];
        return $user_data;
    }

    public function setUser($user_data) {
        global $user_actions;
    
        if(!isset($user_data)) {
            $response['status'] = 'error';
            $response['message'] = 'User not found';
            echo json_encode($response);
            exit;
        }
    
        if(!isset($user_actions[$user_data['role']][$this->action])) {     // action not found
            $response['status'] = 'error';
            $response['message'] = 'Invalid action';
            echo json_encode($response);
            exit;
        }
        
        switch($user_data['role']) {
            case 0:
                $user = new Guest( $user_data );
                break;
            case 1:
                $user = new User( $user_data );
                break;
            case 9:
                $user = new Admin( $user_data );
                break;
            default:
                $user = new Guest( $user_data );
                break;
        }
        $this->user = $user;
        return $user;
    }

}
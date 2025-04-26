<?php

include_once(__DIR__ . '/../functions.php');

trait feat_system {
    
    private function send_empty_success() {
        return array();
    }

    private function register_device($data, $db) {
        $result = array("device" => $this->device);
        return $result;
    }

    private function send_mail($data, $db) {
        $params = $data;

        require('send-mail.php');
        return 'mail sended';
    }

    private function autologin($data, $db) {
        $result = $this->tab_get_autologin($data, $db);

        if(empty($result)) {
            $response['status'] = 'error';
            $response['message'] = 'no autologin found';
            echo json_encode($response);
            exit;
        }
        $params = array(
            'id' => $result[0]['id']
        );
        $result = $this->tab_update_user_signin($params, $db);
        return $this->tab_get_user($params, $db);
    }

    private function signin($data, $db) {
        $data['signin'] = true;
        $result = $this->tab_get_user($data, $db);

        if(empty($result)) {
            $response['status'] = 'wrong user';
            $response['message'] = 'wrong user';
            echo json_encode($response);
            exit;
        }
        if(is_null($result[0]['password'])) {
            // password not initialized yet
            $response['status'] = 'wrong user';
            $response['message'] = 'wrong user';
            echo json_encode($response);
            exit;
        }

        // check password
        if(empty($data['password_hashed'])) {
            if(!password_verify($data['password'], $result[0]['password'])) {
                $response['status'] = 'wrong password';
                $response['message'] = 'wrong password';
                echo json_encode($response);
                exit;
            }
        }
        else {
            if(!hash_equals($result[0]['password'], $data['password_hashed'])) {
                $response['status'] = 'wrong password';
                $response['message'] = 'wrong password';
                echo json_encode($response);
                exit;
            }
        }

        if ((int)$result[0]['tag'] == 0) {
            $response['status'] = 'user deactivated';
            $response['message'] = 'user deactivated';
            echo json_encode($response);
            exit;
        }

        $this->id = $result[0]['id'];
        $data = array(
            "id" => $this->id
        );
        $result = $this->tab_insert_autologin($data, $db);
        $result = $this->tab_update_user_signin($data, $db);

        // unset($data['signin']);
        return $this->tab_get_user($data, $db);
    }

    private function signup($data, $db) {
        $result = $this->tab_get_user($data, $db);
        if(!empty($result)) {
            $response['status'] = 'user already exists';
            $response['message'] = 'user already exists';
            echo json_encode($response);
            exit;
        }

        $data['role'] = 1;
        if(empty($data['tag'])) { $data['tag'] = 1; }

        $result = $this->tab_insert_user($data, $db);
        if(empty($result)) {
            $response['status'] = 'error';
            $response['message'] = 'create error';
            echo json_encode($response);
            exit;
        }
        $this->id = $result[0]['id'];

        /** @var $params - params for inserting device (id, device) - (user is and device from getMachineId()) */
        $params = array(
            "id" => $this->id,
            "device" => $this->device
        );

        $device = $this->tab_get_device($params, $db);
        if (empty($device)) {
            $device = $this->tab_insert_device($params, $db);
        }

        $verification_token = $result[0]['verification_token'];
        $is_verified = $result[0]['is_verified'];

        // $params = array(
        //     'receiver' => $data['email'],
        //     'senderEmail' => 'noreply@resap.sk',
        //     'senderName' => 'RESAP',
        //     'messageTemplateLink' => 'mail-signup-user-to-user',
        //     'user_email' => $data['email']
        // );
        // $this->send_mail($params, $db);

        // $params['receiver'] = 'resap@resap.sk';
        // $params['messageTemplateLink'] = 'mail-signup-user-to-app';
        // $this->send_mail($params, $db);

        if ($is_verified == 1 || true){
            $result = $this->tab_insert_autologin($data, $db);
            return $this->retrieve_user($data, $db);
        }

        // $params = array(
        //     'receiver' => $data['email'],
        //     'senderEmail' => 'noreply@resap.sk',
        //     'senderName' => 'RESAP',
        //     'messageTemplateLink' => 'mail-signup-email-verification',
        //     'verification_token' => $verification_token,
        //     'user_email' => $data['email']
        // );
        // $this->send_mail($params, $db);

        $data = array();
        
        return $this->retrieve_user($data, $db);
    }

    private function forgot_password($data, $db) {
        $allowed_keys = array(
            "email"
        );
        $data = filter_table_data($data, $allowed_keys);
        $data['myself'] = true;

        $result = $this->tab_get_user($data, $db);
        if(empty($result)) {
            $response['status'] = 'wrong user';
            $response['message'] = 'wrong user';
            echo json_encode($response);
            exit;
        }
        $this->id = $result[0]['id'];

        $data = array(
            'user' => $this->id
        );
        $result = $this->get_renew_password($data, $db);

        if(!empty($result)) {
            // delete old
            $result = $this->delete_renew_password($data, $db);
        }

        $result = $this->insert_renew_password($data, $db);

        $result = $this->get_renew_password($data, $db);
        if(empty($result)) {
            $response['status'] = 'wrong user';
            $response['message'] = 'wrong user';
            echo json_encode($response);
            exit;
        }

        $params = array(
            'receiver' => $result[0]['email'],
            'senderEmail' => 'noreply@resap.sk',
            'senderName' => 'RESAP',
            'messageTemplateLink' => 'mail-forgot-password',
            'link' => $result[0]['id']
        );
        return $this->send_mail($params, $db);
    }

    private function verify_renew_password($data, $db) {

        $result = $this->get_renew_password($data, $db);
        if(empty($result)) {
            $response['status'] = 'error';
            $response['message'] = 'not found';
            echo json_encode($response);
            exit;
        }
        $params = array(
            'user' => $result[0]['user']
        );

        $now = time();
        $expiration = strtotime($result[0]['expiration']);

        if($now > $expiration) {
            $data = array(
                'user' => $result[0]['user']
            );
            $result = $this->delete_renew_password($data, $db);

            $response['status'] = 'expired';
            $response['message'] = 'expired';
            echo json_encode($response);
            exit;
        }
        return $result;
    }

    private function renew_password($data, $db) {
        
        $result = $this->verify_renew_password($data, $db);
        
        $this->id = $result[0]['user'];

        $data['id'] = $this->id;
        $result = $this->update_user_password($data, $db);

        $data = array(
            'user' => $this->id
        );
        $result = $this->delete_renew_password($data, $db);

        return 'success';
    }

    private function update_user_profile($data, $db) {
        $user_id = $this->id;

        // change email
        if($this->email != $data['email']) {
            $params = array(
                'email' => $data['email']
            );
            $result = $this->tab_get_user($params, $db);
            if(!empty($result)) {
                $response['status'] = 'user already exists';
                $response['message'] = 'user already exists';
                echo json_encode($response);
                exit;
            }
        }
        
        // check that user exists
        $result = $this->retrieve_user($data, $db);
        if(empty($result)) {
            $response['status'] = 'error';
            $response['message'] = 'user not found';
            echo json_encode($response);
            exit;
        }

        $address_id = !empty($result[0]['address']) ? $result[0]['address'] : null;

        // update address
        $data['id'] = $address_id;
        $result = $this->tab_update_address($data, $db);

        // update user
        $data['address_id'] = !empty($result[0]['id']) ? $result[0]['id'] : null;
        $data['id'] = $this->id;
        $updated_user = $this->tab_update_user($data, $db);

        return $updated_user;
    }

    private function update_company_profile($data, $db) {
        if(empty($data['id'])) {
            $result = $this->tab_insert_company($data, $db);
            $data['id'] = $result[0]['id'];
        }
        $result = $this->tab_get_company($data, $db);
        if(empty($result)) {
            $response['status'] = 'error';
            $response['message'] = 'company not found';
            echo json_encode($response);
            exit;
        }

        $params = array(
            "state" => empty($data['state']) ? 'Slovakia' : $data['state'],
            "city" => $data['city'],
            "street" => $data['street'],
            "street_number" => $data['street_number'],
            "postal_code" => $data['postal_code'],
            "id" => $result[0]['address']
        );
        $result = $this->tab_update_address($params, $db);

        $data['address'] = !empty($result[0]['id']) ? $result[0]['id'] : null;
        return $this->tab_update_company($data, $db);
    }

    private function update_customer_profile($data, $db) {
        if(empty($data['id'])) {
            $result = $this->tab_insert_customer($data, $db);
            $data['id'] = $result[0]['id'];
        }
        $result = $this->tab_get_customer_by_id($data, $db);
        if(empty($result)) {
            $response['status'] = 'error';
            $response['message'] = 'company not found';
            echo json_encode($response);
            exit;
        }

        $params = array(
            "state" => empty($data['state']) ? 'Slovakia' : $data['state'],
            "city" => $data['city'],
            "street" => $data['street'],
            "street_number" => $data['street_number'],
            "postal_code" => $data['postal_code'],
            "id" => $result[0]['address']
        );
        $result = $this->tab_update_address($params, $db);

        $data['address'] = !empty($result[0]['id']) ? $result[0]['id'] : null;
        return $this->tab_update_company($data, $db);
    }

    private function deactivate_user($data, $db) {
        $this->logout($data, $db);
        
        $params = array(
            "id" => $this->id,
            "tag" => 0
        );
        return $this->tab_update_user_tag($params, $db);
    }

}

trait feat_system_guest {
    use feat_system {
        register_device as public;
        signin as public;
        signup as public;
        autologin as public;
        forgot_password as public;
        verify_renew_password as public;
        renew_password as public;
    }
}

trait feat_system_user {

    use feat_system_guest {
        update_user_profile as public;
        update_company_profile as public;
        update_customer_profile as protected;
        deactivate_user as public;
    }

    public function logout($data, $db) {
        $allowed_keys = array(
            "email", "device"
        );
        $data['email'] = $this->email;
        $data['device'] = $this->device;
        $data = filter_table_data($data, $allowed_keys);

        $select = array(
            "Auto_login" => array( 'id' ),
            "User" => array(),
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

        if(empty($result)) {
            return true;
        }
        
        return $this->tab_delete_autologin($result[0], $db);
    }

}

trait feat_system_admin {
    use feat_system_user;
}


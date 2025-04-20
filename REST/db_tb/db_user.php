<?php
include_once(__DIR__ . '/../functions.php');

trait db_user {

    private function create_user_table($db) {
        $sql = <<<TEXT
            CREATE TABLE AppInvoice.User (
                id SERIAL PRIMARY KEY,
                fname VARCHAR(255) NOT NULL,
                lname VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                phone VARCHAR(255) NOT NULL,
                description TEXT NOT NULL DEFAULT '',
                address BIGINT UNSIGNED,
                role INTEGER NOT NULL DEFAULT 0,
                tag INTEGER NOT NULL DEFAULT 0,
                created_DT TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_signin TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                password VARCHAR(255),
                is_verified BOOLEAN NOT NULL DEFAULT FALSE,
                FOREIGN KEY (address) references Address(id) ON DELETE SET NULL ON UPDATE CASCADE
            )
        TEXT;
        return $db->querySQL( $sql);
    }

    private function tab_insert_user($data, $db) {
        $allowed_keys = array(
            "fname", "lname", "email", "phone", "description", "role", "tag",
            "password", "password_hashed", "title",
            "verification_token", "is_verified"
        );
        $data = filter_table_data($data, $allowed_keys);


        // hash password
        if(empty($data['password_hashed'])) {
            $passwordHash = (empty($data['password']) && $data['tag'] == 14)
                ? password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT)
                : password_hash($data['password'], PASSWORD_DEFAULT);
        }
        else {
            $passwordHash = $data['password_hashed'];
        }

        $insert = array(
            "fname" => $data['fname'],
            "lname" => !empty($data['lname']) ? $data['lname'] : "",
            "email" => $data['email'],
            "phone" => $data['phone'],
            "description" => !empty($data['description']) ? $data['description'] : "",
            "address" => !empty($data['address']) ? $data['address'] : null,
            "role" => $data['role'],
            "tag" => !empty($data['tag']) ? $data['tag'] : 0,
            "password" => $passwordHash,
            "is_verified" => !empty($data['is_verified']) ? 1 : 0
        );

        $db->tables['User']->insert($insert, $db);
        $result = array(array(
            'id' => $db->lastInsertId(),
            'is_verified' => $insert['is_verified']
        ));

        return $result;
    }

    private function tab_get_user($data, $db) {
        $allowed_keys = array(
            "id", "email", "phone", "calendar", "signin", "myself", "tag"
        );
        $data = filter_table_data($data, $allowed_keys);

        $select = array(
            "User" => array(
                "id", "fname", "lname", "email", "phone", "description", "address", "role", "tag", "is_verified",
            ),
            "Address" => array( "city", "street", "street_number", "postal_code", "state" )
        );

        if(!empty($data['signin'])) { 
            $select = array(
                "User" => array( "id", "password", "tag" )
            );
        }

        if(!empty($data['myself'])) {
            $select = array(
                "User" => array( "id" )
            );
        }
    
        // build condition and params
        if(!empty($data['id'])) {
            $where = array( 'User.id' => $data['id'] );
        }
        else if(!empty($data['email'])) {
            $where = array( 'User.email' => $data['email'] );
        }
        else if(!empty($data['phone'])) {
            $where = array( 'User.phone' => $data['phone'] );
        }
        else {
            $where = null;
        }

        $result = $db->tables['User']->select($select, $db, $where);
        return $result;
    }

    private function get_users($data, $db) {
        return $this->get_user($data, $db);
    }

    private function tab_update_user($data, $db) {
        $allowed_keys = array(
            "id", "fname", "lname", "email", "phone", "description", "address_id"
        );
        $data = filter_table_data($data, $allowed_keys);

        $update = array(
            "fname" => $data['fname'],
            "lname" => !empty($data['lname']) ? $data['lname'] : "",
            "email" => $data['email'],
            "phone" => $data['phone'],
            "description" => !empty($data['description']) ? $data['description'] : "",
            "address" => !empty($data['address_id']) ? $data['address_id'] : null
        );
        $where = array( "id" => $data['id'] );
        
        $result = $db->tables['User']->update($update, $db, $where);
        return true;
    }

    private function update_user_email_verification($data, $db) {
        $allowed_keys = array(
            "id", "is_verified"
        );

        $sql = <<<TEXT
            UPDATE "User"
            SET is_verified = $1
            WHERE "User".id = $2
        TEXT;

        $params = array(
            $data['is_verified'],
            $data['id']
        );

        return $db->querySQL($sql, $params);
    }

    private function tab_update_user_signin($data, $db) {
        $allowed_keys = array(
            "id", "lastsignin"
        );
        $data['lastsignin'] = date("Y-m-d H:i:s");
        $data = filter_table_data($data, $allowed_keys);

        $update = array(
            "last_signin" => $data['lastsignin']
        );
        $where = array(
            "User.id" => $data['id']
        );
        $result = $db->tables['User']->update($update, $db, $where);
        
        return true;
    }

    private function update_user_password($data, $db) {
        $allowed_keys = array(
            "id", "password"
        );
        $data = filter_table_data($data, $allowed_keys);
        
        $sql = <<<TEXT
            UPDATE "User"
            SET password = $2
            WHERE "User".id = $1
        TEXT;
        
        if(empty($data['id'])) {
            $response['status'] = 'error';
            $response['message'] = 'Missing id';
            echo json_encode($response);
            exit;
        }
        $params = array(
            $data['id'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        );
        
        return $db->querySQL($sql, $params);
    }

    private function tab_update_user_tag($data, $db) {
        $allowed_keys = array(
            "id", 'tag'
        );
        $data = filter_table_data($data, $allowed_keys);
        $data['id'] = $this->id;

        $update = array(
            "tag" => $data['tag']
        );
        $where = array(
            "User.id" => $data['id']
        );
        $result = $db->tables['User']->update($update, $db, $where);

        return true;
    }

    private function tab_update_user_role($data, $db) {
        $allowed_keys = array(
            "id", "role"
        );
        $data = filter_table_data($data, $allowed_keys);
        $data['id'] = $this->user_id;

        $update = array(
            "role" => $data['role']
        );
        $where = array(
            "User.id" => $data['id']
        );
        $result = $db->tables['User']->update($update, $db, $where);
        
        return true;
    }

}

trait db_user_guest {
    
    use db_user {
        tab_get_user as public;
        update_user_email_verification as public;
        tab_update_user_signin as public;
        tab_update_user_tag as public;
    }
}

trait db_user_user {
    
    use db_user_guest {
        tab_update_user as protected;
    }
}

trait db_user_admin {
    
    use db_user_user;
}


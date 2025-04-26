<?php

include_once(__DIR__ . '/../db_tb/db_user.php');
include_once(__DIR__ . '/../db_tb/db_auto_login.php');
include_once(__DIR__ . '/../db_tb/db_device.php');
include_once(__DIR__ . '/../db_tb/db_company.php');
include_once(__DIR__ . '/../db_tb/db_address.php');
include_once(__DIR__ . '/../db_tb/db_invoice.php');
include_once(__DIR__ . '/../db_tb/db_invoice_item.php');

include_once(__DIR__ . '/../feat/feat_system.php');
include_once(__DIR__ . '/../feat/feat_invoice.php');

include_once(__DIR__ . '/../functions.php');

class Guest 
{
    use db_user_guest,
        db_auto_login_guest,
        db_device_guest,
        db_company_guest,
        db_invoice_guest,
        db_invoice_item_guest;    
    
    use feat_system_guest,
        feat_invoice_guest;

    protected $device;
    protected $id;
    protected $fname;
    protected $lname;
    protected $email;
    protected $phone;
    protected $role;
    protected $tag;
    protected $verification_token;
    protected $is_verified;

    public function __construct($data) {
        $allowed_keys = array(
            'device', 'id', 'fname', 'lname', 'email', 'phone', 'role', 'tag', 'calendar',
            'verification_token', 'is_verified'
        );
        $data = filter_table_data($data, $allowed_keys);

        $this->device =     !empty($data['device']) ? $data['device']   : "";
        $this->id =         !empty($data['id']) ? $data['id']           : 0;
        $this->fname =      !empty($data['fname']) ? $data['fname']     : "";
        $this->lname =      !empty($data['lname']) ? $data['lname']     : "";
        $this->email =      !empty($data['email']) ? $data['email']     : "";
        $this->phone =      !empty($data['phone']) ? $data['phone']     : "";
        $this->role =       !empty($data['role']) ? $data['role']       : 0;
        $this->tag =        !empty($data['tag']) ? $data['tag']         : 0;
        $this->verification_token = !empty($data['verification_token']) ? $data['verification_token'] : "";
        $this->is_verified = !empty($data['is_verified']) ? $data['is_verified'] : false;
    }

    public function set_id($id) { $this->id = $id; }

    public function retrieve_user($data, $db) {
        $data = array();
        if(empty($this->id)) {
            return $this->send_empty_success();
        }
        $data['id'] = $this->id;
        return $this->tab_get_user($data, $db);
    }

}

class User extends Guest
{
    use db_user_user,
        db_company_user,
        db_address_user,
        db_invoice_user;

    use feat_system_user,
        feat_invoice_user;
    
    public function __construct($data)
    {
        parent::__construct($data);
    }
}

class Admin extends User
{
    use db_user_admin;

    use feat_system_admin;
    
    public function __construct($data)
    {
        parent::__construct($data);
    }
}
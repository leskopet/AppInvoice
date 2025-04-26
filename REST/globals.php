<?php

$preset_db = array(
    "root" => array(
        "type" => "mysql",
        "host" => "localhost",
        "port" => 3306,
        "username" => "root",
        "password" => ""
    ),
    "AppInvoice" => array(
        "type" => "mysql",
        "host" => "localhost",
        "port" => 3306,
        "dbname" => "AppInvoice",
        "username" => "AppInvoice",
        "password" => "AppInvoice",
        "appname" => "AppInvoice",
        "tables" => array(
            "Device" => array(
                "id", "created_at", "last_login"
            ),
            "Address" => array(
                "id", "state", "region", "city", "street", "street_number", "postal_code"
            ),
            "User" => array(
                "id", "fname", "lname", "email", "phone", "description", "address", "role", "tag", "created_DT", "last_signin", "password", "is_verified"
            ),
            "Auto_login" => array(
                "id", "user", "device"
            ),
            "Company" => array(
                "id", "user", "title", "description", "email", "phone", "ico", "dic", "icdph", "iban", "swift", "bank", "address"
            ),
            "Invoice" => array(
                "id", "suplier", "customer", "title", "description", "total", "vat", "total_vat", "status", "created", "suplied", "due_date"
            ),
            "Invoice_item" => array(
                "id", "invoice", "title", "description", "quantity", "unit", "price"
            )
        )
    )
);

$data_keys = array(
    array(
        "auth","data","action"
    ),
    array(
        "id","email","device",
        "title","description","tag",
        "fname","lname","phone","password","role","calendar","address","user_email",
        "verification_token", "is_verified", "password_hashed",
        "ico","dic","icdph","iban","swift","bank",
        "state","city","street","street_number","postal_code",
        "total","vat","total_vat","status","created","suplied","due_date",
        "suplier","customer","items"
    ),
    array(
        "id","title","description","tag","email","phone",
        "ico","dic","icdph","iban","swift","bank",
        "state","city","street","street_number","postal_code",
    ),
    array(
        "id","title","description","quantity","price","unit"
    )
);

$user_actions = array(
    array(      // guest
        'registerdevice' => 'register_device',
        'auth' => 'retrieve_user',
        'autologin' => 'autologin',
        'signin' => 'signin',
        'signup' => 'signup',
        'signupcompany' => 'signup_company',
        'forgotpassword' => 'forgot_password',
        'getrenewpassword' => 'verify_renew_password',
        'renewpassword' => 'renew_password',
        'createinvoice' => 'create_invoice'
    ),
    array(      // logged in user
        'auth' => 'retrieve_user',
        'autologin' => 'autologin',
        'logout' => 'logout',
        'getusercompanies' => 'tabU_get_user_companies',
        'getcompany' => 'tab_get_company',
        'updateuser' => 'update_user_profile',
        'updatecompany' => 'update_company_profile',
        'createinvoice' => 'create_invoice',
        'getinvoice' => 'get_invoice_alldata',
        'getuserinvoices' => 'tab_get_user_invoices_byUser',
        'updateinvoice' => 'set_invoice',
        'deleteinvoice' => 'delete_invoice',
        'getcustomers' => 'tab_get_companies_byUserInvoices',
        'getcustomer' => 'tab_get_customer',
        'getstatistics' => 'get_statistics',
        'setstatus' => 'set_invoice_status',
        'deactivateuser' => 'deactivate_user'
    ),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(  // admin
        'auth' => 'retrieve_user',
        'autologin' => 'autologin',
        'logout' => 'logout',
        'getusercompanies' => 'tabU_get_user_companies',
        'getcompany' => 'tab_get_company',
        'updateuser' => 'update_user_profile',
        'updatecompany' => 'update_company_profile',
        'createinvoice' => 'create_invoice',
        'getinvoice' => 'get_invoice_alldata',
        'getuserinvoices' => 'tab_get_user_invoices_byUser',
        'updateinvoice' => 'set_invoice',
        'deleteinvoice' => 'delete_invoice',
        'getcustomers' => 'tab_get_companies_byUserInvoices',
        'getcustomer' => 'tab_get_customer',
        'getstatistics' => 'get_statistics',
        'setstatus' => 'set_invoice_status',
        'deactivateuser' => 'deactivate_user'
    )
);

?>
<?php

// error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require_once __DIR__ . '/../Functions/BackendConnect.php';

session_start();

// check session for device id
if (!isset($_SESSION['AppInvoice_device']) || empty($_SESSION['AppInvoice_device'])) {
    
    // check cookie for device id
    if (isset($_COOKIE['AppInvoice_device']) && !empty($_COOKIE['AppInvoice_device'])) {
        $_SESSION['AppInvoice_device'] = $_COOKIE['AppInvoice_device'];
    }
    else {
        $action = "registerdevice";
        $data = "";
        $result = sendBackendRequest($action, $data);
        if($result['status'] == 'success') {
            $_SESSION['AppInvoice_device'] = $result['data']['device'];
            setcookie("AppInvoice_device", $result['data']['device'], time() + (86400 * 30), "/");
        }
        else {
            $_SESSION['AppInvoice_device'] = "";
        }
    }
}
$device = $_SESSION['AppInvoice_device'];

// check session for user
if(!isset($_SESSION['AppInvoice_user']) || empty($_SESSION['AppInvoice_user'])) {
    // check cookie for user
    if (isset($_COOKIE['AppInvoice_user']) && !empty($_COOKIE['AppInvoice_user'])) {
        $_SESSION['AppInvoice_user'] = $_COOKIE['AppInvoice_user'];
    }
}
if (isset($_SESSION['AppInvoice_user']) && !empty($_SESSION['AppInvoice_user'])) {
    $action = "autologin";
    $data = array(
        "device" => $device,
        "email" => $_SESSION['AppInvoice_user']
    );
    $result = sendBackendRequest($action, $data);
    if($result['status'] == 'success') {
        $_SESSION['AppInvoice_user'] = $result['data'][0]['email'];
        setcookie("AppInvoice_user", $result['data'][0]['email'], time() + (86400 * 30), "/");
        $user = $result['data'][0];
    }
    else {
        $_SESSION['AppInvoice_user'] = "";
        setcookie("AppInvoice_user", "", time() - 3600, "/");
        $user = array(
            "email" => "",
            "fname" => "",    
            "lname" => ""
        );
    }
    // $user = $_SESSION['AppInvoice_user'];
}
else {
    $user = array(
        "email" => "",
        "fname" => "",    
        "lname" => ""
    );
}



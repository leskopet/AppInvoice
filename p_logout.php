<?php

require_once __DIR__ . '/components/init.php';

if(!empty($user['email'])) {
    $action = "logout";
    $data = array(
        "device" => $device,
        "email" => $user['email']
    );
    $result = sendBackendRequest($action, $data);
    if($result['status'] == 'success') {
        unset($_SESSION['AppInvoice_user']);
        setcookie('AppInvoice_device', '', time() - 3600);
        header("Location: p_login.php");
        exit();
    }
    else {
        $form = array(
            "username" => $user['email'],
            "alert" => $result['message']
        );
    }
}
else {
    header("Location: p_login.php");
    exit();
}

echo $form['alert'];

?>


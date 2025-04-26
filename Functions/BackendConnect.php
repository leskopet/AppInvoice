<?php

function sendBackendRequest($action, $data, $output_log = false) {
    $server = 'http://localhost/AppInvoice';

    $auth = array(
        "device" => "",
        "email" => ""
    );

    if (!isset($_SESSION['AppInvoice_device']) || empty($_SESSION['AppInvoice_device'])) {
        $auth['device'] = "";
    }
    else {
        $auth['device'] = $_SESSION['AppInvoice_device'];
    }
    if(!isset($_SESSION['AppInvoice_user']) || empty($_SESSION['AppInvoice_user'])) {
        $auth['email'] = "";
    }
    else {
        $auth['email'] = $_SESSION['AppInvoice_user'];
    }
        
    $params = array(
        "auth" => $auth,
        "action" => $action,
        "data" => $data
    );

    $params = json_encode($params);
    $request = array("param" => $params);

    // create post request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $server . "/Backend/server.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if($output_log) {
        print_r($response);
    }

    $result = json_decode($response, true);
    return $result;
}

?>
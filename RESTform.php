<?php

// error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/Functions/RESTConnect.php';

$server = 'http://localhost/AppInvoice';

session_start();

// check session for device id
if (!isset($_SESSION['AppInvoice_device']) || empty($_SESSION['AppInvoice_device'])) {
    $action = "registerdevice";
    $data = "";
    $result = sendRESTRequest($action, $data);
    if($result['status'] == 'success') {
        $_SESSION['AppInvoice_device'] = $result['data']['device'];
    }
    else {
        $_SESSION['AppInvoice_device'] = "";
    }
}
$device = $_SESSION['AppInvoice_device'];
echo "Device: " . $device . '<br>';

/////////////////////////////////
$params = array(
    "auth" => array(
        "device" => $device,
        "email" => "peto.lesko@gmail.com"
    ),
    "action" => "getcustomers",
    "data" => array(
        "email" => "peto.lesko@gmail.com"
    )
);
/////////////////////////////////

$params = json_encode($params);
echo "Request: " . $params . '<br>';

$request = array("param" => $params);

// create post request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $server . "/REST/server.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Response: " . $response . '<br>';

?>
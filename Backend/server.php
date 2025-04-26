<?php

header('Access-Control-Allow-Origin: *');

// enable debugging
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// include dependencies
include_once(__DIR__ . '/globals.php');
include_once(__DIR__ . '/functions.php');
include_once(__DIR__ . '/classes/classUser.php');
include_once(__DIR__ . '/classes/classSystem.php');
include_once(__DIR__ . '/classes/classDb.php');

// constants
define('APP_NAME', 'AppInvoice');
define('TEST_MODE', false);
$output_log = false;

// get params
// if(isset($_GET['param'])) {
//     $params = json_decode(trim($_GET['param']), true);
//     if($output_log) { echo $_GET['param'] . '<br>'; }
// }
if(isset($_POST['param'])) {
    $params = json_decode(trim($_POST['param']), true);
    if($output_log) { echo $_POST['param'] . '<br>'; }
}

// output logging
if(isset($params) && !empty($params['output_log'])) {
    $output_log = true;
}

// prepare response structure
$response = array(
    "status" => "success",
    "message" => "",
    "data" => ""
);

// set database object
$db = setDB($preset_db[APP_NAME]);

// create system object
$system = new System($params, $db);

// authenticate user
$result = $system->authenticate_user();

// user data
$user_data = $system->buildUserData($result);

// create user object
$user = $system->setUser($user_data);

// run action - user has to have permissions / roles
$func = $user_actions[$user_data['role']][$system->action];
try {
    $response['data'] = $user->$func( $system->data, $db );
} catch (\Throwable $th) {
    $response['status'] = 'error';
    $response['message'] = 'Access denied';
    $response['data'] = $th->getMessage() . ' ' . $th->getLine() . ' ' . $th->getFile();
}

echo json_encode($response);
?>
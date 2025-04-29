<?php

require_once __DIR__ . '/components/init.php';
include_once __DIR__ . '/components/settings.php';

if(!isset($server_url)) {
    $server_url = 'http://localhost/AppInvoice';
}

if(empty($user['email']) || $user['role'] != 9) {
    header("Location: p_login.php");
    exit();
}

$empty_form = array(
    "request" => "",
    "response" => "",
    "status" => "",
    "message" => "",
    "data" => ""
);
$preset_form = array(
    "auth" => array(
        "device" => $device,
        "email" => $user['email']
    ),
    "action" => "",
    "data" => array(
        "" => ""
    )
);
$form = $empty_form;
$form['request'] = json_encode($preset_form);

$page = "";
if (isset($_GET['page'])) {
    $page = $_GET['page'];
}

// check if form was submitted
if (isset($_POST['submitRequest'])) {
    
    $form['request'] = $_POST['request'];
    $request = array("param" => $form['request']);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $server_url . "/Backend/server.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $form['response'] = $response;
    if($result = json_decode($response, true)) {
        if(!empty($result['data'])) { $form['data'] = $result['data']; }
        if(!empty($result['status'])) { $form['status'] = $result['status']; }
        if(!empty($result['message'])) { $form['message'] = $result['message']; }
    };
    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AppInvoice</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</head>
<body class="vw-100">

    <nav class="navbar justify-content-between navbar-expand-lg navbar-light bg-light px-5 fixed-top shadow">
        <a class="navbar-brand" href="index.php">Invoice App</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="p_invoice.php?page=invoice">Add Invoice</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="p_lists.php?page=invoices">List</a>
                </li>
                <li class="nav-item">
                    <!-- If user is logged in, show profile link -->
                    <a class="nav-link" href="p_profile.php?page=acount" id="profile-link">Profile</a>
                    <!-- If user is not logged in, show login link -->
                    <a class="nav-link d-none" href="p_login.php" id="login-link">Login</a>
                </li>
            </ul>
        </div>
    </nav>
  
  <!-- Side Menu for Subcategories -->
  <div class="row vh-100">
    <div class="row my-5">
        <div class="fixed-left bg-light col-md-3 pe-0 py-4 shadow">
            <div class="p-3">
                <h2>Submenu</h2>
                <ul class="list-group">
                    <li class="list-group-item"><a class="text-dark" href="p_admin.php?page=reqres">Request / Response</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 py-4">
            <div id="reqres" class="p-3 <?php echo $page == 'reqres' ? '' : 'd-none'; ?>">
                <h1>Request / response</h1>
                <div class="col-5">
                    <form action="p_admin.php?page=reqres" method="POST">
                        <div class="form-group">
                            <label class="text-secondary" for="request">Request (in JSON format) *</label>
                            <textarea name="request" id="request" class="form-control" rows="5" cols="50" required><?php echo $form['request']; ?></textarea>
                        </div>
                        <input type="submit" name="submitRequest" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Send">
                        <p class="mt-4">Response: <?php print_r($form['response']); ?></p>
                        <p class="mt-3 mb-0">Status: <?php print_r($form['status']); ?></p>
                        <p class="mt-0 mb-0">Message: <?php print_r($form['message']); ?></p>
                        <p class="mt-0">Data: <?php print_r($form['data']); ?></p>
                    </form>
                </div>
            </div>
        </div>
    </div>  
  </div>
    
  
  <!-- Footer -->
  <div class="bg-dark text-white p-2 text-center fixed-bottom">
    <p>&copy; 2025 AppInvoice</p>
  </div>

</body>
</html>
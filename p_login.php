<?php

require_once __DIR__ . '/components/init.php';

if(!empty($user['email'])) {
    header("Location: index.php");
    exit();
}

function login_data_preprocessing($data) {
    if(empty($data['email'])) { $data['alert'] = "Email empty!"; }
    if(empty($data['password'])) { $data['alert'] = "Password empty!"; }
    
    $data['alert'] = filter_var($data['email'], FILTER_VALIDATE_EMAIL) ? "" : "Invalid email!";
    return $data;
}

// check if form was submitted
if (isset($_POST['username']) && isset($_POST['password'])) {

    $action = "signin";
    $data = array(
        "email" => $_POST['username'],
        "password" => $_POST['password']
        //"password_hashed" => password_hash($_POST['password'], PASSWORD_DEFAULT)
    );
    $data = login_data_preprocessing($data);

    if(!empty($data['alert'])) {
        $form = $data;
    }
    else {
        $result = sendBackendRequest($action, $data);

        if($result['status'] == 'success') {
            $_SESSION['AppInvoice_user'] = $result['data'][0]['email'];
            setcookie("AppInvoice_user", $result['data'][0]['email'], time() + (86400 * 30), "/");
            header("Location: index.php");
            exit();
        }
        else {
            $form = array(
                "username" => $_POST['username'],
                "password" => $_POST['password'],
                "alert" => $result['message']
            );
        }
    }
}
else {
    $result = "";
    $form = array(
        "username" => "",
        "password" => "",
        "alert" => ""
    );
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</head>
<body>
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
                    <!-- If user is logged in, show profile link -->
                    <a class="nav-link d-none" href="p_profile.php?page=acount" id="profile-link">Profile</a>
                    <!-- If user is not logged in, show login link -->
                    <a class="nav-link" href="p_login.php" id="login-link">Login</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="row my-5">

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6 card p-4 shadow bg-light">
                    <h2 class="text-center mb-4">Login to your account</h2>
                    <form action="p_login.php" method="POST">
                        <div class="form-group">
                            <label for="username">Username *</label>
                            <input type="text" id="username" name="username" class="form-control" required
                                value="<?php echo $form['username']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" class="form-control" required
                                value="<?php echo $form['password']; ?>">
                        </div>
                        <button type="submit" class="btn btn-outline-secondary btn-block mt-4 w-100">Login</button>
                        <p class="text-danger mt-4"><?php echo $form['alert']; ?></p>
                    </form>
                    <div class="text-center mt-4">
                        <p>Don't have an account? <a class="link-secondary" href="p_signup.php">Register here</a></p>
                        <!-- <p>Forgot your password? <a class="link-secondary" href="p_forgotPassword.php">Reset here</a></p> -->
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
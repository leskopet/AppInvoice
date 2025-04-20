<?php

require_once __DIR__ . '/components/init.php';

if(!empty($user['email'])) {
    header("Location: index.php");
    exit();
}

function signup_data_preprocessing($data) {

    if(empty($data['fname'])) { $data['alert'] = "First name empty!"; }
    if(empty($data['lname'])) { $data['alert'] = "Last name empty!"; }
    if(empty($data['email'])) { $data['alert'] = "Email empty!"; }
    if(empty($data['phone'])) { $data['alert'] = "Phone number empty!"; }
    if(empty($data['password'])) { $data['alert'] = "Password empty!"; }
    if(empty($data['password-confirm'])) { $data['alert'] = "Password confirmation empty!"; }

    if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $data['alert'] = "Invalid email!";
    }
    if(!preg_match("/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/", $data['phone'])) {
        $data['alert'] = "Invalid phone number!";
    }
    if($data['password'] != $data['password-confirm']) {
        $data['alert'] = "Passwords do not match!";
    }
    return $data;
}

// check if form was submitted
if (isset($_POST['sumbit'])) {
    $data = signup_data_preprocessing($_POST);
    if(!empty($data['alert'])) {
        $form = $data;
    }
    else {
        $action = "signup";
        $result = sendRESTRequest($action, $data);

        if($result['status'] == 'success') {
            $_SESSION['AppInvoice_user'] = $result['data'][0]['email'];
            setcookie("AppInvoice_user", $result['data'][0]['email'], time() + (86400 * 30), "/");
            header("Location: index.php");
            exit();
        }
        else {
            $form = array(
                "fname" => $_POST['fname'],
                "lname" => $_POST['lname'],
                "email" => $_POST['email'],
                "phone" => $_POST['phone'],
                "password" => $_POST['password'],
                "password-confirm" => $_POST['password-confirm'],
                "alert" => $result['message']
            );
        }
    }
}
else {
    $result = "";
    $form = array(
        "fname" => "",
        "lname" => "",
        "email" => "",
        "phone" => "",
        "password" => "",
        "password-confirm" => "",
        "alert" => ""
    );
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
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

    <div class="row my-5">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6 card p-4 shadow bg-light">
                    <h2 class="text-center mb-4">Sign up to your account</h2>
                    <form action="p_signup.php" method="POST">
                        <div class="form-group">
                            <label for="fname">First Name *</label>
                            <input type="text" id="fname" name="fname" class="form-control"
                                value="<?php echo $form['fname']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="lname">Last Name *</label>
                            <input type="text" id="lname" name="lname" class="form-control"
                                value="<?php echo $form['lname']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control"
                                value="<?php echo $form['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone *</label>
                            <input type="tel" id="phone" name="phone" class="form-control"
                                value="<?php echo $form['phone']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" name="password" class="form-control"
                                value="<?php echo $form['password']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password-confirm">Confirm Password *</label>
                            <input type="password" id="password-confirm" name="password-confirm" class="form-control"
                                value="<?php echo $form['password-confirm']; ?>" required>
                        </div>
                        <input type="submit" name="sumbit" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Sign up">
                        <p class="text-danger mt-4"><?php echo $form['alert']; ?></p>
                    </form>
                    <div class="text-center mt-4">
                        <p>Already have an account? <a class="link-secondary" href="p_login.php">Login here</a></p>
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
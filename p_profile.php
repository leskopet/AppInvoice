<?php

require_once __DIR__ . '/components/init.php';

if(empty($user['email'])) {
    header("Location: p_login.php");
    exit();
}
$admin = $user['role'] == 9;
$empty_form = array(
    "id" => 0,
    "title" => "",
    "fname" => "",
    "lname" => "",
    "email" => "",
    "phone" => "",
    "ico" => "",
    "dic" => "",
    "icdph" => "",
    "iban" => "",
    "swift" => "",
    "bank" => "",
    "description" => "",
    "street" => "",
    "street_number" => "",
    "city" => "",
    "postal_code" => "",
    "state" => "",
    "username" => "",
    "password" => "",
    "alert" => ""
);

function user_data_preprocessing($data) {

    if(empty($data['fname'])) { $data['alert'] = "First name empty!"; }
    if(empty($data['lname'])) { $data['alert'] = "Last name empty!"; }
    if(empty($data['email'])) { $data['alert'] = "Email empty!"; }
    if(empty($data['phone'])) { $data['alert'] = "Phone number empty!"; }
    if(empty($data['street'])) { $data['alert'] = "Street empty!"; }
    if(empty($data['street_number'])) { $data['alert'] = "Street number empty!"; }
    if(empty($data['city'])) { $data['alert'] = "City empty!"; }
    if(empty($data['postal_code'])) { $data['alert'] = "Postal code empty!"; }
    if(empty($data['state'])) { $data['state'] = "Slovensko"; }

    if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $data['alert'] = "Invalid email!";
    }
    if(!preg_match("/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/", $data['phone'])) {
        $data['alert'] = "Invalid phone number!";
    }

    return $data;
}
function company_data_preprocessing($data) {

    if(empty($data['title'])) { $data['alert'] = "Title empty!"; }
    if(empty($data['description'])) { $data['description'] = ""; }
    if(empty($data['ico'])) { $data['alert'] = "ICO empty!"; }
    if(empty($data['dic'])) { $data['alert'] = "DIC empty!"; }
    if(empty($data['icdph'])) { $data['icdph'] = ""; }
    if(empty($data['iban'])) { $data['alert'] = "IBAN empty!"; }
    if(empty($data['swift'])) { $data['alert'] = "SWIFT empty!"; }
    if(empty($data['bank'])) { $data['alert'] = "Bank empty!"; }

    if(!preg_match("/^\d{8}$/", $data['ico'])) {
        $data['alert'] = "Invalid ICO!";
    }
    if(!preg_match("/^\d{10}$/", $data['dic'])) {
        $data['alert'] = "Invalid DIC!";
    }
    if(!preg_match("/^SK\d{22}$/", str_replace(" ", "", $data['iban']))) {
        $data['alert'] = "Invalid IBAN!";
    }

    return $data;
}

// init page
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    if ($page == 'acount') {
        $form = $user;
    }
    else if ($page == 'company') {
        $action = "getusercompanies";
        $data = array(
            "email" => $user['email']
        );
        $result = sendRESTRequest($action, $data);
        if(empty($result['data'])) { 
            $form = $empty_form;
            $form['phone'] = $user['phone'];
            $form['email'] = $user['email'];
            $form['title'] = $user['fname'] . " " . $user['lname'];
            $form['state'] = $user['state'];
            $form['city'] = $user['city'];
            $form['street'] = $user['street'];
            $form['street_number'] = $user['street_number'];
            $form['postal_code'] = $user['postal_code'];
        }
        else { $form = $result['data'][0]; }
    }
    else {
        $form = $empty_form;
    }
    $form['alert'] = "";
}
else {
    $page = "";
    $form = $empty_form;
}

// check if form was submitted
if (isset($_POST['submitUser'])) {
    $data = user_data_preprocessing($_POST);
    if(!empty($data['alert'])) {
        $form = $data;
    }
    else {
        $action = "updateuser";
        $result = sendRESTRequest($action, $data);
        if($result['status'] == 'success') {
            $_SESSION['AppInvoice_user'] = $data['email'];
            setcookie("AppInvoice_user", $data['email'], time() + (86400 * 30), "/");
            header("Location: p_profile.php?page=acount");
            exit();
        }
        else {
            $form = array(
                "fname" => $_POST['fname'],
                "lname" => $_POST['lname'],
                "email" => $_POST['email'],
                "phone" => $_POST['phone'],
                "street" => $_POST['street'],
                "street_number" => $_POST['street_number'],
                "city" => $_POST['city'],
                "postal_code" => $_POST['postal_code'],
                "state" => $_POST['state'],
                "username" => $_POST['username'],
                "password" => $_POST['password'],
                "alert" => $result['message']
            );
        }
    }
}
if (isset($_POST['submitCompany'])) {
    $data = company_data_preprocessing($_POST);
    if(!empty($data['alert'])) {
        $form = $data;
    }
    else {
        $action = "updatecompany";
        $result = sendRESTRequest($action, $data);
        if($result['status'] == 'success') {
            header("Location: p_profile.php?page=company");
            exit();
        }
        else {
            $form = array(
                "id" => $_POST['id'],
                "title" => $_POST['title'],
                "description" => $_POST['description'],
                "email" => $_POST['email'],
                "phone" => $_POST['phone'],
                "ico" => $_POST['ico'],
                "dic" => $_POST['dic'],
                "icdph" => $_POST['icdph'],
                "iban" => $_POST['iban'],
                "swift" => $_POST['swift'],
                "bank" => $_POST['bank'],
                "street" => $_POST['street'],
                "street_number" => $_POST['street_number'],
                "city" => $_POST['city'],
                "postal_code" => $_POST['postal_code'],
                "state" => $_POST['state'],
                "alert" => $result['message']
            );
        }
    }
}
if (isset($_POST['deactivate'])) {
    $action = "deactivateuser";
    $result = sendRESTRequest($action,array());
    if($result['status'] == 'success' && $result['data'] == true) {
        $_SESSION['AppInvoice_user'] = "";
        setcookie("AppInvoice_user", "", time() - 3600, "/");
        header("Location: index.php");
        exit();
    }
    else {
        $form['alert'] = $result['message'] . " " . json_encode($result['data']);
    }
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
                    <li class="list-group-item"><a class="text-dark" href="p_profile.php?page=acount">Acount</a></li>
                    <li class="list-group-item"><a class="text-dark" href="p_profile.php?page=company">Company</a></li>
                    <li class="list-group-item"><a class="text-dark" href="p_profile.php?page=info">Info</a></li>
                </ul>
            </div>
            <?php if($admin) { ?>
                <div class="p-3">
                    <p class="ps-3"><a class="text-dark" href="p_admin.php?page=reqres">Admin page</a></p>
                </div>
            <?php } ?>
            <div class="p-3">
                <p class="ps-3"><a class="text-dark" href="p_logout.php">Logout</a></p>
                <p class="ps-3"><a class="text-dark" href="p_profile.php?page=deactivate">Deactivate user</a></p>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 py-4">
            <div id="acount" class="p-3 <?php echo $page == 'acount' ? '' : 'd-none'; ?>">
                <h1>Acount</h1>
                <div class="col-5">
                    <form action="p_profile.php?page=acount" method="POST">
                        <div class="form-group">
                            <label class="text-secondary" for="fname">First Name *</label>
                            <input type="text" id="fname" name="fname" class="form-control"
                                value="<?php echo $form['fname']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="lname">Last Name *</label>
                            <input type="text" id="lname" name="lname" class="form-control"
                                value="<?php echo $form['lname']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control"
                                value="<?php echo $form['email']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="phone">Phone *</label>
                            <input type="tel" id="phone" name="phone" class="form-control"
                                value="<?php echo $form['phone']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="street">Street *</label>
                            <input type="text" id="street" name="street" class="form-control"
                                value="<?php echo $form['street']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="street_number">Street number *</label>
                            <input type="text" id="street_number" name="street_number" class="form-control"
                                value="<?php echo $form['street_number']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="city">City *</label>
                            <input type="text" id="city" name="city" class="form-control"
                                value="<?php echo $form['city']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="state">State</label>
                            <input type="text" id="state" name="state" class="form-control"
                                value="<?php echo $form['state']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="postal_code">Postal code *</label>
                            <input type="text" id="postal_code" name="postal_code" class="form-control"
                                value="<?php echo $form['postal_code']; ?>" required>
                        </div>

                        <input type="submit" name="submitUser" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Save">
                        <p class="text-danger mt-4"><?php echo $form['alert']; ?></p>
                    </form>
                </div>
            </div>
            <div id="company" class="p-3 <?php echo $page == 'company' ? '' : 'd-none'; ?>">
                <h1>Company</h1>
                <form action="p_profile.php?page=company" method="POST">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="hidden" name="id" value="<?php echo $form['id']; ?>">
                            <div class="form-group">
                                <label class="text-secondary" for="title">Company name *</label>
                                <input type="text" id="title" name="title" class="form-control"
                                    value="<?php echo $form['title']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="description">Description</label>
                                <input type="text" id="description" name="description" class="form-control"
                                    value="<?php echo $form['description']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="ico">IČO *</label>
                                <input type="text" id="ico" name="ico" class="form-control"
                                    value="<?php echo $form['ico']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="dic">DIČ *</label>
                                <input type="text" id="dic" name="dic" class="form-control"
                                    value="<?php echo $form['dic']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="icdph">IČDPH</label>
                                <input type="text" id="icdph" name="icdph" class="form-control"
                                    value="<?php echo $form['icdph']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="iban">IBAN *</label>
                                <input type="text" id="iban" name="iban" class="form-control"
                                    value="<?php echo $form['iban']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="swift">SWIFT *</label>
                                <input type="text" id="swift" name="swift" class="form-control"
                                    value="<?php echo $form['swift']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="bank">BANK *</label>
                                <input type="text" id="bank" name="bank" class="form-control"
                                    value="<?php echo $form['bank']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-5 ms-auto">
                            <div class="form-group">
                                <label class="text-secondary" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control"
                                    value="<?php echo $form['email']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control"
                                    value="<?php echo $form['phone']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="street">Street</label>
                                <input type="text" id="street" name="street" class="form-control"
                                    value="<?php echo $form['street']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="street_number">Street number</label>
                                <input type="text" id="street_number" name="street_number" class="form-control"
                                    value="<?php echo $form['street_number']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="city">City</label>
                                <input type="text" id="city" name="city" class="form-control"
                                    value="<?php echo $form['city']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="state">State</label>
                                <input type="text" id="state" name="state" class="form-control"
                                    value="<?php echo $form['state']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="postal_code">Postal code</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control"
                                    value="<?php echo $form['postal_code']; ?>">
                            </div>
                        </div>
                        <input type="submit" name="submitCompany" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Save">
                        <p class="text-danger mt-4"><?php echo $form['alert']; ?></p>
                    </div>
                </form>
            </div>
            <div id="info" class="p-3 <?php echo $page == 'info' ? '' : 'd-none'; ?>">
                <h1>About AppInvoice</h1>
                <div class="col-5">
                    <p class="py-3">
                        AppInvoice is a simple and easy to use invoice software. It is free and open source.
                    </p>
                </div>
            </div>
            <div id="deactivate" class="p-3 <?php echo $page == 'deactivate' ? '' : 'd-none'; ?>">
                <h1>Deactivate user</h1>
                <div class="col-5">
                    <p class="py-3">
                        Are you sure you want to deactivate your user?
                    </p>
                    <form method="post">
                        <input type="submit" name="deactivate" class="btn btn-outline-danger btn-block mt-4 w-100" value="Deactivate">
                    </form>
                    <p class="text-danger mt-4"><?php echo $form['alert']; ?></p>
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
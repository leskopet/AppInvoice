<?php

require_once __DIR__ . '/components/init.php';

function invoice_data_preprocessing($data) {
    $clr_invoice = array(
        "id" => 0,
        "title" => "",
        "created" => "",
        "suplied" => "",
        "due_date" => "",
        "suplier" => array(
            "id" => 0,
            "title" => "",
            "email" => "",
            "phone" => "",
            "ico" => "",
            "dic" => "",
            "icdph" => "",
            "iban" => "",
            "swift" => "",
            "bank" => "",
            "city" => "",
            "street" => "",
            "street_number" => "",
            "postal_code" => "",
            "state" => ""
        ),
        "customer" => array(
            "id" => 0,
            "title" => "",
            "ico" => "",
            "dic" => "",
            "icdph" => "",
            "city" => "",
            "street" => "",
            "street_number" => "",
            "postal_code" => "",
            "state" => ""
        ),
        'items' => array(),
        "total" => 0,
        "allert" => ""
    );
    $data['allert'] = "";
    if(empty($data)) { 
        $data = $clr_invoice;
        $data['allert'] = "Invoice data empty!";
        return $data; 
    }

    if(empty($data['title'])) { $data['allert'] = "Invoice title empty!"; }
    if(empty($data['created'])) { $data['created'] = date("Y-m-d"); }
    if(empty($data['suplied'])) { $data['suplied'] = date("Y-m-d"); }
    if(empty($data['due_date'])) { $data['due_date'] = date("Y-m-d", strtotime("+14 days")); }
    if(empty($data['suplier'])) { $data['allert'] = "Invoice suplier empty!"; }
    if(empty($data['customer'])) { $data['allert'] = "Invoice customer empty!"; }
    if(empty($data['items'])) { $data['allert'] = "Invoice items empty!"; }

    if($data['created'] > $data['suplied']) { $data['allert'] = "Invoice created date is greater than suplied date!"; }
    if($data['created'] > $data['due_date']) { $data['allert'] = "Invoice created date is greater than due date!"; }
    if($data['suplied'] > $data['due_date']) { $data['allert'] = "Invoice suplied date is greater than due date!"; }

    $data['total'] = 0;
    foreach($data['items'] as $item) {
        $data['total'] += $item['total_price'];
    }
    $data['vat'] = 0;
    $data['total_vat'] = $data['total'] + $data['vat'];
    
    return $data;
}
function suplier_data_preprocessing($data) {
    $clr_suplier = array(
        "id" => 0,
        "title" => "",
        "description" => "",
        "email" => "",
        "phone" => "",
        "ico" => "",
        "dic" => "",
        "icdph" => "",
        "iban" => "",
        "swift" => "",
        "bank" => "",
        "city" => "",
        "street" => "",
        "street_number" => "",
        "postal_code" => "",
        "state" => "",
        "allert" => ""
    );
    if(empty($data)) { 
        $data = $clr_suplier;
        $data['allert'] = "Suplier data empty!";
        return $data; 
    }

    if(empty($data['title'])) { $data['allert'] = "Suplier title empty!"; }
    if(empty($data['description'])) { $data['description'] = ""; }
    if(empty($data['email'])) { $data['allert'] = "Suplier email empty!"; }
    if(empty($data['phone'])) { $data['allert'] = "Suplier phone empty!"; }
    if(empty($data['ico'])) { $data['allert'] = "Suplier ICO empty!"; }
    if(empty($data['dic'])) { $data['allert'] = "Suplier DIC empty!"; }
    if(empty($data['icdph'])) { $data['icdph'] = ""; }
    if(empty($data['iban'])) { $data['allert'] = "Suplier IBAN empty!"; }
    if(empty($data['swift'])) { $data['allert'] = "Suplier SWIFT empty!"; }
    if(empty($data['bank'])) { $data['allert'] = "Suplier bank empty!"; }
    if(empty($data['city'])) { $data['allert'] = "Suplier city empty!"; }
    if(empty($data['street'])) { $data['allert'] = "Suplier street empty!"; }
    if(empty($data['street_number'])) { $data['allert'] = "Suplier street number empty!"; }
    if(empty($data['postal_code'])) { $data['allert'] = "Suplier postal code empty!"; }
    if(empty($data['state'])) { $data['state'] = "Slovensko"; }

    if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $data['allert'] = "Invalid email!";
    }
    if(!preg_match("/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/", $data['phone'])) {
        $data['allert'] = "Invalid phone number!";
    }
    if(!preg_match("/^\d{8}$/", $data['ico'])) {
        $data['allert'] = "Invalid ICO!";
    }
    if(!preg_match("/^\d{10}$/", $data['dic'])) {
        $data['allert'] = "Invalid DIC!";
    }
    if(!preg_match("/^SK\d{22}$/", str_replace(" ", "", $data['iban']))) {
        $data['allert'] = "Invalid IBAN!";
    }

    return $data;
}
function customer_data_preprocessing($data) {
    $clr_customer = array(
        "id" => 0,
        "title" => "",
        "description" => "",
        "ico" => "",
        "dic" => "",
        "icdph" => "",
        "city" => "",
        "street" => "",
        "street_number" => "",
        "postal_code" => "",
        "state" => "",
        "allert" => ""
    );
    if(empty($data)) { 
        $data = $clr_customer;
        $data['allert'] = "Customer data empty!";
        return $data; 
    }

    if(empty($data['title'])) { $data['allert'] = "Customer title empty!"; }
    if(empty($data['description'])) { $data['description'] = ""; }
    if(empty($data['ico'])) { $data['allert'] = "Customer ICO empty!"; }
    if(empty($data['dic'])) { $data['allert'] = "Customer DIC empty!"; }
    if(empty($data['icdph'])) { $data['icdph'] = ""; }
    if(empty($data['city'])) { $data['allert'] = "Customer city empty!"; }
    if(empty($data['street'])) { $data['allert'] = "Customer street empty!"; }
    if(empty($data['street_number'])) { $data['allert'] = "Customer street number empty!"; }
    if(empty($data['postal_code'])) { $data['allert'] = "Customer postal code empty!"; }
    if(empty($data['state'])) { $data['state'] = "Slovensko"; }

    if(!preg_match("/^\d{8}$/", $data['ico'])) {
        $data['alert'] = "Invalid ICO!";
    }
    if(!preg_match("/^\d{10}$/", $data['dic'])) {
        $data['alert'] = "Invalid DIC!";
    }

    return $data;
}
function item_data_preprocessing($data) {
    $clr_item = array(
        "id" => 0,
        "ordernumber" => 0,
        "title" => "",
        "quantity" => 0,
        "unit" => "",   
        "price" => 0,
        "total_price" => 0,
        "allert" => ""
    );
    if(empty($data)) { 
        $data = $clr_item;
        $data['allert'] = "Item data empty!";
        return $data; 
    }

    if(empty($data['ordernumber'])) { $data['allert'] = "Item cannot be saved!"; }
    if(empty($data['title'])) { $data['allert'] = "Item title empty!"; }
    if(empty($data['quantity'])) { $data['allert'] = "Item quantity empty!"; }
    if(empty($data['unit'])) { $data['unit'] = ""; }
    if(empty($data['price'])) { $data['allert'] = "Item price empty!"; }

    $data['quantity'] = str_replace(",", ".", $data['quantity']);
    $data['price'] = str_replace(",", ".", $data['price']);

    if(!preg_match("/^\d+(\.\d{1,2})?$/", $data['quantity'])) {
        $data['allert'] = "Invalid quantity!";
    }
    if(!preg_match("/^\d+(\.\d{1,2})?$/", $data['price'])) {
        $data['allert'] = "Invalid price!";
    }

    $data['quantity'] = round($data['quantity'], 2);
    $data['price'] = round($data['price'], 2);
    $data['total_price'] = $data['quantity'] * $data['price'];
    $data['total_price'] = round($data['total_price'], 2);

    return $data;
}

// error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$empty_form = array(
    "id" => 0,
    "title" => "",
    "created" => "",
    "suplied" => "",
    "due_date" => "",
    "suplier" => array(
        "id" => 0,
        "title" => "",
        "email" => "",
        "phone" => "",
        "ico" => "",
        "dic" => "",
        "icdph" => "",
        "iban" => "",
        "swift" => "",
        "bank" => "",
        "city" => "",
        "street" => "",
        "street_number" => "",
        "postal_code" => "",
        "state" => ""
    ),
    "customer" => array(
        "id" => 0,
        "title" => "",
        "ico" => "",
        "dic" => "",
        "icdph" => "",
        "city" => "",
        "street" => "",
        "street_number" => "",
        "postal_code" => "",
        "state" => ""
    ),
    'items' => array(),
    "total" => 0,
    "allert" => ""
);
$supliers = array(array(
    "id" => 0,
    "title" => "",
    "description" => "",
    "email" => "",
    "phone" => "",
    "ico" => "",
    "dic" => "",
    "icdph" => "",
    "iban" => "",
    "swift" => "",
    "bank" => "",
    "city" => "",
    "street" => "",
    "street_number" => "",
    "postal_code" => "",
    "state" => ""
));
$customers = array(array(
    "id" => 0,
    "title" => "",
    "ico" => "",
    "dic" => "",
    "icdph" => "",
    "city" => "",
    "street" => "",
    "street_number" => "",
    "postal_code" => "",
    "state" => ""
));
if(!empty($user['email'])) {
    // get supliers
    $action = "getusercompanies";
    $data = array(
        "email" => $user['email']
    );
    $result = sendBackendRequest($action, $data);
    if(!empty($result['data'])) { $supliers = $result['data']; }
}

// init page
if (isset($_GET['page'])) { $page = $_GET['page']; }
else { $page = ""; }

$url_query = "";

$form_changed = isset($_GET['changed']) && !empty($_GET['changed']);
// check session for invoice
if ($form_changed && isset($_SESSION['AppInvoice_invoice']) && !empty($_SESSION['AppInvoice_invoice'])) {
    $form = $_SESSION['AppInvoice_invoice'];
    $url_query .= "&changed=1";
}
else {
    $form = $empty_form;
    $form['suplier'] = $supliers[0];
    $form['customer'] = $customers[0];
}
if(empty($form['created'])) { $form['created'] = date("Y-m-d"); }
if(empty($form['suplied'])) { $form['suplied'] = date("Y-m-d"); }
if(empty($form['due_date'])) { $form['due_date'] = date("Y-m-d", strtotime("+14 days")); }
if(!isset($form['allert']) || empty($form['allert'])) { $form['allert'] = ""; }

// get invoice
if(isset($_GET['id'])) {
    $action = "getinvoice";
    $data = array(
        "id" => $_GET['id']
    );
    $result = sendBackendRequest($action, $data);
    if(empty($result['data'])) {
        $_SESSION['AppInvoice_invoice'] = $form;
        header("Location: p_invoice.php?page=invoice");
        exit();
    }
    if (!$form_changed) {
        $form = $result['data'][0];
        for ($i=0; $i < count($form['items']); $i++) { 
            $form['items'][$i]['ordernumber'] = $i+1;
            $total_price = $form['items'][$i]['quantity'] * $form['items'][$i]['price'];
            $form['items'][$i]['total_price'] = round($total_price,2);
        }
    }
    $form['allert'] = "";

    $url_query .= "&id=" . $_GET['id'];
}
elseif (isset($_GET['copy'])) {
    $action = "getinvoice";
    $data = array(
        "id" => $_GET['copy']
    );
    $result = sendBackendRequest($action, $data);
    if(empty($result['data'])) {
        $_SESSION['AppInvoice_invoice'] = $form;
        header("Location: p_invoice.php?page=invoice");
        exit();
    }
    if (!$form_changed) {
        $form = $result['data'][0];
        $form['id'] = 0;
        $form['title'] = "";
        $form['created'] = date("Y-m-d");
        $form['suplied'] = date("Y-m-d");
        $form['due_date'] = date("Y-m-d", strtotime("+14 days"));
        $form['total'] = 0;
        $form['total_vat'] = 0;
        $form['items'] = array();
        $_SESSION['AppInvoice_invoice'] = $form;

        header("Location: p_invoice.php?page=invoice&changed=1");
        exit();
    }
    else {
        $form['allert'] = "Invoice not saved!";
        $_SESSION['AppInvoice_invoice'] = $form;

        header("Location: p_invoice.php?page=invoice&id=" . $_GET['copy']);
        exit();
    }
}
elseif (isset($_GET['customer'])) {
    $action = "getcustomer";
    $data = array(
        "id" => $_GET['customer']
    );
    $result = sendBackendRequest($action, $data);
    if(empty($result['data']) || $result['status'] != 'success') {
        $_SESSION['AppInvoice_invoice'] = $form;
        header("Location: p_invoice.php?page=invoice");
        exit();
    }
    $form['customer'] = $result['data'][0];
    $_SESSION['AppInvoice_invoice'] = $form;

    header("Location: p_invoice.php?page=invoice&changed=1");
    exit();
}

// check if form was submitted
if (isset($_POST['submitAddInvoice'])) {
    // data preprocessing
    $action = "createinvoice";
    $data = $_SESSION['AppInvoice_invoice'];
    $data = invoice_data_preprocessing($data);

    // request to server
    if(empty($data['allert'])) {
        $result = sendBackendRequest($action, $data);
        if($result['status'] == 'success') {
            header("Location: p_invoice.php?page=invoice&id=" . $result['data'][0]['id']);
            exit();
        }
        else {
            $form = $data;
            $form['allert'] = $result['message'] . " " . json_encode($result['data']);
            $url_query .= "&changed=1";
        }   
    }
    else {
        $form = $data;
    }
}
if (isset($_POST['submitUpdateInvoice'])) {
    $action = "updateinvoice";
    $data = $_SESSION['AppInvoice_invoice'];
    $data = invoice_data_preprocessing($data);

    // request to server
    if(empty($data['allert'])) {
        $result = sendBackendRequest($action, $data);
        if($result['status'] == 'success') {
            header("Location: p_invoice.php?page=invoice&id=" . $data['id']);
            exit();
        }
        else {
            $form = $data;
            $form['allert'] = $result['message'];
            $url_query .= "&changed=1";
        }
    }
    else {
        $form = $data;
    }
}
if (isset($_POST['submitDeleteInvoice'])) {
    if(!isset($_POST['id']) || empty($_POST['id'])) {
        $form['allert'] = "Missing invoice id";
    }
    else {
        $action = "deleteinvoice";
        $data = array(
            "id" => $_POST['id']
        );
        $result = sendBackendRequest($action, $data);
        if($result['status'] == 'success') {
            header("Location: p_invoice.php?page=invoice");
            exit();
        }
        else {
            $form['allert'] = $result['message'];
            $url_query .= "&changed=1";
        }
    }
}
if (isset($_POST['submitShowInvoice'])) {
    header("Location: toPdf.php");
    exit();
}
if(isset($_POST['submitInvoice'])) {
    if(!isset($_POST['title']) || empty($_POST['title'])) {
        $form['allert'] = "Empty title";
    }
    else { $form['allert'] = ""; }
    $form['title'] = $_POST['title'];
    $form['created'] = !empty($_POST['created']) ? $_POST['created'] : date('Y-m-d');
    $form['suplied'] = !empty($_POST['suplied']) ? $_POST['suplied'] : date('Y-m-d');
    $form['due_date'] = !empty($_POST['due_date']) ? $_POST['due_date'] : date('Y-m-d', strtotime("+14 days"));
}
if(isset($_POST['submitSuplier'])) { 
    $form['suplier'] = array(
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
        "allert" => ""
    );
    $form['suplier'] = suplier_data_preprocessing($form['suplier']);
    if(!empty($form['suplier']['allert'])) {
        $form['allert'] = $form['suplier']['allert'];
    }
    else { $form['allert'] = ""; }
}
if(isset($_POST['submitCustomer'])) {
    $form['customer'] = array(
        "id" => $_POST['id'],
        "title" => $_POST['title'],
        "ico" => $_POST['ico'],
        "dic" => $_POST['dic'],
        "icdph" => $_POST['icdph'],
        "street" => $_POST['street'],
        "street_number" => $_POST['street_number'],
        "city" => $_POST['city'],
        "postal_code" => $_POST['postal_code'],
        "state" => $_POST['state'],
        "allert" => ""
    );
    $form['customer'] = customer_data_preprocessing($form['customer']);
    if(!empty($form['customer']['allert'])) {
        $form['allert'] = $form['customer']['allert'];
    }
    else { $form['allert'] = ""; }
}
if(isset($_POST['submitItem'])) {
    $item = array(
        "ordernumber" => count($form['items']) + 1,
        "title" => $_POST['title'],
        "quantity" => $_POST['quantity'],
        "unit" => $_POST['unit'],
        "price" => $_POST['price'],
        "allert" => ""
    );
    $item = item_data_preprocessing($item);
    if(!empty($item['allert'])) {
        $form['allert'] = $item['allert'];
    }
    else {
        $form['items'][] = $item;
        $form['allert'] = "";
    }
    
}
if(isset($_POST['submitDelItem'])) {
    $ordernumber = $_POST['ordernumber'];
    if (empty($ordernumber)) { $form['allert'] = "Missing ordernumber"; }
    else {
        $item_id = null;
        if(isset($form['items'][$ordernumber - 1]['id']) && !empty($form['items'][$ordernumber - 1]['id'])) {
            $item_id = $form['items'][$ordernumber - 1]['id'];
        }
        unset($form['items'][$ordernumber - 1]);
        $form['items'] = array_values($form['items']);
        if(empty($form['items'] || empty($form['items'][0]))) { $form['items'] = array(); }

        $correction = 0;
        for($i = 0; $i < count($form['items']); $i++) {
            if(empty($form['items'][$i]['title'])) { $correction++; continue; }
            $form['items'][$i]['ordernumber'] = $i + 1 - $correction;
        }

        if(!empty($item_id)) {
            $form['items'][] = array('id' => $item_id);
        }
    }
}

// save to session
$_SESSION['AppInvoice_invoice'] = $form;

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
                <?php if(!empty($user['email'])) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="p_lists.php?page=invoices">List</a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <?php if(!empty($user['email'])) { ?>
                        <a class="nav-link" href="p_profile.php?page=acount" id="profile-link">Profile</a>
                    <?php } else { ?>
                        <a class="nav-link" href="p_login.php" id="login-link">Login</a>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </nav>
  
  <!-- Side Menu for Subcategories -->
  <div class="row vh-100">
    <div class="row my-5">
        <div class="fixed-left bg-light col-md-3 pe-0 py-4 shadow">
            <div class="p-3">
                <h2>Invoice</h2>
                <ul class="list-group">
                    <li class="list-group-item"><a class="text-dark" href="<?php echo "p_invoice.php?page=invoice" . $url_query; ?>">Basic info</a></li>
                    <li class="list-group-item"><a class="text-dark" href="<?php echo "p_invoice.php?page=suplier" . $url_query; ?>">Suplier</a></li>
                    <li class="list-group-item"><a class="text-dark" href="<?php echo "p_invoice.php?page=customer" . $url_query; ?>">Customer</a></li>
                    <li class="list-group-item"><a class="text-dark" href="<?php echo "p_invoice.php?page=items" . $url_query; ?>">Items</a></li>
                </ul>
            </div>
            <div class="p-3">
                <p class="ps-3">
                    <?php if(empty($user['email'])) { ?>
                        <form action="p_invoice.php?page=invoice" method="POST">
                            <input type="submit" name="submitShowInvoice" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Show invoice">
                        </form>
                    <?php }
                    else if(empty($form['id'])) { ?>
                        <form action="p_invoice.php?page=invoice" method="POST">
                            <input type="submit" name="submitAddInvoice" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Create Invoice">
                        </form>
                    <?php }
                    else if(!$form_changed) { ?>
                        <form action="p_invoice.php?page=invoice" method="POST">
                            <input type="submit" name="submitShowInvoice" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Show invoice">
                        </form>
                        <a href="p_invoice.php?page=invoice&copy=<?php echo $form['id']; ?>" class="btn btn-outline-secondary btn-block mt-4 w-100">Copy invoice</a>
                        <form action="p_invoice.php?page=invoice" method="POST">
                            <input type="hidden" name="id" value="<?php echo $form['id']; ?>">
                            <input type="submit" name="submitDeleteInvoice" class="btn btn-outline-danger btn-block mt-4 w-100" value="Delete invoice">
                        </form>
                    <?php }
                    else { ?>
                        <form action="p_invoice.php?page=invoice" method="POST">
                            <input type="submit" name="submitUpdateInvoice" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Update Invoice">
                        </form>
                        <form action="p_invoice.php?page=invoice" method="POST">
                            <input type="hidden" name="id" value="<?php echo $form['id']; ?>">
                            <input type="submit" name="submitDeleteInvoice" class="btn btn-outline-danger btn-block mt-4 w-100" value="Delete invoice">
                        </form>
                    <?php } ?>
                </p>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 py-4">
            <div id="invoice" class="p-3 <?php echo $page == 'invoice' ? '' : 'd-none'; ?>">
                <h1>Invoice - basic info</h1>
                <div class="col-5">
                    <form action="p_invoice.php?page=invoice<?php echo !$form_changed ? '&changed=1' . $url_query : '' . $url_query; ?>" method="POST">
                        <div class="form-group">
                            <label class="text-secondary" for="title">Invoice number *</label>
                            <input type="text" id="title" name="title" class="form-control" required
                                value="<?php echo $form['title']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="created">Date of creation *</label>
                            <input type="date" id="created" name="created" class="form-control" required
                                value="<?php echo $form['created']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="suplied">Suply date *</label>
                            <input type="date" id="suplied" name="suplied" class="form-control" required
                                value="<?php echo $form['suplied']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="text-secondary" for="due_date">Due date *</label>
                            <input type="date" id="due_date" name="due_date" class="form-control" required
                                value="<?php echo $form['due_date']; ?>">
                        </div>
                        <input type="submit" name="submitInvoice" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Save">
                        <p class="text-danger mt-4"><?php echo $form['allert']; ?></p>
                    </form>
                </div>
            </div>
            <div id="suplier" class="p-3 <?php echo $page == 'suplier' ? '' : 'd-none'; ?>">
                <h1>Suplier info</h1>
                <form action="p_invoice.php?page=suplier<?php echo !$form_changed ? '&changed=1' . $url_query : '' . $url_query; ?>" method="POST">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="hidden" name="id" value="<?php echo $form['suplier']['id']; ?>">
                            <div class="form-group">
                                <label class="text-secondary" for="title">Company name *</label>
                                <input type="text" id="title" name="title" class="form-control" required
                                    value="<?php echo $form['suplier']['title']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="description">Description</label>
                                <input type="text" id="description" name="description" class="form-control"
                                    value="<?php echo $form['suplier']['description']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="ico">IČO *</label>
                                <input type="text" id="ico" name="ico" class="form-control" required
                                    value="<?php echo $form['suplier']['ico']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="dic">DIČ *</label>
                                <input type="text" id="dic" name="dic" class="form-control" required
                                    value="<?php echo $form['suplier']['dic']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="icdph">IČDPH</label>
                                <input type="text" id="icdph" name="icdph" class="form-control"
                                    value="<?php echo $form['suplier']['icdph']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="iban">IBAN *</label>
                                <input type="text" id="iban" name="iban" class="form-control" required
                                    value="<?php echo $form['suplier']['iban']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="swift">SWIFT *</label>
                                <input type="text" id="swift" name="swift" class="form-control" required
                                    value="<?php echo $form['suplier']['swift']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="bank">BANK *</label>
                                <input type="text" id="bank" name="bank" class="form-control" required
                                    value="<?php echo $form['suplier']['bank']; ?>">
                            </div>
                        </div>
                        <div class="col-md-5 ms-auto">
                            <div class="form-group">
                                <label class="text-secondary" for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" required
                                    value="<?php echo $form['suplier']['email']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="phone">Phone *</label>
                                <input type="tel" id="phone" name="phone" class="form-control" required
                                    value="<?php echo $form['suplier']['phone']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="street">Street *</label>
                                <input type="text" id="street" name="street" class="form-control" required
                                    value="<?php echo $form['suplier']['street']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="street_number">Street number *</label>
                                <input type="text" id="street_number" name="street_number" class="form-control" required
                                    value="<?php echo $form['suplier']['street_number']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="city">City *</label>
                                <input type="text" id="city" name="city" class="form-control" required
                                    value="<?php echo $form['suplier']['city']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="state">State</label>
                                <input type="text" id="state" name="state" class="form-control"
                                    value="<?php echo $form['suplier']['state']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="postal_code">Postal code *</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control" required
                                    value="<?php echo $form['suplier']['postal_code']; ?>">
                            </div>
                        </div>
                        <input type="submit" name="submitSuplier" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Save">
                        <p class="text-danger mt-4"><?php echo $form['allert']; ?></p>
                    </div>
                </form>
            </div>
            <div id="customer" class="p-3 <?php echo $page == 'customer' ? '' : 'd-none'; ?>">
                <h1>Customer info</h1>
                <form action="p_invoice.php?page=customer<?php echo !$form_changed ? '&changed=1' . $url_query : '' . $url_query; ?>" method="POST">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="hidden" name="id" value="<?php echo $form['customer']['id']; ?>">
                            <div class="form-group">
                                <label class="text-secondary" for="title">Company name *</label>
                                <input type="text" id="title" name="title" class="form-control" required
                                    value="<?php echo $form['customer']['title']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="ico">IČO *</label>
                                <input type="text" id="ico" name="ico" class="form-control" required
                                    value="<?php echo $form['customer']['ico']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="dic">DIČ *</label>
                                <input type="text" id="dic" name="dic" class="form-control" required
                                    value="<?php echo $form['customer']['dic']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="icdph">IČDPH</label>
                                <input type="text" id="icdph" name="icdph" class="form-control"
                                    value="<?php echo $form['customer']['icdph']; ?>">
                            </div>
                        </div>
                        <div class="col-md-5 ms-auto">
                            <div class="form-group">
                                <label class="text-secondary" for="street">Street *</label>
                                <input type="text" id="street" name="street" class="form-control" required
                                    value="<?php echo $form['customer']['street']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="street_number">Street number *</label>
                                <input type="text" id="street_number" name="street_number" class="form-control" required
                                    value="<?php echo $form['customer']['street_number']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="city">City *</label>
                                <input type="text" id="city" name="city" class="form-control" required
                                    value="<?php echo $form['customer']['city']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="state">State</label>
                                <input type="text" id="state" name="state" class="form-control"
                                    value="<?php echo $form['customer']['state']; ?>">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="postal_code">Postal code *</label>
                                <input type="text" id="postal_code" name="postal_code" class="form-control" required
                                    value="<?php echo $form['customer']['postal_code']; ?>">
                            </div>
                        </div>
                        <input type="submit" name="submitCustomer" class="btn btn-outline-secondary btn-block mt-4 w-100" value="Save">
                        <p class="text-danger mt-4"><?php echo $form['allert']; ?></p>
                    </div>
                </form>
            </div>
            <div id="items" class="p-3 <?php echo $page == 'items' ? '' : 'd-none'; ?>">
                <h1>Items</h1>
                <!-- items table -->
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Unit</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price per unit</th>
                            <th scope="col">Total price</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($form['items'] as $item) { 
                            if (!empty($item['ordernumber'])) { ?>
                        <tr>
                            <th scope="row"><?php echo $item['ordernumber']; ?></th>
                            <td class="w-50"><?php echo $item['title']; ?></td>
                            <td><?php echo $item['unit']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo $item['price']; ?></td>
                            <td><?php echo $item['total_price']; ?></td>
                            <td class="text-end">
                                <form class="d-inline" action="p_invoice.php?page=items<?php echo !$form_changed ? '&changed=1' . $url_query : '' . $url_query; ?>" method="POST">
                                    <input type="hidden" name="ordernumber" value="<?php echo $item['ordernumber']; ?>">
                                    <button type="submitDelItem" name="submitDelItem" value="Delete" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
                <form action="p_invoice.php?page=items<?php echo !$form_changed ? '&changed=1' . $url_query : '' . $url_query; ?>" method="POST">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="text-secondary" for="name">Title *</label>
                                <input type="text" id="title" name="title" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="unit">Unit</label>
                                <input type="text" id="unit" name="unit" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="quantity">Quantity *</label>
                                <input type="decimal" id="quantity" name="quantity" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="text-secondary" for="price">Price per unit *</label>
                                <input type="decimal" id="price" name="price" class="form-control" required>
                            </div>
                            <input type="submit" name="submitItem" value="Add item" class="btn btn-outline-secondary btn-block mt-4 w-100">
                        </div>
                    </div>
                </form>
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
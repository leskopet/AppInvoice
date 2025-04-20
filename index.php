<?php

// error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

require_once __DIR__ . '/components/init.php';

$statistics = array();
$clr_statistics = array(
    'invoices' => array(
        'count_total' => 0,
        'count_open' => 0,
        'count_sent' => 0,
        'count_paid' => 0,
        'count_overdue' => 0,
        'sum_total' => 0,
        'sum_open' => 0,
        'sum_expected' => 0,
        'sum_paid' => 0,
        'sum_overdue' => 0
    ),
    'customers' => array(
        'count_total' => 0,
    ),
    'upcoming_deadlines' => array(),
    'overdue_invoices' => array(),
    'allert' => ''
);
$statistics = $clr_statistics;
if(!empty($user['email'])) {
    $action = "getstatistics";
    $data = array();
    $result = sendRESTRequest($action, $data);
    
    if(isset($result['status']) && $result['status'] == 'success' && !empty($result['data'])) {
        $statistics = $result['data'];
    }
    else {
        $statistics = $clr_statistics;
        $statistics['allert'] = $result['data'];
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
        
        <!-- Main Content -->
        <?php if(!empty($user['email'])) { ?>
            <div class="px-5 py-4 ">
              <!-- Hero Section -->
              <div class="jumbotron jumbotron-fluid bg-light">
                <div class="container">
                  <h1 class="display-4">Welcome, <span id="user-name"><?php echo $user['fname'] . ' ' . $user['lname']; ?></span>!</h1>
                  <p class="lead">Your email: <span id="user-email"><?php echo $user['email']; ?></span></p>
                  <p class="lead text-danger <?php if(empty($statistics['allert'])) { echo 'd-none'; } ?>">Error: <span id="user-error"><?php echo $statistics['allert']; ?></span></p>
                  <p class="lead">You have <span id="total-invoices"><?php echo $statistics['invoices']['count_total']; ?></span> invoices sent, <span id="total-payments"><?php echo $statistics['invoices']['count_paid']; ?></span> payments received, and <span id="total-clients"><?php echo $statistics['customers']['count_total']; ?></span> clients.</p>
                </div>
              </div>
              
              <!-- Recent Activity Section -->
              <div class="container pb-5">
                <h2 class="mb-4">Statistics</h2>
                <ul class="list-group">
                  <li class="list-group-item">Not send sum of payments: <span id="open-invoices"><?php echo $statistics['invoices']['sum_open']; ?></span></li>
                  <li class="list-group-item">Expected sum of payments: <span id="expected-sum"><?php echo $statistics['invoices']['sum_expected']; ?></span></li>
                  <li class="list-group-item">Paid sum of payments: <span id="paid-sum"><?php echo $statistics['invoices']['sum_paid']; ?></span></li>
                  <li class="list-group-item">Overdue sum of payments: <span id="overdue-sum"><?php echo $statistics['invoices']['sum_overdue']; ?></span></li>
                  <li class="list-group-item">Total sum of payments: <span id="total-sum"><?php echo $statistics['invoices']['sum_total']; ?></span></li>
                </ul>
              </div>
              
              <!-- Upcoming Deadlines Section -->
              <div class="container pb-5">
                <h2 class="mb-4">Upcoming Deadlines</h2>
                <ul class="list-group">
                    <?php if(!empty($statistics['upcoming_deadlines'])) { 
                        foreach($statistics['upcoming_deadlines'] as $deadline) { ?>
                        <li class="list-group-item">Payment <span><?php echo $deadline['invoice']; ?></span> due to be received from <span><?php echo $deadline['customer']; ?></span> on <span><?php echo $deadline['date']; ?></span></li>
                    <?php } 
                    } else { echo '<li class="list-group-item">No upcoming deadlines</li>'; } ?>
                </ul>
              </div>
              
              <!-- Upcoming Deadlines Section -->
              <div class="container pb-5">
                <h2 class="mb-4">Overdue invoices</h2>
                <ul class="list-group">
                    <?php if(!empty($statistics['overdue_invoices'])) { 
                        foreach($statistics['overdue_invoices'] as $invoice) { ?>
                        <li class="list-group-item">Payment <span><?php echo $invoice['invoice']; ?></span> due to be received from <span><?php echo $invoice['customer']; ?></span> on <span><?php echo $invoice['date']; ?></span></li>
                    <?php } 
                    } else { echo '<li class="list-group-item">No overdue invoices</li>'; } ?>
                </ul>
              </div>
              
              <!-- Quick Actions Section -->
              <div class="container pb-5">
                <h2 class="mb-4">Quick Actions</h2>
                <div class="row">
                  <div class="col-md-4">
                    <a class="btn btn-primary" href="p_invoice.php?page=invoice">Create a new invoice</a>
                  </div>
                  <div class="col-md-4">
                    <a class="btn btn-primary" href="p_lists.php?page=invoices">View invoice history</a>
                  </div>
                </div>
              </div>
            </div>
        <?php } else { ?>
            <div class="px-5 py-4 ">
                <!-- Hero Section -->
                <div class="jumbotron jumbotron-fluid bg-light">
                <div class="container">
                    <h1 class="display-4">Create Professional Invoices in Minutes</h1>
                    <p class="lead">Our app helps you manage your finances, track payments, and get paid faster</p>
                    <p><a class="btn btn-primary btn-lg" href="p_signup.php" role="button">Sign Up for Free</a></p>
                </div>
                </div>
                
                <!-- Features Section -->
                <div class="container">
                <h2 class="mb-4">What You Can Do with Our App</h2>
                <div class="row">
                    <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                        <h5 class="card-title">Create and send professional invoices with ease</h5>
                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                        <h5 class="card-title">Track payments and stay on top of your finances</h5>
                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                        <h5 class="card-title">Manage multiple clients and projects in one place</h5>
                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                
                <!-- Benefits Section -->
                <div class="container">
                <h2 class="mb-4">Why Choose Our App?</h2>
                <div class="row">
                    <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                        <h5 class="card-title">Save time and reduce paperwork</h5>
                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                        <h5 class="card-title">Improve cash flow and get paid faster</h5>
                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                        <h5 class="card-title">Enhance your professional image with custom invoices</h5>
                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                
                <!-- Testimonials Section -->
                <div class="container">
                <h2 class="mb-4">What Our Users Say</h2>
                <div class="row">
                    <div class="col-md-6">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                        <p class="card-text">"Our app has saved us so much time and hassle with invoicing. Highly recommend!" - John Doe</p>
                        </div>
                    </div>
                    </div>
                    <div class="col-md-6">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                        <p class="card-text">"I was able to create and send my first invoice in just a few minutes. Love it!" - Jane Doe</p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                
                <!-- Call-to-Action Section -->
                <div class="container pb-5">
                <p class="lead">Try our app today and start streamlining your invoicing process</p>
                <p><a class="btn btn-primary btn-lg" href="p_signup.php" role="button">Sign Up for Free</a></p>
                </div>
            </div>
        <?php } ?>
        
    </div>  
     
  </div>
    
  
  <!-- Footer -->
  <div class="bg-dark text-white p-2 text-center fixed-bottom">
    <p>&copy; 2025 AppInvoice</p>
  </div>
</body>
</html>
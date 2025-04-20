<?php

require_once __DIR__ . '/components/init.php';

if(empty($user['email'])) {
    header("Location: p_login.php");
    exit();
}
$invoices = array();
$customers = array();
$allert = "";

// init page
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    if ($page == 'invoices') {
        $action = "getuserinvoices";
        $data = array(
            "email" => $user['email']
        );
        $result = sendRESTRequest($action, $data);
        $invoices = $result['data'];
    }
    else if ($page == 'customers') {
        $action = "getcustomers";
        $data = array(
            "email" => $user['email']
        );
        $result = sendRESTRequest($action, $data);
        $customers = $result['data'];
        for ($i=0; $i < count($customers); $i++) { 
            $customers[$i]['order_number'] = $i+1;
        }
    }
}
else {
    $page = "";
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    if($action == 'setstatus') {
        if(!isset($_POST['id']) || empty($_POST['id'])) {
            $allert = "Missing invoice id";
        }
        elseif(!isset($_POST['status']) || empty($_POST['status']) || $_POST['status'] < 0 || $_POST['status'] > 3) {
            $allert = "Missing or invalid invoice status";
        }
        else {
            $data = array(
                "id" => $_POST['id'],
                "status" => $_POST['status']
            );
            $result = sendRESTRequest($action, $data);
            if($result['status'] == 'success') {
                header("Location: p_lists.php?page=invoices");
                exit();
            }
            else {
                $allert = $result['message'] . '<br>' . $result['data'];
            }
        }
    }
    else { $allert = "Wrong action"; }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AppInvoice</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
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
                <h2>Lists</h2>
                <ul class="list-group">
                    <li class="list-group-item"><a class="text-dark" href="p_lists.php?page=invoices">Invoices</a></li>
                    <li class="list-group-item"><a class="text-dark" href="p_lists.php?page=customers">Customers</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 py-4">
            <div id="invoices" class="p-3 <?php echo $page == 'invoices' ? '' : 'd-none'; ?>">
                <h1>Invoices</h1>
                <div class="">
                    <!-- table of invoices -->
                    <table class="table table-striped">
                        <tr>
                            <th width="15%">Invoice no.</th>
                            <th width="40%">Customer</th>
                            <th width="15%">Created At</th>
                            <th class="text-center" width="10%">Status</th>
                            <th class="text-center" width="20%">Actions</th>
                        </tr>
                        <?php
                        if (isset($invoices) && count($invoices) > 0) {
                            foreach ($invoices as $invoice) { ?>
                                <tr class="align-middle">
                                    <td><?php echo $invoice['title']; ?></td>
                                    <td><?php echo $invoice['customer_title']; ?></td>
                                    <td><?php echo $invoice['created']; ?></td>
                                    <td class="text-center">
                                        <span class="text-decoration-underline btn" data-invoice="<?php echo $invoice['id']; ?>" data-invoice-status="<?php echo $invoice['status']; ?>" data-target="#invoiceStatusModal">
                                            <?php 
                                                switch ($invoice['status']) {
                                                    case 0: echo 'Draft'; break;
                                                    case 1: echo 'Sent'; break;
                                                    case 2: echo 'Paid'; break;
                                                    case 3: echo 'Overdue'; break;
                                                    default: echo 'Unknown';
                                                }
                                            ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="p_invoice.php?page=invoice&id=<?php echo $invoice['id']; ?>" class="btn btn-primary">Open</a>
                                    </td>
                                </tr>
                        <?php    }
                        } else { ?>
                            <p>No invoices found.</p>
                        <?php } ?>
                    </table>
                    <p class="allert text-danger <?php if(empty($allert)) { echo 'd-none'; } ?>"><?php echo $allert;?></p>
                </div>
            </div>
            <div id="customers" class="p-3 <?php echo $page == 'customers' ? '' : 'd-none'; ?>">
                <h1>Customers</h1>
                <div class="">
                    <!-- table of customers -->
                    <table class="table table-striped">
                        <tr>
                            <th width="10%">Order no.</th>
                            <th width="35%">Customer</th>
                            <th width="15%">Email</th>
                            <th class="text-center" width="15%">Phone</th>
                            <th class="text-center" width="15%">Actions</th>
                        </tr>
                        <?php
                        if (isset($customers) && count($customers) > 0) {
                            foreach ($customers as $customer) { ?>
                                <tr class="align-middle">
                                    <td><?php echo $customer['order_number']; ?></td>
                                    <td><?php echo $customer['title']; ?></td>
                                    <td><?php echo $customer['email']; ?></td>
                                    <td><?php echo $customer['phone']; ?></td>
                                    <td class="text-center">
                                        <a href="p_invoice.php?page=invoice&customer=<?php echo $customer['id']; ?>" class="btn btn-primary">Create Inv.</a>
                                    </td>
                                </tr>
                        <?php    }
                        } else { ?>
                            <p>No customers found.</p>
                        <?php } ?>
                    </table>
                    <p class="allert text-danger <?php if(empty($allert)) { echo 'd-none'; } ?>"><?php echo $allert;?></p>
                </div>
            </div>
        </div>
    </div>  
  </div>
  
  <!-- Modal -->
  <div class="modal fade" id="invoiceStatusModal" tabindex="-1" role="dialog" aria-labelledby="invoiceStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="invoiceStatusModalLabel">Select an option</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="invoiceStatusForm" method="POST" action="p_lists.php?page=invoices">
            <input type="hidden" id="status-invoice-id" name="id">
            <input type="hidden" name="action" value="setstatus">
            <div class="form-group">
              <div class="custom-control custom-radio">
                <input type="radio" id="customRadio1" name="status" class="custom-control-input" value="0">
                <label class="custom-control-label" for="customRadio1">Draft</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" id="customRadio2" name="status" class="custom-control-input" value="1">
                <label class="custom-control-label" for="customRadio2">Sent</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" id="customRadio3" name="status" class="custom-control-input" value="2">
                <label class="custom-control-label" for="customRadio3">Paid</label>
              </div>
              <div class="custom-control custom-radio">
                <input type="radio" id="customRadio4" name="status" class="custom-control-input" value="3">
                <label class="custom-control-label" for="customRadio4">Overdue</label>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveInvoiceStatus">Save changes</button>
        </div>
      </div>
    </div>
  </div>
    
  
  <!-- Footer -->
  <div class="bg-dark text-white p-2 text-center fixed-bottom">
    <p>&copy; 2025 AppInvoice</p>
  </div>

  <script>
    let invoiceStatusModal = $('#invoiceStatusModal');
    document.querySelectorAll('[data-invoice-status]').forEach(item => {
        item.addEventListener('click', () => {
            let id = item.getAttribute('data-invoice');
            let status = item.getAttribute('data-invoice-status');
            $('#status-invoice-id').val(id);
            $('[name="status"][value="' + status + '"]').prop('checked', true);
            invoiceStatusModal.modal('show');
        })
    })
    
    $('#saveInvoiceStatus').on('click', () => {
        let form = $('#invoiceStatusForm');
        form.submit();
    })
    
    document.querySelectorAll('[data-dismiss="modal"]').forEach(item => {
        item.addEventListener('click', () => {
            invoiceStatusModal.modal('hide');
        })
    })
  </script>
</body>
</html>
<?php

// enable debugging
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();
if(!isset($_SESSION['AppInvoice_invoice']) && empty($_SESSION['AppInvoice_invoice'])) {
    header("Location: p_invoice.php?page=invoice");
    exit();
}
$invoice = $_SESSION['AppInvoice_invoice'];

$invoice_number = $invoice['title'];
$invoice_date = !empty($invoice['created']) ? date('d.m.Y', strtotime($invoice['created'])) : date('d.m.Y');
$delivery_date = !empty($invoice['suplied']) ? date('d.m.Y', strtotime($invoice['suplied'])) : date('d.m.Y');
$payment_date = !empty($invoice['due_date']) ? date('d.m.Y', strtotime($invoice['due_date'])) : date('d.m.Y', strtotime('+14 days'));
$payment_date_for_QR = date('Ymd', strtotime($delivery_date));
$variable_code = $invoice_number;

$suplier_name = $invoice['suplier']['title'];
$suplier_address = [
    'street' => $invoice['suplier']['street'] . ' ' . $invoice['suplier']['street_number'],
    'city' => $invoice['suplier']['city'],
    'state' => $invoice['suplier']['state'],
    'zip' => $invoice['suplier']['postal_code']
];
$suplier_info = $invoice['suplier']['description'];
$suplier_ico = $invoice['suplier']['ico'];
$suplier_dic = $invoice['suplier']['dic'];
$suplier_tax = !empty($invoice['suplier']['icdph']);
$suplier_icdph = $suplier_tax ? $invoice['suplier']['icdph'] : "";
$suplier_dphInfo = $suplier_tax ? $suplier_icdph : 'Neplatiteľ DPH';

$suplier_phone = $invoice['suplier']['phone'];
$suplier_email = $invoice['suplier']['email'];

$suplier_iban = $invoice['suplier']['iban'];
$suplier_swift = $invoice['suplier']['swift'];
$suplier_bank = $invoice['suplier']['bank'];

$customer_name = $invoice['customer']['title'];
$customer_address = [
    'street' => $invoice['customer']['street'] . ' ' . $invoice['customer']['street_number'],
    'city' => $invoice['customer']['city'],
    'state' => $invoice['customer']['state'],
    'zip' => $invoice['customer']['postal_code']
];
$customer_ico = $invoice['customer']['ico'];
$customer_dic = $invoice['customer']['dic'];
$customer_icdph = $invoice['customer']['icdph'];

// $customer_phone = $invoice['customer']['phone'];
// $customer_email = $invoice['customer']['email'];

$items = $invoice['items'];

$price_subtotal = 0;
foreach ($items as $item) {
    $price_subtotal += $item['total_price'];
}
$tax_rate = $suplier_tax ? 23 : 0;
$tax = $price_subtotal * $tax_rate / 100;
$tax = round($tax, 2);
$price_total = $price_subtotal + $tax;
$price_total = round($price_total, 2);

include __DIR__ . '/../Functions/QRGenerate.php';
$QRdata = [
    'IBAN' => str_replace(' ', '', $suplier_iban),
    'amount' => str_replace(',', '.', $price_total),
    'currency' => 'EUR',
    'date' => $payment_date_for_QR,
    'VS' => $variable_code,
    'SS' => "",
    'KS' => "",
    'message' => "Platba za faktúru " . $invoice_number,
    'customer_name' => $suplier_name
];
$params = [

];

$url_to_return_qrcode = generateQRbySquare($QRdata, $params);
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="sk" lang="sk">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $invoice_number; ?></title>
    <meta name="author" content="Peter Leško" />
    <meta name="description" content="<?php echo $invoice_number; ?>" />
    <style type="text/css">
      * {
        margin: 0;
        padding: 0;
        text-indent: 0;
        font-family: Helvetica, sans-serif;
      }
      .s1 {
        color: black;
        font-style: normal;
        font-weight: normal;
        font-family: Arial, sans-serif;
        text-decoration: none;
        font-size: 16pt;
      }
      .s2 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 16pt;
      }
      .s3 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 12pt;
      }
      .s4 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 10pt;
      }
      .s5 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 12pt;
      }
      .s6 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 9pt;
      }
      .s8 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 8pt;
      }
      .s9 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 8pt;
      }
      .s10 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 14pt;
      }
      .s11 {
        color: black;
        font-family: "Times New Roman", serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 10pt;
      }
      .s12 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: bold;
        text-decoration: none;
        font-size: 16pt;
      }
      .s13 {
        color: black;
        font-family: Arial, sans-serif;
        font-style: normal;
        font-weight: normal;
        text-decoration: none;
        font-size: 7pt;
      }
      table,
      tbody {
        vertical-align: top;
        overflow: visible;
      }
    </style>
  </head>
  <body 
    style="
      font-family: Helvetica, sans-serif;"
    >
    <!-- whole content -->
    <table
      style="
        border-collapse: collapse;
        margin-left: 0pt;
        margin-right: 0pt;
        vertical-align: middle;"
      cellspacing="0"
    >
      <!-- Top margin/padding -->
      <!-- empty row / table structure -->
      <tr>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
        <td style="width: 45pt; border: 0pt solid black;"></td>
      </tr>
      <!-- header title -->
      <tr>
        <td
          style="
            height: 22pt;
            border-top-style: solid;
            border-top-width: 2pt;
            border-left-style: solid;
            border-left-width: 2pt;
            border-bottom-style: solid;
            border-bottom-width: 2pt;
            border-right-style: solid;
            border-right-width: 0pt;
            vertical-align: middle;
            padding-left: 3pt;
            font-size: 16pt;
          "
          colspan="6"
        >
          FAKTÚRA
        </td>
        <td
          style="
            border-top-style: solid;
            border-top-width: 2pt;
            border-left-style: solid;
            border-left-width: 0pt;
            border-bottom-style: solid;
            border-bottom-width: 2pt;
            border-right-style: solid;
            border-right-width: 2pt;
            vertical-align: middle;
            text-align: right;
            padding-right: 5pt;
            font-size: 16pt;
          "
          colspan="6"
        > <?php echo $invoice_number; ?>
        </td>
      </tr>
      <!-- suplier info -->
      <tr>
        <td
          style="
            border-top-style: solid;
            border-top-width: 2pt;
            border-left-style: solid;
            border-left-width: 2pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 1pt;
            vertical-align: top;
            padding-top: 11pt;
            padding-bottom: 11pt;
            padding-left: 11pt;
            padding-right: 11pt;
          "
          colspan="6"
          rowspan="2"
        >
          <p
            style="
              text-indent: 0pt;
              font-size: 16pt;
              text-align: left;
            "
          >
            <?php echo $suplier_name; ?>
          </p>
          <p
            style="
              text-indent: 0pt;
              font-size: 12pt;
              text-align: left;
            "
          >
            <?php echo $suplier_address["street"]; ?>
          </p>
          <p
            style="
              font-size: 12pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            <?php echo $suplier_address["zip"] . ", " . $suplier_address["city"]; ?>
          </p>
          <p
            style="
              font-size: 12pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            <?php echo $suplier_address["state"]; ?>
          </p>
          <p style="font-size: 7pt;"><br /></p>
          <p
            style="
              font-size: 10pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            <?php echo $suplier_info; ?>
          </p>
          <p style="font-size: 7pt;"><br /></p>
          <p
            style="
              font-size: 12pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            IČO:<?php echo $suplier_ico; ?>
          </p>
          <p
            style="
              font-size: 12pt; text-indent: 0pt; text-align: left"
          >
            DIČ:<?php echo $suplier_dic; ?>
          </p>
          <p
            style="
              font-size: 12pt; text-indent: 0pt; text-align: left"
          >
            <!-- IČDPH: SK -->
            <?php echo $suplier_dphInfo; ?>
          </p>
          <p style="font-size: 7pt;"><br /></p>
          <p
            style="
              font-size: 10pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            TELEFÓN: <?php echo $suplier_phone; ?>
          </p>
          <p
            style="
              font-size: 10pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            EMAIL: <?php echo $suplier_email; ?>
          </p>
          <p style="font-size: 7pt;"><br /></p>
          <p
            style="
              font-size: 10pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            IBAN: <b><?php echo $suplier_iban; ?></b>
          </p>
          <p
            style="
              font-size: 10pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            SWIFT: <?php echo $suplier_swift; ?>
          </p>
          <p
            style="
              font-size: 10pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            BANKA: <?php echo $suplier_bank; ?>
          </p>
        </td>
        <td
          style="
            height: 19pt;
            border-top-style: solid;
            border-top-width: 2pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 1pt;
            padding-left: 3pt;
            padding-right: 3pt;
          "
          colspan="3"
        >
          <p
            style="
              font-size: 8pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            Forma úhrady: peňažný prevod
          </p>
        </td>
        <td
          style="
            height: 19pt;
            border-top-style: solid;
            border-top-width: 2pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 2pt;
            padding-left: 3pt;
            padding-right: 3pt;
          "
          colspan="3"
        >
          <p
            style="
              font-size: 8pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            Variabilný symbol: <?php echo $variable_code; ?>
          </p>
        </td>
      </tr>
      <!-- customer info -->
      <tr>
        <td
          style="
            height: 247pt;
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 2pt;
            vertical-align: top;
            padding-top: 5pt;
            padding-left: 11pt;
            padding-right: 11pt;
          "
          colspan="6"
        >
          <p
            style="
              font-size: 8pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            Odberateľ
          </p>
          <p style="font-size: 10pt;"><br /></p>
          <p
            style="font-size: 14pt; text-indent: 0pt; text-align: left"
          >
            <?php echo $customer_name; ?>
          </p>
          <p
            style="text-indent: 0pt; text-align: left"
          >
            <?php echo $customer_address["street"]; ?>
          </p>
          <p
            style="
              font-size: 12pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            <?php echo $customer_address["zip"] . ", " . $customer_address["city"]; ?>
          </p>
          <p
            style="
              font-size: 12pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            <?php echo $customer_address["state"]; ?>
          </p>
          <p style="font-size: 7pt;"><br /></p>
          <p
            style="
              font-size: 10pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            IČO: <?php echo $customer_ico; ?>
          </p>
          <p
            style="
              font-size: 10pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            DIČ: <?php echo $customer_dic; ?>
          </p>
          <p
            style="
              font-size: 10pt;
              text-indent: 0pt;
              text-align: left;
            "
          >
            IČDPH: <?php echo $customer_icdph; ?>
          </p>
        </td>
      </tr>
      <!-- dates -->
      <tr>
        <td
          style="
            height: 12pt;
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 2pt;
            border-bottom-style: solid;
            border-bottom-width: 0pt;
            border-right-style: solid;
            border-right-width: 1pt;
            font-size: 8pt;
            padding-left: 2pt;
            text-align: left;
          "
          colspan="4"
        >
          Dátum vystavenia
        </td>
        <td
          style="
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 0pt;
            border-right-style: solid;
            border-right-width: 1pt;
            font-size: 8pt;
            padding-left: 2pt;
            text-align: left;
          "
          colspan="4"
        >
          Dátum dodania
        </td>
        <td
          style="
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 0pt;
            border-right-style: solid;
            border-right-width: 2pt;
            font-size: 8pt;
            padding-left: 2pt;
            text-align: left;
          "
          colspan="4"
        >
          Dátum splatnosti
        </td>
      </tr>
      <tr>
        <td
          style="
            height: 17pt;
            border-top-style: solid;
            border-top-width: 0pt;
            border-left-style: solid;
            border-left-width: 2pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 1pt;
            text-align: center;
            padding-left: 75pt;
            padding-right: 5pt;
          "
          colspan="4"
        >
          <?php echo $invoice_date; ?>
        </td>
        <td
          style="
            border-top-style: solid;
            border-top-width: 0pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 1pt;
            text-align: center;
            padding-left: 75pt;
            padding-right: 5pt;
          "
          colspan="4"
        >
          <?php echo $delivery_date; ?>
        </td>
        <td
          style="
            border-top-style: solid;
            border-top-width: 0pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 2pt;
            text-align: center;
            padding-left: 75pt;
            padding-right: 5pt;
          "
          colspan="4"
        >
          <?php echo $payment_date; ?>
        </td>
      </tr>
      <!-- table with items -->
      <tr>
        <td
          style="
            height: 230pt;
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 2pt;
            border-bottom-style: none;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 2pt;
            vertical-align: top;
          "
          colspan="12"
        >
          <p style="text-indent: 0pt; text-align: left"><br /></p>
          <p style="padding-left: 11pt; text-indent: 0pt; text-align: left">
            <span>
              <!-- table with items -->
              <table
                style="
                  border-collapse: collapse;
                  margin-left: 12pt;
                  margin-right: 12pt;
                  text-align: center;
                  vertical-align: middle;"
                cellspacing="0"
              >
                <!-- header -->
                <tr>
                  <td
                    style="
                      height: 14pt;
                      width: 360pt;
                      border-top-style: solid;
                      border-top-width: 1pt;
                      border-left-style: solid;
                      border-left-width: 1pt;
                      border-bottom-style: solid;
                      border-bottom-width: 1pt;
                      border-right-style: solid;
                      border-right-width: 1pt;
                      font-size: 8pt;
                    "
                  >
                    Popis položky
                  </td>
                  <td
                    style="
                      width: 63pt;
                      border-top-style: solid;
                      border-top-width: 1pt;
                      border-left-style: solid;
                      border-left-width: 1pt;
                      border-bottom-style: solid;
                      border-bottom-width: 1pt;
                      border-right-style: solid;
                      border-right-width: 1pt;
                      font-size: 8pt;
                    "
                  >
                    Množstvo
                  </td>
                  <td
                    style="
                      width: 39pt;
                      border-top-style: solid;
                      border-top-width: 1pt;
                      border-left-style: solid;
                      border-left-width: 1pt;
                      border-bottom-style: solid;
                      border-bottom-width: 1pt;
                      border-right-style: solid;
                      border-right-width: 1pt;
                      font-size: 8pt;
                    "
                  >
                    MJ
                  </td>
                  <td
                    style="
                      width: 68pt;
                      border-top-style: solid;
                      border-top-width: 1pt;
                      border-left-style: solid;
                      border-left-width: 1pt;
                      border-bottom-style: solid;
                      border-bottom-width: 1pt;
                      border-right-style: solid;
                      border-right-width: 1pt;
                      font-size: 8pt;
                    "
                  >
                    Cena za MJ
                  </td>
                  <td
                    style="
                      width: 71pt;
                      border-top-style: solid;
                      border-top-width: 1pt;
                      border-left-style: solid;
                      border-left-width: 1pt;
                      border-bottom-style: solid;
                      border-bottom-width: 1pt;
                      border-right-style: solid;
                      border-right-width: 1pt;
                      font-size: 8pt;
                    "
                  >
                    Celková cena
                  </td>
                </tr>
                <!-- ITEMS -->
                <?php foreach( $items as $item ) { ?>
                  <tr>
                    <td
                      style="
                        height: 14pt;
                        width: 269pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                        font-size: 8pt;
                        text-align: left;
                        padding-left: 3pt;
                      "
                    >
                      <?php echo $item["title"]; ?>
                    </td>
                    <td
                      style="
                        width: 63pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                        font-size: 8pt;
                      "
                    >
                      <?php echo number_format($item['quantity'], 2, ',', ' '); ?>
                    </td>
                    <td
                      style="
                        width: 39pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                        font-size: 8pt;
                      "
                    >
                      <?php echo $item["unit"]; ?>
                    </td>
                    <td
                      style="
                        width: 68pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                        font-size: 8pt;
                      "
                    >
                      <?php echo number_format($item['price'], 2, ',', ' '); ?>
                    </td>
                    <td
                      style="
                        width: 71pt;
                        border-top-style: solid;
                        border-top-width: 1pt;
                        border-left-style: solid;
                        border-left-width: 1pt;
                        border-bottom-style: solid;
                        border-bottom-width: 1pt;
                        border-right-style: solid;
                        border-right-width: 1pt;
                        font-size: 8pt;
                      "
                    >
                      <?php echo number_format($item['total_price'], 2, ',', ' '); ?>
                    </td>
                  </tr>
                <?php } ?>
                <?php if($suplier_tax) { ?>
                <!-- dph -->
                <tr>
                  <td
                    style="
                      height: 14pt;
                      width: 439pt;
                      border-top-style: solid;
                      border-top-width: 1pt;
                      border-left-style: solid;
                      border-left-width: 1pt;
                      border-bottom-style: solid;
                      border-bottom-width: 1pt;
                      border-right-style: solid;
                      border-right-width: 1pt;
                      font-size: 8pt;
                      text-align: right;
                      padding-left: 3pt;
                      padding-right: 3pt;
                    "
                    colspan="4"
                  >
                    Dph <?php echo number_format($tax_rate, 2, ',', ' '); ?>%:
                  </td>
                  <td
                    style="
                      width: 71pt;
                      border-top-style: solid;
                      border-top-width: 1pt;
                      border-left-style: solid;
                      border-left-width: 1pt;
                      border-bottom-style: solid;
                      border-bottom-width: 1pt;
                      border-right-style: solid;
                      border-right-width: 1pt;
                      font-size: 8pt;
                    "
                  >
                    <?php echo number_format($tax, 2, ',', ' ');; ?>
                  </td>
                </tr>
                <?php } ?>
                <!-- sum -->
                <tr>
                  <td
                    style="
                      height: 14pt;
                      width: 439pt;
                      border-top-style: solid;
                      border-top-width: 1pt;
                      border-left-style: solid;
                      border-left-width: 1pt;
                      border-bottom-style: solid;
                      border-bottom-width: 1pt;
                      border-right-style: solid;
                      border-right-width: 1pt;
                      font-size: 8pt;
                      text-align: right;
                      padding-left: 3pt;
                      padding-right: 3pt;
                    "
                    colspan="4"
                  >
                    Spolu:
                  </td>
                  <td
                    style="
                      width: 71pt;
                      border-top-style: solid;
                      border-top-width: 1pt;
                      border-left-style: solid;
                      border-left-width: 1pt;
                      border-bottom-style: solid;
                      border-bottom-width: 1pt;
                      border-right-style: solid;
                      border-right-width: 1pt;
                      font-size: 8pt;
                    "
                  >
                    <?php echo number_format($price_total, 2, ',', ' ');; ?>
                  </td>
                </tr>
              </table>
            </span>
          </p>
        </td>
      </tr>
      <!-- QR code -->
      <tr>
        <td
          style="
            height: 140pt;
            border-top-style: none;
            border-top-width: 0pt;
            border-left-style: solid;
            border-left-width: 2pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 2pt;
            padding-left: 8pt;
          "
          colspan="12"
        >
          <p style="text-indent: 0pt; text-align: left"><br /></p>
          <p style="padding-left: 11pt; text-indent: 0pt; text-align: left">
            <span
              ><table border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td>
                    <img src="<?php echo $url_to_return_qrcode; ?>" alt="QR Code PayBySquare">
                  </td>
                </tr></table
            ></span>
          </p>
        </td>
      </tr>
      <!-- footer signes -->
      <tr>
        <td
          style="
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 2pt;
            border-bottom-style: solid;
            border-bottom-width: 2pt;
            border-right-style: solid;
            border-right-width: 1pt;
            vertical-align: top;
            padding-top: 3pt;
            padding-left: 3pt;
            font-size: 8pt;
          "
          colspan="4"
          rowspan="5"
        >
          Vyhotovil:
        </td>
        <td
          style="
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 2pt;
            border-right-style: solid;
            border-right-width: 1pt;
            vertical-align: top;
            padding-top: 3pt;
            padding-left: 3pt;
            font-size: 8pt;
          "
          colspan="4"
          rowspan="5"
        >
          Prevzal:
        </td>
        <!-- total price -->
        <td
          style="
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 1pt;
            font-size: 8pt;
            padding-left: 3pt;
          "
          colspan="2"
        >
          Celková suma:
        </td>
        <td
          style="
            height: 13pt;
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 2pt;
            font-size: 8pt;
            padding-right: 5pt;
            text-align: right;
          "
          colspan="2"
        >
          <?php echo number_format($price_total, 2, ',', ' '); ?> EUR
        </td>
      </tr>
      <!-- footer total prices -->
      <tr>
        <td
          style="
            height: 13pt;
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 1pt;
            font-size: 8pt;
            padding-left: 3pt;
          "
          colspan="2"
        >
          Uhradené zálohami:
        </td>
        <td
          style="
            height: 13pt;
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 2pt;
            font-size: 8pt;
            padding-right: 5pt;
            text-align: right;
          "
          colspan="2"
        >
          <?php echo number_format(0, 2, ',', ' '); ?> EUR
        </td>
      </tr>
      <!-- footer total prices -->
      <tr>
        <td
          style="
            height: 13pt;
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 1pt;
            font-size: 8pt;
            padding-left: 3pt;
          "
          colspan="2"
        >
          Zostáva uhradiť:
        </td>
        <td
          style="
            height: 13pt;
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 1pt;
            border-right-style: solid;
            border-right-width: 2pt;
            font-size: 8pt;
            padding-right: 5pt;
            text-align: right;
          "
          colspan="2"
        >
            <?php echo number_format($price_total, 2, ',', ' '); ?> EUR
        </td>
      </tr>
      <!-- footer total prices -->
      <tr>
        <td
          style="
            height: 12pt;
            border-top-style: solid;
            border-top-width: 1pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 0pt;
            border-right-style: solid;
            border-right-width: 2pt;
            font-size: 8pt;
            padding-top: 3pt;
            padding-left: 3pt;
            vertical-align: top;
          "
          colspan="4"
        >
          K úhrade:
        </td>
      </tr>
      <tr>
        <td
          style="
            height: 43pt;
            border-top-style: solid;
            border-top-width: 0pt;
            border-left-style: solid;
            border-left-width: 1pt;
            border-bottom-style: solid;
            border-bottom-width: 2pt;
            border-right-style: solid;
            border-right-width: 2pt;
            padding-right: 5pt;
            text-align: right;
          "
          colspan="4"
        >
          <p
            style="
              font-size: 16pt;
              font-weight: bold;
              text-indent: 0pt;
            "
          >
            <?php echo number_format($price_total, 2, ',', ' '); ?> EUR
          </p>
          <!-- <p
            style="font-size: 7pt; text-indent: 0pt;"
          >
            osemstopäťdesiatdva eur
          </p> -->
        </td>
      </tr>
    </table>
    <p style="text-indent: 0pt; text-align: left" />
  </body>
</html>

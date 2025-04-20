<?php

function generateQRbySquare($QRdata, $params = array()) {
    // https://api.freebysquare.sk/pay/v1/generate-png
    // ?size=400&color=3&transparent=true
    // &amount=10.00&currencyCode=EUR
    // &dueDate=20220524&variableSymbol=1234567890&constantSymbol=1234&specificSymbol=1234567890
    // &paymentNote=Testovacia platba
    // &iban=SK8975000000000012345671
    // &beneficiaryName=Neplat Ma&beneficiaryAddressLine1=Teraforma 15&beneficiaryAddressLine2=Kalinovo, 987 05

    if(!isset($params['size'])) {
        $params['size'] = "";
    }
    if(!isset($params['color'])) {
        $params['color'] = "";
    }
    if(!isset($params['transparent'])) {
        $params['transparent'] = "";
    }
    $url  = "https://api.freebysquare.sk/pay/v1/generate-png";
    $url .= !empty($params['size'])             ? "?size=" .                    $params['size']             : "?size=" . "130";
    $url .= !empty($params['color'])            ? "&color=" .                   $params['color']            : "&color=" . "3";
    $url .= !empty($params['transparent'])      ? "&transparent=" .             $params['transparent']      : "&transparent=" . "true";
    $url .= !empty($QRdata['amount'])           ? "&amount=" .                  $QRdata['amount']           : "";
    $url .= !empty($QRdata['currency'])         ? "&currencyCode=" .            $QRdata['currency']         : "&currencyCode=" . "EUR";
    $url .= !empty($QRdata['date'])             ? "&dueDate=" .                 $QRdata['date']             : "";
    $url .= !empty($QRdata['VS'])               ? "&variableSymbol=" .          $QRdata['VS']               : "";
    $url .= !empty($QRdata['KS'])               ? "&constantSymbol=" .          $QRdata['KS']               : "";
    $url .= !empty($QRdata['SS'])               ? "&specificSymbol=" .          $QRdata['SS']               : "";
    $url .= !empty($QRdata['message'])          ? "&paymentNote=" .             $QRdata['message']          : "";
    $url .= !empty($QRdata['IBAN'])             ? "&iban=" .                    $QRdata['IBAN']             : "";
    $url .= !empty($QRdata['customer_name'])    ? "&beneficiaryName=" .         $QRdata['customer_name']    : "";
    $url .= !empty($QRdata['AddrLine1'])        ? "&beneficiaryAddressLine1=" . $QRdata['AddrLine1']        : "";
    $url .= !empty($QRdata['AddrLine2'])        ? "&beneficiaryAddressLine2=" . $QRdata['AddrLine2']        : "";

    return $url;
}

function generateQR($QRdata, $params = array()) {
    // key
    $key = '1LGyawxAQhOSpHXYurZikD9VlIB5Kf7J';

    // text
    $text = "https://payme.sk/";                        // domain
    $text .= "?V=1";                                    // version
    $text .= !empty($QRdata['IBAN'])           ? "&IBAN=" .    $QRdata['IBAN']           : "";       // IBAN
    $text .= !empty($QRdata['amount'])         ? "&AM=" .      $QRdata['amount']         : "";       // amount
    $text .= !empty($QRdata['currency'])       ? "&CC=" .      $QRdata['currency']       : "EUR";    // currency
    $text .= !empty($QRdata['date'])           ? "&DT=" .      $QRdata['date']           : "";       // up to date
    $text .= !empty($QRdata['VS'])             ? "&PI=/VS" .   $QRdata['VS']             : "";       // payment identificator, VS
    $text .= !empty($QRdata['SS'])             ? "/SS" .       $QRdata['SS']             : "";       // payment identificator, SS
    $text .= !empty($QRdata['KS'])             ? "/KS" .       $QRdata['KS']             : "";       // payment identificator, KS
    $text .= !empty($QRdata['message'])        ? "&MSG=" .     $QRdata['message']        : "";       // message
    $text .= !empty($QRdata['customer_name'])  ? "&CN=" .      $QRdata['customer_name']  : "";       // customer name

    //create the main url to call the api using your details. Make sure you urlencode the text part to be safe!
    $url = "https://www.qrcoder.co.uk/api/v4/?key=" . $key . "&text=" . urlencode($text);

    // params
    $url .= !empty($params['size']) ? "&size=" . $params['size'] : "";

    return $url;
}

?>
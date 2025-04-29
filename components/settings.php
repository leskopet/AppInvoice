<?php

$server = array(
    "protocol" => "http",
    "host" => "localhost",
    "port" => "",
    "path" => "/AppInvoice"
);

$server_url = $server['protocol'];
$server_url .= '://';
$server_url .= $server['host'];
$server_url .= !empty($server['port']) ? ':' . $server['port'] : '';
$server_url .= $server['path'];

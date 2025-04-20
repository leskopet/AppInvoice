<?php

include_once ( __DIR__ . '/general_input_validation.php');

/**
 * strip tags, htmlspecialchars and trim the sended value
 *
 * @param string $value
 *
 * @return string
 */
function filter_value($value)
{
    $value = stripslashes($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value);
    $value = trim($value);
    // $value = quotemeta($value);
    return $value;
}

/**
 * filter data - loop through all array elements and filter values
 *
 * @param array $data
 * @param int $level
 *
 * @return array
 */
function filter_data($data, $level = 0)
{
    global $data_keys;

    if (!is_array($data)) {
        if ($level == 0) {
            exit;
        }

        return filter_value($data);
    }

    // loop through all array elements
    foreach ($data as $key => $value) {

        // filter keys
        if (!in_array($key, $data_keys[$level]) && !is_int($key)) {
            unset($data[$key]);
            continue;
        }

        // filter each value as data array
        $data[$key] = filter_data($value, $level + 1);
    }
    return $data;
}

function filter_table_data($data, $allowed_keys = null, $level = 0)
{
    global $data_keys;

    $keys_for_check = is_null($allowed_keys) ? $data_keys[$level] : $allowed_keys;

    if (!is_array($data)) {
        if ($level == 0) {
            exit;
        }

        return filter_value($data);
    }

    // loop through all array elements
    foreach ($data as $key => $value) {

        // filter keys
        if (!in_array($key, $keys_for_check) && !is_int($key)) {
            unset($data[$key]);
            continue;
        }

        // filter each value as data array
        $data[$key] = filter_table_data($value, $allowed_keys, $level + 1);
    }
    return $data;
}

/**
 * general validation
 *
 * @param array $data
 *
 * @return bool
 */
function validation($data)
{
    global $user_actions;

    // first level validation
    if ((empty($data['auth']) || empty($data['action'])) 
        || (empty($data['action']))) {
        return false;
    }

    // second level validation
    if(!in_array($data['action'], array_keys($user_actions[0])) && !in_array($data['action'], array_keys($user_actions[1]))) {
        return false;
    }
    if (empty($data['auth']['device']) && $data['action'] != 'registerdevice') {
        return false;
    }

    // third level validation
    if(!empty($data['auth']['email'])) {
        if (!filter_var($data['auth']['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
    }

    return true;
}

/**
 * construct connection string
 *
 * @param array $db
 *
 * @return string
 */
function conn_string($db)
{

    $connString = "";
    // construct your connection string here
    if ($db['type'] == 'pgsql') {
        $connString .= "host=" . $db['host'];
        $connString .= " port=" . $db['port'];
        $connString .= " dbname=" . $db['dbname'];
        $connString .= " user=" . $db['username'];
        $connString .= " password=" . $db['password'];
        $connString .= " options='--application_name=" . $db['appname'] . "'";
    }
    if ($db['type'] == 'mysql') {
        $connString .= "host=" . $db['host'];
        $connString .= " port=" . $db['port'];
        $connString .= " dbname=" . $db['dbname'];
        $connString .= " user=" . $db['username'];
        $connString .= " password=" . $db['password'];
    }
    return $connString;
}

function querySQL($db, $sql, $params = array(), $types = null)
{
    if($db['type'] == 'pgsql') {
        $conn_string = conn_string($db);
        $connection = pg_connect($conn_string);
        $ret = pg_query_params($connection, $sql, $params);
        if (!$ret) { // looking up for errors
            $response['status'] = 'error';
            $response['message'] = 'Failed to query database - querySQL';
            $response['data'] = pg_last_error();
            echo json_encode($response);
            exit;
        }
        $result = pg_fetch_all($ret);
        return $result;
    }
    if($db['type'] == 'mysql') {
        $connection = new mysqli($db['host'], $db['username'], $db['password'], $db['dbname'], $db['port']);
        // prepare and bind
        $stmt = $connection->prepare($sql);
        if (is_null($types)) {
            $types = str_repeat('s', count($params));
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $ret = $stmt->get_result();
        if (!$ret) { // looking up for errors
            $response['status'] = 'error';
            $response['message'] = 'Failed to query database - querySQL';
            $response['data'] = $connection->error;
            echo json_encode($response);
            exit;
        }
        $result = $ret->fetch_all(MYSQLI_ASSOC);
        return $result;
    }
}

// set database
function setDB($db) {
    if(!isset($db)) {
        $response['status'] = 'error';
        $response['message'] = 'Database not found';
        echo json_encode($response);
        exit;
    }
    $database = new Database($db['host'], $db['username'], $db['password'], $db['dbname'], $db['type']);
    foreach($db['tables'] as $table => $columns) {
        $database->addTable($table, $columns);
    }
    return $database;
}

<?php
/**
 * @param $date - will come as date('format')
 * @param $date2 - will come as date('format')
 * @return bool
 */
function validateTwoDates($date, $date2): bool{
    if ($date < $date2) {
        return true;
    }
    return false;
}

/**
 * Validate string
 * @param $string
 * @return bool
 */
function validateString($string) {
    $param_string = trim($string);

    if (preg_match('~[0-9]+~', $param_string)) {
        return false;
    }
    return true;
}

/**
 * @param $email
 * @return bool
 */
function validateEmail($email){
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return true;
}

/**
 * @param $value
 * @return bool
 */
function validateNumber($value)
{
    if (!is_numeric($value)) {
        return false;
    }
    return true;
}

/**
 * @param $phone
 * @return bool
 */
function validatePhone($phone){
    if (!preg_match('/^\+421[0-9]{9}/', $phone)) {
        return false;
    }
    return true;
}

/**
 * @param $value
 * @return bool
 */
function validateRequired($value){
    if (empty($value)) {
        return false;
    }
    return true;
}

/**
 * @param $value
 * @return bool
 */
function validateICO($value){
    if (!preg_match('/^([0-9]{6}|[0-9]{8})$/', $value)){
        return false;
    }
    return true;
}

/**
 * @param $value
 * @return bool
 */
function validateDIC($value){
    if (!preg_match('/^([0-9]{10})$/', $value)){
        return false;
    }
    return true;
}

/**
 * @param $value
 * @return bool
 */
function validateICDPH($value){
    if (!preg_match('/^SK([0-9]{10})$/', $value)){
        return false;
    }
    return true;

}
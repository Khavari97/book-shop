<?php
include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../database/bookShopMain/table/AccessTokenTable.php";
include_once "../../utils/const/const.php";
include_once "../../utils/validation/inputValidation.php";
include_once "../../utils/const/errorConst.php";
error_reporting(~E_NOTICE);

$ans = array();

if (isset($_REQUEST[$ACCESS_TOKEN_KEY_L_S])) $accessTokenInput = $_REQUEST[$ACCESS_TOKEN_KEY_L_S];
else {
    $ans[$CODE_KEY_L] = $CODE_107;
    $ans[$MESSAGE_KEY_L] = $CODE_107_MESSAGE;
    die(json_encode($ans));
}

if (!isAccessTokenValid($accessTokenInput)) {
    $ans[$CODE_KEY_L] = $CODE_207;
    $ans[$MESSAGE_KEY_L] = $CODE_207_MESSAGE;
    die(json_encode($ans));
}

require_once('../../utils/checkAccessToken.php');

$database = new BookShopMainDatabase();
$conn = $database->connection;

$current_date_time = date($DATE_TIME_FORMAT);

$email = $_REQUEST[$EMAIL_KEY_L];
$name = $_REQUEST[$NAME_KEY_L];
$password = $_REQUEST[$PASSWORD_KEY_L];
$password_confirmation = $_REQUEST[$PASSWORD_CONFIRMATION_KEY_L_S];
//$major = $_REQUEST[$MAJOR_KEY_L];
$payment = $_REQUEST[$PAYMENT_KEY_L];

if (!is_null($password)) {
    if (!isPasswordValid($password)) {
        $ans[$CODE_KEY_L] = $CODE_202;
        $ans[$MESSAGE_KEY_L] = $CODE_202_MESSAGE;
        die(json_encode($ans));
    }
    if (is_null($password_confirmation)) {
        $ans[$CODE_KEY_L] = $CODE_103;
        $ans[$MESSAGE_KEY_L] = $CODE_103_MESSAGE;
        die(json_encode($ans));
    }
    if (!isPasswordAndPasswordConfirmationMatch($password, $password_confirmation)) {
        $ans[$CODE_KEY_L] = $CODE_203;
        $ans[$MESSAGE_KEY_L] = $CODE_203_MESSAGE;
        die(json_encode($ans));
    }
}
if (!is_null($email)) {
    if (!isEmailValid($email)) {
        $ans[$CODE_KEY_L] = $CODE_201;
        $ans[$MESSAGE_KEY_L] = $CODE_201_MESSAGE;
        die(json_encode($ans));
    }
    if (email_exists($email)) {
        $ans[$CODE_KEY_L] = $CODE_302;
        $ans[$MESSAGE_KEY_L] = $CODE_302_MESSAGE;
        $conn->close();
        die(json_encode($ans));
    }
    if (!email_verified($email)) {
        $ans[$CODE_KEY_L] = $CODE_303;
        $ans[$MESSAGE_KEY_L] = $CODE_303_MESSAGE;
        $conn->close();
        die(json_encode($ans));
    }
}
/*if (!is_null($major)) {
    if (!isMajorIdValid($major)) {
        $ans[$CODE_KEY_L] = $CODE_208;
        $ans[$MESSAGE_KEY_L] = $CODE_208_MESSAGE;
        die(json_encode($ans));
    }
    if (!major_exists()) {
        $ans[$CODE_KEY_L] = $CODE_312;
        $ans[$MESSAGE_KEY_L] = $CODE_312_MESSAGE;
        $conn->close();
        die(json_encode($ans));
    }
}*/
if (!is_null($name)) {
    if (!isNameValid($name)) {
        $ans[$CODE_KEY_L] = $CODE_204;
        $ans[$MESSAGE_KEY_L] = $CODE_204_MESSAGE;
        die(json_encode($ans));
    }
}
if(!is_null($payment)) {
    if(!isPaymentValid($payment)) {
        $ans[$CODE_KEY_L] = $CODE_209;
        $ans[$MESSAGE_KEY_L] = $CODE_209_MESSAGE;
        die(json_encode($ans));
    }
}


if (!update_user($userId, $email, $name, $password, $payment)) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    $conn->close();
    die(json_encode($ans));
}

$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
$conn->close();
die(json_encode($ans));

//--------------------------------------------------------------------------------------------------

function email_exists($email)
{
    global $conn;
    global $EMAIL_KEY_L, $USER_KEY_L;
    $query = "SELECT $EMAIL_KEY_L FROM $USER_KEY_L WHERE LOWER($EMAIL_KEY_L) = LOWER('$email');";
    $count = $conn->query($query)->num_rows;
    return $count != 0;
}

function email_verified($email)
{
    global $conn;
    global $EMAIL_KEY_L, $EMAIL_VERIFIED_KEY_L_S, $EMAIL_VERIFICATION_KEY_L_S, $EXPIRE_AT_KEY_L_S;
    global $current_date_time;
    $query = "SELECT $EMAIL_VERIFIED_KEY_L_S FROM $EMAIL_VERIFICATION_KEY_L_S WHERE 
              LOWER($EMAIL_KEY_L) = LOWER('$email') AND '$current_date_time' < $EXPIRE_AT_KEY_L_S;";
    $email_relation = $conn->query($query);
    if ($email_relation->num_rows > 0) return $email_relation->fetch_assoc()[$EMAIL_VERIFIED_KEY_L_S] != 0;
    else return false;
}

/*function major_exists()
{
    global $conn;
    global $MAJOR_ID_KEY_L_S, $MAJOR_KEY_L;
    global $major;
    $query = "SELECT $MAJOR_ID_KEY_L_S FROM $MAJOR_KEY_L WHERE $MAJOR_ID_KEY_L_S = $major;";
    return ($conn->query($query)->num_rows != 0);
}*/

function update_user($userId, $email, $name, $password, $payment)
{
    global $conn;
    $query = build_query($userId, $email, $name, $password, $payment);
    $conn->query($query);
    return $conn->errno == 0;
}

function build_query($userId, $email, $name, $password, $payment)
{
    global $USER_KEY_L,
           $USER_ID_KEY_L_S, $EMAIL_KEY_L, $NAME_KEY_L, $PASSWORD_KEY_L,
           $PAYMENT_KEY_L, $UPDATED_AT_KEY_L_S;
    global $current_date_time;
    $query = "UPDATE $USER_KEY_L SET";
    $before = false;
    if (!is_null($email)) {
        $query .= " $EMAIL_KEY_L = '$email'";
        $before = true;
    }
    if (!is_null($name)) {
        if ($before) $query .= " ,";
        $query .= " $NAME_KEY_L = '$name'";
        $before = true;
    }
    if (!is_null($password)) {
        $encryptedPass = password_hash($password, PASSWORD_BCRYPT);
        if ($before) $query .= " ,";
        $query .= " $PASSWORD_KEY_L = '$encryptedPass'";
        $before = true;
    }
    if (!is_null($payment)) {
        if ($before) $query .= " ,";
        $query .= " $PAYMENT_KEY_L = '$payment'";
        $before = true;
    }
    if ($before) $query .= " ,";
    $query .= " $UPDATED_AT_KEY_L_S = '$current_date_time'";
    $query .= " WHERE $USER_ID_KEY_L_S = $userId;";
    return $query;
}

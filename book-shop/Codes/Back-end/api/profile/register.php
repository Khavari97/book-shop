<?php
include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../utils/const/const.php";
include_once "../../utils/validation/inputValidation.php";
include_once "../../utils/const/errorConst.php";
error_reporting(~E_NOTICE);

$ans = array();

$database = new BookShopMainDatabase();
$conn = $database->connection;

$current_date_time = date($DATE_TIME_FORMAT);

$email = $_REQUEST[$EMAIL_KEY_L];
if (is_null($email)) {
    $ans[$CODE_KEY_L] = $CODE_101;
    $ans[$MESSAGE_KEY_L] = $CODE_101_MESSAGE;
    die(json_encode($ans));
}

if (!isEmailValid($email)) {
    $ans[$CODE_KEY_L] = $CODE_201;
    $ans[$MESSAGE_KEY_L] = $CODE_201_MESSAGE;
    die(json_encode($ans));
}

$name = $_REQUEST[$NAME_KEY_L];
if (is_null($name)) {
    $ans[$CODE_KEY_L] = $CODE_104;
    $ans[$MESSAGE_KEY_L] = $CODE_104_MESSAGE;
    die(json_encode($ans));
}

if (!isNameValid($name)) {
    $ans[$CODE_KEY_L] = $CODE_204;
    $ans[$MESSAGE_KEY_L] = $CODE_204_MESSAGE;
    die(json_encode($ans));
}

$password = $_REQUEST[$PASSWORD_KEY_L];
if (is_null($password)) {
    $ans[$CODE_KEY_L] = $CODE_102;
    $ans[$MESSAGE_KEY_L] = $CODE_102_MESSAGE;
    die(json_encode($ans));
}

if (!isPasswordValid($password)) {
    $ans[$CODE_KEY_L] = $CODE_202;
    $ans[$MESSAGE_KEY_L] = $CODE_202_MESSAGE;
    die(json_encode($ans));
}

$password_confirmation = $_REQUEST[$PASSWORD_CONFIRMATION_KEY_L_S];
if (is_null($password_confirmation)) {
    $ans[$CODE_KEY_L] = $CODE_103;
    $ans[$MESSAGE_KEY_L] = $CODE_103_MESSAGE;
    die(json_encode($ans));
}

/*$major = $_REQUEST[$MAJOR_KEY_L];
if (is_null($major)) {
    $ans[$CODE_KEY_L] = $CODE_108;
    $ans[$MESSAGE_KEY_L] = $CODE_108_MESSAGE;
    die(json_encode($ans));
}*/

/*if (!isMajorIdValid($major)) {
    $ans[$CODE_KEY_L] = $CODE_208;
    $ans[$MESSAGE_KEY_L] = $CODE_208_MESSAGE;
    die(json_encode($ans));
}*/

if (!isPasswordAndPasswordConfirmationMatch($password,$password_confirmation)) {
    $ans[$CODE_KEY_L] = $CODE_203;
    $ans[$MESSAGE_KEY_L] = $CODE_203_MESSAGE;
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


/*if (!major_exists()) {
    $ans[$CODE_KEY_L] = $CODE_312;
    $ans[$MESSAGE_KEY_L] = $CODE_312_MESSAGE;
    $conn->close();
    die(json_encode($ans));
}*/

if (!register_user($email, $name, $password)) {
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

function register_user($email, $name, $password)
{
    global $USER_KEY_L, $EMAIL_KEY_L, $NAME_KEY_L, $PASSWORD_KEY_L,
           $MAJOR_KEY_L, $PURCHASES_KEY_L
           , $CREATED_AT_KEY_L_S, $UPDATED_AT_KEY_L_S;
    global $current_date_time;
    global $conn;
    $encryptedPass = password_hash($password,PASSWORD_BCRYPT);
    $query = "INSERT INTO $USER_KEY_L ($EMAIL_KEY_L, $NAME_KEY_L,
            $PASSWORD_KEY_L, $MAJOR_KEY_L, $PURCHASES_KEY_L,
            $CREATED_AT_KEY_L_S , $UPDATED_AT_KEY_L_S )
            VALUES ('$email','$name','$encryptedPass', 1, '[]','$current_date_time','$current_date_time');";
    $conn->query($query);
    return $conn->errno == 0;
}

<?php
include_once('../../utils/const/const.php');
include_once('../../database/bookShopMain/BookShopMainDatabase.php');
include_once('../../database/bookShopMain/table/AccessTokenTable.php');
include_once('../../utils/const/errorConst.php');
include_once('../../utils/validation/inputValidation.php');
error_reporting(E_ALL);
$ans = array();
if (isset($_POST[$ACCESS_TOKEN_KEY_L_S])) $accessTokenInput = $_POST[$ACCESS_TOKEN_KEY_L_S];
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

if (is_null($_FILES[$FILE_KEY_L])) {
    $ans[$CODE_KEY_L] = $CODE_125;
    $ans[$MESSAGE_KEY_L] = $CODE_125_MESSAGE;
    die(json_encode($ans));
}

$file_name = get_file_name();
$destination = $_SERVER['DOCUMENT_ROOT'] . "/book-shop/Codes/Back-end/files/" . $file_name;
if (copy($_FILES[$FILE_KEY_L]['tmp_name'], $destination)) {
    $file_size = $_FILES[$FILE_KEY_L]['size'] / 1000000;
    $currentDateTime = date($DATE_TIME_FORMAT);
    $relative_link =  $_SERVER['HTTP_HOST'] . "/book-shop/Codes/Back-end/files/" . $file_name;
    if (insert_file($file_size, $currentDateTime, $relative_link)) {
        $file_id = get_last_file_id();
        if ($file_id !== false) {
            $ans[$CODE_KEY_L] = $CODE_7898;
            $ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
            $ans[$FILE_URL_KEY_L_S] = $relative_link;
            $ans[$FILE_ID_KEY_L_S] = $file_id;
            $ans[$FILE_SIZE_KEY_L_S] = $file_size;
            die(json_encode($ans));
        }
    }
}


$ans[$CODE_KEY_L] = $CODE_405;
$ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
die(json_encode($ans));

function get_file_name()
{
    global $FILE_KEY_L;
    $exploded = explode(".", $_FILES[$FILE_KEY_L]['name']);
    $file_format = $exploded[count($exploded) - 1];
    return uniqid() . "." . $file_format;
}

function insert_file($size, $time, $url)
{
    global $FILE_KEY_L, $LINK_KEY_L, $CREATED_AT_KEY_L_S, $UPDATED_AT_KEY_L_S,$SIZE_KEY_L;
    global $database;
    $query = "INSERT INTO $FILE_KEY_L ($LINK_KEY_L,$SIZE_KEY_L, $CREATED_AT_KEY_L_S, $UPDATED_AT_KEY_L_S) 
                                         VALUES ('$url',$size, '$time', '$time');";
    $database->connection->query($query);
    return $database->connection->errno == 0;
}

function get_last_file_id()
{
    global $FILE_KEY_L;
    global $database;
    $query = "SELECT LAST_INSERT_ID() FROM $FILE_KEY_L;";
    $file_id_relation = $database->connection->query($query);
    if (!$file_id_relation || $database->connection->errno != 0) return false;
    return $file_id_relation->fetch_assoc()["LAST_INSERT_ID()"];
}

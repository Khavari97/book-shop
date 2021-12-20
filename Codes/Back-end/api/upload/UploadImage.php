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

if (is_null($_FILES[$IMAGE_KEY_L])) {
    $ans[$CODE_KEY_L] = $CODE_124;
    $ans[$MESSAGE_KEY_L] = $CODE_124_MESSAGE;
    die(json_encode($ans));
}

$image_name = get_image_name();
$destination = $_SERVER['DOCUMENT_ROOT'] . "/book-shop/Codes/Back-end/images/" . $image_name;

if (copy($_FILES[$IMAGE_KEY_L]['tmp_name'], $destination)) {
    $currentDateTime = date($DATE_TIME_FORMAT);
    $relative_link = $_SERVER['HTTP_HOST'] . "/book-shop/Codes/Back-end/images/" . $image_name;
    if (insert_image($currentDateTime,$relative_link)) {
        $image_id = get_last_image_id();
        if($image_id!==false) {
            $ans[$CODE_KEY_L] = $CODE_7898;
            $ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
            $ans[$IMAGE_URL_KEY_L_S] = $relative_link;
            $ans[$IMAGE_ID_KEY_L_S] = $image_id;
            die(json_encode($ans));
        }
    }
}


$ans[$CODE_KEY_L] = $CODE_405;
$ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
die(json_encode($ans));


function get_image_name()
{
    global $IMAGE_KEY_L;
    $exploded = explode(".", $_FILES[$IMAGE_KEY_L]['name']);
    $file_format = $exploded[count($exploded) - 1];
    return uniqid() . "." . $file_format;
}

function insert_image($time, $url)
{
    global $IMAGE_KEY_L, $LINK_KEY_L, $CREATED_AT_KEY_L_S, $UPDATED_AT_KEY_L_S;
    global $database;
    $query = "INSERT INTO $IMAGE_KEY_L ($LINK_KEY_L, $CREATED_AT_KEY_L_S, $UPDATED_AT_KEY_L_S) 
                                         VALUES ('$url', '$time', '$time');";
    $database->connection->query($query);
    return $database->connection->errno == 0;
}
function get_last_image_id() {
    global $IMAGE_KEY_L;
    global $database;
    $query = "SELECT LAST_INSERT_ID() FROM $IMAGE_KEY_L;";
    $image_id_relation = $database->connection->query($query);
    if (!$image_id_relation || $database->connection->errno != 0) return false;
    return  $image_id_relation->fetch_assoc()["LAST_INSERT_ID()"];
}

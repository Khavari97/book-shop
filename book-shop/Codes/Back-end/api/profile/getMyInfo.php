<?php
include_once('../../database/bookShopMain/BookShopMainDatabase.php');
include_once('../../database/bookShopMain/table/AccessTokenTable.php');
include_once('../../utils/const/const.php');
include_once('../../utils/const/errorConst.php');
$ans = array();

if (isset($_REQUEST[$ACCESS_TOKEN_KEY_L_S])) $accessTokenInput = $_REQUEST[$ACCESS_TOKEN_KEY_L_S];
else {
    $ans[$CODE_KEY_L] = $CODE_107;
    $ans[$MESSAGE_KEY_L] = $CODE_107_MESSAGE;
    die(json_encode($ans));
}

require_once('../../utils/checkAccessToken.php');

$database = new BookShopMainDatabase();
$query1 = "SELECT $USER_ID_KEY_L_S, $EMAIL_KEY_L, $NAME_KEY_L, $MAJOR_KEY_L, $PAYMENT_KEY_L
          ,$PURCHASES_KEY_L, $CREATED_AT_KEY_L_S, $UPDATED_AT_KEY_L_S 
          FROM $USER_KEY_L WHERE $USER_ID_KEY_L_S = $userId";
$user = $database->connection->query($query1)->fetch_assoc();
$user[$PURCHASES_KEY_L] = json_decode($user[$PURCHASES_KEY_L]);

$query2 = "SELECT $TITLE_KEY_L FROM $MAJOR_KEY_L WHERE $MAJOR_ID_KEY_L_S = $user[$MAJOR_KEY_L]";
$user[$MAJOR_KEY_L] = $database->connection->query($query2)->fetch_assoc()[$TITLE_KEY_L];

$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
$ans[$USER_KEY_L] = $user;
die(json_encode($ans));



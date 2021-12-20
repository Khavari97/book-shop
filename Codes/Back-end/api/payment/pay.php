<?php

//Include Part
include_once "../../utils/const/const.php";
include_once "../../utils/const/errorConst.php";
include_once "../../utils/validation/inputValidation.php";
include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../database/bookShopMain/table/AccessTokenTable.php";
include_once "../../database/bookShopMain/table/UserTable.php";
include_once "../../database/bookShopMain/table/NoteTable.php";
include_once "../../database/bookShopMain/table/PaymentTable.php";

$ans = array();

//Phase 1: Get Inputs
if ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $POST_KEY_U) {
    if (isset($_POST[$ACCESS_TOKEN_KEY_L_S])) $accessTokenInput = $_POST[$ACCESS_TOKEN_KEY_L_S]; else $accessTokenInput = NULL;
    if (isset($_POST[$NOTE_ID_KEY_L_S])) $noteIdInput = $_POST[$NOTE_ID_KEY_L_S]; else $noteIdInput = NULL;
} elseif ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $GET_KEY_U) {
    if (isset($_GET[$ACCESS_TOKEN_KEY_L_S])) $accessTokenInput = $_GET[$ACCESS_TOKEN_KEY_L_S]; else $accessTokenInput = NULL;
    if (isset($_GET[$NOTE_ID_KEY_L_S])) $noteIdInput = $_GET[$NOTE_ID_KEY_L_S]; else $noteIdInput = NULL;
} else {
    $ans[$CODE_KEY_L] = $CODE_404;
    $ans[$MESSAGE_KEY_L] = $CODE_404_MESSAGE;
    die(json_encode($ans));
}

//Phase 2: Check Existence
if ($accessTokenInput == NULL) {
    $ans[$CODE_KEY_L] = $CODE_107;
    $ans[$MESSAGE_KEY_L] = $CODE_107_MESSAGE;
    die(json_encode($ans));
}
if ($noteIdInput == NULL) {
    $ans[$CODE_KEY_L] = $CODE_123;
    $ans[$MESSAGE_KEY_L] = $CODE_123_MESSAGE;
    die(json_encode($ans));
}

//Phase 3: Check Validation
if (!isAccessTokenValid($accessTokenInput)) {
    $ans[$CODE_KEY_L] = $CODE_207;
    $ans[$MESSAGE_KEY_L] = $CODE_207_MESSAGE;
    die(json_encode($ans));
}
if (!isNoteIdValid($noteIdInput)) {
    $ans[$CODE_KEY_L] = $CODE_223;
    $ans[$MESSAGE_KEY_L] = $CODE_223_MESSAGE;
    die(json_encode($ans));
}

//Phase 4: Check Access Token
require_once "../../utils/checkAccessToken.php";

//Phase 5: Check Note Existence
$mainDatabase = new BookShopMainDatabase();
$noteTable = new NoteTable($mainDatabase);
$selectNoteResult = $noteTable->selectById(NoteTable::$PRICE_COLUMN, $noteIdInput);
if ($selectNoteResult->num_rows !== 1) {
    $ans[$CODE_KEY_L] = $CODE_325;
    $ans[$MESSAGE_KEY_L] = $CODE_325_MESSAGE;
    die(json_encode($ans));
}

//Phase 6: Insert Payment
$paymentTable = new PaymentTable($mainDatabase);
$insert = $paymentTable->insert($noteIdInput, $userId);
$selectNoteResultRow = $selectNoteResult->fetch_assoc();
$params = array(
    'order_id' => $mainDatabase->connection->insert_id,
    'amount' => $selectNoteResultRow[NoteTable::$PRICE_COLUMN],
    'callback' => "http://bookshopkhu.sion-project.ir/Back-end/api/payment/finishPayment.php?userId=$userId&note=$noteIdInput",
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.idpay.ir/v1.1/payment');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'X-API-KEY: 6eaa0648-600a-41e4-a9e3-8d3485cda5e9',
    'X-SANDBOX: 1'
));
$result = curl_exec($ch);
curl_close($ch);
$ans[$LINK_KEY_L] = json_decode($result)->link;

//Phase 7: Add to Purchase
$userTable = new UserTable($mainDatabase);

//Finishing
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
die(json_encode($ans));
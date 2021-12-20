<?php

//Include Part
include_once "../../utils/const/const.php";
include_once "../../utils/const/errorConst.php";
include_once "../../utils/validation/inputValidation.php";
include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../database/bookShopMain/table/UserTable.php";
include_once "../../database/bookShopMain/table/AccessTokenTable.php";

$ans = array();

//Phase 1: Get Inputs
if ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $POST_KEY_U) {
    if (isset($_POST[$EMAIL_KEY_L])) $emailInput = $_POST[$EMAIL_KEY_L]; else $emailInput = NULL;
    if (isset($_POST[$PASSWORD_KEY_L])) $passwordInput = $_POST[$PASSWORD_KEY_L]; else $passwordInput = NULL;
} elseif ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $GET_KEY_U) {
    if (isset($_GET[$EMAIL_KEY_L])) $emailInput = $_GET[$EMAIL_KEY_L]; else $emailInput = NULL;
    if (isset($_GET[$PASSWORD_KEY_L])) $passwordInput = $_GET[$PASSWORD_KEY_L]; else $passwordInput = NULL;
} else {
    $ans[$CODE_KEY_L] = $CODE_404;
    $ans[$MESSAGE_KEY_L] = $CODE_404_MESSAGE;
    die(json_encode($ans));
}

//Phase 2: Check Existence
if ($emailInput == NULL) {
    $ans[$CODE_KEY_L] = $CODE_101;
    $ans[$MESSAGE_KEY_L] = $CODE_101_MESSAGE;
    die(json_encode($ans));
}
if ($passwordInput == NULL) {
    $ans[$CODE_KEY_L] = $CODE_102;
    $ans[$MESSAGE_KEY_L] = $CODE_102_MESSAGE;
    die(json_encode($ans));
}

//Phase 3: Check Validation
if (!isEmailValid($emailInput)) {
    $ans[$CODE_KEY_L] = $CODE_201;
    $ans[$MESSAGE_KEY_L] = $CODE_201_MESSAGE;
    die(json_encode($ans));
}
if (!isPasswordValid($passwordInput)) {
    $ans[$CODE_KEY_L] = $CODE_202;
    $ans[$MESSAGE_KEY_L] = $CODE_202_MESSAGE;
    die(json_encode($ans));
}

//Phase 4: Check Email existence in database
$bookShopMainDatabase = new BookShopMainDatabase();
$userTable = new UserTable($bookShopMainDatabase);
$userSelectResult = $userTable->selectByEmail("`" . UserTable::$USER_ID_COLUMN . "`,`"
    . UserTable::$EMAIL_COLUMN . "`,`"
    . UserTable::$NAME_COLUMN . "`,`" . UserTable::$MAJOR_COLUMN . "`,`"
    . UserTable::$PASSWORD_COLUMN . "`,`" . UserTable::$PURCHASES_COLUMN . "`", $emailInput);
if ($userSelectResult->num_rows == 1) {
    $userSelectResultRow = $userSelectResult->fetch_assoc();
    $enc_pass = $userSelectResultRow[$PASSWORD_KEY_L];
    if (!password_verify($passwordInput, $enc_pass)) {
        $ans[$CODE_KEY_L] = $CODE_301;
        $ans[$MESSAGE_KEY_L] = $CODE_301_MESSAGE;
        die(json_encode($ans));
    }
    unset($userSelectResultRow[$PASSWORD_KEY_L]);
} else if ($userSelectResult->num_rows > 1) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    die(json_encode($ans));
} else { /*($userSelectResult->num_rows < 1)*/
    $ans[$CODE_KEY_L] = $CODE_301;
    $ans[$MESSAGE_KEY_L] = $CODE_301_MESSAGE;
    die(json_encode($ans));
}

//Phase 5: Check Session Number
$accessTokenTable = new AccessTokenTable($bookShopMainDatabase);
$accessTokenSelectResult = $accessTokenTable->selectByTarget("`" . AccessTokenTable::$ID_COLUMN . "`,`" . AccessTokenTable::$EXPIRE_AT_COLUMN . "`", $userSelectResultRow[UserTable::$USER_ID_COLUMN]);
$accessTokenSelectResultSize = $accessTokenSelectResult->num_rows;
if ($accessTokenSelectResultSize > 0) {
    while ($accessTokenRow = $accessTokenSelectResult->fetch_assoc()) {
        if ($accessTokenRow[AccessTokenTable::$EXPIRE_AT_COLUMN] < date($DATE_TIME_FORMAT)) {
            $accessTokenTable->deleteById($accessTokenRow[AccessTokenTable::$ID_COLUMN]);
            $accessTokenSelectResultSize--;
        }
    }
    if ($accessTokenSelectResultSize >= $MAX_SESSION_NUMBER) {
        $ans[$CODE_KEY_L] = $CODE_401;
        $ans[$MESSAGE_KEY_L] = $CODE_401_MESSAGE;
        die(json_encode($ans));
    }
}

//Phase 6: Create New Access Token
$token = generateUniqueAccessToken();
$accessTokenTable->insert($token, $userSelectResultRow[UserTable::$USER_ID_COLUMN], date($DATE_TIME_FORMAT, time() + $ACCESS_TOKEN_LIFE_TIME));
$ans[$ACCESS_TOKEN_KEY_L_S] = $token;
$ans[$USER_KEY_L] = $userSelectResultRow;
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
die(json_encode($ans));

//Functions
function generateAccessToken()
{
    return bin2hex(random_bytes(64));
}

function generateUniqueAccessToken()
{
    global $accessTokenTable;
    $token = generateAccessToken();
    $accessTokenSelectResult = $accessTokenTable->selectByAccessToken("`" . AccessTokenTable::$ID_COLUMN . "`", $token);
    if ($accessTokenSelectResult->num_rows != 0) generateUniqueAccessToken();
    return $token;
}
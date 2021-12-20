<?php

//Include Part
include_once "../../utils/const/const.php";
include_once "../../utils/const/errorConst.php";
include_once "../../utils/validation/inputValidation.php";
include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../database/bookShopMain/table/UserTAble.php";
include_once "../../database/bookShopMain/table/RestorePasswordTable.php";

$ans = array();

//Phase 1: Get Inputs
if ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $POST_KEY_U) {
    if (isset($_POST[$EMAIL_KEY_L])) $emailInput = $_POST[$EMAIL_KEY_L]; else $emailInput = NULL;
} elseif ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $GET_KEY_U) {
    if (isset($_GET[$EMAIL_KEY_L])) $emailInput = $_GET[$EMAIL_KEY_L]; else $emailInput = NULL;
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

//Phase 3: Check Validation
if (!isEmailValid($emailInput)) {
    $ans[$CODE_KEY_L] = $CODE_201;
    $ans[$MESSAGE_KEY_L] = $CODE_201_MESSAGE;
    die(json_encode($ans));
}

//Phase 4: Check Email existence in database
$bookShopMainDatabase = new BookShopMainDatabase();
$userTable = new  UserTable($bookShopMainDatabase);
$userSelectResult = $userTable->selectByEmail("`" . UserTable::$USER_ID_COLUMN . "`", $emailInput);
if ($userSelectResult->num_rows > 1) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    die(json_encode($ans));
}
if ($userSelectResult->num_rows == 0) {
    $ans[$CODE_KEY_L] = $CODE_304;
    $ans[$MESSAGE_KEY_L] = $CODE_304_MESSAGE;
    die(json_encode($ans));
}

//Phase 5: Check Previous Password Restore Code
$restorePasswordTable = new RestorePasswordTable($bookShopMainDatabase);
$userSelectResultRow = $userSelectResult->fetch_assoc();
$selectRestorePasswordResult = $restorePasswordTable->selectByTarget("`" . RestorePasswordTable::$EXPIRE_AT_COLUMN . "`,`" . RestorePasswordTable::$ATTEMPTS_NUMBER_COLUMN . "`", $userSelectResultRow[UserTable::$USER_ID_COLUMN]);
if ($selectRestorePasswordResult->num_rows == 1) {
    $selectRestorePasswordResultRow = $selectRestorePasswordResult->fetch_assoc();
    if ($selectRestorePasswordResultRow[RestorePasswordTable::$EXPIRE_AT_COLUMN] < date($DATE_TIME_FORMAT)) {
        $code = generateRestoreCode();
        $restorePasswordTable->updateByTarget($userSelectResultRow[UserTable::$USER_ID_COLUMN], $code, date($DATE_TIME_FORMAT, time() + $RESTORE_PASSWORD_CODE_LIFE_TIME), 0);
        $ans[$CODE_KEY_L] = $CODE_7898;
        $ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
        die(json_encode($ans));
    }
    if ($selectRestorePasswordResultRow[RestorePasswordTable::$ATTEMPTS_NUMBER_COLUMN] >= $MAX_RESTORE_PASSWORD_REQUEST_ATTEMPTS_NUMBER) {
        $ans[$CODE_KEY_L] = $CODE_402;
        $ans[$MESSAGE_KEY_L] = $CODE_402_MESSAGE;
        die(json_encode($ans));
    }
    $code = generateRestoreCode();
    $restorePasswordTable->updateRestoreCodeAndAttemptsNumberByTarget($userSelectResultRow[UserTable::$USER_ID_COLUMN], $code, $selectRestorePasswordResultRow[RestorePasswordTable::$ATTEMPTS_NUMBER_COLUMN] + 1);
    $ans[$CODE_KEY_L] = $CODE_7898;
    $ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
    die(json_encode($ans));
}

//Phase 6: First Restore Password Code
$code = generateRestoreCode();
$restorePasswordTable->insert($userSelectResultRow[UserTable::$USER_ID_COLUMN], $code, date($DATE_TIME_FORMAT, time() + $RESTORE_PASSWORD_CODE_LIFE_TIME), 0);
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
die(json_encode($ans));

//Functions
function generateRestoreCode()
{
    return bin2hex(random_bytes(4));
}
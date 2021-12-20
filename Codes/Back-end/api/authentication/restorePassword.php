<?php

//Include Part
include_once "../../utils/const/const.php";
include_once "../../utils/const/errorConst.php";
include_once "../../utils/validation/inputValidation.php";
include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../database/bookShopMain/table/UserTable.php";
include_once "../../database/bookShopMain/table/RestorePasswordTable.php";

$ans = array();

//Phase 1: Get Inputs
if ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $POST_KEY_U) {
    if (isset($_POST[$EMAIL_KEY_L])) $emailInput = $_POST[$EMAIL_KEY_L]; else $emailInput = NULL;
    if (isset($_POST[$CODE_KEY_L])) $codeInput = $_POST[$CODE_KEY_L]; else $codeInput = NULL;
    if (isset($_POST[$PASSWORD_KEY_L])) $passwordInput = $_POST[$PASSWORD_KEY_L]; else $passwordInput = NULL;
    if (isset($_POST[$PASSWORD_CONFIRMATION_KEY_L_S])) $passwordConfirmationInput = $_POST[$PASSWORD_CONFIRMATION_KEY_L_S]; else $passwordConfirmationInput = NULL;
} elseif ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $GET_KEY_U) {
    if (isset($_GET[$EMAIL_KEY_L])) $emailInput = $_GET[$EMAIL_KEY_L]; else $emailInput = NULL;
    if (isset($_GET[$CODE_KEY_L])) $codeInput = $_GET[$CODE_KEY_L]; else $codeInput = NULL;
    if (isset($_GET[$PASSWORD_KEY_L])) $passwordInput = $_GET[$PASSWORD_KEY_L]; else $passwordInput = NULL;
    if (isset($_GET[$PASSWORD_CONFIRMATION_KEY_L_S])) $passwordConfirmationInput = $_GET[$PASSWORD_CONFIRMATION_KEY_L_S]; else $passwordConfirmationInput = NULL;
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
if ($codeInput == NULL) {
    $ans[$CODE_KEY_L] = $CODE_105;
    $ans[$MESSAGE_KEY_L] = $CODE_105_MESSAGE;
    die(json_encode($ans));
}
if ($passwordInput == NULL) {
    $ans[$CODE_KEY_L] = $CODE_102;
    $ans[$MESSAGE_KEY_L] = $CODE_102_MESSAGE;
    die(json_encode($ans));
}
if ($passwordConfirmationInput == NULL) {
    $ans[$CODE_KEY_L] = $CODE_103;
    $ans[$MESSAGE_KEY_L] = $CODE_103_MESSAGE;
    die(json_encode($ans));
}

//Phase 3: Check Validation
if (!isEmailValid($emailInput)) {
    $ans[$CODE_KEY_L] = $CODE_201;
    $ans[$MESSAGE_KEY_L] = $CODE_201_MESSAGE;
    die(json_encode($ans));
}
if (!isRestorePasswordCodeValid($codeInput)) {
    $ans[$CODE_KEY_L] = $CODE_205;
    $ans[$MESSAGE_KEY_L] = $CODE_205_MESSAGE;
    die(json_encode($ans));
}
if (!isPasswordValid($passwordInput)) {
    $ans[$CODE_KEY_L] = $CODE_202;
    $ans[$MESSAGE_KEY_L] = $CODE_202_MESSAGE;
    die(json_encode($ans));
}
if (!isPasswordAndPasswordConfirmationMatch($passwordInput, $passwordConfirmationInput)) {
    $ans[$CODE_KEY_L] = $CODE_203;
    $ans[$MESSAGE_KEY_L] = $CODE_203_MESSAGE;
    die(json_encode($ans));
}

//Phase 4: Check Email Existence in database
$bookShopMainDatabase = new BookShopMainDatabase();
$userTable = new UserTable($bookShopMainDatabase);
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

//Phase 5: Check Previous Restore Code Status
$restorePasswordTable = new RestorePasswordTable($bookShopMainDatabase);
$userSelectResultRow = $userSelectResult->fetch_assoc();
$restorePasswordSelectResult = $restorePasswordTable->selectByTarget("`" . RestorePasswordTable::$RESTORE_CODE_COLUMN . "`,`" . RestorePasswordTable::$EXPIRE_AT_COLUMN . "`,`" . RestorePasswordTable::$ATTEMPTS_NUMBER_COLUMN . "`", $userSelectResultRow[UserTable::$USER_ID_COLUMN]);
if ($restorePasswordSelectResult->num_rows == 0) {
    $ans[$CODE_KEY_L] = $CODE_304;
    $ans[$MESSAGE_KEY_L] = $CODE_304_MESSAGE;
    die(json_encode($ans));
}
$restorePasswordSelectResultRow = $restorePasswordSelectResult->fetch_assoc();
if ($restorePasswordSelectResultRow[RestorePasswordTable::$EXPIRE_AT_COLUMN] < date($DATE_TIME_FORMAT)) {
    $ans[$CODE_KEY_L] = $CODE_307;
    $ans[$MESSAGE_KEY_L] = $CODE_307_MESSAGE;
    die(json_encode($ans));
}
if ($restorePasswordSelectResultRow[RestorePasswordTable::$ATTEMPTS_NUMBER_COLUMN] >= $MAX_RESTORE_PASSWORD_REQUEST_ATTEMPTS_NUMBER) {
    $ans[$CODE_KEY_L] = $CODE_402;
    $ans[$MESSAGE_KEY_L] = $CODE_402_MESSAGE;
    die(json_encode($ans));
}
if ($restorePasswordSelectResultRow[RestorePasswordTable::$RESTORE_CODE_COLUMN] != $codeInput) {
    $restorePasswordTable->updateAttemptsNumberByTarget($userSelectResultRow[UserTable::$USER_ID_COLUMN], $restorePasswordSelectResultRow[RestorePasswordTable::$ATTEMPTS_NUMBER_COLUMN] + 1);
    $ans[$CODE_KEY_L] = $CODE_306;
    $ans[$MESSAGE_KEY_L] = $CODE_306_MESSAGE;
    die(json_encode($ans));
}

//Phase 6: Update Password
$restorePasswordTable->deleteByTarget($userSelectResultRow[UserTable::$USER_ID_COLUMN]);
$userTable->updatePasswordByUserId($userSelectResultRow[UserTable::$USER_ID_COLUMN], $passwordInput);
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
die(json_encode($ans));
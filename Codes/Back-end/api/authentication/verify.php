<?php

//Include
include_once "../../utils/const/const.php";
include_once "../../utils/const/errorConst.php";
include_once "../../utils/validation/inputValidation.php";
include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../database/bookShopMain/table/EmailVerificationTable.php";

$ans = array();

//Phase 1: Get Inputs
if ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $POST_KEY_U) {
    if (isset($_POST[$EMAIL_KEY_L])) $emailInput = $_POST[$EMAIL_KEY_L]; else $emailInput = NULL;
    if (isset($_POST[$CODE_KEY_L])) $codeInput = $_POST[$CODE_KEY_L]; else $codeInput = NULL;
} elseif ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $GET_KEY_U) {
    if (isset($_GET[$EMAIL_KEY_L])) $emailInput = $_GET[$EMAIL_KEY_L]; else $emailInput = NULL;
    if (isset($_GET[$CODE_KEY_L])) $codeInput = $_GET[$CODE_KEY_L]; else $codeInput = NULL;
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
    $ans[$CODE_KEY_L] = $CODE_106;
    $ans[$MESSAGE_KEY_L] = $CODE_106_MESSAGE;
    die(json_encode($ans));
}

//Phase 3: Check Validation
if (!isEmailValid($emailInput)) {
    $ans[$CODE_KEY_L] = $CODE_201;
    $ans[$MESSAGE_KEY_L] = $CODE_201_MESSAGE;
    die(json_encode($ans));
}
if (!isVerificationCodeValid($codeInput)) {
    $ans[$CODE_KEY_L] = $CODE_206;
    $ans[$MESSAGE_KEY_L] = $CODE_206_MESSAGE;
    die(json_encode($ans));
}

//Phase 4: Check Email existence in database
$bookShopMainDatabase = new BookShopMainDatabase();
$emailVerificationTable = new EmailVerificationTable($bookShopMainDatabase);
$emailVerificationSelectResult = $emailVerificationTable->selectByEmail("`" . EmailVerificationTable::$VERIFICATION_CODE_COLUMN . "`,`" . EmailVerificationTable::$EMAIL_VERIFIED_COLUMN . "`,`" . EmailVerificationTable::$EXPIRE_AT_COLUMN . "`,`" . EmailVerificationTable::$ATTEMPTS_NUMBER_COLUMN . "`", $emailInput);
if ($emailVerificationSelectResult->num_rows < 1) {
    $ans[$CODE_KEY_L] = $CODE_304;
    $ans[$MESSAGE_KEY_L] = $CODE_304_MESSAGE;
    die(json_encode($ans));
}

//Phase 5: Check Previous Verification Status
$emailVerificationSelectResultRow = $emailVerificationSelectResult->fetch_assoc();
if ($emailVerificationSelectResultRow[EmailVerificationTable::$EMAIL_VERIFIED_COLUMN] == 1) {
    $ans[$CODE_KEY_L] = $CODE_305;
    $ans[$MESSAGE_KEY_L] = $CODE_305_MESSAGE;
    die(json_encode($ans));
}

//Phase 6: Expiration Date
if ($emailVerificationSelectResultRow[EmailVerificationTable::$EXPIRE_AT_COLUMN] < date($DATE_TIME_FORMAT)) {
    $ans[$CODE_KEY_L] = $CODE_309;
    $ans[$MESSAGE_KEY_L] = $CODE_309_MESSAGE;
    die(json_encode($ans));
}

//Phase 7: Check Attempts Number and Verification Code
if ($emailVerificationSelectResultRow[EmailVerificationTable::$ATTEMPTS_NUMBER_COLUMN] >= $MAX_VERIFICATION_REQUEST_ATTEMPTS_NUMBER) {
    $ans[$CODE_KEY_L] = $CODE_403;
    $ans[$MESSAGE_KEY_L] = $CODE_403_MESSAGE;
    die(json_encode($ans));
}
if ($codeInput !== $emailVerificationSelectResultRow[EmailVerificationTable::$VERIFICATION_CODE_COLUMN]) {
    $emailVerificationTable->updateAttemptsNumberByEmail($emailInput, $emailVerificationSelectResultRow[EmailVerificationTable::$ATTEMPTS_NUMBER_COLUMN] + 1);
    $ans[$CODE_KEY_L] = $CODE_308;
    $ans[$MESSAGE_KEY_L] = $CODE_308_MESSAGE;
    die(json_encode($ans));
}

//Phase 8: Change Verification Status
$emailVerificationTable->updateEmailVerifiedByEmail($emailInput, 1);
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
die(json_encode($ans));
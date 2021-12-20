<?php

//Include Part
include_once "../../utils/const/const.php";
include_once "../../utils/const/errorConst.php";
include_once "../../utils/validation/inputValidation.php";
include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../database/bookShopMain/table/UserTable.php";
include_once "../../database/bookShopMain/table/EmailVerificationTable.php";

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
$userTable = new UserTable($bookShopMainDatabase);
$emailVerificationTable = new EmailVerificationTable($bookShopMainDatabase);
$userSelectResult = $userTable->selectByEmail("`" . UserTable::$USER_ID_COLUMN . "`", $emailInput);
if ($userSelectResult->num_rows == 1) {
    $ans[$CODE_KEY_L] = $CODE_302;
    $ans[$MESSAGE_KEY_L] = $CODE_302_MESSAGE;
    die(json_encode($ans));
}
if ($userSelectResult->num_rows > 1) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    die(json_encode($ans));
}

//Phase 5: Check Verification Status and Check Older Verification Code
$emailVerificationSelectResult = $emailVerificationTable->selectByEmail("`" . EmailVerificationTable::$EMAIL_VERIFIED_COLUMN . "`,`" . EmailVerificationTable::$EXPIRE_AT_COLUMN . "`,`" . EmailVerificationTable::$ATTEMPTS_NUMBER_COLUMN . "`", $emailInput);
if ($emailVerificationSelectResult->num_rows == 1) {
    $emailVerificationSelectResultRow = $emailVerificationSelectResult->fetch_assoc();
    if ($emailVerificationSelectResultRow[EmailVerificationTable::$EMAIL_VERIFIED_COLUMN] == 1) {
        $ans[$CODE_KEY_L] = $CODE_305;
        $ans[$MESSAGE_KEY_L] = $CODE_305_MESSAGE;
        die(json_encode($ans));
    }
    if ($emailVerificationSelectResultRow[EmailVerificationTable::$EXPIRE_AT_COLUMN] < date($DATE_TIME_FORMAT)) {
        $verificationCode = generateVerificationCode();
        $emailVerificationTable->updateByEmail($emailInput, $verificationCode, 0, date(($DATE_TIME_FORMAT), time() + $VERIFICATION_CODE_LIFE_TIME), 0);
        mail($emailInput, "Email Verification Code From Book Shop Khu", "Welcome!\nYour Verification Code is $verificationCode");
        $ans[$CODE_KEY_L] = $CODE_7898;
        $ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
        die(json_encode($ans));
    }
    if ($emailVerificationSelectResultRow[EmailVerificationTable::$ATTEMPTS_NUMBER_COLUMN] < $MAX_VERIFICATION_REQUEST_ATTEMPTS_NUMBER) {
        $verificationCode = generateVerificationCode();
        $emailVerificationTable->updateVerificationCodeAndAttemptsNumberByEmail($emailInput,
            $verificationCode, $emailVerificationSelectResultRow[EmailVerificationTable::$ATTEMPTS_NUMBER_COLUMN] + 1);
        mail($emailInput, "Email Verification Code From Book Shop Khu", "Welcome!\nYour Verification Code is $verificationCode");
        $ans[$CODE_KEY_L] = $CODE_7898;
        $ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
        die(json_encode($ans));
    } else {
        $ans[$CODE_KEY_L] = $CODE_403;
        $ans[$MESSAGE_KEY_L] = $CODE_403_MESSAGE;
        die(json_encode($ans));
    }
}

//Phase 6: First Verification Code
$verificationCode = generateVerificationCode();
$emailVerificationTable->insert($emailInput, $verificationCode, 0, date(($DATE_TIME_FORMAT), time() + $VERIFICATION_CODE_LIFE_TIME), 0);
mail($emailInput, "Email Verification Code From Book Shop Khu", "Welcome!\nYour Verification Code is $verificationCode");
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
die(json_encode($ans));

//Functions
function generateVerificationCode()
{
    return bin2hex(random_bytes(4));
}

<?php

//Include Part
require_once "../../utils/const/const.php";
require_once "../../utils/const/errorConst.php";
require_once "../../utils/validation/inputValidation.php";
require_once "../../database/bookShopMain/BookShopMainDatabase.php";
require_once "../../database/bookShopMain/table/AccessTokenTable.php";
require_once "../../database/bookShopMain/table/FileTable.php";
require_once "../../database/bookShopMain/table/InstructorTable.php";
require_once "../../database/bookShopMain/table/MajorTable.php";
require_once "../../database/bookShopMain/table/CourseTable.php";
require_once "../../database/bookShopMain/table/ImageTable.php";
require_once "../../database/bookShopMain/table/NoteTable.php";

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
if ($accessTokenInput === NULL) {
    $ans[$CODE_KEY_L] = $CODE_107;
    $ans[$MESSAGE_KEY_L] = $CODE_107_MESSAGE;
    die(json_encode($ans));
}
if ($noteIdInput === NULL) {
    $ans[$CODE_KEY_L] = $CODE_122;
    $ans[$MESSAGE_KEY_L] = $CODE_122_MESSAGE;
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
$selectNoteResult = $noteTable->selectByIdAndOwner(NoteTable::$NOTE_ID_COLUMN, $noteIdInput, $userId);
if ($selectNoteResult->num_rows !== 1) {
    $ans[$CODE_KEY_L] = $CODE_325;
    $ans[$MESSAGE_KEY_L] = $CODE_325_MESSAGE;
    die(json_encode($ans));
}

//Phase 11: Delete Note
$deleteNoteResult = $noteTable->deleteById($noteIdInput);
if (!$deleteNoteResult) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    die(json_encode($ans));
}

//Finishing
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
die(json_encode($ans));
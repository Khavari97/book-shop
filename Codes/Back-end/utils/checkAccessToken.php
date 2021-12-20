<?php

/*
 * Require Variables: $accessTokenInput
 * Output Variables: $userId
 * Include: BookShopMainDatabase.php , AccessTokenTable.php , const.php , errorConst.php
 */

if (!isset($mainDatabase)) $mainDatabase = new BookShopMainDatabase();
if (!isset($accessTokenTable)) $accessTokenTable = new AccessTokenTable($mainDatabase);

$accessTokenSelectResult = $accessTokenTable->selectByAccessToken("`" . AccessTokenTable::$ID_COLUMN . "`, `" . AccessTokenTable::$TARGET_COLUMN . "`, `" . AccessTokenTable::$EXPIRE_AT_COLUMN . "`", $accessTokenInput);
if ($accessTokenSelectResult->num_rows > 1) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    die(json_encode($ans));
} else if ($accessTokenSelectResult->num_rows < 1) {
    $ans[$CODE_KEY_L] = $CODE_311;
    $ans[$MESSAGE_KEY_L] = $CODE_311_MESSAGE;
    die(json_encode($ans));
}

$accessTokenSelectResultRow = $accessTokenSelectResult->fetch_assoc();
if ($accessTokenSelectResultRow[AccessTokenTable::$EXPIRE_AT_COLUMN] < date($DATE_TIME_FORMAT)) {
    $accessTokenSearchResult = $accessTokenTable->deleteById($accessTokenSelectResultRow[AccessTokenTable::$ID_COLUMN]);
    $ans[$CODE_KEY_L] = $CODE_310;
    $ans[$MESSAGE_KEY_L] = $CODE_310_MESSAGE;
    die(json_encode($ans));
}
$userId = $accessTokenSelectResultRow[AccessTokenTable::$TARGET_COLUMN];

unset($accessTokenSelectResult);
unset($accessTokenSelectResultRow);
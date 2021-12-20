<?php
include_once('../../database/bookShopMain/BookShopMainDatabase.php');
include_once('../../database/bookShopMain/table/AccessTokenTable.php');
include_once ('../../utils/validation/inputValidation.php');
include_once('../../utils/const/const.php');
include_once('../../utils/const/errorConst.php');
error_reporting(E_ALL);

$ans = array();

if (isset($_REQUEST[$ACCESS_TOKEN_KEY_L_S])) $accessTokenInput = $_REQUEST[$ACCESS_TOKEN_KEY_L_S];
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

if (isset($_REQUEST[$CONVERSATION_ID_KEY_L_S])) $conversationID = $_REQUEST[$CONVERSATION_ID_KEY_L_S];
else {
    $ans[$CODE_KEY_L] = $CODE_110;
    $ans[$MESSAGE_KEY_L] = $CODE_110_MESSAGE;
    die(json_encode($ans));
}

if (!isConversationIdValid($conversationID)) {
    $ans[$CODE_KEY_L] = $CODE_211;
    $ans[$MESSAGE_KEY_L] = $CODE_211_MESSAGE;
    die(json_encode($ans));
}

if (isset($_REQUEST[$MESSAGE_ID_KEY_L_S])) $messageID = $_REQUEST[$MESSAGE_ID_KEY_L_S];
else {
    $ans[$CODE_KEY_L] = $CODE_111;
    $ans[$MESSAGE_KEY_L] = $CODE_110_MESSAGE;
    die(json_encode($ans));
}

if (!isMessageIdValid($messageID)) {
    $ans[$CODE_KEY_L] = $CODE_212;
    $ans[$MESSAGE_KEY_L] = $CODE_212_MESSAGE;
    die(json_encode($ans));
}

$database = new BookShopMainDatabase();

$result = see_message($messageID, $conversationID);

if ($result == 0) {
    $ans[$CODE_KEY_L] = $CODE_7898;
    $ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;

} else if ($result == 1146) {
    $ans[$CODE_KEY_L] = $CODE_313;
    $ans[$MESSAGE_KEY_L] = $CODE_313_MESSAGE;
} else if ($result == 314) {
    $ans[$CODE_KEY_L] = $CODE_314;
    $ans[$MESSAGE_KEY_L] = $CODE_314_MESSAGE;
} else {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
}
die (json_encode($ans));


//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
function see_message($messageID, $conversationID)
{
    global $database;
    global $SEEN_KEY_L;
    global $MESSAGE_ID_KEY_L_S;
    $database->connection->query(
        "UPDATE message_$conversationID SET $SEEN_KEY_L = 1
                WHERE $MESSAGE_ID_KEY_L_S = $messageID;"
    );
    if ($database->connection->affected_rows == 0) return 314;
    $error_no =  $database->connection->errno;
    $database->connection->close();
    return $error_no;
}

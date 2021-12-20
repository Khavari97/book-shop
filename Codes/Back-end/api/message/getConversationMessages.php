<?php
include_once('../../database/bookShopMain/BookShopMainDatabase.php');
include_once('../../utils/validation/inputValidation.php');
include_once('../../database/bookShopMain/table/AccessTokenTable.php');
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

$database = new BookShopMainDatabase();

$messages = get_messages($conversationID);

if (!$messages) {
    $ans[$CODE_KEY_L] = $CODE_313;
    $ans[$MESSAGE_KEY_L] = $CODE_313_MESSAGE;
    die(json_encode($ans));
}

$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
$ans[$MESSAGES_KEY_L] = $messages;
die(json_encode($ans));


//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
function get_messages($conversationID)
{
    global $database, $userId;
    global $MESSAGE_KEY_L, $PARTICIPANT1_KEY_L, $CONVERSATION_ID_KEY_L_S, $CONVERSATION_KEY_L, $DIRECTION_KEY_L;
    $messagesRelation = $database->connection
        ->query("SELECT * FROM $MESSAGE_KEY_L" . "_$conversationID");
    if ($messagesRelation->num_rows == 0) return false;
    $query = "SELECT $PARTICIPANT1_KEY_L FROM $CONVERSATION_KEY_L WHERE $CONVERSATION_ID_KEY_L_S = $conversationID";
    $participantRelation = $database->connection->query($query);
    if ($participantRelation->num_rows == 0) return false;
    $changeDir = ($participantRelation->fetch_assoc()[$PARTICIPANT1_KEY_L] != $userId);
    $messages = array();
    $index = 0;
    if ($changeDir) {
        while ($messagesTuple = $messagesRelation->fetch_assoc()) {
            $messages[$index] = $messagesTuple;
            if ($messages[$index][$DIRECTION_KEY_L] == 1) $messages[$index][$DIRECTION_KEY_L] = '0';
            else $messages[$index][$DIRECTION_KEY_L] = '1';
            $index++;
        }
    } else {
        while ($messagesTuple = $messagesRelation->fetch_assoc()) {
            $messages[$index] = $messagesTuple;
            $index++;
        }
    }
    $database->connection->close();
    return $messages;
}
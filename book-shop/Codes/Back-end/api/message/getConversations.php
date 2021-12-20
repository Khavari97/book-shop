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

$database = new BookShopMainDatabase();

$conversations = get_conversations($userId);

$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
$ans[$CONVERSATIONS_KEY_L] = $conversations;
$database->connection->close();
die(json_encode($ans));



//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
function get_conversations($userID)
{
    global $database;
    global $CONVERSATION_ID_KEY_L_S;
    global $PARTICIPANT1_KEY_L;
    global $PARTICIPANT2_KEY_L;
    global $UPDATED_AT_KEY_L_S;
    global $CONVERSATION_KEY_L;
    $conversationsRelation = $database->connection
        ->query("SELECT $CONVERSATION_ID_KEY_L_S, $PARTICIPANT1_KEY_L, $PARTICIPANT2_KEY_L, $UPDATED_AT_KEY_L_S
                        FROM $CONVERSATION_KEY_L
                        WHERE $PARTICIPANT1_KEY_L = '$userID' or $PARTICIPANT2_KEY_L = '$userID';");
    $conversations = array();
    $index = 0;
    while ($conversationTuple = $conversationsRelation->fetch_assoc()) {
        $conversations[$index] = $conversationTuple;
        $conversations[$index][$PARTICIPANT1_KEY_L] = get_user_data_from_id($conversations[$index][$PARTICIPANT1_KEY_L]);
        $conversations[$index][$PARTICIPANT2_KEY_L] = get_user_data_from_id($conversations[$index][$PARTICIPANT2_KEY_L]);
        $index++;
    }
    return $conversations;
}
//___________________________________________________________________________________________________________________________
function get_user_data_from_id ($id)
{
    global $database;
    global $USER_KEY_L, $USER_ID_KEY_L_S, $MAJOR_TITLE_KEY_L_S, $TITLE_KEY_L,$NAME_KEY_L, $MAJOR_KEY_L, $MAJOR_ID_KEY_L_S;
    $userRelation = $database->connection
        ->query("SELECT $USER_ID_KEY_L_S, $NAME_KEY_L, $MAJOR_KEY_L
                        FROM $USER_KEY_L
                        WHERE $USER_ID_KEY_L_S = $id;");
    $user = $userRelation->fetch_assoc();

    $majorRelation = $database->connection
        ->query("SELECT $TITLE_KEY_L FROM $MAJOR_KEY_L
                        WHERE $MAJOR_ID_KEY_L_S = $user[$MAJOR_KEY_L];");

    unset($user[$MAJOR_KEY_L]);
    $user[$MAJOR_TITLE_KEY_L_S] = $majorRelation->fetch_assoc()[$TITLE_KEY_L];

    return $user;
    }
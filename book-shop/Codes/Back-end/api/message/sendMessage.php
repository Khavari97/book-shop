<?php
include_once('../../database/bookShopMain/BookShopMainDatabase.php');
include_once('../../database/bookShopMain/table/AccessTokenTable.php');
include_once('../../utils/const/const.php');
include_once ('../../utils/validation/inputValidation.php');
include_once('../../utils/const/errorConst.php');
error_reporting(E_ALL);

$ans = array();

if (isset($_REQUEST[$ACCESS_TOKEN_KEY_L_S])) $accessTokenInput = $_REQUEST[$ACCESS_TOKEN_KEY_L_S];
else {
    $ans[$CODE_KEY_L] = $CODE_107;
    $ans[$MESSAGE_KEY_L] = $CODE_107_MESSAGE;
    die(json_encode($ans));
}

require_once('../../utils/checkAccessToken.php');

if (!isAccessTokenValid($accessTokenInput)) {
    $ans[$CODE_KEY_L] = $CODE_207;
    $ans[$MESSAGE_KEY_L] = $CODE_207_MESSAGE;
    die(json_encode($ans));
}

if (isset($_REQUEST[$RECEIVER_KEY_L])) $receiverID = $_REQUEST[$RECEIVER_KEY_L];
else {
    $ans[$CODE_KEY_L] = $CODE_112;
    $ans[$MESSAGE_KEY_L] = $CODE_112_MESSAGE;
    die(json_encode($ans));
}

if(!isUserIdValid($receiverID)) {
    $ans[$CODE_KEY_L] = $CODE_213;
    $ans[$MESSAGE_KEY_L] = $CODE_213_MESSAGE;
    die(json_encode($ans));
}

if (isset($_REQUEST[$CONTENT_KEY_L])) $content = $_REQUEST[$CONTENT_KEY_L];
else {
    $ans[$CODE_KEY_L] = $CODE_113;
    $ans[$MESSAGE_KEY_L] = $CODE_113_MESSAGE;
    die(json_encode($ans));
}

if(!isMessageValid($content)) {
    $ans[$CODE_KEY_L] = $CODE_214;
    $ans[$MESSAGE_KEY_L] = $CODE_214_MESSAGE;
    die(json_encode($ans));
}

$currentDateTime = date($DATE_TIME_FORMAT);

$database = new BookShopMainDatabase();


$senderID = $userId;

//sender id = participant 1
$conversationID = get_conversation_id($senderID, $receiverID);
if ($conversationID != -1) {
    insert_into_message($conversationID, $content, 1, 0, $currentDateTime, $currentDateTime);
} else {
    //sender id = participant 2
    $conversationID = get_conversation_id($receiverID, $senderID);
    if ($conversationID != -1) {
        insert_into_message($conversationID, $content, 0, 0, $currentDateTime, $currentDateTime);
    } else {
        //checking if receiver exists then making a new message table
        if (user_exists($receiverID)) {
            insert_into_conversation($senderID, $receiverID, $currentDateTime, $currentDateTime);
            $conversationID = get_conversation_id($senderID, $receiverID);
            create_message_table($conversationID);
            insert_into_message($conversationID, $content, 1, 0, $currentDateTime, $currentDateTime);
        } else {
            $ans[$CODE_KEY_L] = $CODE_315;
            $ans[$MESSAGE_KEY_L] = $CODE_315_MESSAGE;
            $database->connection->close();
            die(json_encode($ans));
        }
    }
}

update_conversation();

$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
$database->connection->close();
die(json_encode($ans));


//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
function insert_into_conversation($participant1_id, $participant2_id, $create_datetime, $update_datetime)
{
    global $database;
    global $CONVERSATION_KEY_L;
    $result = $database->connection
        ->query("INSERT INTO $CONVERSATION_KEY_L 
        VALUES (NULL, '$participant1_id', '$participant2_id',
        '$create_datetime', '$update_datetime'); ");
    return $result;
}

//___________________________________________________________________________________________________________________________
function insert_into_message($conversation_id, $content, $direction, $seen, $create_datetime, $update_datetime)
{
    global $database;
    $result = $database->connection
        ->query("INSERT INTO message_$conversation_id
        VALUES (NULL, '$content', '$direction', '$seen',
        '$create_datetime', '$update_datetime'); ");
    return $result;
}

//___________________________________________________________________________________________________________________________
function create_message_table($conversation_id)
{
    global $database;
    $result = $database->connection->query("CREATE TABLE `message_$conversation_id` ( 
        `message_id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT , 
        `content` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL , 
        `direction` BOOLEAN NOT NULL , `seen` BOOLEAN NOT NULL , 
        `created_at` DATETIME NOT NULL , 
        `updated_at` DATETIME NOT NULL , 
        PRIMARY KEY (`message_id`)) ENGINE = InnoDB;");
    return $result;
}

//___________________________________________________________________________________________________________________________
function get_conversation_id($participant1_id, $participant2_id)
{
    global $database;
    global $CONVERSATION_KEY_L;
    global $CONVERSATION_ID_KEY_L_S;
    global $PARTICIPANT1_KEY_L;
    global $PARTICIPANT2_KEY_L;
    $conversationIDSet = $database->connection
        ->query("SELECT $CONVERSATION_ID_KEY_L_S FROM $CONVERSATION_KEY_L WHERE $PARTICIPANT1_KEY_L = '$participant1_id'
                    AND $PARTICIPANT2_KEY_L = '$participant2_id';");
    $conversationIDRelation = $conversationIDSet->fetch_assoc();
    if (is_null($conversationIDRelation)) return -1;
    else return $conversationIDRelation[$CONVERSATION_ID_KEY_L_S];
}
//_________________________________________________________________________________________________________________________
function user_exists($userID)
{
    global $database;
    global $USER_ID_KEY_L_S;
    global $USER_KEY_L;
    $set = $database->connection
        ->query("SELECT $USER_ID_KEY_L_S FROM $USER_KEY_L WHERE $USER_ID_KEY_L_S = '$userID';");
    if ($set->num_rows == 0) return false;
    else return true;
}

//___________________________________________________________________________________________________________________________
function update_conversation()
{
    global $database;
    global $UPDATED_AT_KEY_L_S;
    global $currentDateTime;
    global $CONVERSATION_ID_KEY_L_S;
    global $conversationID;
    global $CONVERSATION_KEY_L;
    $database->connection
        ->query("UPDATE $CONVERSATION_KEY_L SET $UPDATED_AT_KEY_L_S = '$currentDateTime'
     WHERE $CONVERSATION_ID_KEY_L_S = '$conversationID'; ");
}
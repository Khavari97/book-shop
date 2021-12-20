<?php
include_once('../../database/bookShopMain/BookShopMainDatabase.php');
include_once('../../database/bookShopMain/table/AccessTokenTable.php');
include_once('../../utils/const/const.php');
include_once('../../utils/validation/inputValidation.php');
include_once('../../utils/const/errorConst.php');
error_reporting(E_ALL);

$ans = [];
$database = new BookShopMainDatabase();

$instructors = get_instructors();
$database->connection->close();
if ($instructors === false) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    die(json_encode($ans));
}
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
$ans[$INSTRUCTORS_KEY_L] = $instructors;
die(json_encode($ans));

//-----------------------------------------------------------
function get_instructors()
{
    global $database;
    global $INSTRUCTOR_KEY_L;
    $query = "SELECT * FROM $INSTRUCTOR_KEY_L;";
    $instructors_relation = $database->connection->query($query);
    $instructors = [];
    $index = 0;
    while ($instructor_tuple = $instructors_relation->fetch_assoc()) {
        $instructors[$index] = $instructor_tuple;
        $index++;
    }
    if ($database->connection->errno != 0) return false;
    return $instructors;
}
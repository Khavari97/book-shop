<?php
include_once('../../database/bookShopMain/BookShopMainDatabase.php');
include_once('../../database/bookShopMain/table/AccessTokenTable.php');
include_once('../../utils/const/const.php');
include_once('../../utils/validation/inputValidation.php');
include_once('../../utils/const/errorConst.php');
error_reporting(E_ALL);

$ans = [];
$database = new BookShopMainDatabase();

$majors = get_majors();
$database->connection->close();
if ($majors === false) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    die(json_encode($ans));
}
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
$ans[$MAJORS_KEY_L] = $majors;
die(json_encode($ans));


//-----------------------------------------------------------
function get_majors()
{
    global $database;
    global $MAJOR_KEY_L;
    $query = "SELECT * FROM $MAJOR_KEY_L;";
    $majors_relation = $database->connection->query($query);
    $majors = [];
    $index = 0;
    while ($major_tuple = $majors_relation->fetch_assoc()) {
        $majors[$index] = $major_tuple;
        $index++;
    }
    if ($database->connection->errno != 0) return false;
    return $majors;
}
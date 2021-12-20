<?php
include_once('../../database/bookShopMain/BookShopMainDatabase.php');
include_once('../../database/bookShopMain/table/AccessTokenTable.php');
include_once('../../utils/const/const.php');
include_once('../../utils/validation/inputValidation.php');
include_once('../../utils/const/errorConst.php');
error_reporting(E_ALL);

$ans = [];
$database = new BookShopMainDatabase();

$courses = get_courses();
$database->connection->close();
if ($courses === false) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    die(json_encode($ans));
}
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
$ans[$COURSES_KEY_L] = $courses;
die(json_encode($ans));


//-----------------------------------------------------------
function get_courses()
{
    global $database;
    global $COURSE_KEY_L;
    $query = "SELECT * FROM $COURSE_KEY_L;";
    $courses_relation = $database->connection->query($query);
    $courses = [];
    $index = 0;
    while ($course_tuple = $courses_relation->fetch_assoc()) {
        $courses[$index] = $course_tuple;
        $index++;
    }
    if ($database->connection->errno != 0) return false;
    return $courses;
}
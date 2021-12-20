<?php
include_once('../../database/bookShopMain/BookShopMainDatabase.php');
include_once('search_utils/TagInfoFinder.php');
include_once('../../database/bookShopMain/table/AccessTokenTable.php');
include_once('search_utils/ImageURLFinder.php');
include_once('search_utils/FileURLFinder.php');
include_once('../../utils/const/const.php');
include_once('../../utils/validation/inputValidation.php');
include_once('../../utils/const/errorConst.php');
error_reporting(~E_NOTICE);

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

$note_ids = $_REQUEST[$NOTE_IDS_KEY_L_S];
$title = $_REQUEST[$TITLE_KEY_L];
$price = $_REQUEST[$PRICE_KEY_L];
$owner = $_REQUEST[$OWNER_KEY_L];
$instructors = $_REQUEST[$INSTRUCTORS_KEY_L];
$semesters = $_REQUEST[$SEMESTERS_KEY_L];
$courses = $_REQUEST[$COURSES_KEY_L];
$majors = $_REQUEST[$MAJORS_KEY_L];

if (!is_null($title)) {
    if(!isTitleValid($title)) {
        $ans[$CODE_KEY_L] = $CODE_215;
        $ans[$MESSAGE_KEY_L] = $CODE_215_MESSAGE;
        die(json_encode($ans));
    }
}
if (!is_null($price)) {
    if(!isPriceValid($price)) {
        $ans[$CODE_KEY_L] = $CODE_216;
        $ans[$MESSAGE_KEY_L] = $CODE_216_MESSAGE;
        die(json_encode($ans));
    }
}
if (!is_null($owner)) {
    if(!isUserIdValid($owner)) {
        $ans[$CODE_KEY_L] = $CODE_217;
        $ans[$MESSAGE_KEY_L] = $CODE_217_MESSAGE;
        die(json_encode($ans));
    }
}

$start = $_REQUEST[$START_KEY_L];
if (is_null($start)) $start = 0;
else {
    if(!isStartValid($start)) {
        $ans[$CODE_KEY_L] = $CODE_222;
        $ans[$MESSAGE_KEY_L] = $CODE_222_MESSAGE;
        die(json_encode($ans));
    }
}
$step = $_REQUEST[$STEP_KEY_L];
if(!is_null($step)) {
    if(!isStepValid($step)) {
        $ans[$CODE_KEY_L] = $CODE_223;
        $ans[$MESSAGE_KEY_L] = $CODE_223_MESSAGE;
        die(json_encode($ans));
    }
}
if (!is_null($note_ids))
    $query = build_id_query();
else $query = build_normal_query();
if (!$query) $notes = [];
else $notes = get_notes($query,$start,$step);
$database->connection->close();

if ($notes===false) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    die(json_encode($ans));
}

$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
$ans[$NOTES_KEY_L] = $notes;
die(json_encode($ans));


//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
function build_normal_query()
{
    global $title, $price, $owner, $instructors, $semesters, $courses, $majors;
    global $TITLE_KEY_L, $PRICE_KEY_L, $OWNER_KEY_L, $TAGS_KEY_L,$NOTE_KEY_L;
    $query = "SELECT * FROM $NOTE_KEY_L N WHERE ";
    $before = false;
    if (!is_null($title)) {
        $title = trim($title);
        if ($before) $query .= "AND ";
        $query .= "LOWER($TITLE_KEY_L) LIKE LOWER('%$title%') ";
        $before = true;
    }
    if (!is_null($price)) {
        if ($before) $query .= "AND ";
        $query .= "$PRICE_KEY_L = $price ";
        $before = true;
    }
    if (!is_null($owner)) {
        if ($before) $query .= "AND ";
        $query .= "$OWNER_KEY_L = $owner ";
        $before = true;
    }

    $tags = [];

    if (!is_null($instructors)) $tags = array_merge($tags, json_decode($instructors));
    if (!is_null($semesters)) $tags = array_merge($tags, json_decode($semesters));
    if (!is_null($courses)) $tags = array_merge($tags, json_decode($courses));
    if (!is_null($majors)) $tags = array_merge($tags, json_decode($majors));

    for ($i = 0; $i < count($tags); $i++) {
        if ($before) $query .= "AND ";
        $query .= "JSON_SEARCH (N.$TAGS_KEY_L, 'one' , '$tags[$i]') IS NOT NULL ";
        $before = true;
    }

    $query .= ";";
    if (!$before) return false;
    return $query;
}
//___________________________________________________________________________________________________________________________
function get_notes($query,$start,$step) {
    global $database;
    global $TAGS_KEY_L, $FILES_KEY_L, $IMAGES_KEY_L, $COURSES_KEY_L, $SEMESTERS_KEY_L, $MAJORS_KEY_L, $INSTRUCTORS_KEY_L
           ,$SIZE_KEY_L, $ans;
    $notes_relation = $database->connection->query($query);
    $notes = [];
    $index = 0;
    $tags = [];
    $images = [];
    $files = [];
    while ($note_tuple = $notes_relation->fetch_assoc()) {
        $notes[$index] = $note_tuple;
        $tags[$index] = json_decode($notes[$index][$TAGS_KEY_L]);
        unset($notes[$index][$TAGS_KEY_L]);
        $files[$index] = json_decode($notes[$index][$FILES_KEY_L]);
        $images[$index] = json_decode($notes[$index][$IMAGES_KEY_L]);
        $index++;
    }
    if ($database->connection->errno!=0) return false;
    $size =  $notes_relation->num_rows;
    $ans[$SIZE_KEY_L] = $size;
    if (is_null($step) || $step > $size) $step = $size;
    $notes = array_slice($notes,$start,$step);

    $image_url_finder = new ImageURLFinder();
    $file_url_finder = new FileURLFinder();
    $n = count($notes);
    for ($index = 0; $index < $n; $index++) {
        $tag_info_finder = new TagInfoFinder($tags[$index],$database);
        $courses = $tag_info_finder->get_courses_titles();
        $semesters = $tag_info_finder->get_semesters();
        $majors = $tag_info_finder->get_major_titles();
        $instructors = $tag_info_finder->get_instructor_names();
        $notes[$index][$COURSES_KEY_L] = $courses;
        $notes[$index][$SEMESTERS_KEY_L] = $semesters;
        $notes[$index][$MAJORS_KEY_L] = $majors;
        $notes[$index][$INSTRUCTORS_KEY_L] = $instructors;
        $notes[$index][$IMAGES_KEY_L] = $image_url_finder->get_urls($images[$index], $database);
        $notes[$index][$FILES_KEY_L] = $file_url_finder->get_urls($files[$index], $database);
    }

    return $notes;
}
//___________________________________________________________________________________________________________________________
function build_id_query()
{
    global $note_ids;
    global $NOTE_ID_KEY_L_S, $NOTE_KEY_L;
    $note_ids = json_decode($note_ids);
    $query = "SELECT * FROM $NOTE_KEY_L WHERE ";
    $n = count($note_ids);
    for ($i = 0; $i < $n; $i++) {
        $query .= "$NOTE_ID_KEY_L_S = $note_ids[$i]";
        if ($i == $n - 1) $query .= ";";
        else $query .= " OR ";
    }
    return $query;
}
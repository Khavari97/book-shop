<?php
include_once('../../database/bookShopMain/BookShopMainDatabase.php');
include_once('../../database/bookShopMain/table/AccessTokenTable.php');
include_once('search_utils/TagInfoFinder.php');
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

$reference_ids = $_REQUEST[$REFERENCE_IDS_KEY_L_S];
$title = $_REQUEST[$TITLE_KEY_L];
$price = $_REQUEST[$PRICE_KEY_L];
$owner = $_REQUEST[$OWNER_KEY_L];
$writer = $_REQUEST[$WRITER_KEY_L];
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
if (!is_null($writer)) {
    if(!isWriterValid($writer)) {
        $ans[$CODE_KEY_L] = $CODE_224;
        $ans[$MESSAGE_KEY_L] = $CODE_224_MESSAGE;
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
if (is_null($start)) $start = 0;
$step = $_REQUEST[$STEP_KEY_L];
if(!is_null($step)) {
    if(!isStepValid($step)) {
        $ans[$CODE_KEY_L] = $CODE_223;
        $ans[$MESSAGE_KEY_L] = $CODE_223_MESSAGE;
        die(json_encode($ans));
    }
}
if (!is_null($reference_ids))
    $query = build_id_query();
else $query = build_normal_query();
if (!$query) $references = [];
else $references = get_references($query,$start,$step);
$database->connection->close();

if ($references===false) {
    $ans[$CODE_KEY_L] = $CODE_405;
    $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
    die(json_encode($ans));
}

$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
$ans[$REFERENCES_KEY_L] = $references;
die(json_encode($ans));


//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
//___________________________________________________________________________________________________________________________
function build_normal_query()
{
    global $title, $price, $owner, $writer, $instructors, $semesters, $courses, $majors;
    global $TITLE_KEY_L, $PRICE_KEY_L, $OWNER_KEY_L, $WRITER_KEY_L, $TAGS_KEY_L, $REFERENCE_KEY_L;
    $query = "SELECT * FROM $REFERENCE_KEY_L R WHERE ";
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
    if (!is_null($writer)) {
        $writer = trim($writer);
        if ($before) $query .= "AND ";
        $query .= "LOWER($WRITER_KEY_L) LIKE LOWER('%$writer%') ";
        $before = true;
    }

    $tags = [];

    if (!is_null($instructors)) $tags = array_merge($tags, json_decode($instructors));
    if (!is_null($semesters)) $tags = array_merge($tags, json_decode($semesters));
    if (!is_null($courses)) $tags = array_merge($tags, json_decode($courses));
    if (!is_null($majors)) $tags = array_merge($tags, json_decode($majors));

    for ($i = 0; $i < count($tags); $i++) {
        if ($before) $query .= "AND ";
        $query .= "JSON_SEARCH (R.$TAGS_KEY_L, 'one' , '$tags[$i]') IS NOT NULL ";
        $before = true;
    }

    $query .= ";";
    if (!$before) return false;
    return $query;
}
//___________________________________________________________________________________________________________________________
function get_references($query,$start,$step) {
    global $database;
    global $TAGS_KEY_L, $FILES_KEY_L, $IMAGES_KEY_L, $COURSES_KEY_L, $SEMESTERS_KEY_L, $MAJORS_KEY_L, $INSTRUCTORS_KEY_L
           ,$SIZE_KEY_L,$ans;
    $references_relation = $database->connection->query($query);
    $references = [];
    $index = 0;
    $tags = [];
    $images = [];
    $files = [];
    while ($reference_tuple = $references_relation->fetch_assoc()) {
        $references[$index] = $reference_tuple;
        $tags[$index] = json_decode($references[$index][$TAGS_KEY_L]);
        unset($references[$index][$TAGS_KEY_L]);
        $files[$index] = json_decode($references[$index][$FILES_KEY_L]);
        $images[$index] = json_decode($references[$index][$IMAGES_KEY_L]);
        $index++;
    }
    if ($database->connection->errno!=0) return false;
    $size = $references_relation->num_rows;
    $ans[$SIZE_KEY_L] = $size;
    if (is_null($step) || $step > $size) $step = $size;
    $references = array_slice($references,$start,$step);

    $image_url_finder = new ImageURLFinder();
    $file_url_finder = new FileURLFinder();
    $n = count($references);
    for ($index = 0; $index < $n; $index++) {
        $tag_info_finder = new TagInfoFinder($tags[$index],$database);
        $courses = $tag_info_finder->get_courses_titles();
        $semesters = $tag_info_finder->get_semesters();
        $majors = $tag_info_finder->get_major_titles();
        $instructors = $tag_info_finder->get_instructor_names();
        $references[$index][$COURSES_KEY_L] = $courses;
        $references[$index][$SEMESTERS_KEY_L] = $semesters;
        $references[$index][$MAJORS_KEY_L] = $majors;
        $references[$index][$INSTRUCTORS_KEY_L] = $instructors;
        $references[$index][$IMAGES_KEY_L] = $image_url_finder->get_urls($images[$index], $database);
        $references[$index][$FILES_KEY_L] = $file_url_finder->get_urls($files[$index], $database);
    }

    return $references;
}
//___________________________________________________________________________________________________________________________
function build_id_query()
{
    global $reference_ids;
    global $REFERENCE_ID_KEY_L_S, $REFERENCE_KEY_L;
    $reference_ids = json_decode($reference_ids);
    $query = "SELECT * FROM $REFERENCE_KEY_L WHERE ";
    $n = count($reference_ids);
    for ($i = 0; $i < $n; $i++) {
        $query .= "$REFERENCE_ID_KEY_L_S = $reference_ids[$i]";
        if ($i == $n - 1) $query .= ";";
        else $query .= " OR ";
    }
    return $query;
}
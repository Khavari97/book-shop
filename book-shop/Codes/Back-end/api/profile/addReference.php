<?php

//Include Part
require_once "../../utils/const/const.php";
require_once "../../utils/const/errorConst.php";
require_once "../../utils/validation/inputValidation.php";
require_once "../../database/bookShopMain/BookShopMainDatabase.php";
require_once "../../database/bookShopMain/table/AccessTokenTable.php";
require_once "../../database/bookShopMain/table/FileTable.php";
require_once "../../database/bookShopMain/table/InstructorTable.php";
require_once "../../database/bookShopMain/table/MajorTable.php";
require_once "../../database/bookShopMain/table/CourseTable.php";
require_once "../../database/bookShopMain/table/ImageTable.php";
require_once "../../database/bookShopMain/table/ReferenceTable.php";

$ans = array();

//Phase 1: Get Inputs
if ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $POST_KEY_U) {
  if (isset($_POST[$ACCESS_TOKEN_KEY_L_S])) $accessTokenInput = $_POST[$ACCESS_TOKEN_KEY_L_S]; else $accessTokenInput = NULL;
  if (isset($_POST[$TITLE_KEY_L])) $titleInput = $_POST[$TITLE_KEY_L]; else $titleInput = NULL;
  if (isset($_POST[$DESCRIPTION_KEY_L])) $descriptionInput = $_POST[$DESCRIPTION_KEY_L]; else $descriptionInput = NULL;
  if (isset($_POST[$PRICE_KEY_L])) $priceInput = $_POST[$PRICE_KEY_L]; else $priceInput = NULL;
  if (isset($_POST[$WRITER_KEY_L])) $writerInput = $_POST[$WRITER_KEY_L]; else $writerInput = NULL;
  if (isset($_POST[$FILES_KEY_L])) $filesInput = $_POST[$FILES_KEY_L]; else $filesInput = NULL;
  if (isset($_POST[$IMAGES_KEY_L])) $imagesInput = $_POST[$IMAGES_KEY_L]; else $imagesInput = NULL;
  if (isset($_POST[$TAGS_KEY_L])) $tagsInput = $_POST[$TAGS_KEY_L]; else $tagsInput = NULL;
} elseif ($_SERVER[$REQUEST_METHOD_KEY_U_S] == $GET_KEY_U) {
  if (isset($_GET[$ACCESS_TOKEN_KEY_L_S])) $accessTokenInput = $_GET[$ACCESS_TOKEN_KEY_L_S]; else $accessTokenInput = NULL;
  if (isset($_GET[$TITLE_KEY_L])) $titleInput = $_GET[$TITLE_KEY_L]; else $titleInput = NULL;
  if (isset($_GET[$DESCRIPTION_KEY_L])) $descriptionInput = $_GET[$DESCRIPTION_KEY_L]; else $descriptionInput = NULL;
  if (isset($_GET[$PRICE_KEY_L])) $priceInput = $_GET[$PRICE_KEY_L]; else $priceInput = NULL;
  if (isset($_GET[$WRITER_KEY_L])) $writerInput = $_GET[$WRITER_KEY_L]; else $writerInput = NULL;
  if (isset($_GET[$FILES_KEY_L])) $filesInput = $_GET[$FILES_KEY_L]; else $filesInput = NULL;
  if (isset($_GET[$IMAGES_KEY_L])) $imagesInput = $_GET[$IMAGES_KEY_L]; else $imagesInput = NULL;
  if (isset($_GET[$TAGS_KEY_L])) $tagsInput = $_GET[$TAGS_KEY_L]; else $tagsInput = NULL;
} else {
  $ans[$CODE_KEY_L] = $CODE_404;
  $ans[$MESSAGE_KEY_L] = $CODE_404_MESSAGE;
  die(json_encode($ans));
}

//Phase 2: Check Existence
if ($accessTokenInput === NULL) {
  $ans[$CODE_KEY_L] = $CODE_107;
  $ans[$MESSAGE_KEY_L] = $CODE_107_MESSAGE;
  die(json_encode($ans));
}
if ($titleInput === NULL) {
  $ans[$CODE_KEY_L] = $CODE_114;
  $ans[$MESSAGE_KEY_L] = $CODE_114_MESSAGE;
  die(json_encode($ans));
}
if ($descriptionInput === NULL) {
  $ans[$CODE_KEY_L] = $CODE_115;
  $ans[$MESSAGE_KEY_L] = $CODE_115_MESSAGE;
  die(json_encode($ans));
}
if ($priceInput === NULL) {
  $ans[$CODE_KEY_L] = $CODE_116;
  $ans[$MESSAGE_KEY_L] = $CODE_116_MESSAGE;
  die(json_encode($ans));
}
if ($writerInput === NULL) {
  $ans[$CODE_KEY_L] = $CODE_117;
  $ans[$MESSAGE_KEY_L] = $CODE_117_MESSAGE;
  die(json_encode($ans));
}
if ($filesInput === NULL) {
  $ans[$CODE_KEY_L] = $CODE_118;
  $ans[$MESSAGE_KEY_L] = $CODE_118_MESSAGE;
  die(json_encode($ans));
}
if ($imagesInput === NULL) {
  $ans[$CODE_KEY_L] = $CODE_119;
  $ans[$MESSAGE_KEY_L] = $CODE_119_MESSAGE;
  die(json_encode($ans));
}
if ($tagsInput === NULL) {
  $ans[$CODE_KEY_L] = $CODE_120;
  $ans[$MESSAGE_KEY_L] = $CODE_120_MESSAGE;
  die(json_encode($ans));
}

//Phase 3: Check Validation
if (!isAccessTokenValid($accessTokenInput)) {
  $ans[$CODE_KEY_L] = $CODE_207;
  $ans[$MESSAGE_KEY_L] = $CODE_207_MESSAGE;
  die(json_encode($ans));
}
if (!isTitleValid($titleInput)) {
  $ans[$CODE_KEY_L] = $CODE_215;
  $ans[$MESSAGE_KEY_L] = $CODE_215_MESSAGE;
  die(json_encode($ans));
}
if (!isDescriptionValid($descriptionInput)) {
  $ans[$CODE_KEY_L] = $CODE_228;
  $ans[$MESSAGE_KEY_L] = $CODE_228_MESSAGE;
  die(json_encode($ans));
}
if (!isPriceValid($priceInput)) {
  $ans[$CODE_KEY_L] = $CODE_216;
  $ans[$MESSAGE_KEY_L] = $CODE_216_MESSAGE;
  die(json_encode($ans));
}
if (!isWriterValid($writerInput)) {
  $ans[$CODE_KEY_L] = $CODE_224;
  $ans[$MESSAGE_KEY_L] = $CODE_224_MESSAGE;
  die(json_encode($ans));
}
$filesInput = json_decode($filesInput);
if ($filesInput === NULL || !is_array($filesInput)) {
  $ans[$CODE_KEY_L] = $CODE_225;
  $ans[$MESSAGE_KEY_L] = $CODE_225_MESSAGE;
  die(json_encode($ans));
}
foreach ($filesInput as $file) {
  if (!isFileIdValid($file)) {
    $ans[$CODE_KEY_L] = $CODE_225;
    $ans[$MESSAGE_KEY_L] = $CODE_225_MESSAGE;
    die(json_encode($ans));
  }
}
$imagesInput = json_decode($imagesInput);
if ($imagesInput === NULL || !is_array($imagesInput)) {
  $ans[$CODE_KEY_L] = $CODE_226;
  $ans[$MESSAGE_KEY_L] = $CODE_226_MESSAGE;
  die(json_encode($ans));
}
foreach ($imagesInput as $image) {
  if (!isImageIdValid($image)) {
    $ans[$CODE_KEY_L] = $CODE_226;
    $ans[$MESSAGE_KEY_L] = $CODE_226_MESSAGE;
    die(json_encode($ans));
  }
}
$tagsInput = json_decode($tagsInput);
if ($tagsInput === NULL || !is_array($tagsInput)) {
  $ans[$CODE_KEY_L] = $CODE_227;
  $ans[$MESSAGE_KEY_L] = $CODE_227_MESSAGE;
  die(json_encode($ans));
}
foreach ($tagsInput as $tag) {
  if (!isTagValid($tag)) {
    $ans[$CODE_KEY_L] = $CODE_227;
    $ans[$MESSAGE_KEY_L] = $CODE_227_MESSAGE;
    die(json_encode($ans));
  }
}

//Phase 4: Check Access Token
require_once "../../utils/checkAccessToken.php";

//Phase 5: Check File IDs
$mainDatabase = new BookShopMainDatabase();
$fileTable = new FileTable($mainDatabase);
$encodedFiles = "";
foreach ($filesInput as $file) {
  $encodedFiles .= "$file,";
}
if ($encodedFiles !== "") {
  $encodedFiles = substr($encodedFiles, 0, -1);
  $fileSelectResult = $fileTable->selectByIds(FileTable::$FILE_ID_COLUMN, $encodedFiles);
  if ($fileSelectResult->num_rows !== count($filesInput)) {
    $ans[$CODE_KEY_L] = $CODE_320;
    $ans[$MESSAGE_KEY_L] = $CODE_320_MESSAGE;
    die(json_encode($ans));
  }
}

//Phase 6: Check Image IDs
$imageTable = new ImageTable($mainDatabase);
$encodedImages = "";
foreach ($imagesInput as $image) {
  $encodedImages .= "$image,";
}
if ($encodedImages!=="") {
  $encodedImages = substr($encodedImages, 0, -1);
  $imageSelectResult = $imageTable->selectByIds(ImageTable::$IMAGE_ID_COLUMN, $encodedImages);
  if ($imageSelectResult->num_rows !== count($imagesInput)) {
    $ans[$CODE_KEY_L] = $CODE_321;
    $ans[$MESSAGE_KEY_L] = $CODE_321_MESSAGE;
    die(json_encode($ans));
  }
}

//Phase 7: Check Instructor IDs
$instructorTable = new InstructorTable($mainDatabase);
$instructorsNumber = 0;
$encodedInstructors = "";
foreach ($tagsInput as $tag) {
  if (substr($tag, 0, 1) === "i") {
    $encodedInstructors .= substr($tag, 1) . ",";
    $instructorsNumber++;
  }
}
if ($encodedInstructors !== "") {
  $encodedInstructors = substr($encodedInstructors, 0, -1);
  $instructorSelectResult = $instructorTable->selectByIds(InstructorTable::$INSTRUCTOR_ID_COLUMN, $encodedInstructors);
  if ($instructorSelectResult->num_rows !== $instructorsNumber) {
    $ans[$CODE_KEY_L] = $CODE_317;
    $ans[$MESSAGE_KEY_L] = $CODE_317_MESSAGE;
    die(json_encode($ans));
  }
}

//Phase 8: Check Course IDs
$courseTable = new CourseTable($mainDatabase);
$coursesNumber = 0;
$encodedCourses = "";
foreach ($tagsInput as $tag) {
  if (substr($tag, 0, 1) === "c") {
    $encodedCourses .= substr($tag, 1) . ",";
    $coursesNumber++;
  }
}
if ($encodedCourses !== "") {
  $encodedCourses = substr($encodedCourses, 0, -1);
  $courseSelectResult = $courseTable->selectByIds(CourseTable::$COURSE_ID_COLUMN, $encodedCourses);
  if ($courseSelectResult->num_rows !== $coursesNumber) {
    $ans[$CODE_KEY_L] = $CODE_318;
    $ans[$MESSAGE_KEY_L] = $CODE_318_MESSAGE;
    die(json_encode($ans));
  }
}

//Phase 9: Check Major IDs
$majorTable = new MajorTable($mainDatabase);
$majorsNumber = 0;
$encodedMajors = "";
foreach ($tagsInput as $tag) {
  if (substr($tag, 0, 1) === "m") {
    $encodedMajors .= substr($tag, 1) . ",";
    $majorsNumber++;
  }
}
if ($encodedMajors !== "") {
  $encodedMajors = substr($encodedMajors, 0, -1);
  $majorSelectResult = $majorTable->selectByIds(MajorTable::$MAJOR_ID_COLUMN, $encodedMajors);
  if ($majorSelectResult->num_rows !== $majorsNumber) {
    $ans[$CODE_KEY_L] = $CODE_319;
    $ans[$MESSAGE_KEY_L] = $CODE_319_MESSAGE;
    die(json_encode($ans));
  }
}

//Phase 10: Add Reference
$referenceTable = new ReferenceTable($mainDatabase);
$insertReferenceResult = $referenceTable->insert($titleInput, $descriptionInput, $priceInput, $userId, $writerInput, json_encode($filesInput), json_encode($imagesInput), json_encode($tagsInput), date($DATE_TIME_FORMAT), date($DATE_TIME_FORMAT));
if (!$insertReferenceResult) {
  $ans[$CODE_KEY_L] = $CODE_405;
  $ans[$MESSAGE_KEY_L] = $CODE_405_MESSAGE;
  die(json_encode($ans));
}

//Finishing
$ans[$CODE_KEY_L] = $CODE_7898;
$ans[$MESSAGE_KEY_L] = $CODE_7898_MESSAGE;
die(json_encode($ans));
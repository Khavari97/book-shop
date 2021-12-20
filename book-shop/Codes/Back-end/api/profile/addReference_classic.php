<?php

include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../database/bookShopMain/bookShopMainDatabaseAuthenticationInformation.php";
include_once "../../utils/validation/InputValidation.php";
include_once "../../utils/const/Const.php";
include_once "../../utils/const/errorConst.php";
include_once "Profile_Utils/utilFunctions.php";

// connection info
$server_name = $BookShopMainDatabaseServerName;
$user_name = $BookShopMainDatabaseUsername;
$db_password = $BookShopMainDatabasePassword;
$db_name = $BookShopMainDatabaseName;

// output
$ans = array();


// Create connection

$conn = new mysqli($server_name, $user_name , $db_password ,$db_name);

#test connection
if ($conn->connect_error)
die("Connection failed: " . $conn->connect_error);

$access_token = "" ;
if(isset($_POST[$ACCESS_TOKEN_KEY_L_S]) && $_POST[$ACCESS_TOKEN_KEY_L_S] !=null)
 $access_token = $_POST[$ACCESS_TOKEN_KEY_L_S] ;
else if (isset($_GET[$ACCESS_TOKEN_KEY_L_S]) && $_GET[$ACCESS_TOKEN_KEY_L_S] != null  )
 $access_token = $_GET[$ACCESS_TOKEN_KEY_L_S] ;
else {
  $ans[$CODE_KEY_L] = $CODE_107 ;
  $ans[$MESSAGE_KEY_L] = $CODE_107_MESSAGE ;
  die(json_encode($ans));
}

$userId = getUserId($access_token , $conn );

//_____________________________________________________________________
   #checking input existance
//_____________________________________________________________________

//Cheking title input
$title = "" ;
if(isset($_POST[$TITLE_KEY_L]) && $_POST[$TITLE_KEY_L] != null )
 $title = $_POST[$TITLE_KEY_L] ;
else if (isset($_GET[$TITLE_KEY_L]) && $_GET[$TITLE_KEY_L] != null )
 $title = $_GET[$TITLE_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_114 ;
  $ans[$MESSAGE_KEY_L] = $CODE_114_MESSAGE ;
  die(json_encode($ans));
}



$description = "" ;
if(isset($_POST[$DESCRIPTION_KEY_L]) && $_POST[$DESCRIPTION_KEY_L] != null )
 $description = $_POST[$DESCRIPTION_KEY_L] ;
else if (isset($_GET[$DESCRIPTION_KEY_L]) && $_GET[$DESCRIPTION_KEY_L] != null )
 $description = $_GET[$DESCRIPTION_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_115 ;
  $ans[$MESSAGE_KEY_L] = $CODE_115_MESSAGE ;
  die(json_encode($ans));
}



$price = 0 ;
if(isset($_POST[$PRICE_KEY_L]) && $_POST[$PRICE_KEY_L] != null )
 $price = $_POST[$PRICE_KEY_L] ;
else if (isset($_GET[$PRICE_KEY_L]) && $_GET[$PRICE_KEY_L] != null )
 $price = $_GET[$PRICE_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_116 ;
  $ans[$MESSAGE_KEY_L] = $CODE_116_MESSAGE ;
  die(json_encode($ans));
}

// owner is skiped


$writer="";
if(isset($_POST[$WRITER_KEY_L]) && $_POST[$WRITER_KEY_L] != null )
 $writer = $_POST[$WRITER_KEY_L] ;
else if (isset($_GET[$WRITER_KEY_L]) && $_GET[$WRITER_KEY_L] != null )
 $writer = $_GET[$WRITER_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_117 ;
  $ans[$MESSAGE_KEY_L] = $CODE_117_MESSAGE ;
  die(json_encode($ans));
}



$files = "";
if(isset($_POST[$FILES_KEY_L]) && $_POST[$FILES_KEY_L] != null )
 $files = $_POST[$FILES_KEY_L] ;
else if (isset($_GET[$FILES_KEY_L]) && $_GET[$FILES_KEY_L] != null )
 $files = $_GET[$FILES_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_118 ;
  $ans[$MESSAGE_KEY_L] = $CODE_118_MESSAGE ;
  die(json_encode($ans));
}



$images = "";
if(isset($_POST[$IMAGES_KEY_L]) && $_POST[$IMAGES_KEY_L] != null )
 $images = $_POST[$IMAGES_KEY_L] ;
else if (isset($_GET[$IMAGES_KEY_L]) && $_GET[$IMAGES_KEY_L] != null )
 $images = $_GET[$IMAGES_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_119 ;
  $ans[$MESSAGE_KEY_L] = $CODE_119_MESSAGE ;
  die(json_encode($ans));
}


$tags = "";
if(isset($_POST[$TAGS_KEY_L]) && $_POST[$TAGS_KEY_L] != null )
 $tags = $_POST[$TAGS_KEY_L] ;
else if (isset($_GET[$TAGS_KEY_L]) && $_GET[$TAGS_KEY_L] != null )
 $tags = $_GET[$TAGS_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_120 ;
  $ans[$MESSAGE_KEY_L] = $CODE_120_MESSAGE ;
  die(json_encode($ans));
}


//_____________________________________________________________________
   #checking input validation
//_____________________________________________________________________

#check title
if(!isTitleValid($title)){
  $ans[$CODE_KEY_L] = $CODE_215 ;
  $ans[$MESSAGE_KEY_L] = $CODE_215_MESSAGE ;
  die(json_encode($ans));
}

#check description
if(!isDescriptionValid($description)){
  $ans[$CODE_KEY_L] = $CODE_228 ;
  $ans[$MESSAGE_KEY_L] = $CODE_228_MESSAGE ;
  die(json_encode($ans));
}

#check price
if(!isPriceValid($price)){
  $ans[$CODE_KEY_L] = $CODE_216 ;
  $ans[$MESSAGE_KEY_L] = $CODE_216_MESSAGE ;
  die(json_encode($ans));
}

#check writer
if(!isWriterValid($writer)){
  $ans[$CODE_KEY_L] = $CODE_224 ;
  $ans[$MESSAGE_KEY_L] = $CODE_224_MESSAGE ;
  die(json_encode($ans));
}

#check files
$files_array = json_decode($files, true);

foreach($files_array as $file){
if(!isFileIdValid($file)){
  $ans[$CODE_KEY_L] = $CODE_225 ;
  $ans[$MESSAGE_KEY_L] = $CODE_225_MESSAGE ;
  die(json_encode($ans));
}
exists($file,"files",$conn); // checking file existance
}


#check image
$images_array = json_decode($images, true);

foreach($images_array as $image){
if(!isImageIdValid($image)){
  $ans[$CODE_KEY_L] = $CODE_226 ;
  $ans[$MESSAGE_KEY_L] = $CODE_226_MESSAGE ;
  die(json_encode($ans));
}
exists($image,"images",$conn); // checking image existance
}


#check tag
$tags_array = json_decode($tags, true);
foreach($tags_array as $tag ){
if(!isTagValid($tag)){
  $ans[$CODE_KEY_L] = $CODE_227 ;
  $ans[$MESSAGE_KEY_L] = $CODE_227_MESSAGE ;
  die(json_encode($ans));
}
exists($tag,"tags",$conn); // checking tag existance
}


  $today = date("Y-m-d") ." ".date("H:i:s");

  $create_query = "INSERT INTO reference( title , description , price , owner , writer ,files,images,tags, created_at , updated_at )
  values('$title' , '$description' , $price , $userId , '$writer' , '$files ','$images', '$tags','$today' , '$today' ) ; " ;


  if ($result = $conn -> query($create_query)){
    $ans[$CODE_KEY_L] = 7898 ;
    $ans[$MESSAGE_KEY_L] = "Success!" ;
    die(json_encode($ans));
  }
  else
    die("No information updated");

  $conn->close();

?>

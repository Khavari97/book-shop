<?php

class Parameter
{
    public $key;
    public $default_value;
    public $value;

    public $existance_error_code ;
    public $existance_error_message;

    public $validation_error_code ;
    public $validation_error_message ;

    public String $validity_function ;

    function __construct( String $key , $default_value , $existance_error_code , $existance_error_message , $validation_error_code , $validation_error_message , $validity_function)
    {

        $this ->key = $key ;
        $this ->default_value = $default_value ;

        $this ->existance_error_code = $existance_error_code ;
        $this ->existance_error_message = $existance_error_message ;

        $this ->validation_error_code  = $validation_error_code  ;
        $this ->validation_error_message = $validation_error_message ;

        $this ->validity_function = $validity_function ;
    }

}

// Functions

 // Statement creator module
# Simple key : post request keys containing 1 argument
# Composite key :  post request keys which are json array
function createStatement( Array $simple_parameter , Array $composite_parameter , $user_id , $group , mysqli $conn  ){
  $CODE_KEY_L = "code";
  $MESSAGE_KEY_L = "message";
  // Checking existance and validity of simple parameters
  foreach($simple_parameter  as $parameter ){
    //echo $parameter->$validation_error_code ;

    if (isset($_POST[$parameter->key]) || isset($_GET[$parameter->key]) ) {
      if($_REQUEST[$parameter->key] == null){
        $ans[$CODE_KEY_L] = $parameter->existance_error_code ;
        $ans[$MESSAGE_KEY_L] = $parameter->existance_error_message ;
        die(json_encode($ans));
      }

      $parameter ->value = $_REQUEST[$parameter->key];
      $func = $parameter ->validity_function ;

      if(! $func($parameter ->value)){
        $ans[$CODE_KEY_L] = $parameter->validation_error_code  ;
        $ans[$MESSAGE_KEY_L] = $parameter->validation_error_message  ;
        die(json_encode($ans));
      }


    }  else
    $parameter ->value = $parameter->default_value ;

  } // End for


  // Checking existance and validity of composite parameters
  foreach($composite_parameter  as $parameter ){
    if (isset($_POST[$parameter->key]) || isset($_GET[$parameter->key]) ) {

      if($_REQUEST[$parameter->key] == null){
        $ans[$CODE_KEY_L] = $parameter->existance_error_code ;
        $ans[$MESSAGE_KEY_L] = $parameter->existance_error_message ;
        die(json_encode($ans));
      }

      $parameter ->value = $_REQUEST[$parameter->key];
      $parameter_component = json_decode($parameter ->value, true);
      $func = $parameter ->validity_function ;

      foreach($parameter_component as $component){
    //  echo $component ;
      exists($component , $parameter ->key , $conn ); // passes if they all exist

      if(! $func($component) ){
        $ans[$CODE_KEY_L] = $parameter->validation_error_code  ;
        $ans[$MESSAGE_KEY_L] = $parameter->validation_error_message  ;
        die(json_encode($ans));
      }
      }

    } else
    $parameter ->value = $parameter->default_value ;
  } // end composite parameter for


  $title = (String) $simple_parameter[title] ->value ;
  $description =(String) $simple_parameter[description] ->value ;
  $price = (int) $simple_parameter[price] ->value ;


  $files = (String) $composite_parameter[files] ->value ;
  $images =(String) $composite_parameter[images] ->value ;
  $tags = (String) $composite_parameter[tags] ->value ;
  $today = date("Y-m-d") ." ".date("H:i:s");


  switch($group){

    case "NOTE" :
        $create_query = "INSERT INTO note (title, description, price, owner, files, images, tags, created_at, updated_at)
        values( '$title' , '$description'  , $price , $user_id , '$files' ,'$images', '$tags' ,'$today' , '$today') ; " ;
        break;

    case "BOOK" :
        $writer = (String) $simple_parameter[writer] ->value ;
        $create_query = "INSERT INTO book (title, description, price, owner,writer, files, images, tags, created_at, updated_at)
        values( '$title' , '$description'  , $price , $user_id, '$writer' , '$files' ,'$images', '$tags' ,'$today' , '$today') ; " ;
        break;

    case "REFERENCE" :
        $writer = (String) $simple_parameter[writer] ->value ;
        $create_query = "INSERT INTO reference (title, description, price, owner,writer, files, images, tags, created_at, updated_at)
        values( '$title' , '$description'  , $price , $user_id, '$writer' , '$files' ,'$images', '$tags' ,'$today' , '$today') ; " ;
        break;

  }

  return $create_query ;
}


 // Getting userId by access token
 function getUserId(String $access_token ,  mysqli $conn ){
   //initials
 $CODE_KEY_L = "code";
 $MESSAGE_KEY_L = "message";

 $CODE_310 = 310;
 $CODE_310_MESSAGE = "Access Token has been expired.";

 $CODE_311 = 311;
 $CODE_311_MESSAGE = "Wrong Access Token.";

 $CODE_207 = 207;
 $CODE_207_MESSAGE = "Invalid Access Token Input.";

 $today = date("Y-m-d") ." ".date("H:i:s");

 if(!isAccessTokenValid($access_token)){
   $ans[$CODE_KEY_L] = $CODE_207 ;
   $ans[$MESSAGE_KEY_L] = $CODE_207_MESSAGE ;
   die(json_encode($ans));
 }

 $access_token_query = "SELECT user_id , expire_at
 FROM user u
 JOIN access_token act
 ON act.target = u.user_id
 WHERE act.token = '$access_token' ; "
  ;

 $result = $conn -> query($access_token_query);

  if (mysqli_num_rows($result) > 0){
    $info = $result->fetch_assoc();
    $userId = $info[$USER_ID_KEY_L_S];
  }
  else
    {
      $ans[$CODE_KEY_L] = $CODE_311 ;
      $ans[$MESSAGE_KEY_L] = $CODE_311_MESSAGE ;
      die(json_encode($ans)); // Wrong access token
    }

    $expire_date = $info[$EXPIRE_AT_KEY_L_S];
    if($expire_date<$today){
      $ans[$CODE_KEY_L] = $CODE_310 ;
      $ans[$MESSAGE_KEY_L] = $CODE_310_MESSAGE ;
      die(json_encode($ans)); // Access token has Expired
    }

  return $userId;

}

function exists($id , $type ,  mysqli $conn){
  $CODE_KEY_L = "code";
  $MESSAGE_KEY_L = "message";
  $id = (int) $id ;

  //echo $type;
  switch($type){
    case "files" :
    $query = "SELECT file_id
    FROM file f
    WHERE f.file_id = '$id' ; "
     ;
     break ;

     case "images" :
     $query = "SELECT image_id
     FROM image i
     WHERE i.image_id = '$id'  ; "
      ;
      break ;

  }

  $ans = array();
  $result = $conn -> query($query) ;

   if (! (mysqli_num_rows($result) > 0)){
     switch($type){
       case "files" :
       $ans[$CODE_KEY_L] = 320 ;
       $ans[$MESSAGE_KEY_L] = "Requested File ID does not exist." ;
       break;

       case "images" :
       $ans[$CODE_KEY_L] = 321 ;
       $ans[$MESSAGE_KEY_L] = "Requested Image ID does not exist." ;
       break;

     }
     die(json_encode($ans));
   }

}


?>

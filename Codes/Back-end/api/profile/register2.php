<?php

include_once "../../database/bookShopMain/BookShopMainDatabase.php";
include_once "../../database/bookShopMain/bookShopMainDatabaseAuthenticationInformation.php";
include_once "../../utils/validation/InputValidation.php";
include_once "../../utils/const/const.php";
include_once "../../utils/const/errorConst.php";

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

#___________________________________________________________________________________________________________________________________________________
// Inputs existance check
#___________________________________________________________________________________________________________________________________________________

//Cheking email input
$email = "" ;
if(isset($_POST[$EMAIL_KEY_L]) && $_POST[$EMAIL_KEY_L] != null )
 $email = $_POST[$EMAIL_KEY_L] ;
else if (isset($_GET[$EMAIL_KEY_L]) && $_GET[$EMAIL_KEY_L] != null )
 $email = $_GET[$EMAIL_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_101 ;
  $ans[$MESSAGE_KEY_L] = $CODE_101_MESSAGE ;
  die(json_encode($ans));
}

//Cheking name input
$name = "" ;
if(isset($_POST[$NAME_KEY_L] ) && $_POST[$NAME_KEY_L] != null)
 $name = $_POST[$NAME_KEY_L] ;
else if (isset($_GET[$NAME_KEY_L] ) && $_GET[$NAME_KEY_L] != null )
 $name = $_GET[$NAME_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_104 ;
  $ans[$MESSAGE_KEY_L] = $CODE_104_MESSAGE ;
  die(json_encode($ans));
}

//Cheking password input
$password = "" ;
if(isset($_POST[$PASSWORD_KEY_L] ) && $_POST[$PASSWORD_KEY_L] != null )
 $password = $_POST[$PASSWORD_KEY_L] ;
else if (isset($_GET[$PASSWORD_KEY_L] ) && $_GET[$PASSWORD_KEY_L] != null )
 $password = $_GET[$PASSWORD_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_102 ;
  $ans[$MESSAGE_KEY_L] = $CODE_102_MESSAGE ;
  die(json_encode($ans));
}

//Cheking password confirmation input
$password_confirmation = "" ;
if(isset($_POST[$PASSWORD_CONFIRMATION_KEY_L_S])  && $_POST[$PASSWORD_CONFIRMATION_KEY_L_S] != null )
 $password_confirmation = $_POST[$PASSWORD_CONFIRMATION_KEY_L_S] ;
else if (isset($_GET[$PASSWORD_CONFIRMATION_KEY_L_S]) && $_GET[$PASSWORD_CONFIRMATION_KEY_L_S] != null )
 $password_confirmation = $_GET[$PASSWORD_CONFIRMATION_KEY_L_S] ;
else {
  $ans[$CODE_KEY_L] = $CODE_103 ;
  $ans[$MESSAGE_KEY_L] = $CODE_103_MESSAGE ;
  die(json_encode($ans));
}

//Cheking major input
$major = "" ;
if(isset($_POST[$MAJOR_KEY_L]) && $_POST[$MAJOR_KEY_L] != null )
 $major = $_POST[$MAJOR_KEY_L] ;
else if (isset($_GET[$MAJOR_KEY_L]) && $_GET[$MAJOR_KEY_L] != null)
 $major = $_GET[$MAJOR_KEY_L] ;
else {
  $ans[$CODE_KEY_L] = $CODE_108 ;
  $ans[$MESSAGE_KEY_L] = $CODE_108_MESSAGE ;
  die(json_encode($ans));
}


#___________________________________________________________________________________________________________________________________________________
// Inputs validation check
#___________________________________________________________________________________________________________________________________________________

//email validaton
if(!isEmailValid($email)){
  $ans[$CODE_KEY_L] = $CODE_201  ;
  $ans[$MESSAGE_KEY_L] = $CODE_201_MESSAGE  ;
  die(json_encode($ans));
}

//name validaton
if(!isNameValid($name)){
  $ans[$CODE_KEY_L] = $CODE_204  ;
  $ans[$MESSAGE_KEY_L] = $CODE_204_MESSAGE  ;
  die(json_encode($ans));
}

//password validation
if(!isPasswordValid($password)){
  $ans[$CODE_KEY_L] = $CODE_202  ;
  $ans[$MESSAGE_KEY_L] = $CODE_202_MESSAGE  ;
  die(json_encode($ans));
}

//major validation
if(!isMajorIdValid($major)){
  $ans[$CODE_KEY_L] = $CODE_208  ;
  $ans[$MESSAGE_KEY_L] = $CODE_208_MESSAGE  ;
  die(json_encode($ans));
}

#___________________________________________________________________________________________________________________________________________________
// Password and Password confirmation match
#___________________________________________________________________________________________________________________________________________________

if($password != $password_confirmation ){
  $ans[$CODE_KEY_L] = $CODE_203 ;
  $ans[$MESSAGE_KEY_L] = $CODE_203_MESSAGE ;
  die(json_encode($ans));
}

#___________________________________________________________________________________________________________________________________________________
// Email existance and Repetitious
#___________________________________________________________________________________________________________________________________________________

  $email_existance_query = "SELECT *
  FROM user
  WHERE email = '$email' ";
  if($conn -> query($email_existance_query)->num_rows > 0){
    $ans[$CODE_KEY_L] = $CODE_302 ;
    $ans[$MESSAGE_KEY_L] = $CODE_302_MESSAGE ;
    die(json_encode($ans));
  }

 #___________________________________________________________________________________________________________________________________________________
  // Email verification
 #___________________________________________________________________________________________________________________________________________________
    $today = date("Y-m-d") ." ".date("H:i:s");
    $email_verification_query = "SELECT email_verified
    FROM email_verification
    WHERE email = '$email' AND '$today' < expire_at AND email_verified = 1 ; ";

    $result = $conn->query($email_verification_query)->fetch_assoc();

    $confirm_code = 0 ;
    if(isset($result[$EMAIL_VERIFIED_KEY_L_S]))
    $confirm_code = $result[$EMAIL_VERIFIED_KEY_L_S];

    if($confirm_code == 0){
      $ans[$CODE_KEY_L] = $CODE_303 ;
      $ans[$MESSAGE_KEY_L] = $CODE_303_MESSAGE ;
      die(json_encode($ans));
    }

  #___________________________________________________________________________________________________________________________________________________
    // Major existance
  #___________________________________________________________________________________________________________________________________________________
    $major_existance_sql = "SELECT * FROM major
    WHERE major_id = '$major' ";
    echo $major ;

    if($conn -> query($major_existance_sql)->num_rows == 0){
      $ans[$CODE_KEY_L] = $CODE_312 ;
      $ans[$MESSAGE_KEY_L] = $CODE_312_MESSAGE ;
      die(json_encode($ans));
    }
  #___________________________________________________________________________________________________________________________________________________
    // Insertion phase
  #___________________________________________________________________________________________________________________________________________________
    $today = date("Y-m-d") ." ".date("H:i:s");

    $register_query = "INSERT INTO user (email , name , password , major, payment , purchases , created_at , updated_at )
    VALUES('$email','$name','$password' , $major , '' , '[]' ,'$today' ,'$today' );
    " ;

    if ($result = $conn -> query($register_query)){
      $ans[$CODE_KEY_L] = 7898 ;
      $ans[$MESSAGE_KEY_L] = "Success!" ;
      die(json_encode($ans));
    }

    $conn->close();

?>

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


#getting user info using access token
 $today = date("Y-m-d") ." ".date("H:i:s");

 if(isset($_POST[$ACCESS_TOKEN_KEY_L_S] ))
  $access_token = $_POST[$ACCESS_TOKEN_KEY_L_S] ;
 else if (isset($_GET[$ACCESS_TOKEN_KEY_L_S] ))
  $access_token = $_GET[$ACCESS_TOKEN_KEY_L_S] ;
 else {
   $ans[$CODE_KEY_L] = $CODE_107 ;
   $ans[$MESSAGE_KEY_L] = $CODE_107_MESSAGE ;
   die(json_encode($ans));
 }

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

# end of getting user id

 $num_of_inputs = 0 ;
 $update_query = "UPDATE $USER_KEY_L
  SET" ;

 # name block
 if( isset($_REQUEST[$NAME_KEY_L])  && $_REQUEST[$NAME_KEY_L] != null )
 {
      $name = $_REQUEST[$NAME_KEY_L] ;

      if(!isNameValid($name)){

        $ans[$CODE_KEY_L] = $CODE_204 ;
        $ans[$MESSAGE_KEY_L] = $CODE_204_MESSAGE ;
        die(json_encode($ans));

      }

      $num_of_inputs ++ ;
      $update_query = $update_query." name = \"$name\" ,";

 }

 # password block
 if( (isset($_POST[$PASSWORD_KEY_L]) ||isset($_GET[$PASSWORD_KEY_L])) && $_REQUEST[$PASSWORD_KEY_L] != null )
 {
      $password = $_REQUEST[$PASSWORD_KEY_L];

      if(!isPasswordValid($password)){
        $ans[$CODE_KEY_L] = $CODE_202 ;
        $ans[$MESSAGE_KEY_L] = $CODE_202_MESSAGE ;
        die(json_encode($ans));
     }


      if(isset($_POST[$PASSWORD_CONFIRMATION_KEY_L_S]))
        $confrim_password = $_POST[$PASSWORD_CONFIRMATION_KEY_L_S] ;
      else if(isset($_GET[$PASSWORD_CONFIRMATION_KEY_L_S]))
        $confrim_password = $_GET[$PASSWORD_CONFIRMATION_KEY_L_S] ;
      else {
        $ans[$CODE_KEY_L] = $CODE_103 ;
        $ans[$MESSAGE_KEY_L] = $CODE_103_MESSAGE ;
        die(json_encode($ans));
       }

      if($password != $confrim_password ){
        $ans[$CODE_KEY_L] = $CODE_203 ;
        $ans[$MESSAGE_KEY_L] = $CODE_203_MESSAGE ;
        die(json_encode($ans));
      }

      $num_of_inputs ++ ;
      $update_query = $update_query." password = \"$password\" ,";

 }

$delete_old_email = false ;
$old_email = "";
$get_old_email_query = "";
 # e-mail block
 if( isset($_REQUEST[$EMAIL_KEY_L]) && $_REQUEST[$EMAIL_KEY_L] != null )
 {
      $email = $_REQUEST[$EMAIL_KEY_L] ;

      if(!isEmailValid($email)){
        $ans[$CODE_KEY_L] = $CODE_201 ;
        $ans[$MESSAGE_KEY_L] = $CODE_201_MESSAGE ;
        die(json_encode($ans));
      }

      if(EmailExists($email,$conn)){
        $ans[$CODE_KEY_L] = $CODE_302 ;
        $ans[$MESSAGE_KEY_L] = $CODE_302_MESSAGE ;
        die(json_encode($ans));
      }

      if(!EmailVerified($email,$conn)){
        $ans[$CODE_KEY_L] = $CODE_303 ;
        $ans[$MESSAGE_KEY_L] = $CODE_303_MESSAGE ;
        die(json_encode($ans));
      }
      $num_of_inputs ++ ;
      $update_query = $update_query." email = \"$email\" ,";
      $delete_old_email = true ;
      $get_old_email_query = "SELECT email FROM user
      WHERE user_id = '$userId' " ;
 }

 # major block
 if( isset($_REQUEST[$MAJOR_KEY_L]) && $_REQUEST[$MAJOR_KEY_L] != null )
 {
 $major = $_REQUEST[$MAJOR_KEY_L] ;

 if(!isMajorIdValid($major)){
   $ans[$CODE_KEY_L] = $CODE_221 ;
   $ans[$MESSAGE_KEY_L] = $CODE_221_MESSAGE ;
   die(json_encode($ans));
 }

 if(!majorExists($major,$conn)){
   $ans[$CODE_KEY_L] = $CODE_312 ;
   $ans[$MESSAGE_KEY_L] = $CODE_312_MESSAGE ;
   die(json_encode($ans));
 }

 $num_of_inputs ++ ;
 $update_query = $update_query." major = \"$major\" ,";

 }


 # payment block
 if( isset($_REQUEST[$PAYMENT_KEY_L]) && $_REQUEST[$PAYMENT_KEY_L] != null )
 {
 $payment = $_REQUEST[$PAYMENT_KEY_L] ;

 if(!isPaymentValid($payment)){
   $ans[$CODE_KEY_L] = $CODE_209 ;
   $ans[$MESSAGE_KEY_L] = $CODE_209_MESSAGE ;
   die(json_encode($ans));
 }

 $num_of_inputs ++ ;
 $update_query = $update_query." payment = \"$payment\" ,";

 }


 # purchases block
 if( isset($_REQUEST[$PURCHASES_KEY_L]) && $_REQUEST[$PURCHASES_KEY_L] != null )
 {
 $purchases = $_REQUEST[$PURCHASES_KEY_L] ;

 // validation skiped

 $num_of_inputs ++ ;
 $update_query = $update_query." purchases = \"$purchases\" ,";

 }


 # Adding current date-time as updated_at
 $today = date("Y-m-d") ." ".date("H:i:s");
 $update_query = $update_query." updated_at = \"$today\" ,";

 # final valid query
 $update_query = substr($update_query,0,strlen($update_query)-1)."WHERE user_id = \"$userId\" ".";";

//echo $update_query ;

# if all attributes are null
if($num_of_inputs > 0){
  if($delete_old_email){
    $result = $conn -> query($get_old_email_query);
    $old_email = $result->fetch_assoc()[$EMAIL_KEY_L];
  }

if ($result = $conn -> query($update_query)){
  //
  if($delete_old_email){
    $conn -> query("DELETE FROM email_verification
             where email = '$old_email' ; ");
  }
  $ans[$CODE_KEY_L] = 7898 ;
  $ans[$MESSAGE_KEY_L] = "Success" ;
  die(json_encode($ans));
}

}//else
//  die("No information updated");
//echo $ans[$MESSAGE_KEY_L];

$conn->close();

#functions
function EmailExists(String $email ,  mysqli $conn){

$sql = "SELECT *
FROM user
WHERE email = \"$email\" ";
if($conn -> query($sql)->num_rows == 0)
return false;

return true ;
}

function EmailVerified(String $email, mysqli $conn){
$today = date("Y-m-d") ." ".date("H:i:s");
$sql = "SELECT email_verified
FROM email_verification
WHERE email = \"$email\" AND '$today' < expire_at AND email_verified = 1 ; ";

$result = $conn->query($sql)->fetch_assoc();

$confirm_code = 0 ;
if(isset($result["email_verified"]))
$confirm_code = $result["email_verified"];
if($confirm_code == 0)
return false;

return true;
}

function majorExists($major_id,$conn){
$sql = "SELECT * FROM major
WHERE major_id = '$major_id' ";

if($conn -> query($sql)->num_rows == 0)
return false;

return true ;
}

?>

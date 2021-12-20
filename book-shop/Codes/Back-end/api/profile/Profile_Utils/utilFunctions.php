<?php
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
   $userId = $info["user_id"];
 }
 else
   {
     $ans[$CODE_KEY_L] = $CODE_311 ;
     $ans[$MESSAGE_KEY_L] = $CODE_311_MESSAGE ;
     die(json_encode($ans)); // Wrong access token
   }

   $expire_date = $info["expire_at"];
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

  switch($type){
    case "files" :
    $id = (int) $id ;
    $query = "SELECT file_id
    FROM file f
    WHERE f.file_id = $id ; "
     ;
     break ;

     case "images" :
     $id = (int) $id ;
     $query = "SELECT image_id
     FROM image i
     WHERE i.image_id = '$id'  ; "
      ;
      break ;

      case "tags" :
      $query = tagExists($id ,$conn );
      break;

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

       case "tags" :
       if($id[0]=="M"){
         $ans[$CODE_KEY_L] = 319 ;
         $ans[$MESSAGE_KEY_L] = "Requested Major does not exist." ;
       }

       if($id[0]=="I"){
         $ans[$CODE_KEY_L] = 317 ;
         $ans[$MESSAGE_KEY_L] = "Requested Instructor does not exist." ;
       }

       if($id[0]=="C"){
         $ans[$CODE_KEY_L] = 318 ;
         $ans[$MESSAGE_KEY_L] = "Requested Course does not exist." ;
       }

       break;

     }
     die(json_encode($ans));
   }

}

function tagExists($id ,  mysqli $conn){
$table = $id[0];
$tagId = (int)substr($id,1);
$query = "";
switch($table){
  case "M" :
  $query = "SELECT major_id
  FROM major m
  WHERE m.major_id = '$tagId'  ; ";
  break;

  case "I" :
  $query = "SELECT instructor_id
  FROM instructor i
  WHERE i.instructor_id = '$tagId'  ; ";
  break;

  case "C" :
  $query = "SELECT course_id
  FROM course c
  WHERE c.course_id = '$tagId'  ; ";
  break;

}

return $query ;
}

?>

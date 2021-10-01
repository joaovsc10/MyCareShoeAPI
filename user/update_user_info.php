<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  
// include database and object files
require_once (__DIR__.'\..\db\db_connect.php');
require_once 'user.php';
  
// get database connection
$database = new DbConnect();
$db = $database->getConnection();
  
// prepare patient object
$user = new User($db);
  
// get posted data
$data = array();
parse_str($_SERVER['QUERY_STRING'], $data);

// set patient's property values
$fields= array("profile_id", "email", "password", "username", "access_permission", "user_id");

foreach($fields as $field)
{

    
	if(isset($data[$field])){
        
		$user->$field = $data[$field];
	}else{
        if(isset($_POST[$field])){
            $data[$field]=$_POST[$field];
            $user->$field =$_POST[$field];
        }
    }

    if($field == "password")
        $data["password"] = sha1( $data["password"]);
}


// update the info
$json_update=$user->update($data);
  
echo json_encode($json_update);


?>
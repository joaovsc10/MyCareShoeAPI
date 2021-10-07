
<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once 'user.php';
require_once(__DIR__ . '\..\db\db_connect.php');


// instantiate database and product object
$database = new DbConnect();
$db = $database->getConnection();

$queries = array();
parse_str($_SERVER['QUERY_STRING'], $queries);

$username = "";

$password = "";

$email = "";

$usernameEmail = "";



if (isset($_POST['usernameEmail'])) {

	$usernameEmail = $_POST['usernameEmail'];
} else {

	if (isset($queries['usernameEmail']))

		$usernameEmail = $queries['usernameEmail'];
}

if (isset($usernameEmail)) {
	if (filter_var($usernameEmail, FILTER_VALIDATE_EMAIL)) {

		$email = $usernameEmail;
	} else {

		$username = $usernameEmail;
	}
}

if (isset($_POST['password'])) {

	$password = $_POST['password'];
} else {

	if (isset($queries['password']))

		$password = $queries['password'];
}


$userObject = new User($db);

// Login
if (!empty($password)) {

	$hashed_password = sha1($password);

	if (!empty($username))


		$json_array = $userObject->loginUsers($username, $hashed_password, 0);

	if (!empty($email))

		$json_array = $userObject->loginUsers($email, $hashed_password, 1);



	echo json_encode($json_array);
}



?>

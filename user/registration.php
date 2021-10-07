
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

$patient_number = "";


if (isset($_POST['username'])) {

    $username = $_POST['username'];
}

if (isset($_POST['password'])) {

    $password = $_POST['password'];
}


if (isset($_POST['email'])) {

    $email = $_POST['email'];
}

if (isset($_POST['patient_number'])) {

    $patient_number = $_POST['patient_number'];
}



$userObject = new User($db);


// Registration

if (!empty($username) && !empty($password) && !empty($email) && !empty($patient_number)) {

    $hashed_password = sha1($password);

    $json_registration = $userObject->createNewRegisterUser($username, $hashed_password, $email, $patient_number);

    echo json_encode($json_registration);
}


?>

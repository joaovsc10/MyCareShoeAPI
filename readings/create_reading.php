<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'sensors.php';
require_once (__DIR__.'\..\db\db_connect.php');

$emptyFound=FALSE;

$database = new DbConnect();
$db = $database->getConnection();

$sensors = new Sensors($db);



// make sure data is not empty
foreach($_POST as $element) {

	if(empty($element) && $element!=0){
		$emptyFound=TRUE;
	}

}

if(!$emptyFound)
{
    // set sensors property values
    $sensors->S1 = $_POST["S1"];
    $sensors->S2 = $_POST["S2"];
	$sensors->S3 = $_POST["S3"];
	$sensors->S4 = $_POST["S4"];
	$sensors->S5 = $_POST["S5"];
	$sensors->S6 = $_POST["S6"];
	$sensors->S7 = $_POST["S7"];
	$sensors->S8 = $_POST["S8"];
	$sensors->S9 = $_POST["S9"];
	$sensors->S10 =$_POST["S10"];
	$sensors->S11 = $_POST["S11"];
	$sensors->S12 = $_POST["S12"];
	$sensors->S13 = $_POST["S13"];
	$sensors->S14 = $_POST["S14"];
	$sensors->S15 = $_POST["S15"];
	$sensors->S16 = $_POST["S16"];
	$sensors->S17 = $_POST["S17"];
	$sensors->S18 = $_POST["S18"];
	$sensors->S19 = $_POST["S19"];
	$sensors->S20 = $_POST["S20"];
	$sensors->S21 = $_POST["S21"];
	$sensors->S22 = $_POST["S22"];
	$sensors->S23 = $_POST["S23"];
	$sensors->S24 = $_POST["S24"];
	$sensors->S25 = $_POST["S25"];
	$sensors->S26 = $_POST["S26"];
	$sensors->T1 = $_POST["T1"];
	$sensors->T2 = $_POST["T2"];
	$sensors->H1 = $_POST["H1"];
	$sensors->H2 = $_POST["H2"];
	$sensors->date = $_POST["date"];
	$sensors->patient_number = $_POST["patient_number"];

    // create the sensors
    if($sensors->create()){


        // set response code - 201 created
        http_response_code(201);

        // tell the user
        echo json_encode(array("success" => "1","message" => "Sensors reading was created.", "reading_id" => $db->lastInsertId() , "date" => $sensors->date));

    }

    // if unable to create the sensors, tell the user
    else{

        // set response code - 503 service unavailable
        http_response_code(503);

        // tell the user
        echo json_encode(array("success" => "0","message" => "Unable to create sensors reading."));
    }
}

// tell the user data is incomplete
else{

    // set response code - 400 bad request
    http_response_code(400);

    // tell the user
    echo json_encode(array("success" => "0", "message" => "Unable to create sensors reading. Data is incomplete."));
}

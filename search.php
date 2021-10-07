<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
require_once('readings\sensors.php');
require_once('statistics\statistics.php');
require_once('warnings\warning.php');
require_once('logs\logs.php');
require_once('db\db_connect.php');

// get database connection
$database = new DbConnect();
$db = $database->getConnection();


$queries = array();
parse_str($_SERVER['QUERY_STRING'], $queries);


if ((isset($queries['topic']) && $queries['topic'] == 'sensorsReading') || (isset($_POST['topic']) && $_POST['topic'] == 'sensorsReading'))


	$object = new Sensors($db);
else {
	if ((isset($queries['topic']) && $queries['topic'] == 'statistics') || (isset($_POST['topic']) && $_POST['topic'] == 'statistics'))
		$object = new Statistics($db);

	if ((isset($queries['topic']) && $queries['topic'] == 'logs') || (isset($_POST['topic']) && $_POST['topic'] == 'logs'))
		$object = new Logs($db);

	if ((isset($queries['topic']) && $queries['topic'] == 'warnings') || (isset($_POST['topic']) && $_POST['topic'] == 'warnings'))
		$object = new Warning($db);
}

// get keywords
$keywords = ['patient_number', 'day', 'hour', 'month', 'start_date', 'end_date', 'topic'];
$queries2 = array("patient_number" => "", "day" => "", "hour" => "", "month" => "", "start_date" => "",  "topic" => "");
foreach ($keywords as $keyword) {

	if (isset($_POST[$keyword]))
		$queries2[$keyword] = isset($_POST[$keyword]) ? $_POST[$keyword] : '';
	else
		$queries2[$keyword] = isset($queries[$keyword]) ? $queries[$keyword] : '';
}

if ($queries2['start_date'] != '' && $queries2['end_date'] != '') {

	$stmt = $object->searchByStartEndDate($queries2);
} else {
	// query sensor reading
	if ($queries2['day'] != '' && $queries2['hour'] == '') {
		$stmt = $object->searchByPatientDate($queries2);
	} else {
		if ($queries2['hour'] != '') {

			$stmt = $object->searchByPatientHour($queries2);
		} else {
			if ($queries2['month'] != '')
				$stmt = $object->searchByPatientMonth($queries2);
			else
				$stmt = $object->searchByPatient($queries2);
		}
	}
}
$num = $stmt->rowCount();


// check if more than 0 record found
if ($num > 0) {

	// sensor reading array
	$data_array = array();
	$data_array["records"] = array();

	// retrieve our table contents
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		// extract row
		// this will make $row['name'] to
		// just $name only
		extract($row);

		if ($queries2['topic'] == 'sensorsReading')

			$data_item = array(
				"reading_id" =>  $reading_id,
				"S1" => $S1,
				"S2" => $S2,
				"S3" => $S3,
				"S4" => $S4,
				"S5" => $S5,
				"S6" => $S6,
				"S7" => $S7,
				"S8" => $S8,
				"S9" => $S9,
				"S10" => $S10,
				"S11" => $S11,
				"S12" => $S12,
				"S13" => $S13,
				"S14" => $S14,
				"S15" => $S15,
				"S16" => $S16,
				"S17" => $S17,
				"S18" => $S18,
				"S19" => $S19,
				"S20" => $S20,
				"S21" => $S21,
				"S22" => $S22,
				"S23" => $S23,
				"S24" => $S24,
				"S25" => $S25,
				"S26" => $S26,
				"T1" => $T1,
				"T2" => $T2,
				"H1" => $H1,
				"H2" => $H2,
				"date" => $date,
				"patient_number" => $patient_number
			);
		else
		if ($queries2['topic'] == 'statistics')

			$data_item = array(
				"statistics_id" =>  $statistics_id,
				"cadence" => $cadence,
				"steps" => $steps,
				"balance" => $balance,
				"date" => $date,
				"patient_number" => $patient_number,
				"right_foot_stance" => $right_foot_stance,
				"left_foot_stance" => $left_foot_stance
			);

		if ($queries2['topic'] == 'warnings') {
			$data_item = array(
				"warning_id" =>  $warning_id,
				"patient_number" => $patient_number,
				"sensor" => $sensor,
				"warning_date" => $warning_date
			);
		}

		if ($queries2['topic'] == 'logs') {
			$data_item = array(
				"table_name" =>  $table_name,
				"tracking_id" => $tracking_id,
				"data_id" => $data_id,
				"field" => $field,
				"old_value" => $old_value,
				"new_value" => $new_value,
				"modified" => $modified
			);
		}

		array_push($data_array["records"], $data_item);
	}

	// set response code - 200 OK
	http_response_code(200);

	// show sensors reading data
	echo json_encode($data_array);
} else {
	// set response code - 404 Not found
	http_response_code(404);

	// tell the user no sensor readings data found
	echo json_encode(
		array("message" => "No data found.")
	);
}

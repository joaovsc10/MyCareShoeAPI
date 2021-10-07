<?php


require_once (__DIR__.'\..\db\db_connect.php');
	
	
class Patient{
	
  
    // database connection and table name
    private $db;
    private $table_name = "personal_info";
  
    // object properties
    public $gender;
    public $birth;
    public $height;
    public $weight;
    public $feet_size;
    public $diabetes;
    public $type_feet;
	public $name;
	public $patient_number;
    public $pressure_threshold;
    public $occurences_number;
    public $time_interval;
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
	
	function readAll(){
  
    // select all query
	  $query = "SELECT
                *
            FROM
                " . $this->table_name . " p
         
            ";
	   
	// prepare query statement
	$stmt = $this->conn->prepare($query);
	
	$stmt->execute();
	
    return $stmt;
	}

		// read patient's data
	function read($patient_number){
  
    // select all query
	  $query = "SELECT
                *
            FROM
                " . $this->table_name . " p
            WHERE
                p.patient_number = :patient_number
            ";
	   
	// prepare query statement
	$stmt = $this->conn->prepare($query);
	
	$stmt->bindParam(":patient_number", $patient_number);
  
        // execute query
    if($stmt->execute()){
	
		$num = $stmt->rowCount();
	}
  
    // get retrieved row
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
  
    $num = $stmt->rowCount();
  
  
	if($num==1){
    // set values to object properties
	$this->gender = $row['gender'];
    $this->birth = $row['birth'];
    $this->height = $row['height'];
    $this->weight = $row['weight'];
    $this->feet_size = $row['feet_size'];
    $this->diabetes = $row['diabetes'];
	$this->type_feet = $row['type_feet'];
	$this->name = $row['name'];
	$this->patient_number = $row['patient_number'];
    $this->pressure_threshold = $row['pressure_threshold'];
    $this->occurences_number = $row['occurences_number'];
    $this->time_interval = $row['time_interval'];
	
    return $stmt;
	}
}

function createPatientInfo($patient_number, $updateData){

    $query = "INSERT INTO " . $this->table_name . "(";
    $params = array();

    for($i=0; $i<2; $i++){

    if($i==1){
            $query = substr($query,0,-1);
            $query.= ") values (";
    }

    foreach($updateData as $key=>$value){
	  if($i==0){
        if(isset($key)){
            $query .= "$key,";      
        }
    }
    else{
        if(isset($key)){
            $query .= ":$key,";     
            $params[$key] = $value; 
        }
    }
    }

    if($i==1){
        $query = substr($query,0,-1).")";
    }


}


 // prepare query statement
 $stmt = $this->conn->prepare($query);
 
 // execute the query
 if($stmt->execute($params)){
     return true;
 }

 return false;

}

// update the patient
function update($updateData){


 $query="SELECT * FROM  " . $this->table_name . " WHERE patient_number = :patient_number";
 
 $stmt = $this->conn->prepare($query);
	
 $stmt->bindParam(":patient_number", $updateData['patient_number']);

 $stmt->execute();
 
 $row = $stmt->fetch(PDO::FETCH_ASSOC);
 
 if($stmt->rowCount() ==0){

    $return=$this->createPatientInfo($updateData['patient_number'], $updateData);

 }else{

  $query = "UPDATE " . $this->table_name . " SET ";
  $params = array();
  
  foreach($updateData as $key=>$value){
	  
	  if(isset($key)){
		  $query .= "$key = :$key, ";
		  
		$params[$key] = $value;
	  }
  }
  // Cut off last comma and append WHERE clause
$query = substr($query,0,-2)." WHERE patient_number = :patient_number";
// Store id for prepared statement

  

    // prepare query statement
    $stmt = $this->conn->prepare($query);
 
    // execute the query
    if($stmt->execute($params)){
        return true;
    }
  
    return false;
}

    return $return;
}
}

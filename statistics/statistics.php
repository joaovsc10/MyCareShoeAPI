<?php


    require_once (__DIR__.'\..\db\db_connect.php');
	
	
class Statistics{



    // database connection and table name
    private $db;
    private $table_name = "statistics";
  
    // object properties
    public $statistics_id;
    public $patient_number;
    public $cadence;
    public $steps;
    public $balance;
    public $date;
	public $right_foot_stance;
	public $left_foot_stance;
    
  
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
	
	
	function searchByStartEndDate($data){
  

	if($data['start_date']!=$data['end_date'])
	{
    $query = "SELECT
                *
            FROM
                " . $this->table_name . "
            WHERE 
			
			patient_number = :patient_number AND
			
			date 
			
			BETWEEN
			
                :start_date
			AND
				:end_date
	";
	
	$startEndDifferent=true;
	
	
	}
	else{
		
		$query = "SELECT
                *
            FROM
                " . $this->table_name . " s
            WHERE 
			
			DATE(s.date)= :start_date
			
			AND
			
			patient_number=:patient_number
	";
	$startEndDifferent=false;
		
	}
	 $stmt = $this->conn->prepare( $query );
	 
	if($startEndDifferent)
		$stmt->bindParam(":end_date", $data['end_date']);
	
	$stmt->bindParam(":start_date", $data['start_date']);
	$stmt->bindParam(":patient_number", $data['patient_number']);
  
    // prepare query statement
   
	
    // execute query
    if($stmt->execute()){
	
		return $stmt;
	}
	
}


	// create statistic
	function create(){
		
		
		// query to insert statistic
		$query = "INSERT INTO " . $this->table_name . " (cadence, steps,balance,date,patient_number, left_foot_stance, right_foot_stance) VALUES (:cadence, :steps, :balance,:date,:patient_number, :left_foot_stance, :right_foot_stance)";
	  
	 
	  // prepare query
      $stmt = $this->conn->prepare($query);


		// bind values
		$stmt->bindParam(":cadence", $this->cadence);
		$stmt->bindParam(":steps", $this->steps);
		$stmt->bindParam(":balance", $this->balance);
		$stmt->bindParam(":date", $this->date);
		$stmt->bindParam(":patient_number", $this->patient_number);
		$stmt->bindParam(":left_foot_stance", $this->left_foot_stance);
		$stmt->bindParam(":right_foot_stance", $this->right_foot_stance);
	

		// execute query
    if($stmt->execute()){

        return true;
    }
  
    return false;
		  
}
}

?>
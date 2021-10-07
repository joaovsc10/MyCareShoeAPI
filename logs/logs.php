<?php


require_once (__DIR__.'\..\db\db_connect.php');
	
	
class Logs{
	
  
    // database connection and table name
    private $db;
    private $db_table_name = "log_table";
  
    // object properties
    public $table_name;
    public $tracking_id;
    public $data_id;
	public $field;
	public $old_value;
	public $new_value;
	public $modified_date;
  
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
                " . $this->db_table_name . "
            WHERE 
			
			modified 
			
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
                " . $this->db_table_name . " l
            WHERE 
			
			DATE(l.modified)= :start_date
			
	";
	$startEndDifferent=false;
		
	}
	 $stmt = $this->conn->prepare( $query );
	 
	if($startEndDifferent)
		$stmt->bindParam(":end_date", $data['end_date']);
	
	$stmt->bindParam(":start_date", $data['start_date']);
  
    // prepare query statement
   
	
    // execute query
    if($stmt->execute()){
	
		return $stmt;
	}
	
}
}
?>

<?php

require_once(__DIR__ . '\..\db\db_connect.php');

class User
{

    private $db;

    private $db_table = "user";

    public $user_id;

    public $profile_id;

    public $email;

    public $password;

    public $username;

    public $patient_number;

    public $access_permission;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function isLoginExist($usernameEmail, $password, $isEmailSet)
    {

        if ($isEmailSet == 0)
            $query = "select * from " . $this->db_table . " where username = '$usernameEmail' AND password = '$password' Limit 1";

        elseif ($isEmailSet == 1)
            $query = "select * from " . $this->db_table . " where email = '$usernameEmail' AND password = '$password' Limit 1";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() == 1) {

            $this->user_id = $row['user_id'];
            $this->profile_id = $row['profile_id'];
            $this->email = $row['email'];
            $this->password = $row['password'];
            $this->username = $row['username'];
            $this->patient_number = $row['patient_number'];
            $this->access_permission = $row['access_permission'];
            $this->conn = null;


            return $row;
        }

        $this->conn = null;

        return false;
    }

    public function isDataExist($fieldName, $fieldValue, $isUpdate, $user_id)
    {


        $query = "select * from " . $this->db_table . " where " . $fieldName . " = :field";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":field", $fieldValue);

        // execute query
        $stmt->execute();
        if ($stmt->rowCount() > 0) {

            if ($isUpdate) {
                $query = "select " . $fieldName . " from " . $this->db_table . " where user_id = :user_id";
                $stmt = $this->conn->prepare($query);

                $stmt->bindParam(":user_id", $user_id);

                // execute query
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($stmt->rowCount() == 1) {

                    if ($row[$fieldName] != $fieldValue)

                        return $fieldName;
                }

                return false;
            } else {
                return $fieldName;
            }
        }

        return false;
    }

    public function existingDataValidation($username, $email, $patient_number, $isUpdate, $user_id)
    {

        $data = array();

        $isEmailExisting = $this->isDataExist("email", $email, $isUpdate, $user_id);
        $isUsernameExisting = $this->isDataExist("username", $username, $isUpdate, $user_id);

        if (isset($patient_number))
            $isPatientNumberExisting = $this->isDataExist("patient_number", $patient_number, $isUpdate, $user_id);


        if ($isEmailExisting != false || (isset($isPatientNumberExisting) && $isPatientNumberExisting != false) || $isUsernameExisting != false) {
            $json['message'] = "Error in registering. The following fields already exists: ";


            $data = array(
                "email" => $isEmailExisting,
                "username" => $isUsernameExisting
            );

            if (isset($isPatientNumberExisting)) {
                $data["patient_number"] = $isPatientNumberExisting;
            }
            foreach ($data as $key => $value) {

                if (isset($key) && $value != false) {
                    $json['message'] = $json['message'] . $key . ", ";
                }
            }

            $json['success'] = 0;
            $json['message'] = substr($json['message'], 0, -2) . ".";

            return $json;
        }

        return null;
    }


    public function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function createNewRegisterUser($username, $password, $email, $patient_number)
    {

        $json = $this->existingDataValidation($username, $email, $patient_number, false, null);

        if (!isset($json)) {

            $isValid = $this->isValidEmail($email);

            if ($isValid) {
                $query = "insert into " . $this->db_table . " (username, password, email, patient_number) values (:username, :password, :email, :patient_number)";

                // prepare query statement
                $stmt = $this->conn->prepare($query);

                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":password", $password);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":patient_number", $patient_number);



                if ($stmt->execute()) {

                    $json['success'] = 1;
                    $json['message'] = "Successfully registered the user";
                } else {

                    $json['success'] = 0;
                    $json['message'] = "Error in registering. Probably the username/email already exists";
                }

                $this->conn = null;
            } else {
                $json['success'] = 0;
                $json['message'] = "Error in registering. Email Address is not valid";
            }
        }

        return $json;
    }

    public function loginUsers($usernameEmail, $password, $isEmailSet)
    {

        $json = array();

        $canUserLogin = $this->isLoginExist($usernameEmail, $password, $isEmailSet);


        if ($canUserLogin != false && $this->access_permission == "1") {


            $json['success'] = 1;
            $json['message'] = "Successfully logged in";
            $json['user'] = $canUserLogin;

            session_start();

            $_SESSION['username'] = $this->username;
            $_SESSION['id'] = $this->user_id;
            $_SESSION['profile_id'] = $this->profile_id;

            if($_SESSION['profile_id']==2)
              header("location: http://localhost/mycareshoewebsite/site/index.php");
            else {
              header("location: http://localhost/mycareshoewebsite/site/admin.php");
            }
        } else {
            $json['success'] = sha1("patient");
            if ($canUserLogin && $this->access_permission == "0")
                $json['message'] = "Access denied";
            else
                $json['message'] = "Invalid username or password";
            header("location: http://localhost/mycareshoewebsite/site/log_in.php");
        }
        return $json;
    }

    function update($updateData)
    {

        if(isset($updateData['username']) && isset($updateData['username']))
          $json = $this->existingDataValidation($updateData['username'], $updateData['email'], null, true, $updateData['user_id']);

        if (!isset($json)) {

            $query = "UPDATE " . $this->db_table . " SET ";
            $params = array();

            foreach ($updateData as $key => $value) {

                if (isset($key)) {
                    $query .= "$key = :$key, ";

                    $params[$key] = $value;
                }
            }
            // Cut off last comma and append WHERE clause
            $query = substr($query, 0, -2) . " WHERE user_id = :user_id";
            // Store id for prepared statement



            // prepare query statement
            $stmt = $this->conn->prepare($query);

            // execute the query
            if ($stmt->execute($params)) {

                $json['success'] = 1;
                $json['message'] = "Successfully updated the user information";
            } else {

                $json['success'] = 0;
                $json['message'] = "Error in updating user information";
            }
        }

        return $json;
    }
}
?>

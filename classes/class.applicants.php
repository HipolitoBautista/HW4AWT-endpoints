<?php

class Applicants extends Handler {

    function __construct(){
        parent::__construct();
    }

    function __destruct(){

    }

    public function GET($requestParameters){

        $response["rc"] = -1;
        $response["message"] = "Invalid Student Request";

        $request = isset($requestParameters[1]) ? $requestParameters[1] : -1;

        $this->log->info("get fx ran request: [" . $request . "]");

        $response = $request < 1 ? $this->getAllApplicants() : $this->getSingleApplicant($request); 

        return $response;
    }

    public function POST($requestParameters){
        // Fetch the raw POST data
        $rawData = file_get_contents("php://input");

        // Decode the JSON data into a PHP array
        $data = json_decode($rawData, true);
        $response = "";
    
        // Check if the data exists
        if ($data) {
            $response = $this->InputNewUser($data);
        } else {
            // no data found
            $response = ([
                'status' => 'error',
                'message' => 'No data provided.'
            ]);
        }

        return $response;
    }

    public function PUT($requestParameters){
        
        $response["rc"] = -1;
        $response["message"] = "Invalid Student Request";

        if($requestParameters[1]) { 
        // Fetch the raw POST data
        $rawData = file_get_contents("php://input");
        

        // Decode the JSON data into a PHP array
        $data = json_decode($rawData, true);
        $response = "";
    
        // Check if the data exists
        if ($data) {
            $response = $this->UpdateUser($data, $requestParameters[1]);
        } else {
            // no data found
            $response = ([
                'status' => 'error',
                'message' => 'No data provided to update.'
            ]);
        }
        } else {
            $response = ([
                'status' => 'error',
                'message' => 'enter the ID of the user you want updated'
            ]);
        }

        
        return $response;
    }
    
    public function DELETE($requestParameters){
        
        $response["rc"] = -1;
        $response["message"] = "User does not exist";

        if($requestParameters[1]) { 
            $response = $this->deleteUser($requestParameters[1]);
        } else {
            $response = ([
                'status' => 'error',
                'message' => 'enter the ID of the user you want deleted'
            ]);
        }

        
        return $response;
    }
    
    public function getAllApplicants(){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no applicants found";
    
        try {
            $sql = "SELECT * FROM applicants";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No applicant records found.");
            }
    
            $applicants = array();
            while ($row = $result->fetch_assoc()) {
                $applicants[] = $row;  // Fixed the typo here
            }
    
            $stmt->close();
            $stmt = null;
    
            $response["rc"] = "1";
            $response["log"] = "Success";
            $response["applicants"] = $applicants; 

        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }

    public function getSingleApplicant($id){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no applicants found";
    
        try {
            $sql = "SELECT * FROM applicants WHERE id = $id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
    
            $result = $stmt->get_result();
            if ($result->num_rows < 1) {
                throw new Exception("No applicant records found.");
            }
    
            $applicants = array();
            while ($row = $result->fetch_assoc()) {
                $applicants[] = $row;
            }
    
            $stmt->close();
            $stmt = null;
    
            $response["rc"] = "1";
            $response["log"] = "Successfully inputted user";
            $response["applicants"] = $applicants;

        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }

    public function InputNewUser($data){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "no applicant data found";

        $id = $data['id'];
        $firstName = $data['first_name'];
        $lastName = $data['last_name'];
        $country = $data['country'];
        $username = $data['username'];
        $password = $data['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO applicants (id, first_name, last_name, country, username, password) 
            VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$id, $firstName, $lastName, $country, $username, $hashedPassword]);
            
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully inputted user";
            } else {
                throw new Exception("Unable to input");  // Fixed this line
            }
            
            $stmt->close();
            $stmt = null;

        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
        return $response;
    }

    public function UpdateUser($data, $id){
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "Failed to update applicant data";
    
        $id = $id;
        $firstName = $data['first_name'];
        $lastName = $data['last_name'];
        $country = $data['country'];
        $username = $data['username'];
        $password = $data['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        try {
            // Using the SQL UPDATE statement to update the user's information
            $sql = "UPDATE applicants 
                    SET first_name = ?, last_name = ?, country = ?, username = ?, password = ? 
                    WHERE id = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$firstName, $lastName, $country, $username, $hashedPassword, $id]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully updated user";
            } else {
                throw new Exception("Unable to update user");
            }
    
            $stmt->close();
            $stmt = null;
    
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
    
        return $response;
    }
    
    public function deleteUser($id) {
        $response = array();
        $response["rc"] = "-1";
        $response["log"] = "Failed to delete applicant.";
    
        try {
            // SQL statement to delete a user from the applicants table where id matches
            $sql = "DELETE FROM applicants WHERE id = ?";
    
            $stmt = $this->db->prepare($sql);
            $stmt_status = $stmt->execute([$id]);
    
            if ($stmt_status) {
                $response["rc"] = "1";
                $response["log"] = "Successfully deleted applicant with ID: " . $id;
            } else {
                throw new Exception("No applicant found with ID: " . $id);
            }
    
            $stmt->close();
            $stmt = null;
    
        } catch (Exception $e) {
            $this->log->error("[$e]");
            $response["log"] = $e->getMessage();
        }
    
        return $response;
    }
    
}

?>

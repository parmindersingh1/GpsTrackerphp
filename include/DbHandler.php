<?php
 
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class DbHandler {
 
    private $conn;
 
    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
 
    /* ------------- `users` table method ------------------ */
 
    /**
     * Creating new user
     * @param String $name User full name
     * @param String $vehicle_reg_no User login vehicle_reg_no id
     * @param String $mobile User mobile number
     * @param String $otp user verificaiton code
     */
    public function createUser($name, $vehicle_reg_no, $mobile, $otp) {
        $response = array();
 
        // First check if user already existed in db
        if (!$this->isUserExists($mobile)) {
              
            // insert query
            $stmt = $this->conn->prepare("INSERT INTO users(name, vehicle_reg_no, mobile, status) values(?, ?, ?, 0)");
            $stmt->bind_param("sss", $name, $vehicle_reg_no, $mobile);
 
            $result = $stmt->execute();
 
            $new_user_id = $stmt->insert_id;
 
            $stmt->close();
 
            // Check for successful insertion
            if ($result) {
 
                $otp_result = $this->createOtp($new_user_id, $otp);
 
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same vehicle_reg_no already existed in the db
            return USER_ALREADY_EXISTED;
        }
 
        return $response;
    }
 
    public function createOtp($user_id, $otp) {
 
        // delete the old otp if exists
        $stmt = $this->conn->prepare("DELETE FROM sms_codes where user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
 
 
        $stmt = $this->conn->prepare("INSERT INTO sms_codes(user_id, code, status) values(?, ?, 0)");
        $stmt->bind_param("is", $user_id, $otp);
 
        $result = $stmt->execute();
 
        $stmt->close();
 
        return $result;
    }
     
    /**
     * Checking for duplicate user by mobile number
     * @param String $vehicle_reg_no vehicle_reg_no to check in db
     * @return boolean
     */
    private function isUserExists($mobile) {
        $stmt = $this->conn->prepare("SELECT id from users WHERE mobile = ? and status = 1");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

     private function getActiveUserId($mobile) {
        $stmt = $this->conn->prepare("SELECT id FROM users   WHERE mobile = ? and status = 1");
        $stmt->bind_param("s", $mobile);
 
        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($id);
             
            $stmt->store_result();
 
            if ($stmt->num_rows > 0) {
                 
                $stmt->fetch();                
                                
                $stmt->close();
                 
                return $id;
            } else {
                return NULL;
            }
        } else {
            return NULL;
        } 
    }
 
    public function activateUser($otp) {
        $stmt = $this->conn->prepare("SELECT u.id, u.name, u.vehicle_reg_no, u.mobile, u.status, u.created_at FROM users u, sms_codes WHERE sms_codes.code = ? AND sms_codes.user_id = u.id");
        $stmt->bind_param("s", $otp);
 
        if ($stmt->execute()) {
            // $user = $stmt->get_result()->fetch_assoc();
            $stmt->bind_result($id, $name, $vehicle_reg_no, $mobile, $status, $created_at);
             
            $stmt->store_result();
 
            if ($stmt->num_rows > 0) {
                 
                $stmt->fetch();
                 
                // activate the user
                $this->activateUserStatus($id);
                 
                $user = array();
                $user["name"] = $name;
                $user["vehicle_reg_no"] = $vehicle_reg_no;
                $user["mobile"] = $mobile;
                // $user["status"] = $status;
                // $user["created_at"] = $created_at;
                 
                $stmt->close();
                 
                return $user;
            } else {
                return NULL;
            }
        } else {
            return NULL;
        } 
    }
     
    public function activateUserStatus($user_id){
        $stmt = $this->conn->prepare("UPDATE users set status = 1 where id = ?");
        $stmt->bind_param("i", $user_id);
         
        $stmt->execute();
         
        $stmt = $this->conn->prepare("UPDATE sms_codes set status = 1 where user_id = ?");
        $stmt->bind_param("i", $user_id);
         
        $stmt->execute();
    }

  public function createLocation($mobile,$latitude,$longitude,$uuid,$vehicle_id,$event_type,$gpsTime) {
         $user_id = $this->getActiveUserId($mobile);
         if ($user_id != NULL) {            
            $stmt = $this->conn->prepare("INSERT INTO `gps`.`locations` (`user_id`, `latitude`, `longitude`, `uuid`, `vehicle_id`,event_type,gpsTime) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iddssss", $user_id, $latitude,$longitude,$uuid,$vehicle_id,$event_type,$gpsTime);
     
            $result = $stmt->execute();
     
            $stmt->close();
            return $result;
         } else {
            return NULL;
         } 
          
          // For deggugiing

         //   $stmt = $this->conn->prepare("INSERT INTO `gps`.`locations` (`user_id`, `latitude`, `longitude`, `uuid`, `vehicle_id`) VALUES (?, ?, ?, ?, ?)");
         //   // prepare() can fail because of syntax errors, missing privileges, ....
         //    if ( false===$stmt ) {
         //      // and since all the following operations need a valid/ready statement object
         //      // it doesn't make sense to go on
         //      // you might want to use a more sophisticated mechanism than die()
         //      // but's it's only an example
         //      array_push($res,'prepare() failed: ' . htmlspecialchars($mysqli->error));
         //    }

         //    $rc = $stmt->bind_param("iddss", $user_id, $latitude,$longitude,$uuid,$vehicle_id);
         //    // bind_param() can fail because the number of parameter doesn't match the placeholders in the statement
         //    // or there's a type conflict(?), or ....
         //    if ( false===$rc ) {
         //      // again execute() is useless if you can't bind the parameters. Bail out somehow.
         //      array_push($res,'bind_param() failed: ' . htmlspecialchars($stmt->error));
         //    }

         //    $rc = $stmt->execute();
         //    // execute() can fail for various reasons. And may it be as stupid as someone tripping over the network cable
         //    // 2006 "server gone away" is always an option
         //    if ( false===$rc ) {
         //      array_push($res,'execute() failed: ' . htmlspecialchars($stmt->error));
         //    }

         //    $stmt->close();
         // return $res;
        
  }

  public function logoutUser($mobile) {
     $user_id = $this->getActiveUserId($mobile);
     if ($user_id != NULL) {
        $stmt = $this->conn->prepare("INSERT INTO archived_users(id, name, mobile, vehicle_reg_no) SELECT id, name, mobile, vehicle_reg_no FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Delete Dependencies
        $stmt = $this->conn->prepare("DELETE FROM sms_codes where user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Delete Dependencies
        $stmt = $this->conn->prepare("DELETE FROM images where user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");           
        $stmt->bind_param("i", $user_id);           
        $result_delete = $stmt->execute();                    
        
 
        $stmt->close();
 
        return  $result_delete;
     } 
     return NULL;
  }


  public function uploadImage($mobile,$base64) {
     $user_id = $this->getActiveUserId($mobile);
     if ($user_id != NULL) {
        $stmt = $this->conn->prepare("SELECT id FROM images WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($image_id);
        $stmt->store_result();
        $num_rows = $stmt->num_rows;        
         if($num_rows > 0) {   
            $stmt->fetch();
            $stmt = $this->conn->prepare("UPDATE images SET image = ? WHERE id = $image_id");
            $stmt->bind_param("s",$base64);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
         } else {
            $stmt = $this->conn->prepare("INSERT INTO images(user_id, image) VALUES(?,?)");
            $stmt->bind_param("is", $user_id, $base64 );     
            $result = $stmt->execute();            
            $stmt->close();
            return $result;
         }       
            
     } 
     return NULL;
  }
  
}
?>

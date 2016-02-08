<?php
   

// Procedure
// DELETE FROM gpslocations
  // WHERE sessionID = _sessionID;

   require_once dirname(__FILE__) . '/api/include/DbConnect.php';
     $db = new DbConnect();
     $conn = $db->connect();

     $sessionid   = isset($_GET['sessionid']) ? $_GET['sessionid'] : '0';
     
     $sql =   "DELETE FROM locations WHERE session_id = ?";
    
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $sessionid);
    $response = $stmt->execute();  
    $stmt->close();
    echo $response
?>

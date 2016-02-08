<?php
  
// Procedure
// CREATE DEFINER=`root`@`localhost` PROCEDURE `prcGetRouteForMap`(
// _sessionID VARCHAR(50))
// BEGIN
//   SELECT CONCAT('{ 
     // \"latitude\":\"', CAST(loc.latitude AS CHAR),'\",
     // \"longitude\":\"', CAST(loc.longitude AS CHAR),'\",
     // \"gpsTime\":\"', DATE_FORMAT(loc.gpsTime, '%b %e %Y %h:%i%p'), '\",
     // \"sessionID\":\"', CAST(loc.session_id AS CHAR), '\",
     // \"vehicle\":\"', CAST(loc.vehicle_id AS CHAR), '\",
     // \"userName\":\"', u.name, '\" }') json
//   FROM locations
//   WHERE sessionID = _sessionID
//   ORDER BY lastupdate;
// END ;;

   require_once dirname(__FILE__) . '/api/include/DbConnect.php';
     $db = new DbConnect();
     $conn = $db->connect();

     $sessionid   = isset($_GET['sessionid']) ? $_GET['sessionid'] : '0';

     
     $sql =   "SELECT CONCAT('{ 
     \"latitude\":\"', CAST(loc.latitude AS CHAR),'\",
     \"longitude\":\"', CAST(loc.longitude AS CHAR),'\",
     \"gpsTime\":\"', DATE_FORMAT(loc.gpsTime, '%b %e %Y %h:%i%p'), '\",
     \"sessionID\":\"', CAST(loc.session_id AS CHAR), '\",
     \"vehicle\":\"', CAST(loc.vehicle_id AS CHAR), '\",
     \"userName\":\"', u.name, '\" }') AS json
     FROM locations as loc JOIN users as u on u.id = loc.user_id WHERE session_id = ?  ORDER BY gpsTime";

    
    $stmt = $conn->prepare($sql);


    $stmt->bind_param("s", $sessionid);
    $stmt->execute();
     
    $stmt->bind_result($json);
             
    $stmt->store_result();

    $json_response = '{ "locations": [';
    while($stmt->fetch()) {
        $json_response .= $json; 
        $json_response .= ',';
    }
   
    $json_response = rtrim($json_response, ",");
    $json_response .= '] }';
     $stmt->close();
    header('Content-Type: application/json');
    echo $json_response;

?>
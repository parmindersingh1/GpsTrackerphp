<?php
//  // Mysql procedure
// CREATE PROCEDURE `prcGetAllRoutesForMap`()
// BEGIN
// SELECT loc.session_id, loc.gpsTime, CONCAT('{ 
//  "latitude":"', CAST(loc.latitude AS CHAR),'",
//  "longitude":"', CAST(loc.longitude AS CHAR),'",
//  "gpsTime":"', DATE_FORMAT(loc.gpsTime, '%b %e %Y %h:%i%p'), '",
//  "sessionID":"', CAST(loc.session_id AS CHAR), '",
//  "vehicle":"', CAST(loc.vehicle_id AS CHAR), '",
//  "userName":"', u.name, '" }') json
// FROM (SELECT MAX(id) ID
//       FROM locations
//       WHERE session_id != '0' && CHAR_LENGTH(session_id) != 0 && gpsTime != '0000-00-00 00:00:00'
//       GROUP BY session_id) AS MaxID
// JOIN locations as loc ON loc.id = MaxID.ID JOIN users as u on u.id = loc.user_id
// ORDER BY gpsTime;
// END 

    require_once dirname(__FILE__) . '/api/include/DbConnect.php';
     $db = new DbConnect();
     $conn = $db->connect();

    $response = array();
     $sql =   "SELECT loc.session_id, loc.gpsTime, CONCAT('{ 
     \"latitude\":\"', CAST(loc.latitude AS CHAR),'\",
     \"longitude\":\"', CAST(loc.longitude AS CHAR),'\",
     \"gpsTime\":\"', DATE_FORMAT(loc.gpsTime, '%b %e %Y %h:%i%p'), '\",
     \"sessionID\":\"', CAST(loc.session_id AS CHAR), '\",
     \"vehicle\":\"', CAST(loc.vehicle_id AS CHAR), '\",
     \"userName\":\"', u.name, '\" }') AS json
    FROM (SELECT MAX(id) ID
          FROM locations
          WHERE session_id != '0' && CHAR_LENGTH(session_id) != 0 && gpsTime != '0000-00-00 00:00:00'
          GROUP BY session_id) AS MaxID
    JOIN locations as loc ON loc.id = MaxID.ID JOIN users as u on u.id = loc.user_id
    ORDER BY gpsTime";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
     
    $stmt->bind_result($session_id,$gpsTime,$json);
             
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

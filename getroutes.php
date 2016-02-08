<?php
    
//Procedure
// CREATE DEFINER=`root`@`localhost` PROCEDURE `prcGetRoutes`()
// BEGIN
//   CREATE TEMPORARY TABLE tempRoutes (
//     sessionID VARCHAR(50),
//     userName VARCHAR(50),
//     startTime DATETIME,
//     endTime DATETIME)
//   ENGINE = MEMORY;

//   INSERT INTO tempRoutes (sessionID, userName)
//   SELECT DISTINCT sessionID, userName
//   FROM gpslocations;

//   UPDATE tempRoutes tr
//   SET startTime = (SELECT MIN(gpsTime) FROM gpslocations gl
//   WHERE gl.sessionID = tr.sessionID
//   AND gl.userName = tr.userName);

//   UPDATE tempRoutes tr
//   SET endTime = (SELECT MAX(gpsTime) FROM gpslocations gl
//   WHERE gl.sessionID = tr.sessionID
//   AND gl.userName = tr.userName);

//   SELECT

//   CONCAT('{ "sessionID": "', CAST(sessionID AS CHAR),  '", "userName": "', userName, '", "times": "(', DATE_FORMAT(startTime, '%b %e %Y %h:%i%p'), ' - ', DATE_FORMAT(endTime, '%b %e %Y %h:%i%p'), ')" }') json
//   FROM tempRoutes
//   ORDER BY startTime DESC;

//   DROP TABLE tempRoutes;
// END ;;

    require_once dirname(__FILE__) . '/api/include/DbConnect.php';
     $db = new DbConnect();
     $conn = $db->connect();

    $response = array();
    $response_json = array();


    $sql = "SELECT DISTINCT CAST(loc.session_id AS CHAR), u.name, 
    DATE_FORMAT((SELECT MIN(gpsTime) from locations as minloc WHERE minloc.session_id = loc.session_id
     AND minloc.user_id = loc.user_id), '%b %e %Y %h:%i%p') AS startTime,
    DATE_FORMAT((SELECT MAX(gpsTime) from locations as maxloc WHERE maxloc.session_id = loc.session_id
     AND maxloc.user_id = loc.user_id), '%b %e %Y %h:%i%p') AS endTime
     FROM locations as loc join users as u on loc.user_id = u.id";
    $stmt = $conn->prepare($sql);

    $stmt->execute();
     
    $stmt->bind_result($session_id,$user,$startTime,$endTime);
             
    $stmt->store_result();

    $response_json = '{ "routes": [';

    $tempArray = array();
    while($stmt->fetch()) {
        $tempArray["sessionID"] = $session_id;
        $tempArray["userName"] = $user;
        $tempArray["times"] = $startTime." - ".$endTime;
        $response_json .= json_encode($tempArray);
        $response_json .= ',';      
    }
     $response_json = rtrim($response_json, ",");
     $response_json .= '] }';
    $stmt->close();
     echo $response_json;
    
?>

<?php
 
include './include/DbHandler.php';
$db = new DbHandler();
header('Content-Type: application/json');
 
$response = array();

if(isset($_POST['locations'])) {
    $data = json_decode($_POST['locations'], true);
    if (is_array($data['upload_locations'])) {

        foreach ($data['upload_locations'] as $record) {
                $latitude = isset($record['latitude'])? $record['latitude']: '0';
                $latitude = (float)str_replace(",", ".", $latitude); // to handle European locale decimals
                $longitude = isset($record['longitude'])?$record['longitude'] : '0';
                $longitude      = (float)str_replace(",", ".", $longitude);
                $uuid = isset($record['uuid'])? $record['uuid']: '0';
                $vehicle_id = isset($record['vehicle_id'])? $record['vehicle_id']: '0';
                $gpsTime = isset($record['gpsTime'])? $record['gpsTime']: '0000-00-00 00:00:00';
                $gpsTime    = urldecode($gpsTime);
                $mobile = isset($record['mobile'])? $record['mobile']: '';
                $event_type = isset($record['event_type'])? $record['event_type']: '';
                $session_id = isset($record['session_id'])? $record['session_id']: '';

                if($mobile != '') {
                    $location_result = $db->createLocation($mobile,$latitude,$longitude,$uuid,$vehicle_id,$event_type,$session_id,$gpsTime);
                    if ($location_result != NULL) {     
                        $response["error"] = false;     
                        $response["message"] = "Location created successfully!";        
                    } else {
                        $response["error"] = true;
                        $response["message"] = "Sorry! Failed to create Location.";
                    }

                }  else {
                     $response["error"] = true;
                     $response["message"] = "Sorry! Mobile is missing.";
                }         
      }        
    } 
    else {
        $response["error"] = true;
        $response["message"] = "Sorry! Not Array";
    }
} else {
    $response["error"] = true;
    $response["message"] = "Sorry! Mobile is missing.";
}
// $data = json_decode($_POST['upload_locations'], true);

// $latitude = isset($_POST['latitude'])? $_POST['latitude']: '0';
// $latitude = (float)str_replace(",", ".", $latitude); // to handle European locale decimals
// $longitude = isset($_POST['longitude'])?$_POST['longitude'] : '0';
// $longitude      = (float)str_replace(",", ".", $longitude);
// $uuid = isset($_POST['uuid'])? $_POST['uuid']: '0';
// $vehicle_id = isset($_POST['vehicle_id'])? $_POST['vehicle_id']: '0';
// $gpsTime = isset($_POST['gpsTime'])? $_POST['gpsTime']: '0000-00-00 00:00:00';
// $gpsTime    = urldecode($gpsTime);
// $mobile = isset($_POST['mobile'])? $_POST['mobile']: '';
// $event_type = isset($_POST['event_type'])? $_POST['event_type']: '';
// $session_id = isset($_POST['session_id'])? $_POST['session_id']: '';

 

// if (isset($_POST['mobile']) && $_POST['mobile'] != '') {   
 
//     $location_result = $db->createLocation($mobile,$latitude,$longitude,$uuid,$vehicle_id,$event_type,$session_id,$gpsTime);
 
//     if ($location_result != NULL) {     
//         $response["error"] = false;		
//         $response["message"] = "Location created successfully!";        
//     } else {
//     	$response["error"] = true;
//         $response["message"] = "Sorry! Failed to create Location.";
//     }
// } else {
//     $response["error"] = true;
//     $response["message"] = "Sorry! Mobile is missing.";
// }
 
 
echo json_encode($response);
?>

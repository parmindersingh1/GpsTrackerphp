<?php
 
include './include/DbHandler.php';
$db = new DbHandler();
header('Content-Type: application/json');
 
$response = array();


$latitude = isset($_POST['latitude'])? $_POST['latitude']: '0';
$latitude = (float)str_replace(",", ".", $latitude); // to handle European locale decimals
$longitude = isset($_POST['longitude'])?$_POST['longitude'] : '0';
$longitude      = (float)str_replace(",", ".", $longitude);
$uuid = isset($_POST['uuid'])? $_POST['uuid']: '0';
$vehicle_id = isset($_POST['vehicle_id'])? $_POST['vehicle_id']: '0';
$gpsTime = isset($_POST['gpsTime'])? $_POST['gpsTime']: '0000-00-00 00:00:00';
$gpsTime    = urldecode($gpsTime);
$mobile = isset($_POST['mobile'])? $_POST['mobile']: '';
$event_type = isset($_POST['event_type'])? $_POST['event_type']: '';

 
if (isset($_POST['mobile']) && $_POST['mobile'] != '') {   
 
    $location_result = $db->createLocation($mobile,$latitude,$longitude,$uuid,$vehicle_id,$event_type,$gpsTime);
 
    if ($location_result != NULL) {     
        $response["error"] = false;		
        $response["message"] = "Location created successfully!";        
    } else {
    	$response["error"] = true;
        $response["message"] = "Sorry! Failed to create Location.";
    }
} else {
    $response["error"] = true;
    $response["message"] = "Sorry! Mobile is missing.";
}
 
 
echo json_encode($response);
?>

<?php
 
include './include/DbHandler.php';
$db = new DbHandler();
 header('Content-Type: application/json');
 
$response = array();
$response["error"] = false;
 
if (isset($_POST['regId']) && $_POST['regId'] != '' && isset($_POST['mobile']) && $_POST['mobile'] != '') {
    $regId = $_POST['regId'];
    $mobile = $_POST['mobile'];
 
    $result = $db->updateUserGcmRegId($regId, $mobile);
 
    if ($result != NULL) { 		
        $response["message"] = "GCM Registration Id Updated successfully!";
    } else {
    	$response["error"] = true;
        $response["message"] = "Sorry! Failed to create your account.";
    }
     
     
} else {
    $response["error"] = true;
    $response["message"] = "Sorry! Mobile OR GCM Registration Id is missing.";
}
 
 
echo json_encode($response);
?>

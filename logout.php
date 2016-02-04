<?php
 
include './include/DbHandler.php';
$db = new DbHandler();
header('Content-Type: application/json');
 
$response = array();
$response["error"] = false;
 
if (isset($_POST['mobile']) && $_POST['mobile'] != '') {
    $mobile = $_POST['mobile'];
 
 
    $result = $db->logoutUser($mobile);
 
    if ($result != NULL) { 		
        $response["message"] = "User Logged out successfully!";
    } else {
    	$response["error"] = true;
        $response["message"] = "Sorry! Failed to Logout your account.";
    }

     
} else {
    $response["error"] = true;
    $response["message"] = "Sorry! Mobile is missing.";
}
 
 
echo json_encode($response);
?>

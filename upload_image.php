<?php
 
include './include/DbHandler.php';
$db = new DbHandler();
 header('Content-Type: application/json');
 
$response = array();
$response["error"] = false;
$response["message"] = "Image Uploaded successfully!"+$_POST['mobile'];
 
if (isset($_POST['mobile']) && $_POST['mobile'] != '') {
    $mobile = $_POST['mobile'];
 	$image = $_POST['image'];
 
    $result = $db->uploadImage($mobile,$image);
 
    if ($result != NULL) { 		
        $response["message"] = "Image Uploaded successfully!";
    } else {
    	$response["error"] = true;
        $response["message"] = "Sorry! Failed to Upload Image.";
    }
     
     
} else {
    $response["message"] = "Sorry! Mobile is missing.";
}
 
 
echo json_encode($response);
?>

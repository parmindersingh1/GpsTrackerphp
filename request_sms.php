<?php
 
include './include/DbHandler.php';
$db = new DbHandler();
 header('Content-Type: application/json');
 
$response = array();
 
if (isset($_POST['mobile']) && $_POST['mobile'] != '') {
 
    $name = $_POST['name'];
    $vehicle_reg_no = $_POST['vehicle_reg_no'];
    $mobile = $_POST['mobile'];
 
    $otp = rand(100000, 999999);
 
    $res = $db->createUser($name, $vehicle_reg_no, $mobile, $otp);
 
    if ($res == USER_CREATED_SUCCESSFULLY) {
         
        // send sms
        sendSms($mobile, $otp);
         
        $response["error"] = false;
        $response["message"] = "SMS request is initiated! You will be receiving it shortly.";
    } else if ($res == USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Sorry! Error occurred in registration.";
    } else if ($res == USER_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Mobile number already existed!";
    }
} else {
    $response["error"] = true;
    $response["message"] = "Sorry! mobile number is not valid or missing.";
}
 
echo json_encode($response);
 
function sendSms($mobile, $otp) {
    $sendsms ="";
    $otp_prefix = ':';
 
    //Your message to send, Add URL encoding here.
    $message = "Hello! Welcome to Gps. Your OPT is $otp_prefix $otp";
 
    $response_type = 'json';
 
    //Define route 
    $route = "4";
     
    //Prepare you post parameters
    $postData = array(       
        'To' => $mobile,
        'Message' => $message,
        'UserName' => USERNAME,
        'Password' => PASSWORD,
        'Mask' => MASK,
        'Type' => TYPE
    );


    //We need to URL encode the values
    foreach($postData as $key=>$val)
    {
    $sendsms.= $key."=".urlencode($val);
    $sendsms.= "&"; //append the ampersand (&) sign after each parameter/value
    }

    $sendsms = substr($sendsms, 0, strlen($sendsms)-1);//remove last ampersand (&) sign from the sendsms
    $url = "http://www.smsgatewaycenter.com/library/send_sms_2.php?".$sendsms;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    //get response
    $output = curl_exec($ch);
 
    //Print error if any
    if (curl_errno($ch)) {
        echo 'error:' . curl_error($ch);
    }
 
    curl_close($ch);
}
?>

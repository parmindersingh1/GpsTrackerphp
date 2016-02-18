<?php
if (isset($_GET["regId"]) && isset($_GET["message"])) {
    $regId = $_GET["regId"];
    $message = $_GET["message"];
     
    include_once './api/include/gcm_sendmsg.php';
     
    $gcm = new GCM_SendMsg();
 
    $registatoin_ids = array($regId);
    $message = array("message" => $message);
 
    $result = $gcm->send_notification($registatoin_ids, $message);
 
    echo $result;
}
?>
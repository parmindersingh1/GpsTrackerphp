<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.5/darkly/bootstrap.min.css">
        <link rel="stylesheet" href="css/styles.css">
        
    </head>
    <body>
        <?php
        require_once dirname(__FILE__) . '/api/include/DbConnect.php';
        $db = new DbConnect();
        $conn = $db->connect();

        $stmt = $conn->prepare("select id, gcm_regid FROM users WHERE status = 1 AND gcm_regid IS NOT NULL AND gcm_regid <> '' ");
        $stmt->execute();        
             
        $stmt->store_result();

        $no_of_users = $stmt->num_rows;        

        $stmt->bind_result($id, $gcm_regid);     
        ?>
        <div >
            <h1>No of Devices Registered: <?php echo $no_of_users; ?></h1>
            <hr/>
            <ul class="devices">
                <?php
                if ($no_of_users > 0) {
                    ?>
                    <?php
                    while ($stmt->fetch()) {
                        ?>
                        <li>
                            <form id="<?php echo $id ?>" name="" method="post" onsubmit="return sendPushNotification('<?php echo $id ?>')">
                                <label>RegistrationID: </label> <span><?php echo $gcm_regid ?></span>
                                <br><br>                              
                                <textarea rows="3" name="message" cols="25" class="txt_message" placeholder="Type message here"></textarea>
                                <input type="hidden" name="regId" value="<?php echo $gcm_regid ?>"/>
                                <input type="submit" class="send_btn" value="Send" onclick=""/>
                                </div>
                            </form>
                        </li>
                    <?php }
                } else { ?> 
                    <li>
                        No Users Registered Yet!
                    </li>
                <?php  } 
                $stmt->close();
                ?>
            </ul>
        </div>


        <script type="text/javascript">
            $(document).ready(function(){
                
            });
            function sendPushNotification(id){
                var data = $('form#'+id).serialize();
                $('form#'+id).unbind('submit');                
                $.ajax({
                    url: "device_sendmsg.php",
                    type: 'GET',
                    data: data,
                    beforeSend: function() {
                         
                    },
                    success: function(data, textStatus, xhr) {
                          $('.txt_message').val("");
                    },
                    error: function(xhr, textStatus, errorThrown) {
                         
                    }
                });
                return false;
            }
        </script>

    </body>
</html>
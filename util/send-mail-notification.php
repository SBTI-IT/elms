<?php

    $toEmail = "dipolelodips@gmail.com";
    $subject = "Leave Application";
    $body = "Hello someone applied for a leave click here to approve 192.168.27.121/elms/supervisor/";
    $headers = "From: sender\'s email";

    if(mail($toEmail, $subject, $body, $headers))
        echo "Email successfully sent to $toEmail...";
    else
        echo "Email sending failed...";

?>
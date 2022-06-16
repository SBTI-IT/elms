<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attachment</title>
</head>
<body>
<?php
    if(!empty($_GET['file']))
    {
        $fileName = basename($_GET['file']);
        $filePath = '../attachments/'.$fileName;
        
        if(!empty($fileName) && file_exists($filePath)){
            // Define headers
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Content-Type: application/zip");
            header("Content-Transfer-Encoding: binary");
            
            // Read the file
            readfile($filePath);
            exit;
        }else{
            echo "<h2>File attachment was removed from server.</h2>";
            echo "<h3>Contact IT support for further assistance</h3>";
        }
    }
?>
</body>
</html>
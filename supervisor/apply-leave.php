<?php
session_start();
error_reporting(0);
include('includes/config.php');

require ('../vendor/PHPMailer/src/Exception.php');
require ('../vendor/PHPMailer/src/PHPMailer.php');
require ('../vendor/PHPMailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;

if(strlen($_SESSION['superlogin'])==0)
{   
    header('location:index.php');
}
else
{
    if( isset($_POST['apply']) )
    {
        $err = 0;
        // File name
        $fileName = $_FILES['file']['name'];
        // File upload path
        $targetDir = "../attachments/" . basename($fileName);
        $fileType = pathinfo($targetDir, PATHINFO_EXTENSION);
        
        if( isset($fileName) )
        {
            $allowTypes = array('jpg','png','docx','pdf');
            if(in_array($fileType, $allowTypes))
            {
                if( move_uploaded_file($_FILES['file']['tmp_name'], $targetDir) )
                {
                    $msg = "The file ".$fileName. " has been uploaded successfully."; 

                    $empid=$_SESSION['eid'];
                    $leavetype=$_POST['leavetype'];
                    $fromdate=$_POST['fromdate'];  
                    $todate=$_POST['todate'];
                    $description=$_POST['description']; 
                    $status=0;
                    $isread=0;

                    date_default_timezone_set('Africa/Johannesburg');
                    $fDay = date("d", strtotime($fromdate));
                    $fMonth = date("m", strtotime($fromdate));
                    $fYear = date("Y", strtotime($fromdate));

                    $tDay = date("d", strtotime($todate));
                    $tMonth = date("m", strtotime($todate));
                    $tYear = date("Y", strtotime($todate));

                    if( $tMonth >= $fMonth & $tYear >= $fYear)
                    {
                        $fDate = strtotime($fromdate);
                        $tDate = strtotime($todate);
                        
                        $daysTaken = $tDate - $fDate;
                        $daysTaken = abs(round($daysTaken / 86400)) + 1;

                        $empid = $_SESSION['eid'];
                        $cDays = 0;

                        switch($leavetype)
                        {
                            case "Annual":
                                $q= $dbh->prepare("SELECT Annual_lv FROM employees WHERE id=:empid");
                                $q->bindParam(':empid', $empid, PDO::PARAM_STR);
                                $q->execute();
                                $r = $q->fetchAll(PDO::FETCH_OBJ);
                                $c = 0;

                                foreach($r as $result)
                                    $cDays = $result->Annual_lv;                                   

                                if($Days > 0 & $daysTaken <= $cDays)
                                {
                                    $cDays = $cDays - $daysTaken;
                                    $query = $dbh->prepare("UPDATE employees SET Annual_lv = :annual WHERE id=:empid");
                                    $query->bindParam(':empid', $empid, PDO::PARAM_STR);
                                    $query->bindParam(':annual', $cDays, PDO::PARAM_STR);
                                    $query->execute();
                                }
                                else
                                {
                                    $error="You have used up your annual leaves";
                                    $err = 1;
                                }
                                break;
                            case "Medical Leave": 
                                $q= $dbh->prepare("SELECT Medical_lv FROM employees WHERE id=:empid");
                                $q->bindParam(':empid', $empid, PDO::PARAM_STR);
                                $q->execute();
                                $r = $q->fetchAll(PDO::FETCH_OBJ);

                                foreach($r as $result)
                                    $cDays = $result->Medical_lv;                                   

                                if($cDays > 0 & $daysTaken <= $cDays)
                                {
                                $cDays = $cDays - $daysTaken;
                                $query = $dbh->prepare("UPDATE employees SET Medical_lv = :annual WHERE id=:empid");
                                $query->bindParam(':empid', $empid, PDO::PARAM_STR);
                                $query->bindParam(':annual', $cDays, PDO::PARAM_STR);
                                $query->execute();
                                }
                                else {
                                    $error = "You have used up your medical leaves";
                                    $err = 1;
                                }
                                break;
                            case "Study Leave": 
                                $q= $dbh->prepare("SELECT Study_lv FROM employees WHERE id=:empid");
                                $q->bindParam(':empid', $empid, PDO::PARAM_STR);
                                $q->execute();
                                $r = $q->fetchAll(PDO::FETCH_OBJ);

                                foreach($r as $result)
                                    $cDays = $result->Study_lv;                                   

                                if($cDays > 0 & $daysTaken <= $cDays)
                                {
                                    $cDays = $cDays - $daysTaken;
                                    $query = $dbh->prepare("UPDATE employees SET Study_lv = :annual WHERE id=:empid");
                                    $query->bindParam(':empid', $empid, PDO::PARAM_STR);
                                    $query->bindParam(':annual', $cDays, PDO::PARAM_STR);
                                    $query->execute();
                                }
                                else {
                                    $error = "You have used up your study leaves";
                                    $err = 1;
                                }

                                break;
                            case "Compassionate Leave": 
                                $q= $dbh->prepare("SELECT Maternity_lv FROM employees WHERE id=:empid");
                                $q->bindParam(':empid', $empid, PDO::PARAM_STR);
                                $q->execute();
                                $r = $q->fetchAll(PDO::FETCH_OBJ);

                                foreach($r as $result)
                                    $cDays = $result->Maternity_lv;                                   

                                if($cDays > 0 & $daysTaken <= $cDays) 
                                {
                                    $cDays = $cDays - $daysTaken;
                                    $query = $dbh->prepare("UPDATE employees SET Maternity_lv = :annual WHERE id=:empid");
                                    $query->bindParam(':empid', $empid, PDO::PARAM_STR);
                                    $query->bindParam(':annual', $cDays, PDO::PARAM_STR);
                                    $query->execute();
                                }
                                else
                                {
                                    $error = "You have used up your maternity leaves";
                                    $err = 1;
                                }
                                    
                                break;
                            case "Unpaid Leave": 
                                $q= $dbh->prepare("SELECT Unpaid_lv FROM employees WHERE id=:empid");
                                $q->bindParam(':empid', $empid, PDO::PARAM_STR);
                                $q->execute();
                                $r = $q->fetchAll(PDO::FETCH_OBJ);

                                foreach($r as $result)
                                    $cDays = $result->Unpaid_lv;                                   

                                if($cDays > 0 & $daysTaken <= $cDays)
                                {
                                    $cDays = $cDays - $daysTaken;
                                    $query = $dbh->prepare("UPDATE employees SET Unpaid_lv = :annual WHERE id=:empid");
                                    $query->bindParam(':empid', $empid, PDO::PARAM_STR);
                                    $query->bindParam(':annual', $cDays, PDO::PARAM_STR);
                                    $query->execute();
                                }
                                else
                                {
                                    $error = "You have used up your unpaid leaves";
                                    $err = 1;
                                }

                                break;
                        }

                        if($err == 0)
                        {
                            $sql="INSERT INTO leaves(LeaveType,ToDate,FromDate,Description,Attachment,Status,IsRead,empid) VALUES(:leavetype,:fromdate,:todate,:description,:attachment,:status,:isread,:empid)";
                            $query = $dbh->prepare($sql);
                            $query->bindParam(':leavetype',$leavetype,PDO::PARAM_STR);
                            $query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
                            $query->bindParam(':todate',$todate,PDO::PARAM_STR);
                            $query->bindParam(':description',$description,PDO::PARAM_STR);
                            $query->bindParam(':attachment',basename($fileName),PDO::PARAM_STR);
                            $query->bindParam(':status',$status,PDO::PARAM_STR);
                            $query->bindParam(':isread',$isread,PDO::PARAM_STR);
                            $query->bindParam(':empid',$empid,PDO::PARAM_STR);
                            $query->execute();
                            $lastInsertId = $dbh->lastInsertId();

                            $department = $_SESSION['department'];
                            $myName = $_SESSION['firstName'];
                            $rid = 2;

                            if($lastInsertId)
                            {
                                $sql1 = "SELECT * FROM employees WHERE Department=:department AND RoleID=:rid";
                                $query2 = $dbh->prepare($sql1);
                                $query2->bindParam(':department', $department, PDO::PARAM_STR);
                                $query2->bindParam(':rid', $rid, PDO::PARAM_STR);
                                $query2->execute();
                                $res = $query2->fetchAll(PDO::FETCH_OBJ);  

                                foreach($res as $result)
                                {
                                    $recipient = $result->EmailId;
                                    $firstName = $result->FirstName;
                                }              

                                $mail = new PHPMailer();
                            
                                $mail->isSMTP();
                                $mail->Host = "smtp.gmail.com";
                                $mail->SMTPAuth = true;
                                $mail->Username = "leaveapplications@softstartbti.co.za"; // SMTP Email here
                                $mail->Password = "CPEJ%G5e"; // Email password here
                                $mail->SMTPSecure = 'tls';
                                $mail->Port = 587;

                                $mail->setFrom('leaveapplications@softstartbti.co.za', $_SESSION['myName']); // Set from email
                                //$mail->addAddress('ayanda@softstartbti.co.za', 'ELMS');
                                $mail->addAddress('leaveapplications@softstartbti.co.za', 'ELMS');
                                $mail->Subject = '[LEAVE APPLICATION]';
                                
                                $mail->isHTML(true);

                                $mailContent = "<h2>Good day [Ayanda],</h2>
                                                <h3>Kindly view my leave application on the LMS.</h3>
                                                <h3>Kind regards</h3>";

                                $mail->Body = $mailContent;

                                if(!$mail->Send())
                                    $error = 'Mailer Error: '.$mail->ErrorInfo;
                                else
                                    $msg = "Leave applied successfully";
                            }
                            else
                                $error = "Sorry, there was an error uploading your file.";
                        }
                    }
                    else
                        $error=" ToDate should be greater than FromDate ";
                }
                else
                    $error = "Sorry, there was an error uploading your file.";
            }
            else 
                $error = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
        }
        else
            $error = "Please attach additional documents";
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Supervisor | Apply Leave</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="assets/plugins/materialize/css/materialize.min.css"/>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet"> 
        <link href="assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/custom.css" rel="stylesheet" type="text/css"/>
  <style>
        .errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #dd3d36;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
    background: #fff;
    border-left: 4px solid #5cb85c;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
        </style>
 


    </head>
    <body>
  <?php include('includes/header.php');?>
            
       <?php include('includes/sidebar.php');?>
   <main class="mn-inner">
                <div class="row">
                    <div class="col s12">
                        <div class="page-title">Apply for Leave</div>
                    </div>
                    <div class="col s12 m12 l8">
                        <div class="card">
                            <div class="card-content">
                                <form id="example-form" method="post" name="addemp" enctype="multipart/form-data">
                                    <div>
                                        <h3>Apply for Leave</h3>
                                        <section>
                                            <div class="wizard-content">
                                                <div class="row">
                                                    <div class="col m12">
                                                        <div class="row">
                                                            <?php if($error){?><div class="errorWrap"><strong>ERROR </strong>: <?php echo htmlentities($error); ?> </div><?php } 
                                                                else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div><?php }?>

                                                                <div class="input-field col s12">
                                                                <select  name="leavetype" autocomplete="off">
                                                                <option value="">Select leave type...</option>
                                                                <?php $sql = "SELECT  LeaveType from leavetype";
                                                                $query = $dbh->prepare($sql);
                                                                $query->execute();
                                                                $results=$query->fetchAll(PDO::FETCH_OBJ);
                                                                $cnt=1;
                                                                if($query->rowCount() > 0)
                                                                {
                                                                foreach($results as $result)
                                                                {   ?>                                            
                                                                <option value="<?php echo htmlentities($result->LeaveType);?>"><?php echo htmlentities($result->LeaveType);?></option>
                                                                <?php }} ?>
                                                                </select>
                                                                </div>
                                                                
                                                                <div class="input-field col m6 s12">
                                                                <label for="fromdate">From  Date</label>
                                                                <input placeholder="" id="mask1" name="fromdate" class="masked" type="text" data-inputmask="'alias': 'dd-mm-yyyy'" required>
                                                                </div>
                                                                <div class="input-field col m6 s12">
                                                                <label for="todate">To Date</label>
                                                                <input placeholder="" id="mask1" name="todate" class="masked" type="text" data-inputmask="'alias': 'dd-mm-yyyy'" required>
                                                                </div>
                                                                <div class="input-field col m12 s12">
                                                                <label for="birthdate">Description</label>    

                                                                <textarea id="textarea1" name="description" class="materialize-textarea" length="500" required></textarea>
                                                                </div>
                                                                <!--MY CODE MY CODE -->
                                                                <div class="col m12 s12">
                                                                    <label for="file">Additional Documents </label>
                                                                    <input id="file" type="file" name="file">    
                                                                </div>
                                                        </div>
                                                            <button type="submit" name="apply" id="apply" class="waves-effect waves-light btn orange m-b-xs">Apply</button>                                             
                                                    </div>
                                            </div>
                                        </section>
                                        </section>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div class="left-sidebar-hover"></div>
        
        <!-- Javascripts -->
        <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="assets/js/alpha.min.js"></script>
        <script src="assets/js/pages/form_elements.js"></script>
          <script src="assets/js/pages/form-input-mask.js"></script>
                <script src="assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>
    </body>
</html>
<?php } ?> 

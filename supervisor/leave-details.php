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
else{

    // code for update the read notification status
    $isread=1;
    $did=intval($_GET['leaveid']);  
    date_default_timezone_set('Africa/Johannesburg');
    $admremarkdate=date('d-m-Y G:i:s ', strtotime("now"));
    $sql="update leaves set IsRead=:isread where id=:did";
    $query = $dbh->prepare($sql);
    $query->bindParam(':isread',$isread,PDO::PARAM_STR);
    $query->bindParam(':did',$did,PDO::PARAM_STR);
    $query->execute();

    // code for action taken on leave
    if(isset($_POST['update']))
    { 
        $did=intval($_GET['leaveid']);
        $description=$_POST['description'];
        $status=$_POST['status'];   
        date_default_timezone_set('Africa/Johannesburg');
        $admremarkdate=date('d-m-Y G:i:s ', strtotime("now"));
        $sql="update leaves set AdminRemark=:description,Status=:status,AdminRemarkDate=:admremarkdate where id=:did";
        $query = $dbh->prepare($sql);
        $query->bindParam(':description',$description,PDO::PARAM_STR);
        $query->bindParam(':status',$status,PDO::PARAM_STR);
        $query->bindParam(':admremarkdate',$admremarkdate,PDO::PARAM_STR);
        $query->bindParam(':did',$did,PDO::PARAM_STR);
        $query->execute();
        
        
        $msg="Leave updated successfully";
    }
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Supervisor | Leave Details </title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="../assets/plugins/materialize/css/materialize.min.css"/>
        <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="../assets/plugins/material-preloader/css/materialPreloader.min.css" rel="stylesheet">
        <link href="../assets/plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet">

                <link href="../assets/plugins/google-code-prettify/prettify.css" rel="stylesheet" type="text/css"/>  
        <!-- Theme Styles -->
        <link href="../assets/css/alpha.min.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/custom.css" rel="stylesheet" type="text/css"/>
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
                        <div class="page-title" style="font-size:24px;">Leave Details</div>
                    </div>
                   
                    <div class="col s12 m12 l12">
                        <div class="card">
                            <div class="card-content">
                                <span class="card-title">Leave Details</span>
                                <?php if($msg){?><div class="succWrap"><strong>SUCCESS</strong>: <?php echo htmlentities($msg); ?> </div><?php }?>
                                <table id="example" class="display responsive-table ">
                               
                                 
                                    <tbody>
<?php 
$lid=intval($_GET['leaveid']);
$sql = "SELECT leaves.id as lid,employees.FirstName,employees.LastName,employees.EmpId,employees.id,employees.Gender,employees.Phonenumber,employees.EmailId,leaves.LeaveType,leaves.ToDate,leaves.FromDate,leaves.Description,leaves.Attachment,leaves.PostingDate,leaves.Status,leaves.AdminRemark,leaves.AdminRemarkDate from leaves join employees on leaves.empid=employees.id where leaves.id=:lid";
$query = $dbh -> prepare($sql);
$query->bindParam(':lid',$lid,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{         
    ?>  
        <tr>
            <td style="font-size:16px;"> <b>Employee Name :</b></td>
                <td><a href="editemployee.php?empid=<?php echo htmlentities($result->id);?>" target="_blank">
                <?php echo htmlentities($result->FirstName." ".$result->LastName);?></a></td>
                <td style="font-size:16px;"><b>Emp ID :</b></td>
                <td><?php echo htmlentities($result->EmpId);?></td>
                <td style="font-size:16px;"><b>Gender :</b></td>
                <td><?php echo htmlentities($result->Gender);?>
            </td>
        </tr>

        <tr>
            <td style="font-size:16px;"><b>Emp Email ID :</b></td>
        <td><?php echo htmlentities($result->EmailId);?></td>
            <td style="font-size:16px;"><b>Emp Contact No.:</b></td>
        <td><?php echo htmlentities($result->Phonenumber);?></td>
        <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td style="font-size:16px;"><b>Leave Type :</b></td>
            <td><?php echo htmlentities($result->LeaveType);?></td>
                <td style="font-size:16px;"><b>Leave Date :</b></td>
            <td>From <?php echo htmlentities($result->ToDate);?> to <?php echo htmlentities($result->FromDate);?></td>
            <td style="font-size:16px;"><b>Posting Date</b></td>
            <td><?php echo htmlentities($result->PostingDate);?></td>
        </tr>

        <tr>
            <td style="font-size:16px;"><b>Employee Leave Description : </b></td>
            <td colspan="5"><?php echo htmlentities($result->Description);?></td>            
        </tr>

        <tr>
            <td style="font-size:16px;"><b>Attachment :</b></td>
            <td colspan="5"><a href="../util/download-attach.php?file=<?php echo htmlentities($result->Attachment);?>" target ="_blank">
            <?php echo htmlentities($result->Attachment);?></a></td>                             
        </tr>
    <tr>
        <td style="font-size:16px;"><b>Leave Status :</b></td>
        <td colspan="5"><?php $stats=$result->Status;
        
        if($stats==1){
        ?>
            <span style="color: green">Approved</span>
            <?php } if($stats==2)  { ?>
            <span style="color: red">Not Approved</span>
            <?php } if($stats==0)  { ?>
            <span style="color: blue">Waiting for approval</span>
        <?php } 
        
        if($stats != 0)
        {
            $mail = new PHPMailer();
            
            $supervisorName = $_SESSION['myName'];

            $recName = $result->FirstName;
            $recipient = $result->EmailId;
            $description = $_POST['description'];

            switch($stats)
            {
                case 1: $aStatus = "Approved"; break;
                case 2: $aStatus = "Not Approved"; break;
            }

            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = ""; // SMTP Email here
            $mail->Password = ""; // Email password here
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('leaveapplications@softstartbti.co.za', 'ELMS'); // Set from email
            //$mail->addAddress($recipient,'LMS');
            $mail->addAddress($recipient, 'ELMS');
            $mail->Subject = '[LEAVE APPLICATION]';
            $mail->isHTML(true);

            $mailContent = "<body style=font-family:verdana>
                            <p><b>Good day $recName,
                            <br><br>The status of your application was changed to: <span style=color:green>$aStatus</span>
                            <br>Remark: $description
                            <br><br>Regards,
                            <b>$supervisorName</b></p>
                            <h4 style=color:red>This is an automated email sent by SoftstartBTI ELMS. Do not reply.</h4>
                            </body>";

            $mail->Body = $mailContent;

            if(!$mail->Send())
                $error = 'Mailer Error: '.$mail->ErrorInfo;
            }
        ?>
        </td>
    </tr>
<tr>
<td style="font-size:16px;"><b>Admin Remark : </b></td>
<td colspan="5"><?php
if($result->AdminRemark==""){
  echo "Waiting for approval";  
}
else{
echo htmlentities($result->AdminRemark);
}
?></td>
 </tr>

 <tr>
<td style="font-size:16px;"><b>Admin Action taken on : </b></td>
<td colspan="5"><?php
if($result->AdminRemarkDate==""){
  echo "NA";  
}
else{
echo htmlentities($result->AdminRemarkDate);
}
?></td>
 </tr>
<?php 
if($stats==0)
{

?>
<tr>
 <td colspan="5">
  <a class="modal-trigger waves-effect waves-light btn" href="#modal1">Take&nbsp;Action</a>
<form name="adminaction" method="post">
<div id="modal1" class="modal modal-fixed-footer" style="height: 60%">
    <div class="modal-content" style="width:90%">
        <h4>Leave take action</h4>
          <select class="browser-default" name="status" required="">
                                            <option value="">Choose your option</option>
                                            <option value="1">Approved</option>
                                            <option value="2">Not Approved</option>
                                        </select></p>
                                        <p><textarea id="textarea1" class="materialize-textarea" name="description" placeholder="Description" length="500" maxlength="500"></textarea></p>
    </div>
    <div class="modal-footer" style="width:90%">
       <input type="submit" class="waves-effect waves-light btn orange m-b-xs" name="update" value="Submit">
    </div>

</div>   

 </td>
</tr>
<?php } ?>
   </form>                                     </tr>
                                         <?php $cnt++;} }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
         
        </div>
        <div class="left-sidebar-hover"></div>
        
        <!-- Javascripts -->
        <script src="../assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="../assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="../assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="../assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="../assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
        <script src="../assets/js/alpha.min.js"></script>
        <script src="../assets/js/pages/table-data.js"></script>
         <script src="assets/js/pages/ui-modals.js"></script>
        <script src="assets/plugins/google-code-prettify/prettify.js"></script>
        
    </body>
</html>
<?php } ?>

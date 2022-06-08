
<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(isset($_POST['signin']))
{
$uname=$_POST['username'];
$password=md5($_POST['password']);
$sql ="SELECT EmailId,Password,Status,RoleID,id FROM employees WHERE EmailId=:uname and Password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':uname', $uname, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
    foreach ($results as $result) 
    {
        $status=$result->Status;
        $_SESSION['eid']=$result->id;
        $_SESSION['roleID'] = $result->RoleID;
    }

    if($status==0)
    {
        $msg="Your account is Inactive. Please contact admin";
    } 
    else 
    {
        if($_SESSION['roleID'] == 0)
        {
            echo "<script type='text/javascript'> document.location = 'admin/dashboard.php'; </script>";
            $_SESSION['alogin']=$_POST['username'];
        }   
        else if($_SESSION['roleID'] == 1)
        {
            echo "<script type='text/javascript'> document.location = 'leavehistory.php'; </script>";
            $_SESSION['emplogin']=$_POST['username'];
        }      
    } 
}
else
{
  echo "<script>alert('Incorrect Username and/ Password');</script>";
}
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <!-- Title -->
        <title>Employee leave management system |  Employee</title>
        
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
    </head>
    
    <body class="signin-page">
        <div class="mn-content valign-wrapper">

            <main class="mn-inner container">
  
                <div class="valign">
                      <div class="row">

                          <div class="col s12 m6 l4 offset-l4 offset-m3">
                              <div class="card white darken-1">
                              
                                  <div class="card-content ">
                                  <span class="card-title"><img src="assets/images/softstartImage.png" alt="SIGN IN" style="width:100%"></span>
                                        <div class="card-body">
                                            <div class="row">
                                            <form class="col s12" name="signin" method="post">
                                                <div class="input-field col s12">
                                                    <input id="username" type="text" name="username" class="validate" autocomplete="on" required >
                                                    <label for="email">Username</label>
                                                </div>
                                                <div class="input-field col s12">
                                                    <input id="password" type="password" class="validate" name="password" autocomplete="on" required>
                                                    <label for="password">Password</label>
                                                </div>
                                                <a class="col s12 center-align" href="forgot-password.php">Forgot password?</a>
                                                <div class="col s12 center-align m-t-sm">
                                                    <input type="submit" name="signin" value="Sign in" class="waves-effect waves-light btn orange">
                                                </div>
                                            </form>
                                            </div>
                                        </div>
                                  </div>
                              </div>
                          </div>
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Javascripts -->
        <script src="assets/plugins/jquery/jquery-2.2.0.min.js"></script>
        <script src="assets/plugins/materialize/js/materialize.min.js"></script>
        <script src="assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
        <script src="assets/plugins/jquery-blockui/jquery.blockui.js"></script>
        <script src="assets/js/alpha.min.js"></script>
        
    </body>
</html>
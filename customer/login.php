<?php 
session_start();
include('dbcon.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Gym System - Customer Login</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/matrix-login.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

<div id="loginbox">            
    <form id="loginform" class="form-vertical" method="POST" action="">
        <div class="control-group normal_text"> <h3>Customer Login</h3></div>
        
        <div class="control-group">
            <div class="controls">
                <div class="main_input_box">
                    <span class="add-on bg_lg"><i class="icon-user"> </i></span>
                    <input type="text" name="username" placeholder="Username" required/>
                </div>
            </div>
        </div>
        
        <div class="control-group">
            <div class="controls">
                <div class="main_input_box">
                    <span class="add-on bg_ly"><i class="icon-lock"></i></span>
                    <input type="password" name="password" placeholder="Password" required/>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <span class="pull-left"><a href="../index.php" class="btn btn-info">&laquo; Go Home</a></span>
            
            <span class="pull-left" style="margin-left: 2px"><a href="./index.php" class="btn btn-info">Join Now</a></span>
            <span class="pull-right"><button type="submit" name="login" class="btn btn-success">Login</button></span>
        </div>

        <?php
        if (isset($_POST['login'])) {
            $username = mysqli_real_escape_string($con, $_POST['username']);
            $password = mysqli_real_escape_string($con, $_POST['password']);
            
            $encrypted_password = md5($password);
            
            $query = mysqli_query($con, "SELECT * FROM members WHERE username='$username' AND password='$encrypted_password' AND status='Active'") or die(mysqli_error($con));
            $row = mysqli_fetch_array($query);
            $num_row = mysqli_num_rows($query);

            if ($num_row > 0) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                
                header('location:pages/index.php'); // Redirect to dashboard
            } else {
                echo "
                <div class='alert alert-danger alert-dismissible' role='alert'>
                    Invalid Username/Password or Account not Active!
                    <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                ";
            }
        }
        ?>

    </form>    
    <div class="pull-left">
            <a href="../admin/index.php"><h6>Admin Login</h6></a>
            </div>

            <div class="pull-right">
            <a href="../staff/index.php"><h6>Staff Login</h6></a>
            </div>     
</div>

<script src="../js/jquery.min.js"></script>  
<script src="../js/matrix.login.js"></script> 
<script src="../js/bootstrap.min.js"></script> 
<script src="../js/matrix.js"></script>

</body>
</html>

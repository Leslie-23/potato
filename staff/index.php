<?php session_start();
include('dbcon.php'); ?>
<!DOCTYPE html>
<html lang="en">
     
<head>
        <title>Gym System - Staff</title><meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="css/matrix-style.css" />
        <link rel="stylesheet" href="css/matrix-login.css" />
        <link href="font-awesome/css/font-awesome.css" rel="stylesheet" />
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>

    </head>
    
    <body>
    
        <div id="loginbox">            
            <form id="loginform" method="POST" class="form-vertical" action="#">
            <div class="control-group normal_text"> <h3><img src="./img/icontest3.png" alt="Logo" /></h3></h3><h3>Staff Login</h3></div>
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lg"><i class="icon-user"> </i></span><input type="text" name="user" placeholder="Username" required/>
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_ly"><i class="icon-lock"></i></span><input type="password" name="pass" placeholder="Password" required />
                        </div>
                    </div>
                </div>
                <div class="form-actions center">
                    <!-- <span class="pull-right"><a type="submit" href="index.html" class="btn btn-success" /> Login</a></span> -->
                    <!-- <input type="submit" class="button" title="Log In" name="login" value="Admin Login"></input> -->
                    <button type="submit" class="btn btn-block btn-large btn-success" title="Log In" name="login" value="Admin Login">Staff Login</button>
                </div>
            </form>
            <?php
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($con, $_POST['user']);
    $password = $_POST['pass']; // Don't escape password - we'll hash it
    
    // First try to find the user by username only
    $query = mysqli_query($con, "SELECT * FROM staffs WHERE username='$username'");
    $row = mysqli_fetch_array($query);
    $num_row = mysqli_num_rows($query);
    
    if ($num_row > 0) {
        $storedHash = $row['password'];
        $validPassword = false;
        
        // First try bcrypt verification
        if (password_verify($password, $storedHash)) {
            $validPassword = true;
        } 
        // If bcrypt fails, try MD5
        elseif (md5($password) === $storedHash) {
            $validPassword = true;
            // Upgrade MD5 to bcrypt
            $newHash = password_hash($password, PASSWORD_BCRYPT);
            mysqli_query($con, "UPDATE staffs SET password='$newHash' WHERE user_id='".$row['user_id']."'");
        }
        
        if ($validPassword) {
            $_SESSION['user_id'] = $row['user_id'];
            header('location:staff-pages/index.php');
            exit();
        }
    }
    
    // If we get here, login failed
    echo "<div class='alert alert-danger alert-dismissible' role='alert'>
            Invalid Username and Password
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                <span aria-hidden='true'>&times;</span>
            </button>
          </div>";
}
?>
            <div class="pull-left"> 
            <a href="../index.php"><h6>Admin Login</h6></a>
            </div>

            <div class="pull-right">
            <a href="../customer/login.php"><h6>Customer Login</h6></a>
            </div>
            
        </div>
        
        <script src="js/jquery.min.js"></script>  
        <script src="js/matrix.login.js"></script> 
        <script src="js/bootstrap.min.js"></script> 
<script src="js/matrix.js"></script>
    </body>

</html>

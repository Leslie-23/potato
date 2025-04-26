<?php
include('dbcon.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym System Admin - Customer Registration</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../css/bootstrap.min.css" />
<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../css/fullcalendar.css" />
<link rel="stylesheet" href="../css/matrix-style.css" />
<link rel="stylesheet" href="../css/matrix-media.css" />
<link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
<link rel="stylesheet" href="../css/jquery.gritter.css" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
</head>

<body>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span6 offset3">
            <div class="widget-box">
                <div class="widget-title"> 
                    <span class="icon"> <i class="icon-user"></i> </span>
                    <h5>Customer Registration Form</h5>
                </div>
                <div class="widget-content nopadding">
                    <form action="" method="POST" class="form-horizontal">
                        
                        <div class="control-group">
                            <label class="control-label">Full Name :</label>
                            <div class="controls">
                                <input type="text" class="span11" name="fullname" placeholder="Full Name" required />
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Username :</label>
                            <div class="controls">
                                <input type="text" class="span11" name="username" placeholder="@username" required />
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Password :</label>
                            <div class="controls">
                                <input type="password" class="span11" name="password" placeholder="Password" required />
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Gender :</label>
                            <div class="controls">
                                <select name="gender" required>
                                    <option value="Male" selected>Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- <div class="control-group">
                            <label class="control-label">Service :</label>
                            <div class="controls">
                                <select name="services" required>
                                    <option value="" disabled selected>Select Service</option>
                                    <option value="Fitness">Fitness</option>
                                    <option value="Sauna">Sauna</option>
                                    <option value="Cardio">Cardio</option>
                                </select>
                            </div>
                        </div> -->

                        <!-- <div class="control-group">
                            <label class="control-label">Plan :</label>
                            <div class="controls">
                                <select name="plan" required>
                                    <option value="" disabled selected>Select Plan</option>
                                    <option value="1">One Month</option>
                                    <option value="3">Three Months</option>
                                    <option value="6">Six Months</option>
                                    <option value="12">One Year</option>
                                </select>
                            </div>
                        </div> -->

                        <div class="control-group">
                            <label class="control-label">Address :</label>
                            <div class="controls">
                                <input type="text" class="span11" name="address" placeholder="Address" required />
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Contact :</label>
                            <div class="controls">
                                <input type="text" class="span11" name="contact" placeholder="+233 00 000 0000" required />
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="register" class="btn btn-success btn-block">Register Now</button>
                        </div>
                        
                        <div class="text-center">
                            <a href="./login.php">Already have an account? Login</a>
                        </div>

                    </form>

                    <?php
                    if (isset($_POST['register'])) {
                        $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
                        $username = mysqli_real_escape_string($con, $_POST['username']);
                        $password = mysqli_real_escape_string($con, $_POST['password']);
                        $gender = mysqli_real_escape_string($con, $_POST['gender']);
                        $services = mysqli_real_escape_string($con, $_POST['services']);
                        $plan = mysqli_real_escape_string($con, $_POST['plan']);
                        $address = mysqli_real_escape_string($con, $_POST['address']);
                        $contact = mysqli_real_escape_string($con, $_POST['contact']);

                        $password = md5($password);

                        $qry = "INSERT INTO members(fullname, username, password, dor, gender, services, amount, plan, address, contact, status) 
                                VALUES ('$fullname', '$username', '$password', CURRENT_TIMESTAMP, '$gender', '$services', '0', '$plan', '$address', '$contact', 'Pending')";
                        $result = mysqli_query($con, $qry);

                        if ($result) {
                            echo "<div class='alert alert-success'>Registration successful! Please wait for account activation.</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Registration failed. Please try again.</div>";
                        }
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/excanvas.min.js"></script> 
<script src="../js/jquery.min.js"></script> 
<script src="../js/jquery.ui.custom.js"></script> 
<script src="../js/bootstrap.min.js"></script> 
<script src="../js/jquery.flot.min.js"></script> 
<script src="../js/jquery.flot.resize.min.js"></script> 
<script src="../js/jquery.peity.min.js"></script> 
<script src="../js/fullcalendar.min.js"></script> 
<script src="../js/matrix.js"></script> 
<script src="../js/matrix.dashboard.js"></script> 
<script src="../js/jquery.gritter.min.js"></script> 
<script src="../js/matrix.interface.js"></script> 
<script src="../js/matrix.chat.js"></script> 
<script src="../js/jquery.validate.js"></script> 
<script src="../js/matrix.form_validation.js"></script> 
<script src="../js/jquery.wizard.js"></script> 
<script src="../js/jquery.uniform.js"></script> 
<script src="../js/select2.min.js"></script> 
<script src="../js/matrix.popover.js"></script> 
<script src="../js/jquery.dataTables.min.js"></script> 
<script src="../js/matrix.tables.js"></script> 

</body>
</html>

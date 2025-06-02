<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location:../index.php');    
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<title>Gym System Admin</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../css/bootstrap.min.css" />
<link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="../css/matrix-style.css" />
<link rel="stylesheet" href="../css/matrix-media.css" />
<link href="../font-awesome/css/fontawesome.css" rel="stylesheet" />
<link href="../font-awesome/css/all.css" rel="stylesheet" />
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
<!-- Add EmailJS SDK -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
<script type="text/javascript">
    (function() {
        emailjs.init("6W98kc4gPVLdCp2RV"); // Replace with your EmailJS user ID
    })();
</script>
</head>
<body>

<!--Header-part-->
<div id="header">
  <h1><a href="dashboard.html">Perfect Gym Admin</a></h1>
</div>
<!--close-Header-part--> 

<!--top-Header-menu-->
<?php include 'includes/topheader.php'?>

<!--sidebar-menu-->
<?php $page='staff-management'; include 'includes/sidebar.php'?>
<!--sidebar-menu-->
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a> <a href="staffs.php">Staffs</a> <a href="staffs-entry.php" class="current">Staff Entry</a> </div>
    <h1 class="text-center">GYM's Staff <i class="fas fa-users"></i></h1>
  </div>
  
  <form role="form" action="index.php" method="POST" id="staffForm">
    <?php 
    if(isset($_POST['fullname'])){
        $fullname = $_POST["fullname"];    
        $username = $_POST["username"];
        $password = $_POST["password"];
        $email = $_POST["email"];
        $address = $_POST["address"];
        $designation = $_POST["designation"]; // Changed to match your DB column
        $gender = $_POST["gender"];
        $contact = $_POST["contact"];

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        include 'dbcon.php';
        
        // Use prepared statement to prevent SQL injection
        $qry = "INSERT INTO staffs(fullname, username, password, email, address, designation, gender, contact) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($con, $qry);
        mysqli_stmt_bind_param($stmt, "ssssssss", $fullname, $username, $hashedPassword, $email, $address, $designation, $gender, $contact);
        $result = mysqli_stmt_execute($stmt);

        if(!$result){
            echo "<div class='container-fluid'>
                <div class='row-fluid'>
                <div class='span12'>
                <div class='widget-box'>
                <div class='widget-title'> <span class='icon'> <i class='fas fa-info'></i> </span>
                    <h5>Error Message</h5>
                    </div>
                    <div class='widget-content'>
                        <div class='error_ex'>
                        <h1 style='color:maroon;'>Error 404</h1>
                        <h3>Error occurred while submitting your details</h3>
                        <p>Error: " . mysqli_error($con) . "</p>
                        <a class='btn btn-warning btn-big' href='edit-member.php'>Go Back</a> </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>";
        } else {
            $user_id = mysqli_insert_id($con);
            
            // If this is a trainer, add to trainers table
            if($designation == 'Trainer') {
                $trainer_qry = "INSERT INTO trainers (user_id, specialization, certification, years_of_experience, bio)
                               VALUES (?, 'General Fitness', 'Pending', 0, 'New trainer')";
                $stmt = mysqli_prepare($con, $trainer_qry);
                mysqli_stmt_bind_param($stmt, "i", "$user_id");
                mysqli_stmt_execute($stmt);
            }

            // Send email with credentials
            echo "<script>
                emailjs.send('service_csp86fr', 'template_3s06bzm', {
                    to_email: '$email',
                    fullname: '$fullname',
                    username: '$username',
                    password: '$password',
                    designation: '$designation'
                }).then(function(response) {
                    console.log('Email sent successfully', response);
                }, function(error) {
                    console.log('Failed to send email', error);
                });
            </script>";

            echo "<div class='container-fluid'>
                <div class='row-fluid'>
                <div class='span12'>
                <div class='widget-box'>
                <div class='widget-title'> <span class='icon'> <i class='fas fa-info'></i> </span>
                    <h5>Message</h5>
                    </div>
                    <div class='widget-content'>
                        <div class='error_ex'>
                        <h1>Success</h1>
                        <h3>Staff details has been added!</h3>
                        <p>The requested staff details are added to database. Login credentials have been sent to $email.</p>
                        <a class='btn btn-inverse btn-big' href='staffs.php'>Go Back</a> </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo"<h3>YOU ARE NOT AUTHORIZED TO REDIRECT THIS PAGE. GO BACK to <a href='index.php'> DASHBOARD </a></h3>";
    }
    ?>                                    
  </form>
</div>

<!--Footer-part-->
<div class="row-fluid">
  <div id="footer" class="span12"> <?php echo date("Y");?> &copy; Developed By Leslie Paul Ajayi</a> </div>
</div>

<style>
#footer {
  color: white;
}
</style> 
<!--end-Footer-part-->
<script src="../js/jquery.min.js"></script> 
<script src="../js/jquery.ui.custom.js"></script> 
<script src="../js/bootstrap.min.js"></script> 
<script src="../js/jquery.validate.js"></script> 
<script src="../js/jquery.wizard.js"></script> 
<script src="../js/matrix.js"></script> 
<script src="../js/matrix.wizard.js"></script>
</body>
</html>
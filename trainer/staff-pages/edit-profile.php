<?php
session_start();
if(!isset($_SESSION['user_id']) ){
    header('location:../index.php');
    exit();
}

include "../dbcon.php";

$trainer_id = $_SESSION['user_id'];

// Get trainer details
$trainer_query = "SELECT s.*, t.specialization, t.bio, t.certification, t.years_experience 
                 FROM staffs s
                 LEFT JOIN trainers t ON s.user_id = t.trainer_id
                 WHERE s.user_id = ?";
$stmt = mysqli_prepare($con, $trainer_query);
mysqli_stmt_bind_param($stmt, "i", $trainer_id);
mysqli_stmt_execute($stmt);
$trainer = mysqli_stmt_get_result($stmt);
$trainer_data = mysqli_fetch_assoc($trainer);

// Get all workout plans for specialization selection
$workout_plans_query = "SELECT * FROM workout_plan ORDER BY workout_name";
$workout_plans = mysqli_query($con, $workout_plans_query);

// Get trainer's current specializations
$current_specs_query = "SELECT plan_id FROM trainer_workout_specialization WHERE trainer_id = ?";
$stmt_specs = mysqli_prepare($con, $current_specs_query);
mysqli_stmt_bind_param($stmt_specs, "i", $trainer_id);
mysqli_stmt_execute($stmt_specs);
$current_specs_result = mysqli_stmt_get_result($stmt_specs);
$current_specs = [];
while($row = mysqli_fetch_assoc($current_specs_result)) {
    $current_specs[] = $row['plan_id'];
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Basic validation
    $errors = [];
    
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $specialization = trim($_POST['specialization']);
    $certification = trim($_POST['certification']);
    $years_experience = (int)$_POST['years_experience'];
    $bio = trim($_POST['bio']);
    $selected_specs = $_POST['specializations'] ?? [];
    
    if(empty($fullname)) $errors[] = "Full name is required";
    if(empty($email)) $errors[] = "Email is required";
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if(empty($contact)) $errors[] = "Contact number is required";
    if(empty($specialization)) $errors[] = "Specialization is required";
    
    if(empty($errors)) {
        // Begin transaction
        mysqli_begin_transaction($con);
        
        try {
            // Update staff table
            $update_staff = "UPDATE staffs SET 
                            fullname = ?, 
                            email = ?, 
                            contact = ?, 
                            address = ? 
                            WHERE user_id = ?";
            $stmt_staff = mysqli_prepare($con, $update_staff);
            mysqli_stmt_bind_param($stmt_staff, "ssssi", 
                $fullname, $email, $contact, $address, $trainer_id);
            mysqli_stmt_execute($stmt_staff);
            
            // Update or insert trainer details
            if($trainer_data['trainer_id']) {
                $update_trainer = "UPDATE trainers SET 
                                 specialization = ?, 
                                 certification = ?, 
                                 years_experience = ?, 
                                 bio = ? 
                                 WHERE trainer_id = ?";
                $stmt_trainer = mysqli_prepare($con, $update_trainer);
                mysqli_stmt_bind_param($stmt_trainer, "ssisi", 
                    $specialization, $certification, $years_experience, $bio, $trainer_id);
                mysqli_stmt_execute($stmt_trainer);
            } else {
                $insert_trainer = "INSERT INTO trainers 
                                  (trainer_id, specialization, certification, years_experience, bio) 
                                  VALUES (?, ?, ?, ?, ?)";
                $stmt_trainer = mysqli_prepare($con, $insert_trainer);
                mysqli_stmt_bind_param($stmt_trainer, "issis", 
                    $trainer_id, $specialization, $certification, $years_experience, $bio);
                mysqli_stmt_execute($stmt_trainer);
            }
            
            // Handle specializations
            // First delete existing specializations
            $delete_specs = "DELETE FROM trainer_workout_specialization WHERE trainer_id = ?";
            $stmt_delete = mysqli_prepare($con, $delete_specs);
            mysqli_stmt_bind_param($stmt_delete, "i", $trainer_id);
            mysqli_stmt_execute($stmt_delete);
            
            // Insert new specializations
            if(!empty($selected_specs)) {
                $insert_spec = "INSERT INTO trainer_workout_specialization (trainer_id, plan_id) VALUES (?, ?)";
                $stmt_insert = mysqli_prepare($con, $insert_spec);
                
                foreach($selected_specs as $plan_id) {
                    mysqli_stmt_bind_param($stmt_insert, "ii", $trainer_id, $plan_id);
                    mysqli_stmt_execute($stmt_insert);
                }
            }
            
            // Handle profile picture upload
            if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/profiles/';
                $file_ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
                $filename = 'trainer_' . $trainer_id . '_' . time() . '.' . $file_ext;
                $target_file = $upload_dir . $filename;
                
                // Validate image
                $check = getimagesize($_FILES['profile_pic']['tmp_name']);
                if($check !== false) {
                    if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                        // Update profile picture in database
                        $update_pic = "UPDATE staffs SET image_url = ? WHERE user_id = ?";
                        $stmt_pic = mysqli_prepare($con, $update_pic);
                        $image_path = 'uploads/profiles/' . $filename;
                        mysqli_stmt_bind_param($stmt_pic, "si", $image_path, $trainer_id);
                        mysqli_stmt_execute($stmt_pic);
                    }
                }
            }
            
            // Commit transaction
            mysqli_commit($con);
            
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: profile.php");
            exit();
            
        } catch(Exception $e) {
            mysqli_rollback($con);
            $errors[] = "Error updating profile: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Trainer Profile</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../css/uniform.css" />
    <link rel="stylesheet" href="../css/select2.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="../css/matrix-media.css" />
    <link href="../font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
    <style>
        .profile-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #eee;
            margin-bottom: 15px;
        }
        .form-section {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .form-section h4 {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .checkbox-group {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
        }
        .checkbox-group label {
            display: block;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

<!--Header-part-->
<div id="header">
  <h1><a href="dashboard.html">Perfect Gym Admin</a></h1>
</div>

<!--top-Header-menu-->
<?php include '../includes/header.php'?>

<!--sidebar-menu-->
<?php $page='profile'; include '../includes/sidebar.php'?>

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.php" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> 
    <a href="profile.php">Profile</a> <a href="#" class="current">Edit Profile</a> </div>
    <h1>Edit Trainer Profile</h1>
  </div>
  
  <div class="container-fluid">
    <?php if(!empty($errors)): ?>
        <div class="alert alert-error">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error:</strong> 
            <?php foreach($errors as $error): ?>
                <br><?= htmlspecialchars($error) ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"><i class="icon-edit"></i></span>
            <h5>Edit Your Profile Information</h5>
          </div>
          <div class="widget-content">
            <form method="POST" action="edit-profile.php" class="form-horizontal" enctype="multipart/form-data">
              
              <div class="form-section">
                <h4>Basic Information</h4>
                
                <div class="control-group">
                  <label class="control-label">Profile Picture:</label>
                  <div class="controls">
                    <img src="<?= $trainer_data['image_url'] ? '../'.$trainer_data['image_url'] : '../img/default-trainer-avatar.jpg' ?>" 
                         class="profile-preview" id="profile-preview">
                    <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Full Name:</label>
                  <div class="controls">
                    <input type="text" class="span12" name="fullname" 
                           value="<?= htmlspecialchars($trainer_data['fullname']) ?>" required>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Email:</label>
                  <div class="controls">
                    <input type="email" class="span12" name="email" 
                           value="<?= htmlspecialchars($trainer_data['email']) ?>" required>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Contact Number:</label>
                  <div class="controls">
                    <input type="text" class="span12" name="contact" 
                           value="<?= htmlspecialchars($trainer_data['contact']) ?>" required>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Address:</label>
                  <div class="controls">
                    <input type="text" class="span12" name="address" 
                           value="<?= htmlspecialchars($trainer_data['address']) ?>">
                  </div>
                </div>
              </div>
              
              <div class="form-section">
                <h4>Trainer Details</h4>
                
                <div class="control-group">
                  <label class="control-label">Primary Specialization:</label>
                  <div class="controls">
                    <input type="text" class="span12" name="specialization" 
                           value="<?= htmlspecialchars($trainer_data['specialization']) ?>" required>
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Certification:</label>
                  <div class="controls">
                    <input type="text" class="span12" name="certification" 
                           value="<?= htmlspecialchars($trainer_data['certification']) ?>">
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Years of Experience:</label>
                  <div class="controls">
                    <input type="number" class="span12" name="years_experience" min="0" max="50"
                           value="<?= htmlspecialchars($trainer_data['years_experience']) ?>">
                  </div>
                </div>
                
                <div class="control-group">
                  <label class="control-label">Bio:</label>
                  <div class="controls">
                    <textarea class="span12" name="bio" rows="5"><?= htmlspecialchars($trainer_data['bio']) ?></textarea>
                  </div>
                </div>
              </div>
              
              <div class="form-section">
                <h4>Workout Specializations</h4>
                <div class="control-group">
                  <label class="control-label">Select your specializations:</label>
                  <div class="controls">
                    <div class="checkbox-group">
                      <?php while($plan = mysqli_fetch_assoc($workout_plans)): ?>
                        <label class="checkbox">
                          <input type="checkbox" name="specializations[]" value="<?= $plan['table_id'] ?>"
                            <?= in_array($plan['table_id'], $current_specs) ? 'checked' : '' ?>>
                          <?= htmlspecialchars($plan['workout_name']) ?>
                        </label>
                      <?php endwhile; ?>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="profile.php" class="btn">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--Footer-part-->
<div class="row-fluid">
  <div id="footer" class="span12"> <?php echo date("Y");?> &copy; Developed By Leslie Paul Ajayi</div>
</div>

<!--end-Footer-part-->

<script src="../js/jquery.min.js"></script> 
<script src="../js/jquery.ui.custom.js"></script> 
<script src="../js/bootstrap.min.js"></script>  
<script src="../js/matrix.js"></script> 
<script src="../js/jquery.validate.js"></script> 
<script src="../js/jquery.uniform.js"></script> 
<script src="../js/select2.min.js"></script> 

<script>
$(document).ready(function() {
    // Profile picture preview
    $('#profile_pic').change(function(e) {
        if(this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profile-preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Initialize select2 for better dropdowns
    $('select').select2();
    
    // Form validation
    $('form').validate({
        rules: {
            fullname: "required",
            email: {
                required: true,
                email: true
            },
            contact: "required",
            specialization: "required"
        },
        messages: {
            fullname: "Please enter your full name",
            email: {
                required: "Please enter your email",
                email: "Please enter a valid email address"
            },
            contact: "Please enter your contact number",
            specialization: "Please enter your primary specialization"
        },
        errorClass: "help-inline",
        errorElement: "span",
        highlight: function(element) {
            $(element).parents('.control-group').addClass('error');
        },
        unhighlight: function(element) {
            $(element).parents('.control-group').removeClass('error');
        }
    });
});
</script>

</body>
</html>
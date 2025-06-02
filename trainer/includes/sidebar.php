<!-- Add these in your <head> section -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!--sidebar-menu-->
<div id="sidebar"><a href="#" class="visible-phone"><i class="fas fa-home"></i> Dashboard</a>
  <ul>
    <li class="<?php if($page=='dashboard'){ echo 'active'; }?>"><a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a> </li>
    <!-- <li class="<?php if($page=='member'){ echo 'submenu active'; } else { echo 'submenu';}?>"> <a href="#"><i class="fas fa-users"></i> <span>Manage Members</span></a>
      <ul>
        <li><a href="members.php">List All Members</a></li>
        <li><a href="member-entry.php">Member Entry Form</a></li>
        <li><a href="remove-member.php">Remove Member</a></li>
        <li><a href="edit-member.php">Update Member Details</a></li>
      </ul> -->
    </li>
<!-- 
    <li class="<?php if($page=='equipment'){ echo 'submenu active'; } else { echo 'submenu';}?>"> <a href="#"><i class="fas fa-dumbbell"></i> <span>Gym Equipment</span> </a>
      <ul>
        <li><a href="equipment.php">List Gym Equipment</a></li>
        <li><a href="equipment-entry.php">Add Equipment</a></li>
        <li><a href="remove-equipment.php">Remove Equipment</a></li>
        <li><a href="edit-equipment.php">Update Equipment Details</a></li>
      </ul>
    </li> -->
    <!-- <li class="<?php if($page=='membersts'){ echo 'active'; }?>"><a href="member-status.php"><i class="fas fa-eye"></i> <span>Member's Status</span></a></li> -->
    <!-- <li class="<?php if($page=='payment'){ echo 'active'; }?>"><a href="payment.php"><i class="fas fa-money-bill-wave"></i> <span>Payments</span></a></li> -->
    <li class="<?php if($page=='attendance'){ echo 'active'; }?>"><a href="attendance.php"><i class="fas fa-calendar-check"></i> <span>Manage Attendance</span></a></li>
    <li class="<?php if($page=='reminder'){ echo 'active'; }?>"><a href="reminder.php"><i class="fas fa-bell"></i> <span>Reminders & Announcements</span></a></li>
     <li class="<?php if($page=='manage-customer-progress'){ echo 'active'; }?>"><a href="customer-progress.php"><i class="fas fa-user"></i> <span>Manage Trainees</span></a></li>
    <li class="<?php if($page=='profile'){ echo 'active'; }?>"><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
   
  </ul>
</div>
<!--sidebar-menu-->
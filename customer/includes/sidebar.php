<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div id="sidebar">
  <a href="#" class="visible-phone"><i class="fas fa-home"></i> Dashboard</a>
  <ul>
    <li class="<?php if($page=='dashboard'){ echo 'active'; }?>">
      <a href="index.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
    </li>
    <li class="<?php if($page=='todo'){ echo 'active'; }?>">
      <a href="to-do.php"><i class="fas fa-pencil-alt"></i> <span>To-Do</span></a>
    </li>
    <li class="<?php if($page=='reminder'){ echo 'active'; }?>">
      <a href="customer-reminder.php"><i class="fas fa-clock"></i> <span>Reminders</span></a>
    </li>
    <li class="<?php if($page=='session'){ echo 'active'; }?>">
      <a href="trainer-sessions.php"><i class="fas fa-calendar-alt"></i> <span>Sessions & Schedules</span></a>
    </li>
    <li class="<?php if($page=='announcement'){ echo 'active'; }?>">
      <a href="announcement.php"><i class="fas fa-bullhorn"></i> <span>Announcement</span></a>
    </li>
    <li class="<?php if($page=='report'){ echo 'active'; }?>">
      <a href="my-report.php"><i class="fas fa-file-alt"></i> <span>Reports</span></a>
    </li>
  </ul>
</div>
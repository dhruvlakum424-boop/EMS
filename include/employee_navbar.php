<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="sidebar">
     <div class="sidebar-header" style="display: flex; align-items: center; padding: 20px 15px;">
        <!-- Logo Image -->
        <img src="../admin/Gemini_Generated_Image_v1bhnjv1bhnjv1bh.png" alt="Company Logo" 
             style="width: 40px; height: 40px; margin-right: 10px; border-radius: 5px;">
        <h3 style="margin: 0;">Employee Panel</h3>
    </div>

    <ul class="nav-links">
        <li>
            <a href="../index.php" class="nav-link <?= ($current_page=='index.php')?'active':'' ?>">
                <i class="fas fa-home"></i> Home
            </a>
        </li>

        <li>
            <a href="employee_dashboard.php" class="nav-link <?= ($current_page=='employee_dashboard.php')?'active':'' ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="employee_profile.php" class="nav-link <?= ($current_page=='employee_profile.php')?'active':'' ?>">
                <i class="fas fa-user-circle"></i> My Profile
            </a>
        </li>

        <li>
            <a href="attendance_list.php" class="nav-link <?= ($current_page=='attendance_list.php')?'active':'' ?>">
                <i class="fa-solid fa-clipboard-user"></i> Attendance
            </a>
        </li>

        <li>
            <a href="apply_leave.php" class="nav-link <?= ($current_page=='apply_leave.php')?'active':'' ?>">
                <i class="fas fa-pen-nib"></i> Apply Leave
            </a>
        </li>

        <li>
            <a href="leave_history.php" class="nav-link <?= ($current_page=='leave_history.php')?'active':'' ?>">
                <i class="fas fa-history"></i> Leave History
            </a>
        </li>

        <li>
            <a href="salary_history.php" class="nav-link <?= ($current_page=='salary_history.php')?'active':'' ?>">
                <i class="fas fa-file-invoice-dollar"></i> Salary History
            </a>
        </li>

        <li class="logout-item">
            <a href="employee_logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</nav>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar">

    <div class="sidebar-header" style="display: flex; align-items: center; padding: 20px 15px;">
        <!-- Logo Image -->
        <img src="Gemini_Generated_Image_v1bhnjv1bhnjv1bh.png" alt="Company Logo" 
             style="width: 40px; height: 40px; margin-right: 10px; border-radius: 5px;">
        <h3 style="margin: 0;">Admin Panel</h3>
    </div>

    <ul class="nav-links">
        <li>
            <a href="../index.php" class="nav-link <?= ($current_page=='index.php')?'active':'' ?>">
                <i class="fas fa-home"></i> Home
            </a>
        </li>

        <li>
            <a href="admin_dashboard.php" class="nav-link <?= ($current_page=='admin_dashboard.php')?'active':'' ?>">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
        </li>

        <li>
            <a href="admin_profile.php" class="nav-link <?= ($current_page=='admin_profile.php')?'active':'' ?>">
                <i class="fas fa-user"></i> Admin Profile
            </a>
        </li>

        <li>
            <a href="admin_departments.php" class="nav-link <?= ($current_page=='admin_departments.php')?'active':'' ?>">
                <i class="fas fa-building"></i> Department
            </a>
        </li>

        <li>
            <a href="leave_types.php" class="nav-link <?= ($current_page=='leave_types.php')?'active':'' ?>">
                <i class="fas fa-list"></i> Leave Types
            </a>
        </li>

        <li>
            <a href="admin_employee.php" class="nav-link <?= ($current_page=='admin_employee.php')?'active':'' ?>">
                <i class="fas fa-users"></i> Employees
            </a>
        </li>

        <li>
            <a href="admin_salary.php" class="nav-link <?= ($current_page=='admin_salary.php')?'active':'' ?>">
                <i class="fas fa-money-bill-wave"></i> Salary
            </a>
        </li>

        <li>
            <a href="admin_leave_requests.php" class="nav-link <?= ($current_page=='admin_leave_requests.php')?'active':'' ?>">
                <i class="fas fa-file-alt"></i> Leave Requests
            </a>
        </li>

        <li>
            <a href="employee_attendance_list.php" class="nav-link <?= ($current_page=='employee_attendance_list.php')?'active':'' ?>">
                <i class="fa-solid fa-clipboard-user"></i> Attendance
            </a>
        </li>

        <li>
            <a href="admin_reports.php" class="nav-link <?= ($current_page=='admin_reports.php')?'active':'' ?>">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
        </li>
    </ul>

    <div class="logout-section">
        <a href="admin_logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>

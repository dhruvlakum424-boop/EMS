<?php
session_start();
include('../config/db.php');

$database = new Database();
$db = $database->connect();

if (!isset($_SESSION['emp_id'])) {
    header("Location: ../login.php");
    exit();
}

$current_emp_id = $_SESSION['emp_id'];

// Get employee details
$emp_query = "SELECT emp_id, name FROM employees WHERE emp_id = :emp_id";
$emp_stmt = $db->prepare($emp_query);
$emp_stmt->execute(['emp_id' => $current_emp_id]);
$employee = $emp_stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    header("Location: ../login.php");
    exit();
}

// Month and Year filter
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('n');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Days in month
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $selected_month, $selected_year);

// Get attendance data
$attendance_data = [];
$query = "SELECT DAY(attendance_date) as day, status 
          FROM attendance 
          WHERE emp_id = :eid 
          AND MONTH(attendance_date) = :m 
          AND YEAR(attendance_date) = :y
          ORDER BY attendance_date";

$stmt = $db->prepare($query);
$stmt->execute([
    'eid' => $current_emp_id, 
    'm' => $selected_month, 
    'y' => $selected_year
]);

$rowCount = $stmt->rowCount();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $attendance_data[(int)$row['day']] = strtolower($row['status']);
}

$employee_name = $employee['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/attendance_list.css">

</head>
<body>

    <?php include('../include/employee_navbar.php'); ?>

    <div class="main-content">
        <h2>Attendance Management</h2>
        
        <!-- Employee Info -->
        <div class="employee-info">
            <h3>Employee: <?php echo htmlspecialchars($employee_name); ?> (ID: <?php echo htmlspecialchars($current_emp_id); ?>)</h3>
        </div>
        
        <form class="filter-section" method="GET">
            <div>
                <label>Month:</label>
                <select name="month">
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m == $selected_month ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label>Year:</label>
                <select name="year">
                    <?php 
                    $current_year = date('Y');
                    for($y=$current_year-1; $y<=$current_year+1; $y++): 
                    ?>
                        <option value="<?= $y ?>" <?= $y == $selected_year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-search"></i> Search</button>
        </form>

        <div class="attendance-card">
            <?php if($rowCount == 0): ?>
                <div class="no-data">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No Attendance Records Found</h3>
                    <p>No attendance data found for <?php echo date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)); ?></p>
                </div>
            <?php else: ?>
                <table class="att-table">
                    <thead>
                        <tr>
                            <th style="min-width: 120px;">Date</th>
                            <?php 
                            for($i=1; $i<=$days_in_month; $i++) {
                                $day_name = date('D', mktime(0, 0, 0, $selected_month, $i, $selected_year));
                                echo "<th title='$day_name'>" . sprintf("%02d", $i) . "</th>";
                            }
                            ?>
                            <th class="count-column">P</th>
                            <th class="count-column">A</th>
                            <th class="count-column">H</th>
                            <th class="count-column">L</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Day</strong></td>
                            <?php 
                            $p_count = 0; 
                            $a_count = 0;
                            $h_count = 0;
                            $l_count = 0;
                            
                            for($i=1; $i<=$days_in_month; $i++) {
                                $status = isset($attendance_data[$i]) ? strtolower($attendance_data[$i]) : '';
                                
                                $displayChar = '';
                                $class = '';

                                if($status == 'present' || $status == 'p') {
                                    $displayChar = 'P';
                                    $class = 'status-p';
                                    $p_count++;
                                } elseif($status == 'absent' || $status == 'a') {
                                    $displayChar = 'A';
                                    $class = 'status-a';
                                    $a_count++;
                                } elseif($status == 'holiday' || $status == 'h') {
                                    $displayChar = 'H';
                                    $class = 'status-h';
                                    $h_count++;
                                } elseif($status == 'leave' || $status == 'l') {
                                    $displayChar = 'L';
                                    $class = 'status-l';
                                    $l_count++;
                                }

                                echo "<td class='$class'>$displayChar</td>";
                            } 
                            ?>
                            <td class="status-p count-column"><?= $p_count ?></td>
                            <td class="status-a count-column"><?= $a_count ?></td>
                            <td class="status-h count-column"><?= $h_count ?></td>
                            <td class="status-l count-column"><?= $l_count ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <div style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 12px;">
                    <strong>Legend:</strong> 
                    <span style="color: #2ecc71;">P = Present</span> | 
                    <span style="color: #e74c3c;">A = Absent</span> | 
                    <span style="color: #f39c12;">H = Holiday</span> | 
                    <span style="color: #9b59b6;">L = Leave</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
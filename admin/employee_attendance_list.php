<?php
session_start();
include('../config/db.php'); 

$database = new Database();
$conn = $database->connect();

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$message = "";

if (isset($_POST['submit_attendance'])) {
    $current_date = date('Y-m-d');
    $attendance_data = $_POST['attendance'];

    try {
        foreach ($attendance_data as $emp_id => $status) {
            // Employee ka naam fetch karein
            $stmt_emp = $conn->prepare("SELECT name FROM employees WHERE emp_id = :eid");
            $stmt_emp->execute(['eid' => $emp_id]);
            $emp = $stmt_emp->fetch(PDO::FETCH_ASSOC);
            $emp_name = $emp['name'];

            // Penalty Logic: Absent (A) hai to 500, warna 0
            $penalty = ($status == 'A') ? 500 : 0;

            // Check: Kya aaj ki attendance pehle se marked hai?
            $stmt_check = $conn->prepare("SELECT id FROM attendance WHERE emp_id = :eid AND attendance_date = :adate");
            $stmt_check->execute(['eid' => $emp_id, 'adate' => $current_date]);

            if ($stmt_check->rowCount() > 0) {
                // Update existing attendance & penalty
                $sql = "UPDATE attendance SET status = :status, penalty = :penalty WHERE emp_id = :eid AND attendance_date = :adate";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'status' => $status, 
                    'penalty' => $penalty, 
                    'eid' => $emp_id, 
                    'adate' => $current_date
                ]);
            } else {
                // Insert new attendance & penalty
                $sql = "INSERT INTO attendance (emp_id, employee_name, status, penalty, attendance_date) 
                        VALUES (:eid, :ename, :status, :penalty, :adate)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'eid' => $emp_id, 
                    'ename' => $emp_name, 
                    'status' => $status, 
                    'penalty' => $penalty, 
                    'adate' => $current_date
                ]);
            }
        

        }
        $message = '
       <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px; position: relative; font-family: sans-serif; display: flex; align-items: center; justify-content:建设-between;">
            <div>
                 Attendance & Penalty successfully updated.
            </div>
            <button type="button" onclick="this.parentElement.style.display=\'none\';" style="position: absolute; right: 10px; top: 12px; border: none; background: transparent; font-size: 22px; cursor: pointer; color: #155724; line-height: 1;">&times;</button>
        </div>';
    } catch (PDOException $e) {
        $message = "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
    }
}


// Fetch employees list
$employees = $conn->query("SELECT emp_id, name, department FROM employees");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
   <link rel="stylesheet" href="css/employee_attendance_list.css">
</head>
<body>
   <div class="container">
       <?php include('../include/admin_navbar.php'); ?>

        <div class="main-content">
            <div class="card">
                <h2>Mark Daily Attendance</h2>
                <p>Today's Date: <strong><?php echo date('d-M-Y'); ?></strong></p>
                <?php echo $message; ?>

                <form method="POST">
                    <table id="attendanceTable" class="myTable">
                        <thead>
                            <tr>
                                <th>Emp ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $employees->fetch(PDO::FETCH_ASSOC)) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['emp_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td class="radio-group">
                                    <label style="color: #2ecc71;"><input type="radio" name="attendance[<?php echo $row['emp_id']; ?>]" value="P" checked> P</label>
                                    <label style="color: #e74c3c;"><input type="radio" name="attendance[<?php echo $row['emp_id']; ?>]" value="A"> A</label>
                                    <label style="color: #9b59b6;"><input type="radio" name="attendance[<?php echo $row['emp_id']; ?>]" value="L"> L</label>
                                    <label style="color: #f39c12;"><input type="radio" name="attendance[<?php echo $row['emp_id']; ?>]" value="H"> H</label>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <button type="submit" name="submit_attendance" class="save-btn">Save Attendance & Penalty</button>
                </form>
            </div>
        </div>
    </div>
    <script src="//cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
<script>
    let table = new DataTable('#myTable');
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable();
    });
</script>
</body>
</html>
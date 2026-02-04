<?php
$conn = mysqli_connect("localhost", "root", "", "employee_management_system");

if (!$conn) {
    echo json_encode(['basic_salary' => 0, 'penalty' => 0]);
    exit;
}

if (isset($_POST['emp_id'])) {
    $emp_id = mysqli_real_escape_string($conn, $_POST['emp_id']);

    // 1. Basic Salary fetch karna (employees table se)
    $basic_salary = 0;
    $emp_res = mysqli_query($conn, "SELECT salary FROM employees WHERE id='$emp_id'");
    if ($row = mysqli_fetch_assoc($emp_res)) {
        $basic_salary = $row['salary'] ?? 0;
    }

    // 2. Penalty fetch karna - BOTH leaves aur attendance tables se
    $total_penalty = 0;
    
    // A. Leaves table se penalty
    $leaves_penalty = 0;
    $leaves_query = "SELECT SUM(penalty) AS total FROM leaves 
                      WHERE emp_id = '$emp_id' 
                      OR emp_id LIKE '%-$emp_id' 
                      OR employee_name = (SELECT name FROM employees WHERE id = '$emp_id')";
                      
    $leaves_res = mysqli_query($conn, $leaves_query);
    if ($row = mysqli_fetch_assoc($leaves_res)) {
        $leaves_penalty = $row['total'] ?? 0;
    }

    // B. Attendance table se penalty
    $attendance_penalty = 0;
    $attendance_query = "SELECT SUM(penalty) AS total FROM attendance 
                         WHERE emp_id = '$emp_id' 
                         OR employee_name = (SELECT name FROM employees WHERE id = '$emp_id')";
                         
    $attendance_res = mysqli_query($conn, $attendance_query);
    if ($row = mysqli_fetch_assoc($attendance_res)) {
        $attendance_penalty = $row['total'] ?? 0;
    }

    // Dono tables ki penalty ka total
    $total_penalty = $leaves_penalty + $attendance_penalty;

    echo json_encode([
        'basic_salary' => $basic_salary,
        'penalty' => $total_penalty
    ]);
}
?>
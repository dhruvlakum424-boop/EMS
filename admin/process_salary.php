<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "employee_management_system");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_id = mysqli_real_escape_string($conn, $_POST['emp_id']);
    $month = mysqli_real_escape_string($conn, $_POST['month']);
    $basic_salary = (float)$_POST['basic_salary'];
    $allowance = (float)$_POST['allowance'];
    $deduction = (float)$_POST['deduction']; 
    $paid_date = mysqli_real_escape_string($conn, $_POST['paid_date']);

    $net_salary = ($basic_salary + $allowance) - $deduction;

    $query = "INSERT INTO salary (emp_id, month, basic_salary, allowance, deduction, net_salary, paid_date) 
              VALUES ('$emp_id', '$month', '$basic_salary', '$allowance', '$deduction', '$net_salary', '$paid_date')";

    if (mysqli_query($conn, $query)) {
        // Reset Penalties
        $reset_leaves = "UPDATE leaves SET penalty = 0 WHERE emp_id = '$emp_id' OR employee_name = (SELECT name FROM employees WHERE id = '$emp_id')";
        mysqli_query($conn, $reset_leaves);

        $reset_attendance = "UPDATE attendance SET penalty = 0 WHERE emp_id = '$emp_id' OR employee_name = (SELECT name FROM employees WHERE id = '$emp_id')";
        mysqli_query($conn, $reset_attendance);

        // --- NAYA MESSAGE LOGIC ---
        $_SESSION['success_msg'] = "Salary Paid Successfully!";
        $_SESSION['details'] = "Net Paid: ₹" . number_format($net_salary, 2) . " | Penalties Cleared.";
        header("Location: admin_salary.php");
        exit;
    } else {
        $_SESSION['error_msg'] = "Error: " . mysqli_error($conn);
        header("Location: admin_salary.php");
        exit;
    }
}
?>
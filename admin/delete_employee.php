<?php
session_start();
if(!isset($_SESSION['admin_id'])) { header("Location: admin_login.php"); exit; }

$conn = mysqli_connect("localhost", "root", "", "employee_management_system");

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM employees WHERE id = $id";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: admin_employee.php?msg=deleted");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }

    $_SESSION['success_msg'] = "Deleted! Employee Successfully!";
header("Location: admin_employee.php");
exit;
}
?>
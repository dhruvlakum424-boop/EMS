<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "employee_management_system");

if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "DELETE FROM salary WHERE salary_id = '$id'";
    
    if(mysqli_query($conn, $query)) {
        header("Location: admin_salary.php?msg=deleted");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
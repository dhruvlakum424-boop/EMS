<?php
session_start();
// Database connection
$conn = mysqli_connect("localhost", "root", "", "employee_management_system");

if(isset($_POST['status'])) {
    $id = mysqli_real_escape_string($conn, $_POST['leave_id']); // Form se aayi table ID
    $status = $_POST['status']; // 'Approved' or 'Rejected'

    // Update Query using your column name 'id' and 'status'
    $update_query = "UPDATE leaves SET status = '$status' WHERE id = '$id'";

    if(mysqli_query($conn, $update_query)) {
        echo "<script>
                alert('Leave $status Successfully!');
                window.location.href='admin_leave_requests.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
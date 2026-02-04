<?php
session_start();
include('../config/db.php');

if(isset($_POST['add_leave'])) {
    $db = (new Database())->connect();
    $name = $_POST['leave_name'];
    $desc = $_POST['description'];

    try {
        $sql = "INSERT INTO leave_type (leave_name, description) VALUES (:name, :desc)";
        $stmt = $db->prepare($sql);
        $stmt->execute([':name' => $name, ':desc' => $desc]);
        // echo "<script>alert('Leave Type Added!'); window.location.href='leave_types.php';</script>";
         $_SESSION['success_msg'] = "Leave Type Added Successfully!";
header("Location: leave_types.php");
exit;
    } catch (PDOException $e) { die($e->getMessage()); }
}
?>
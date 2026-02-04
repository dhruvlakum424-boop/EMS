<?php
session_start();
include('../config/db.php');

if(isset($_GET['id'])) {
    $db = (new Database())->connect();
    try {
        $stmt = $db->prepare("DELETE FROM leave_type WHERE leave_type_id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        echo "<script>alert('Leave Type Deleted!'); window.location.href='leave_types.php';</script>";
         $_SESSION['success_msg'] = "Leave Type Deleted Successfully!";
header("Location: leave_types.php");
exit;
    } catch (PDOException $e) { die($e->getMessage()); }
}
?>
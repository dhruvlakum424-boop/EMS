<?php
session_start();
include('../config/db.php');

if(isset($_GET['id'])) {
    $db = (new Database())->connect();
    try {
        $query = "DELETE FROM department WHERE dept_id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $_GET['id']]);

       
        $_SESSION['success_msg'] = "Deleted! Department Successfully!";
header("Location: admin_departments.php");
exit;
    } catch (PDOException $e) { die($e->getMessage()); }
}
?>
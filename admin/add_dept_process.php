<?php
session_start();
include('../config/db.php');

if(isset($_POST['dept_name'])) {
    $db = (new Database())->connect();
    $name = $_POST['dept_name'];
    $short = $_POST['dept_short']; // Agar DB mein column nahi hai toh ise hata dein

    try {
        // Agar 'dept_short' column nahi hai, toh query se use hata dein
        $query = "INSERT INTO department (dept_name, dept_short) VALUES (:name, :short)";
        $stmt = $db->prepare($query);
        $stmt->execute([':name' => $name, ':short' => $short]);

       $_SESSION['success_msg'] = "New Department Added Successfully!";
header("Location: admin_departments.php");
exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>
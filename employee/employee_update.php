<?php
session_start();
include('../config/db.php');

// Agar file employee folder ke andar hai, toh redirect sirf file ka naam hoga
if (!isset($_SESSION['emp_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: employee_profile.php"); 
    exit;
}


$database = new Database();
$db = $database->connect();

$emp_id  = $_SESSION['emp_id'];
$name    = $_POST['name'];    
$email   = $_POST['email'];   
$phone   = $_POST['phone'];   
$dob     = $_POST['dob'];     
$address = $_POST['address']; 

try {
    $sql = "UPDATE employees SET 
            name = :name, 
            email = :email, 
            phone_no = :phone, 
            dob = :dob, 
            address = :address 
            WHERE emp_id = :id";

    $stmt = $db->prepare($sql);
    
    // Yahan dhyan dein: SQL mein ':phone' hai, toh key bhi ':phone' hi honi chahiye
    $params = [
        ':name'    => $name,
        ':email'   => $email,
        ':phone'   => $phone,   // Pehle yahan ':phone_no' tha, use change kiya hai
        ':dob'     => $dob,
        ':address' => $address,
        ':id'      => $emp_id
    ];

    if ($stmt->execute($params)) {
        // --- SAHI TARIKA: Pehle data update, phir redirect ---
        $_SESSION['success_msg'] = "Profile Updated Successfully!";
        header("Location: employee_profile.php");
        exit();
    } else {
        // Agar fail ho jaye toh error message set karein
        $_SESSION['error_msg'] = "Something went wrong. Please try again.";
        header("Location: employee_profile.php");
        exit();
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
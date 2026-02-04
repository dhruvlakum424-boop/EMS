<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['admin_id']) || !isset($_POST['admin_name'])) {
    header("Location: admin_profile.php");
    exit;
}

$database = new Database();
$db = $database->connect();

$admin_id = $_SESSION['admin_id'];
$name     = $_POST['admin_name'];
$email    = $_POST['admin_email'];
$password = $_POST['admin_password'];

try {
    // Check karein agar password change karna hai ya nahi
    if(!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // FIX: 'id' ko 'admin_id' kar diya aur placeholder ko match kiya
        $sql = "UPDATE admin SET full_name = :name, email = :email, password = :pass WHERE admin_id = :id";
        $params = [
            ':name'  => $name,
            ':email' => $email,
            ':pass'  => $hashed_password,
            ':id'    => $admin_id
        ];
    } else {
        // Bina password ke update
        $sql = "UPDATE admin SET full_name = :name, email = :email WHERE admin_id = :id";
        $params = [
            ':name'  => $name,
            ':email' => $email,
            ':id'    => $admin_id
        ];
    }

    // --- SAHI ORDER: Pehle prepare, phir execute ---
    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    if ($stmt->execute($params)) {
        // --- SAHI TARIKA: Pehle data update, phir redirect ---
        $_SESSION['success_msg'] = "Profile Updated Successfully!";
        header("Location: admin_profile.php");
        exit();
    } else {
        // Agar fail ho jaye toh error message set karein
        $_SESSION['error_msg'] = "Something went wrong. Please try again.";
        header("Location: admin_profile.php");
        exit();
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<?php
session_start();
include('../config/db.php');

$response = ['success' => false];

if(isset($_FILES['profile_image']) && isset($_SESSION['emp_id'])) {
    $emp_id = $_SESSION['emp_id'];
    $file = $_FILES['profile_image'];
    $upload_dir = "../uploads/profile_pics/";
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "emp_" . $emp_id . "_" . time() . "." . $ext;

    if(move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        $database = new Database();
        $db = $database->connect();
        $stmt = $db->prepare("UPDATE employees SET profile_image = :img WHERE emp_id = :id");
        if($stmt->execute([':img' => $filename, ':id' => $emp_id])) {
            $response = ['success' => true, 'filename' => $filename];
        }
    }
}
echo json_encode($response);
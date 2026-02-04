<?php
// Errors on karein taaki error dikhe
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Connection file ka path check karein (Agar folder me hai to ../ varna nahi)
$config_path = '../config/db.php';
if (file_exists($config_path)) {
    include($config_path);
} else {
    echo json_encode(['success' => false, 'message' => 'Config file not found at ' . $config_path]);
    exit;
}

$response = ['success' => false, 'message' => 'Unknown error'];

if(isset($_FILES['admin_image']) && isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
    $file = $_FILES['admin_image'];
    
    // Folder path (Ensure karein ye folder project me exist karta ho)
    $upload_dir = "../uploads/admin_pics/";

    if (!is_dir($upload_dir)) { 
        mkdir($upload_dir, 0777, true); 
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = "admin_" . $admin_id . "_" . time() . "." . $ext;

    if(move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        try {
            $database = new Database();
            $db = $database->connect();
            
            // Check karein: Kya table name 'admin' hai aur column 'profile_image'?
            $sql = "UPDATE admin SET profile_image = :img WHERE admin_id = :id";
            $stmt = $db->prepare($sql);
            $res = $stmt->execute([':img' => $filename, ':id' => $admin_id]);
            
            if($res) {
                $response = ['success' => true, 'filename' => $filename];
            } else {
                $response['message'] = 'Database query failed';
            }
        } catch (Exception $e) {
            $response['message'] = 'DB Error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'File could not move to folder. Check permissions.';
    }
} else {
    $response['message'] = 'Session or File missing. ID: ' . ($_SESSION['admin_id'] ?? 'None');
}

echo json_encode($response);
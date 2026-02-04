<!-- <?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$db = (new Database())->connect();

// 1. Data Fetch Karein (Form fill karne ke liye)
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("SELECT * FROM leave_type WHERE leave_type_id = :id");
    $stmt->execute([':id' => $id]);
    $leave = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$leave) {
        die("Leave type not found!");
    }
}

// 2. Update Logic
if(isset($_POST['update_leave']) && isset($_POST['leave_type_id'])) {
    $id = $_POST['leave_type_id'];
    $name = $_POST['leave_name'];
    $desc = $_POST['description'];

    try {
        $sql = "UPDATE leave_type SET 
                leave_name = :name, 
                description = :desc 
                WHERE leave_type_id = :id";
                
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':name' => $name,
            ':desc' => $desc,
            ':id'   => $id
        ]);

        if($result) {
            echo "<script>alert('Updated Successfully!'); window.location.href = 'leave_types.php';</script>";
            exit;
        }
    } catch (PDOException $e) { 
        die("Database Error: " . $e->getMessage()); 
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Leave Type | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .edit-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .edit-card h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .edit-card h2 i {
            color: #e67e22;
        }

        /* Leave types ke liye orange color */

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
            font-family: inherit;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #e67e22;
            box-shadow: 0 0 5px rgba(230, 126, 34, 0.3);
        }

        .btn-container {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-update {
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            flex: 2;
            transition: 0.3s;
        }

        .btn-update:hover {
            background: #219150;
        }

        .btn-cancel {
            background: #95a5a6;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            flex: 1;
            transition: 0.3s;
        }

        .btn-cancel:hover {
            background: #7f8c8d;
        }

        hr {
            margin-bottom: 20px;
            border: 0;
            border-top: 1px solid #eee;
        }
    </style>
</head>

<body>

    <div class="edit-card">
        <h2><i class="fas fa-list-alt"></i> Edit Leave Type</h2>
        <hr>

        <form action="" method="POST">
            <input type="hidden" name="leave_type_id" value="<?php echo $leave['leave_type_id']; ?>">

            <div class="form-group">
                <label>Leave Type Name</label>
                <input type="text" name="leave_name" value="<?php echo htmlspecialchars($leave['leave_name']); ?>"
                    required placeholder="e.g. Sick Leave">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"
                    placeholder="Write description here..."><?php echo htmlspecialchars($leave['description']); ?></textarea>
            </div>

            <div class="btn-container">
                <button type="submit" name="update_leave" class="btn-update">Update Leave Type</button>
                <a href="leave_types.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

</body>

</html> -->
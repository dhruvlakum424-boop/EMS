<!-- <?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$database = new Database();
$db = $database->connect();

// 1. Pehle Data Fetch Karein (Form fill karne ke liye)
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $db->prepare("SELECT * FROM department WHERE dept_id = :id");
    $stmt->execute([':id' => $id]);
    $dept = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$dept) {
        die("Department not found!");
    }
}

// 2. Update Logic (Jab button click ho)
if(isset($_POST['update_dept'])) {
    try {
        $sql = "UPDATE department SET dept_name = :name, dept_short = :short WHERE dept_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':name'  => $_POST['dept_name'],
            ':short' => $_POST['dept_short'],
            ':id'    => $_POST['dept_id']
        ]);
        echo "<script>alert('Department Updated Successfully!'); window.location.href = 'admin_departments.php';</script>";
        exit;
    } catch (PDOException $e) { 
        die("Error: " . $e->getMessage()); 
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Department | Admin Panel</title>
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
            max-width: 450px;
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
            color: #3498db;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }

        .form-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        .btn-container {
            display: flex;
            gap: 10px;
        }

        .btn-update {
            background: #3498db;
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
            background: #2980b9;
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
    </style>
</head>

<body>

    <div class="edit-card">
        <h2><i class="fas fa-edit"></i> Edit Department</h2>
        <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">

        <form action="" method="POST">
            <input type="hidden" name="dept_id" value="<?php echo $dept['dept_id']; ?>">

            <div class="form-group">
                <label>Department Name</label>
                <input type="text" name="dept_name" value="<?php echo htmlspecialchars($dept['dept_name']); ?>"
                    required>
            </div>

            <div class="form-group">
                <label>Short Name (Code)</label>
                <input type="text" name="dept_short" value="<?php echo htmlspecialchars($dept['dept_short']); ?>"
                    required>
            </div>

            <div class="btn-container">
                <button type="submit" name="update_dept" class="btn-update">Update Now</button>
                <a href="admin_departments.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>

</body>

</html> -->
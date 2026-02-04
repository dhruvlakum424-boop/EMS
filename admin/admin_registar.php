<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../config/db.php'); 

$database = new Database();
$db = $database->connect();

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = 'admin'; // Role fix kar diya kyunki ab option nikal diya hai

    if(!empty($name) && !empty($email) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $query = "INSERT INTO admin (full_name, email, phone, password, role) VALUES (:name, :email, :phone, :password, :role)";
            $stmt = $db->prepare($query);

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $role);

            if ($stmt->execute()) {
                header("Location: admin_login.php");
                exit();
            } else {
                $error_msg = "Data insert nahi hua.";
            }
        } catch (PDOException $e) {
            $error_msg = "Database Error: " . $e->getMessage();
        }
    } else {
        $error_msg = "Kripya saari details bharein.";
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration | NexusCorp</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body { 
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); 
            height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center;
            padding: 20px;
        }

        .register-card { 
            background: rgba(255, 255, 255, 0.95); 
            padding: 40px; 
            border-radius: 24px; 
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2); 
            width: 100%;
            max-width: 420px; 
            text-align: center;
            position: relative; /* Back link ke liye */
        }

        /* Top Back Link */
        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 14px;
            color: #764ba2;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: 0.3s;
        }

        .back-link:hover {
            color: #1e3a8a;
            transform: translateX(-3px);
        }

        .form-header h2 { 
            color: #2d3436; 
            font-size: 28px;
            margin-bottom: 8px; 
            font-weight: 700;
            margin-top: 10px;
        }

        .form-header p { 
            color: #636e72; 
            font-size: 15px; 
            margin-bottom: 30px; 
        }

        .error-banner {
            background: #ff7675;
            color: white;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .input-field { margin-bottom: 20px; }

        .input-field input { 
            width: 100%; 
            padding: 14px 18px; 
            border: 2px solid #f1f2f6; 
            border-radius: 12px; 
            outline: none; 
            font-size: 15px;
            transition: all 0.3s ease; 
        }

        .input-field input:focus { 
            border-color: #764ba2; 
            box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.1);
        }

        .submit-btn { 
            width: 100%; 
            padding: 15px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border: none; 
            border-radius: 12px; 
            font-weight: 600; 
            font-size: 16px;
            cursor: pointer; 
            transition: 0.3s; 
            box-shadow: 0 8px 20px rgba(118, 75, 162, 0.3);
        }

        .submit-btn:hover { 
            transform: translateY(-3px); 
            filter: brightness(1.1);
        }

        .footer-link { margin-top: 30px; font-size: 14px; color: #636e72; }
        .footer-link a { color: #764ba2; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>

    <div class="register-card">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back
        </a>

        <div class="form-header">
            <h2>Admin Sign Up</h2>
            <p>NexusCorp Admin Portal</p>
        </div>

        <?php if($error_msg): ?>
            <div class="error-banner"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-field">
                <input type="text" name="full_name" placeholder="Full Name" required>
            </div>
            <div class="input-field">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-field">
                <input type="tel" name="phone" placeholder="Phone Number" required>
            </div>
            <div class="input-field">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" class="submit-btn">Register Now</button>
        </form>

        <div class="footer-link">
            Already have an account? <a href="admin_login.php">Login Here</a>
        </div>
    </div>

</body>
</html>
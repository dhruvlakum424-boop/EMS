<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('../config/db.php');

class User {
    private $conn;
    public function __construct($db) { $this->conn = $db; }

    public function login($email) {
        $sql = "SELECT * FROM employees WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":email" => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // नया method: account active है या नहीं check करने के लिए
    public function isAccountActive($emp_id) {
        $sql = "SELECT is_active FROM employees WHERE emp_id = :emp_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":emp_id" => $emp_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // अगर is_active column नहीं है तो default active रहेगा
        if (!$result || !isset($result['is_active'])) {
            return true;
        }
        
        return $result['is_active'] == '1';
    }
}

$database = new Database();
$db = $database->connect();
$user = new User($db);

$error = "";

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $data = $user->login($email);
    
    if($data) {
        // पहले check करें कि account active है या नहीं
        if(isset($data['is_active']) && $data['is_active'] == '0') {
            $error = "❌ Your account has been disabled by admin. Please contact your administrator.";
        } else {
            // फिर password verify करें
            if(password_verify($password, $data['password'])) {
                $_SESSION['emp_id'] = $data['emp_id']; 
                $_SESSION['name'] = $data['name'];
                header("Location: employee_dashboard.php");
                exit;
            } else {
                // Password plain text में है (backward compatibility के लिए)
                if($password === $data['password']) {
                    $error = "Aapka password secure nahi hai (Plain Text). Registration mein password_hash use karein.";
                } else {
                    $error = "Password galat hai!";
                }
            }
        }
    } else {
        $error = "Email database mein nahi mila!";
    }
}
?>
<!DOCTYPE html>
<html lang="hi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Login - Modern UI</title>
    <link rel="stylesheet" href="css/employee_login.css">
   
</head>

<body>
    <div class="login-card">
        <div class="form-header">
            <h2>Employee Login</h2>

            <div class="status-msg">
                <?php 
                if(isset($_SESSION['user_email'])) {
                    echo "<p style='color:green; font-weight:500;'>Registered: " . htmlspecialchars($_SESSION['user_email']) . "</p>";
                } else if(isset($_COOKIE['user_login'])) {
                    echo "<p style='color:#555;'>Welcome back, <b>" . htmlspecialchars($_COOKIE['user_login']) . "</b>!</p>";
                } else {
                    echo "<p style='color:#777;'>Apne credentials enter karein</p>";
                }
                ?>
            </div>
        </div>

        <?php if(!empty($error)) { ?>
        <div class="status-msg" style="background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;">
            <?php echo $error; ?>
        </div>
        <?php } ?>
        <form action="" method="POST">
            <div class="input-field">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-field">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="login" class="submit-btn">Login</button>
        </form>
        <div style="text-align: center; margin-top: 15px;">
            <a href="../index.php" style="color: #4b5563; text-decoration: none; font-weight: 500;">
                Back to Website
            </a>
        </div>

    </div>
</body>

</html>
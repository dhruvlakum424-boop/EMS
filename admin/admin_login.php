<?php
session_start();
include('../config/db.php');

// Class ko pehle define karna zaroori hai
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email) {
        // Table se data nikalne ke liye query
        $sql = "SELECT * FROM admin WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":email" => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Check agar admin pehle se login hai
if(isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$database = new Database();
$db = $database->connect();
$user = new User($db);

$error = "";




if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $data = $user->login($email);
    
    // Check karein password sahi hai ya nahi
    if($data && password_verify($password, $data['password'])) {
        
        // Purana session clear karke naya banayein
        session_regenerate_id(true);

        $_SESSION['admin_id'] = $data['admin_id']; 
        
        // Aapke database table mein column 'name' hai, usse uthayein
        // Agar column ka naam kuch aur hai toh yahan badal dein
        $_SESSION['full_name'] = $data['full_name']; 
        
        // Session ko turant save karne ke liye
        session_write_close(); 
        // echo "<h1>Login Success!</h1>";
// echo "Redirecting to: " . $redirect;
// exit();
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Email ya password galat hai!";
    }
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Modern UI</title>
    <link rel="stylesheet" href="css/admin_login.css">
    
</head>
<body>
    <div class="login-card">
        <div class="form-header">
            <h2>Admin Login</h2>
            
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
            
            <button type="submit" name="login" class="submit-btn">Login Now</button>
        </form>
        <div style="text-align: center; margin-top: 15px;">
            <a href="../index.php" style="color: #4b5563; text-decoration: none; font-weight: 500;">
                Back to Website
            </a>
        </div>
    </div>
</body>
</html>
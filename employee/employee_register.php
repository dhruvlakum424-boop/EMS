<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../config/db.php'); 

$database = new Database();
$db = $database->connect();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['emp_code'])) {
    $emp_code  = $_POST['emp_code'];
    $full_name = $_POST['full_name'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $dob       = $_POST['dob'];
    $gender    = isset($_POST['gender']) ? $_POST['gender'] : ''; 
    $address   = $_POST['address'];
    $pass      = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $join_date = date('Y-m-d');

    try {
        
        $sql = "INSERT INTO employee (emp_code, full_name, email, password, phone, address, dob, gender, join_date) 
                VALUES (:emp_code, :full_name, :email, :password, :phone, :address, :dob, :gender, :join_date)";

        $stmt = $db->prepare($sql);

    
        $stmt->bindParam(':emp_code', $emp_code);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $pass);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':join_date', $join_date);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration Successful!";
            header("Location: employee_login.php");
            exit();
        }
    } catch (PDOException $e) {
        $error_msg = "Database Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registration Page</title>
    <style>
        /* Aapka puraana CSS yahan aayega (Jo aapne pehle diya tha) */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        /* Optimized Modern CSS */
* { 
    margin: 0; 
    padding: 0; 
    box-sizing: border-box; 
    font-family: 'Poppins', sans-serif; 
}

body { 
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%), 
                url('https://www.transparenttextures.com/patterns/cubes.png'); 
    min-height: 100vh; 
    display: flex; 
    justify-content: center; 
    align-items: center; 
    padding: 20px; 
}

.register-card { 
    background: rgba(255, 255, 255, 0.95); 
    padding: 40px; 
    border-radius: 24px; 
    box-shadow: 0 20px 60px rgba(0,0,0,0.15); 
    width: 100%; 
    max-width: 650px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.form-header { 
    text-align: center; 
    margin-bottom: 30px; 
}

.form-header h2 {
    color: #2d3436;
    font-size: 28px;
    font-weight: 700;
}

/* Role Selection - Pill Style */
.role-selection { 
    display: flex; 
    background: #f1f2f6; 
    padding: 6px; 
    border-radius: 15px; 
    margin-bottom: 30px;
    border: 1px solid #dfe6e9;
}

.role-selection input[type="radio"] { display: none; }

.role-selection label { 
    flex: 1; 
    padding: 12px; 
    text-align: center; 
    cursor: pointer; 
    border-radius: 12px; 
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
    font-weight: 600; 
    color: #636e72; 
}

.role-selection input[type="radio"]:checked + label { 
    background: #764ba2; 
    color: white; 
    box-shadow: 0 4px 15px rgba(118, 75, 162, 0.3);
}

/* Input Rows and Fields */
.input-row { 
    display: flex; 
    gap: 20px; 
    margin-bottom: 20px; 
}

@media (max-width: 600px) {
    .input-row { flex-direction: column; gap: 15px; }
}

.input-field { 
    flex: 1; 
    display: flex; 
    flex-direction: column; 
}

.input-field label { 
    font-size: 14px; 
    font-weight: 600; 
    color: #2d3436; 
    margin-bottom: 8px; 
    margin-left: 4px;
}

.input-field input, .input-field textarea { 
    padding: 14px; 
    border: 2px solid #f1f2f6; 
    border-radius: 12px; 
    outline: none; 
    font-size: 14px; 
    background: #ffffff;
    transition: all 0.3s ease;
    color: #2d3436;
}

.input-field input:focus, .input-field textarea:focus { 
    border-color: #764ba2; 
    box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.1);
    background: #fff;
}

/* Submit Button Animation */
.submit-btn { 
    width: 100%; 
    padding: 16px; 
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
    color: white; 
    border: none; 
    border-radius: 12px; 
    font-size: 16px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: transform 0.2s, box-shadow 0.2s; 
    margin-top: 15px;
    box-shadow: 0 10px 20px rgba(118, 75, 162, 0.2);
}

.submit-btn:hover { 
    transform: translateY(-2px);
    box-shadow: 0 15px 25px rgba(118, 75, 162, 0.3);
}

.submit-btn:active {
    transform: translateY(0);
}

.footer-link { 
    text-align: center; 
    margin-top: 25px; 
    font-size: 14px; 
    color: #636e72; 
}

.footer-link a {
    color: #764ba2;
    text-decoration: none;
    font-weight: 700;
}

.footer-link a:hover {
    text-decoration: underline;
}

/* Error Styling */
.error-alert { 
    color: white; 
    background: #ff7675; 
    padding: 12px; 
    border-radius: 10px; 
    margin-bottom: 20px; 
    text-align: center; 
    font-size: 14px;
    font-weight: 500;
    animation: shake 0.4s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}
    </style>
</head>
<body>

<div class="register-card">
    <div class="form-header">
        <h2>Employee Registration</h2>
    </div>

    <?php if(isset($error_msg)): ?>
        <div class="error-alert"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="role-selection">
        <input type="radio" id="admin" name="role" value="admin">
        <label for="admin"><a href="admin_registar.php" style="text-decoration:none; color:inherit;">Admin</a></label>
        
        <input type="radio" id="employee" name="role" value="employee" checked>
        <label for="employee">Employee</label>
    </div>

    <form action="" method="POST">
        <div class="input-row">
            <div class="input-field">
                <label>Employee Code</label>
                <input type="text" name="emp_code" placeholder="Ex: EMP123" required>
            </div>
            <div class="input-field">
                <label>Full Name</label>
                <input type="text" name="full_name" placeholder="Enter Name" required>
            </div>
        </div>

        <div class="input-row">
            <div class="input-field">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="example@mail.com" required>
            </div>
            <div class="input-field">
                <label>Phone Number</label>
                <input type="tel" name="phone" placeholder="9876543210" required>
            </div>
        </div>

        <div class="input-row">
            <div class="input-field">
                <label>Password</label>
                <input type="password" name="password" placeholder="********" required>
            </div>
            <div class="input-field">
                <label>Date of Birth (DOB)</label>
                <input type="date" name="dob" required>
            </div>
        </div>

        <div class="gender-section">
            <label style="font-size: 13px; font-weight: 600; color: #555;">Gender</label>
            <div class="gender-options">
                <label><input type="radio" name="gender" value="male" required> Male</label>
                <label><input type="radio" name="gender" value="female"> Female</label>
                <label><input type="radio" name="gender" value="other"> Other</label>
            </div>
        </div>

        <div class="input-field" style="margin: 15px 0;">
            <label>Address</label>
            <textarea name="address" rows="3" placeholder="Enter Full Address"></textarea>
        </div>

        <button type="submit" class="submit-btn">Register Now</button>
    </form>

    <div class="footer-link">
        Already have an account? <a href="employee_login.php" style="color:#764ba2; font-weight:600; text-decoration:none;">Login Here</a>
    </div>
</div>

</body>
</html>
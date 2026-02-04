<?php
session_start();
include('../config/db.php');

$database = new Database();
$conn = $database->connect(); // This returns a PDO object

if (!$conn) {
    die("Connection failed.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data (No need for real_escape_string with PDO prepared statements)
    $name = $_POST['emp_name'];
    $email = $_POST['emp_email'];
    $dept = $_POST['emp_dept'];
    $salary = $_POST['emp_salary'];
    $phone_no = $_POST['phone_no'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $join_date = $_POST['join_date'];
    
    // Password handling
    $plain_pass = $_POST['emp_password'];
    $hashed_pass = password_hash($plain_pass, PASSWORD_DEFAULT);
    
    // Auto-generate Employee ID
    $emp_id = "EMP-" . rand(1000, 9999);

    try {
        // Use Prepared Statements for security
        $sql = "INSERT INTO employees (emp_id, name, email, phone_no, dob, address, department, join_date, salary, password) 
                VALUES (:emp_id, :name, :email, :phone_no, :dob, :address, :dept, :join_date, :salary, :password)";
        
        $stmt = $conn->prepare($sql);
        
        // Bind parameters
        $stmt->execute([
            ':emp_id'    => $emp_id,
            ':name'      => $name,
            ':email'     => $email,
            ':phone_no'  => $phone_no,
            ':dob'       => $dob,
            ':address'   => $address,
            ':dept'      => $dept,
            ':join_date' => $join_date,
            ':salary'    => $salary,
            ':password'  => $hashed_pass
        ]);

        $_SESSION['success_msg'] = "New Employee Added Successfully!";

        // Email Logic (Remains the same)
        $to = $email;
        $subject = "Welcome! Your Employee Login Credentials";
        $message = "<html>... [Your existing HTML Message] ...</html>"; // Keep your existing HTML string
        
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: admin@company.com";

        @mail($to, $subject, $message, $headers);
        
        header("Location: admin_employee.php");
        exit();

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
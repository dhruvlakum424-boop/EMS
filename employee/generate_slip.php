<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['emp_id'])) {
    header("Location: employee_login.php");
    exit;
}

$database = new Database();
$db = $database->connect();

// GET ki jagah POST se data lein
if(!isset($_POST['salary_id'])) {
    die("Invalid Request");
}
$salary_id = $_POST['salary_id'];

try {
    // JOIN query taaki employee ka naam aur details bhi mil sakein
    // Is query mein hum salary_id ke saath-saath login user ki emp_id bhi check kar rahe hain
$query = "SELECT s.*, e.name, e.department, e.emp_id as e_code 
          FROM salary s 
          JOIN employees e ON s.emp_id = e.id 
          WHERE s.salary_id = :s_id AND e.emp_id = :session_emp_id";

$stmt = $db->prepare($query);
$stmt->execute([
    's_id' => $salary_id,
    'session_emp_id' => $_SESSION['emp_id'] // Session se login user ki ID
]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$data) {
    die("Unauthorized Access! Aap sirf apni payslip dekh sakte hain.");
}

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Slip - <?php echo $data['e_code']; ?></title>
    <link rel="stylesheet" href="css/generate_slip.css">
</head>
<body>

    <div class="slip-container">
        <div class="header">
            <h2>EMPLOYEE PAYSLIP</h2>
            <p><strong>Month:</strong> <?php echo $data['month']; ?></p>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <p><strong>Employee Name:</strong> <?php echo htmlspecialchars($data['name']); ?></p>
                <p><strong>Employee ID:</strong> <?php echo htmlspecialchars($data['e_code']); ?></p>
            </div>
            <div class="info-box" style="text-align: right;">
                <p><strong>Department:</strong> <?php echo htmlspecialchars($data['department']); ?></p>
                <p><strong>Payment Date:</strong> <?php echo date('d-m-Y', strtotime($data['paid_date'])); ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td style="text-align: right;"><?php echo number_format($data['basic_salary'], 2); ?></td>
                </tr>
                <tr>
                    <td>Allowances</td>
                    <td style="text-align: right;"><?php echo number_format($data['allowance'], 2); ?></td>
                </tr>
                <tr>
                    <td>Deductions</td>
                    <td style="text-align: right; color: #e74c3c;">- <?php echo number_format($data['deduction'], 2); ?></td>
                </tr>
                <tr class="total-row">
                    <td>Net Salary Payable</td>
                    <td style="text-align: right; color: #27ae60;">₹<?php echo number_format($data['net_salary'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <p style="font-size: 12px; color: #888; text-align: center; margin-top: 40px;">
            *This is a computer-generated payslip and does not require a signature.
        </p>

        <div class="btn-group">
            <a href="salary_history.php" class="btn btn-back">Back to History</a>
            <button onclick="window.print()" class="btn btn-print">Print / Download PDF</button>
        </div>
    </div>

</body>
</html>
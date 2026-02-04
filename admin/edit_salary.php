<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "employee_management_system");
if (!$conn) {
    die("Database connection failed");
}

/* ================= FETCH SALARY DATA ================= */
$data = null;

if (isset($_GET['id'])) {

    $id = intval($_GET['id']);

    $query = "
    SELECT 
        s.salary_id,
        s.emp_id,
        s.month,
        s.basic_salary,
        s.allowance,
        s.deduction,
        s.net_salary,
        s.paid_date,
        e.name
    FROM salary s
    JOIN employees e ON s.emp_id = e.id
    WHERE s.salary_id = ?
    ";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if (!$data) {
        die("Invalid Salary Record");
    }
}

/* ================= UPDATE SALARY ================= */
if (isset($_POST['update_salary'])) {

    $sid = intval($_POST['salary_id']);
    $month = $_POST['month'];
    $basic = floatval($_POST['basic_salary']);
    $allowance = floatval($_POST['allowance']);
    $deduction = floatval($_POST['deduction']);
    $paid_date = $_POST['paid_date'];

    $net_salary = ($basic + $allowance) - $deduction;

    $update = "
    UPDATE salary SET 
        month = ?,
        basic_salary = ?,
        allowance = ?,
        deduction = ?,
        net_salary = ?,
        paid_date = ?
    WHERE salary_id = ?
    ";

    $stmt = mysqli_prepare($conn, $update);
    mysqli_stmt_bind_param(
        $stmt,
        "sddddsi",
        $month,
        $basic,
        $allowance,
        $deduction,
        $net_salary,
        $paid_date,
        $sid
    );

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Salary Updated Successfully'); window.location='admin_salary.php';</script>";
        exit;
    } else {
        echo "Update Failed";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Salary</title>
    <style>
       body {
    font-family: Arial, sans-serif;
    background-color: #f4f7f6;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.card {
    background: #ffffff;
    width: 380px;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border: 1px solid #e0e0e0;
}

h3 {
    margin-top: 0;
    color: #333;
    font-size: 20px;
    text-align: center;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
    font-size: 14px;
}

input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box; /* Isse input box card se bahar nahi jayega */
    font-size: 14px;
}

input:focus {
    border-color: #3498db;
    outline: none;
}

button {
    width: 100%;
    padding: 12px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.2s;
}

button:hover {
    background-color: #2980b9;
}  </style>
</head>
<body>

<div class="card">
    <h3>Update Salary for: <?php echo htmlspecialchars($data['name']); ?></h3>

    <form method="POST">
        <input type="hidden" name="salary_id" value="<?php echo $data['salary_id']; ?>">

        <label>Month</label>
        <input type="month" name="month" value="<?php echo $data['month']; ?>" required>

        <label>Basic Salary</label>
        <input type="number" name="basic_salary" value="<?php echo $data['basic_salary']; ?>" required>

        <label>Allowance</label>
        <input type="number" name="allowance" value="<?php echo $data['allowance']; ?>">

        <label>Deduction</label>
        <input type="number" name="deduction" value="<?php echo $data['deduction']; ?>">

        <label>Paid Date</label>
        <input type="date" name="paid_date" value="<?php echo $data['paid_date']; ?>" required>

        <button type="submit" name="update_salary">Update Salary</button>
    </form>
</div>

</body>
</html>

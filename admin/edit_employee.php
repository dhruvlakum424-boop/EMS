<!-- <?php
session_start();
// Database connection
$conn = mysqli_connect("localhost", "root", "", "employee_management_system");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 1. Check karein ki URL me 'id' hai ya nahi
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin_employee.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Fetch Employee Data
$res = mysqli_query($conn, "SELECT * FROM employees WHERE id = '$id'");
$data = mysqli_fetch_assoc($res);

// Agar ID galat hai aur data nahi mila
if (!$data) {
    die("Employee not found!");
}

// 3. Update Logic
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $dept = mysqli_real_escape_string($conn, $_POST['emp_dept']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $join_date = mysqli_real_escape_string($conn, $_POST['join_date']);
    
    // SQL Update Query with new fields
    $update_sql = "UPDATE employees SET 
                    name='$name', 
                    department='$dept', 
                    salary='$salary', 
                    phone_no='$phone_no', 
                    dob='$dob', 
                    address='$address', 
                    join_date='$join_date' 
                   WHERE id='$id'";
    
    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Employee Data Updated Successfully!'); window.location.href='admin_employee.php';</script>";
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Employee | Admin Panel</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            display: flex;
            justify-content: center;
            padding: 40px 0;
        }

        .edit-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 450px;
        }

        h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 13px;
            color: #34495e;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .btn-update {
            background: #27ae60;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-update:hover {
            background: #219150;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="edit-card">
        <h3>Edit Employee:
            <?php echo $data['emp_id']; ?>
        </h3>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo $data['name']; ?>" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_no" value="<?php echo $data['phone_no']; ?>" required>
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" value="<?php echo $data['dob']; ?>" required>
            </div>

            <div class="form-group">
                <label>Department</label>
                <select name="emp_dept" required>
                    <option value="">Select Department</option>
                    <?php
                $dept_query = "SELECT * FROM department";
                $dept_result = mysqli_query($conn, $dept_query);
                while($dept_row = mysqli_fetch_assoc($dept_result)) {
                    // Agar ye department employee ka current department hai, to select kar lo
                    $selected = ($dept_row['dept_name'] == $data['department']) ? "selected" : "";
                    echo "<option value='".$dept_row['dept_name']."' $selected>".$dept_row['dept_name']."</option>";
                }
                ?>
                </select>
            </div>

            <div class="form-group">
                <label>Joining Date</label>
                <input type="date" name="join_date" value="<?php echo $data['join_date']; ?>" required>
            </div>

            <div class="form-group">
                <label>Monthly Salary (₹)</label>
                <input type="number" name="salary" value="<?php echo $data['salary']; ?>" required>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3" required><?php echo $data['address']; ?></textarea>
            </div>

            <button type="submit" name="update" class="btn-update">Save Changes</button>
            <a href="admin_employee.php" class="back-link"><i class="fas fa-arrow-left"></i> Cancel and Go Back</a>
        </form>
    </div>

</body>

</html> -->
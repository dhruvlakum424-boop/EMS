<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "employee_management_system");
if (!$conn) { die("Connection failed"); }

/* ================= ADMIN DATA ================= */
$admin_id = $_SESSION['admin_id'];
$admin_name = "Admin";
$admin_res = mysqli_query($conn, "SELECT full_name FROM admin WHERE admin_id='$admin_id'");
if ($admin_res && mysqli_num_rows($admin_res) > 0) {
    $admin_name = mysqli_fetch_assoc($admin_res)['full_name'];
}

/* ================= SALARY HISTORY ================= */
$salary_history_query = "
SELECT salary.salary_id, salary.emp_id, employees.name, salary.month, salary.net_salary, salary.paid_date
FROM salary
JOIN employees ON salary.emp_id = employees.id
ORDER BY salary.paid_date DESC";
$salary_history_res = mysqli_query($conn, $salary_history_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Management | Admin Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
   <link rel="stylesheet" href="css/admin_salary.css">
</head>
 
<body>

    <div class="container">
        <?php include('../include/admin_navbar.php'); ?>

        <main class="main-content">
            <header>
                <h2>Salary Management</h2>
                <div class="user-info"><span>Welcome, <strong>
                            <?php echo $admin_name; ?>
                        </strong></span></div>
            </header>

            <div class="salary-wrapper">
                <div class="card">
                    <h3>Add Salary Record</h3>
                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                    <form action="process_salary.php" method="POST">
                        <div class="form-group">
                            <label>Select Employee</label>
                            <select name="emp_id" id="employee_select" required>
                                <option value="">-- Select Employee --</option>
                                <?php
                                $res = mysqli_query($conn, "SELECT id, name FROM employees");
                                while ($row = mysqli_fetch_assoc($res)) {
                                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Salary Month</label>
                            <input type="month" name="month" required>
                        </div>
                        <div class="form-group">
                            <label>Basic Salary (₹)</label>
                            <input type="number" name="basic_salary" id="basic_salary_field" readonly>
                        </div>
                        <div class="form-group">
                            <label>Allowance (₹)</label>
                            <input type="number" name="allowance" id="allowance" value="0">
                        </div>
                        <div class="form-group">
                            <label>Deduction / Penalty (₹)</label>
                            <input type="number" name="deduction" id="deduction_field" value="0" readonly>
                        </div>
                        <div class="form-group">
                            <label>Payment Date</label>
                            <input type="date" name="paid_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <button type="submit" class="btn-save">Pay Salary</button>
                    </form>
                </div>

                <div class="card">
                    <h3>Salary History</h3>
                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                    <div class="table-responsive">
                        <table id="salaryTable" class="myTable">
                            <thead>
                                <tr>
                                    <th>Emp ID</th>
                                    <th>Employee</th>
                                    <th>Month</th>
                                    <th>Net Salary</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($salary_history_res)): ?>
                                <tr>
                                    <td>EMP-
                                        <?php echo $row['emp_id']; ?>
                                    </td>
                                    <td><strong>
                                            <?php echo $row['name']; ?>
                                        </strong></td>
                                    <td>
                                        <?php echo date('F Y', strtotime($row['month'])); ?>
                                    </td>
                                    <td>₹
                                        <?php echo number_format($row['net_salary'], 2); ?>
                                    </td>
                                    <td><span class="status-paid">Paid</span></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
<script src="//cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
<script>
    let table = new DataTable('#myTable');
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#salaryTable').DataTable();
    });
</script>

    
    <script>
        $(document).ready(function () {
            // --- PEHLE WALA AJAX CODE ---
            $('#employee_select').on('change', function () {
                var empID = $(this).val();
                if (empID != "") {
                    $.ajax({
                        url: 'get_employee_data.php',
                        method: 'POST',
                        data: { emp_id: empID },
                        dataType: 'json',
                        success: function (response) {
                            $('#basic_salary_field').val(response.basic_salary);
                            $('#deduction_field').val(response.penalty);
                        }
                    });
                } else {
                    $('#basic_salary_field, #deduction_field').val(0);
                }
            });

            // --- NAYA SWEETALERT LOGIC ---
            <?php if(isset($_SESSION['success_msg'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: '<?php echo $_SESSION['success_msg']; ?>',
                    text: '<?php echo $_SESSION['details']; ?>',
                    confirmButtonColor: '#27ae60'
                });
                <?php unset($_SESSION['success_msg']); unset($_SESSION['details']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_msg'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '<?php echo $_SESSION['error_msg']; ?>',
                    confirmButtonColor: '#e74c3c'
                });
                <?php unset($_SESSION['error_msg']); ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>
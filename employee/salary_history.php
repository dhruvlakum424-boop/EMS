<?php
session_start();
include('../config/db.php');

// 1. Session check - Yahan check karein ki 'emp_id' hi use ho raha hai na?
if(!isset($_SESSION['emp_id'])) {
    header("Location: employee_login.php");
    exit;
}

$database = new Database();
$db = $database->connect();
$emp_id = $_SESSION['emp_id'];

try {
    if($db == null) { die("Database connection failed!"); }

    // 2. Query - Table ka naam 'salary' aur column 'emp_id' check karein
    // Purani query ko hata kar ye wali likhein
$query = "SELECT s.* FROM salary s 
          JOIN employees e ON s.emp_id = e.id 
          WHERE e.emp_id = :emp_id 
          ORDER BY s.paid_date DESC";

$stmt = $db->prepare($query);
// Session me jo "EMP-8542" hai wo ab sahi match karega
$stmt->execute(['emp_id' => $emp_id]);
$salaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging ke liye (Jab data dikhne lage toh ise delete kar dena)
    // if(empty($salaries)) { echo "Database mein is ID ke liye koi data nahi mila."; }

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Salary History | Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/salary_history.css">
</head>

<body>

    <div class="container">
        <?php include('../include/employee_navbar.php'); ?>

        <main class="main-content">
            <header>
                <h2>My Salary Records</h2>
                <div class="user-info">
                    <span>ID: <strong>
                            <?php echo htmlspecialchars($emp_id); ?>
                        </strong></span>
                </div>
            </header>

            <section class="salary-section">
                <div class="salary-header">
                    <h3>History of Payments</h3>
                </div>

                <div style="overflow-x: auto;">
                    <table id="salaryhTable" class="myTable">
                        <thead>
                            <tr>
                                <th>Month/Year</th>
                                <th>Basic Salary</th>
                                <th>Allowances</th>
                                <th>Deductions</th>
                                <th>Net Amount</th>
                                <th>Payment Date</th>
                                <th>Payslip</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($salaries)): ?>
                            <?php foreach ($salaries as $row): ?>
                            <tr>
                                <td><strong>
                                        <?php echo htmlspecialchars($row['month']); ?>
                                    </strong></td>
                                <td class="amount">₹
                                    <?php echo number_format($row['basic_salary'], 2); ?>
                                </td>
                                <td class="amount">₹
                                    <?php echo number_format($row['allowance'], 2); ?>
                                </td>
                                <td class="deduction">-₹
                                    <?php echo number_format($row['deduction'], 2); ?>
                                </td>
                                <td class="net-pay">₹
                                    <?php echo number_format($row['net_salary'], 2); ?>
                                </td>
                                <td>
                                    <?php echo date('d M, Y', strtotime($row['paid_date'])); ?>
                                </td>
                                <td>
                                    <form action="generate_slip.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="salary_id" value="<?php echo $row['salary_id']; ?>">
                                        <button type="submit" class="btn-download" style="border:none; cursor:pointer;">
                                            <i class="fas fa-file-pdf"></i> Download
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="no-data">
                                    <i class="fas fa-info-circle"></i> No salary history found for your account.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="//cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('#myTable');
    </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#salaryhTable').DataTable();
        });
    </script>

</body>

</html>
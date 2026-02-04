<?php
session_start();
include('../config/db.php');// Aapki Database class wali file

if(!isset($_SESSION['emp_id'])) {
    header("Location: employee_login.php");
    exit;
}

$database = new Database();
$db = $database->connect();
$emp_id = $_SESSION['emp_id'];

try {
    // Sirf login employee ki leaves fetch karna
    $query = "SELECT * FROM leaves WHERE emp_id = :emp_id ORDER BY applied_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':emp_id', $emp_id);
    $stmt->execute();
    $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave History - Employee Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/leave_history.css">
</head>

<body>

    <div class="container">
        <?php include('../include/employee_navbar.php'); ?>

        <main class="main-content">
            <header>
                <h2>My Leave History</h2>
                <div class="user-info">
                    <span>Logged in as: <strong>
                            <?php echo htmlspecialchars($emp_id); ?>
                        </strong></span>
                </div>
            </header>
            <?php if(isset($_SESSION['msg'])): ?>
    <div class="flash-message">
        <span>
            <i class="fas fa-check-circle"></i> 
            <strong><?php echo $_SESSION['msg']; ?></strong> 
            (Penalty: ₹<?php echo $_SESSION['penalty']; ?>)
        </span>
        <button onclick="this.parentElement.style.display='none'" style="background:none; border:none; cursor:pointer;">&times;</button>
    </div>
    <?php unset($_SESSION['msg']); unset($_SESSION['penalty']); ?>
<?php endif; ?>

            <section class="history-section">
                <h3>Request Records</h3>
                <div style="overflow-x: auto;">
                    <table id="leavehTable" class="myTable">
                        <thead>
                            <tr>
                                <th>Applied On</th>
                                <th>Leave Type</th>
                                <th>From - To Date</th>
                                <th>Reason</th>
                                <th>Status</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if($leaves): ?>
                            <?php foreach($leaves as $row): 
                                    // Status class decide karna
                                    $statusClass = 'status-pending';
                                    if(strtolower($row['status']) == 'approved') $statusClass = 'status-approved';
                                    if(strtolower($row['status']) == 'rejected') $statusClass = 'status-rejected';
                                ?>
                            <tr>
                                <td>
                                    <?php echo date('d-M-Y', strtotime($row['applied_at'])); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['leave_name']); ?>
                                </td>
                                <td>
                                    <?php 
                                            echo date('d M', strtotime($row['from_date'])) . " to " . date('d M Y', strtotime($row['to_date'])); 
                                        ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['reason']); ?>
                                </td>
                                <td><span class="status <?php echo $statusClass; ?>">
                                        <?php echo $row['status']; ?>
                                    </span></td>

                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No leave records found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
<?php if(isset($_SESSION['msg'])): ?>
<script>
    Swal.fire({
        title: "<?php echo $_SESSION['msg']; ?>",
        text: "Applied Penalty: ₹<?php echo $_SESSION['penalty']; ?>",
        icon: "<?php echo $_SESSION['msg_type']; ?>",
        confirmButtonColor: "#3498db"
    });
</script>
<?php unset($_SESSION['msg']); unset($_SESSION['penalty']); unset($_SESSION['msg_type']); ?>
<?php endif; ?>

<script src="//cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
<script>
    let table = new DataTable('#myTable');
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#leavehTable').DataTable();
    });
</script>
</body>

</html>
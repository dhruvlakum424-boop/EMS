<?php
session_start();
include('../config/db.php');

// Agar admin login nahi hai, toh login page par bhej dein
if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$database = new Database();
$db = $database->connect();

// Session se login name lein, agar na mile toh "Admin" dikhayein
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : "Admin";

try {
    // 1. Total Employees
    $stmt1 = $db->prepare("SELECT COUNT(*) as total FROM employees");
    $stmt1->execute();
    $total_employees = $stmt1->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 2. Total Departments
    $stmt2 = $db->prepare("SELECT COUNT(*) as total FROM department");
    $stmt2->execute();
    $total_departments = $stmt2->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 3. Pending Leaves
    $stmt3 = $db->prepare("SELECT COUNT(*) as total FROM leaves WHERE status = 'Pending'");
    $stmt3->execute();
    $total_pending_leaves = $stmt3->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 4. Recent Leave Requests 
    // Yahan 'leave_id' ko badal kar 'id' kar diya hai
    $stmt4 = $db->prepare("SELECT * FROM leaves ORDER BY id DESC LIMIT 5");
    $stmt4->execute();
    $recent_leaves = $stmt4->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Query mein profile_image column hona chahiye
$query = "SELECT full_name, profile_image FROM admin WHERE admin_id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute([':id' => $_SESSION['admin_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Image path check karein
$user_image = !empty($user['profile_image']) ? "../uploads/admin_pics/" . $user['profile_image'] : "https://cdn-icons-png.flaticon.com/512/149/149071.png";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"> -->
    <link rel="stylesheet" href="css/admin_dashboard.css">

</head>

<body>

    <div class="container" style="overflow-x: auto;">
       <?php include('../include/admin_navbar.php'); ?>
        <main class="main-content">
            <header>
                <h2>Dashboard Overview</h2>
                <div class="admin-info">
                    <div class="user-info" style="display: flex; align-items: center; gap: 10px;">
                        <span>Welcome, <strong>
                                <?php echo htmlspecialchars($user['full_name']); ?>
                            </strong></span>

                        <img src="<?php echo $user_image; ?>" class="dash-profile-img" alt="Admin">
                    </div>

                </div>
            </header>

            <div class="cards-container">
                <div class="card">
                    <i class="fas fa-users"></i>
                    <div>
                        <h3>
                            <?php echo $total_employees; ?>
                        </h3>
                        <p>Total Employees</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-building"></i>
                    <div>
                        <h3>
                            <?php echo $total_departments; ?>
                        </h3>
                        <p>Departments</p>
                    </div>
                </div>
                <div class="card">
                    <i class="fas fa-envelope-open-text"></i>
                    <div>
                        <h3>
                            <?php echo $total_pending_leaves; ?>
                        </h3>
                        <p>Pending Leaves</p>
                    </div>
                </div>
            </div>

            <section class="data-section">
                <h3>Recent Leave Requests</h3>
                <table >   
                    <!-- id="dashTable" class="myTable" -->
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Leave Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($recent_leaves)): ?>
                        <?php foreach($recent_leaves as $row): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row['employee_name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['leave_name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['from_date']); ?>
                            </td>

                            <td>
                                <span class="status <?php echo strtolower($row['status']); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>

                            <td>
                                <a href="admin_leave_requests.php?id=<?php echo $row['id']; ?>"
                                    class="btn-view">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">No records found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- <script src="//cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
<script>
    let table = new DataTable('#myTable');
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dashTable').DataTable();
    });
</script> -->
</body>

</html>
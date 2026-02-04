<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('../config/db.php');

if(!isset($_SESSION['emp_id'])) {
    header("Location: employee_login.php");
    exit;
}

$emp_id = $_SESSION['emp_id'];

$database = new Database();
$db = $database->connect();

// 2. Employee Profile fetch karein
$emp_sql = "SELECT * FROM employees WHERE emp_id = :emp_id";
$emp_stmt = $db->prepare($emp_sql);
$emp_stmt->execute([':emp_id' => $emp_id]);
$employees = $emp_stmt->fetch(PDO::FETCH_ASSOC);

if (!$employees) {
    $employees = [
        'name' => 'User Not Found',
        'emp_id' => $emp_id,
        'department' => 'N/A',
        'designation' => 'N/A',
        'email' => 'N/A'
    ];
}

// --- TABLE NAMES FIXED BELOW (leave_request -> leave_requests) ---

// 3. Leave Statistics
// Yahan check karein agar aapki table ka naam 'leave_requests' hai (with 's')
$total_sql = "SELECT COUNT(*) FROM leaves WHERE emp_id = :emp_id";
$total_stmt = $db->prepare($total_sql);
$total_stmt->execute([':emp_id' => $emp_id]);
$total_leaves = $total_stmt->fetchColumn() ?: 0;

$app_sql = "SELECT COUNT(*) FROM leaves WHERE emp_id = :emp_id AND status = 'Approved'";
$app_stmt = $db->prepare($app_sql);
$app_stmt->execute([':emp_id' => $emp_id]);
$approved_leaves = $app_stmt->fetchColumn() ?: 0;

$pen_sql = "SELECT COUNT(*) FROM leaves WHERE emp_id = :emp_id AND status = 'Pending'";
$pen_stmt = $db->prepare($pen_sql);
$pen_stmt->execute([':emp_id' => $emp_id]);
$pending_leaves = $pen_stmt->fetchColumn() ?: 0;

// Query mein profile_image column hona chahiye
$query = "SELECT name, department, profile_image FROM employees WHERE emp_id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute([':id' => $_SESSION['emp_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Image path check karein
$user_image = !empty($user['profile_image']) ? "../uploads/profile_pics/" . $user['profile_image'] : "https://cdn-icons-png.flaticon.com/512/149/149071.png";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
   <link rel="stylesheet" href="css/employee_dashboard.css">
</head>

<body>
    <div class="container">
        
<?php include('../include/employee_navbar.php'); ?>
        <main class="main-content">
            <header>
                <h2>Employee Overview</h2>
                <div class="user-info">
                    <div class="user-info" style="display: flex; align-items: center; gap: 10px;">
                        <span>Welcome, <strong>
                                <?php echo htmlspecialchars($user['name']); ?>
                            </strong></span>

                        <img src="<?php echo $user_image; ?>" class="dash-profile-img" alt="User">
                    </div>

            </header>

            <div class="cards-container">
                <div class="card card-blue">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <h3>
                            <?php echo $total_leaves; ?>
                        </h3>
                        <p>Total Leaves</p>
                    </div>
                </div>
                <div class="card card-green">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h3>
                            <?php echo $approved_leaves; ?>
                        </h3>
                        <p>Approved</p>
                    </div>
                </div>
                <div class="card card-orange">
                    <i class="fas fa-hourglass-half"></i>
                    <div>
                        <h3>
                            <?php echo $pending_leaves; ?>
                        </h3>
                        <p>Pending</p>
                    </div>
                </div>
            </div>

            <section class="data-section">
                <h3>My Profile Details</h3>
                <div class="profile-details">
                    <p><strong>Employee ID:</strong>
                        <?php echo htmlspecialchars($employees['emp_id']); ?>
                    </p>
                    <p><strong>Email:</strong>
                        <?php echo htmlspecialchars($employees['email']); ?>
                    </p>
                    <p><strong>Department:</strong>
                        <?php echo htmlspecialchars($employees['department'] ?? 'Not Assigned'); ?>
                    </p>
                </div>
            </section>
        </main>
    </div>

</body>

</html>
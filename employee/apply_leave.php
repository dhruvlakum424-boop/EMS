<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['emp_id'])) {
    header("Location: employee_login.php");
    exit;
}

$database = new Database();
$db = $database->connect();
$emp_id = $_SESSION['emp_id'];

$yearly_limit = 18; // 18 applications ki limit

try {
    // 1. Employee Name fetch
    $stmt = $db->prepare("SELECT name FROM employees WHERE emp_id = :emp_id");
    $stmt->bindParam(':emp_id', $emp_id);
    $stmt->execute();
    $emp_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $full_name = ($emp_data) ? $emp_data['name'] : "Unknown Employee";

    // 2. Count Total Leaves (Entries counting)
    $leave_count_stmt = $db->prepare("SELECT COUNT(id) as total_entries FROM leaves WHERE emp_id = :emp_id AND status = 'Approved'");
    $leave_count_stmt->bindParam(':emp_id', $emp_id);
    $leave_count_stmt->execute();
    $leave_data = $leave_count_stmt->fetch(PDO::FETCH_ASSOC);
    $total_used_leaves = $leave_data['total_entries'] ? $leave_data['total_entries'] : 0;

    // 3. Penalty Amount fetch (Sum of penalty column)
    $penalty_stmt = $db->prepare("SELECT SUM(penalty) as total_deduction FROM leaves WHERE emp_id = :emp_id AND status = 'Approved'");
    $penalty_stmt->bindParam(':emp_id', $emp_id);
    $penalty_stmt->execute();
    $penalty_data = $penalty_stmt->fetch(PDO::FETCH_ASSOC);
    $total_penalty = $penalty_data['total_deduction'] ? $penalty_data['total_deduction'] : 0;

    // 4. Leave Types for Dropdown
    $type_stmt = $db->prepare("SELECT leave_name FROM leave_type");
    $type_stmt->execute();
    $leave_types = $type_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leave - Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
   <link rel="stylesheet" href="css/apply_leave.css">
</head>

<body>

    <div class="container">
       <?php include('../include/employee_navbar.php'); ?>

        <main class="main-content">
            <header>
                <h2>Apply for Leave</h2>
                <div class="user-info">
                    <span>Welcome, <strong>
                            <?php echo $full_name; ?>
                        </strong> (ID:
                        <?php echo $emp_id; ?>)
                    </span>
                </div>
            </header>

            <div class="leave-summary">
                <div class="stat-card total">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Total Leave Limit</span>
                        <span class="stat-value">
                            <?php echo $yearly_limit; ?>
                        </span>
                    </div>
                </div>

                <div class="stat-card used">
                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Used Leave (Count)</span>
                        <span class="stat-value">
                            <?php echo $total_used_leaves; ?>
                        </span>
                    </div>
                </div>

                <div class="stat-card salary">
                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Deduction (₹)</span>
                        <span class="stat-value"
                            style="color: <?php echo ($total_penalty > 0) ? '#e74c3c' : '#2c3e50'; ?>">
                            ₹
                            <?php echo number_format($total_penalty, 2); ?>
                        </span>
                    </div>
                </div>
            </div>

            <section class="form-section">
                <h3>Leave Request Form</h3>
                <form action="process_leave.php" method="POST">
                    <input type="hidden" name="emp_id" value="<?php echo $emp_id; ?>">
                    <input type="hidden" name="employee_name" value="<?php echo $full_name; ?>">

                    <div class="form-group">
                        <label>Leave Type</label>
                        <select name="leave_name" required>
                            <option value="">-- Choose Type --</option>
                            <?php foreach($leave_types as $type): ?>
                            <option value="<?php echo $type['leave_name']; ?>">
                                <?php echo $type['leave_name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>From Date</label>
                            <input type="date" name="from_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group">
                            <label>To Date</label>
                            <input type="date" name="to_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Reason for Leave</label>
                        <textarea name="reason" rows="4" placeholder="Mention the reason clearly..."
                            required></textarea>
                    </div>

                    <div class="form-row" style="align-items: center; margin-bottom: 25px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Status:</label>
                            <span class="info-tag">Pending (New)</span>
                        </div>
                        <div class="form-group" style="margin-bottom:0; text-align: right;">
                            <label>Date of Application:</label>
                            <span>
                                <?php echo date('d-M-Y'); ?>
                            </span>
                        </div>
                    </div>

                    <button type="submit" name="apply" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Submit Application
                    </button>
                </form>
            </section>
        </main>
    </div>
</body>
 
</html>
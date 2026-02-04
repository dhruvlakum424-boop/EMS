<!-- <?php
include('../config/db.php');
$database = new Database();
$conn = $database->connect(); 

// URL se emp_id lena
if (isset($_GET['id'])) {
    $emp_id = $_GET['id'];

    try {
        // 1. Employee ki personal details
        $emp_stmt = $conn->prepare("SELECT * FROM employees WHERE emp_id = :emp_id");
        $emp_stmt->execute(['emp_id' => $emp_id]);
        $employee = $emp_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            die("<h3>Employee not found!</h3>");
        }

        // 2. Leaves aur Penalty ka calculation
        $leave_stmt = $conn->prepare("SELECT 
                            COUNT(id) as total_leave_count, 
                            SUM(deduction_days) as total_deduction, 
                            SUM(penalty) as total_penalty 
                        FROM leaves WHERE emp_id = :emp_id AND status = 'Approved'");
        $leave_stmt->execute(['emp_id' => $emp_id]);
        $leave_data = $leave_stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Attendance Summary (Counting 'A' for Absent)
        $att_summary_stmt = $conn->prepare("SELECT 
                            SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as total_absents, 
                            SUM(penalty) as total_att_penalty 
                        FROM attendance WHERE emp_id = :emp_id");
        $att_summary_stmt->execute(['emp_id' => $emp_id]);
        $att_data = $att_summary_stmt->fetch(PDO::FETCH_ASSOC);

        // Null handling
        $total_absents = $att_data['total_absents'] ?? 0;
        $total_att_penalty = $att_data['total_att_penalty'] ?? 0;

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("<h3>No Employee ID provided!</h3>");
}

// Employee profile image logic
$emp_upload_dir = __DIR__ . '/../uploads/profile_pics/';
$emp_web_path   = '../uploads/profile_pics/';
$emp_image = (!empty($employee['profile_image']) && file_exists($emp_upload_dir . $employee['profile_image']))
    ? $emp_web_path . $employee['profile_image']
    : "https://cdn-icons-png.flaticon.com/512/149/149071.png";
?>

<!DOCTYPE html>
<html>

<head>
    <title>Employee Details | Admin</title>
   <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 20px 15px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e3e7f0 100%);
        min-height: 100vh;
        color: #2d3748;
        line-height: 1.4;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .card {
        background: #ffffff;
        padding: 25px;
        border-radius: 18px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 750px;
        position: relative;
        overflow: visible;
        margin: 0 auto;
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #3498db, #1a73e8);
        border-radius: 18px 18px 0 0;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 20px;
        text-decoration: none;
        color: #4a6ee0;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 6px 12px;
        border-radius: 6px;
        background: #f0f4ff;
        border: 1px solid #e0e7ff;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 25px;
        padding-bottom: 18px;
        border-bottom: 1px solid #edf2f7;
    }

    .profile-img {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 15px rgba(52, 152, 219, 0.15);
        background: linear-gradient(45deg, #3498db, #1a73e8);
        padding: 3px;
        flex-shrink: 0;
    }

    .profile-header div {
        flex: 1;
    }

    .profile-header h2 {
        margin: 0 0 5px 0;
        font-size: 1.5rem;
        color: #2d3748;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-label {
        font-size: 0.8rem;
        color: #718096;
        font-weight: 500;
    }

    .info-value {
        font-size: 0.95rem;
        color: #2d3748;
        font-weight: 500;
        background: #f8fafc;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: #ffffff;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #3498db;
    }

    .stat-card:nth-child(2) {
        border-left-color: #e67e22;
    }

    .stat-card h3 {
        margin: 0 0 10px 0;
        font-size: 1rem;
        color: #2d3748;
        font-weight: 600;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .stat-item:last-child {
        margin-bottom: 0;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #4a5568;
    }

    .stat-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2d3748;
    }

    .penalty-badge {
        background: #fff5f5;
        color: #e53e3e;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #fed7d7;
        margin-top: 5px;
        text-align: center;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin: 20px 0 12px 0;
        padding-bottom: 6px;
        border-bottom: 2px solid #3498db;
    }

    .compact-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 0.85rem;
    }

    .compact-table thead {
        background: #f8fafc;
    }

    .compact-table th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        color: #4a5568;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.8rem;
    }

    .compact-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #4a5568;
    }

    .compact-table tbody tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .emp-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 8px;
    }

    .emp-tag {
        background: #f1f8ff;
        color: #4a6ee0;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .emp-tag.department {
        background: #f0f0f0;
        color: #4a5568;
    }

    /* Scrollable tables for mobile */
    @media (max-width: 768px) {
        .info-grid,
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .profile-header {
            flex-direction: column;
            text-align: center;
            gap: 12px;
        }
        
        .profile-img {
            width: 80px;
            height: 80px;
        }
        
        .profile-header h2 {
            font-size: 1.3rem;
        }
        
        .table-wrapper {
            overflow-x: auto;
            margin: 0 -10px;
            padding: 0 10px;
        }
        
        .compact-table {
            min-width: 500px;
        }
    }

    @media (max-width: 480px) {
        .card {
            padding: 20px 15px;
        }
    }
</style>
</head>

<body>
    <div class="card">
        <a href="admin_reports.php" class="back-link">← Back to Reports</a>

        <div class="profile-header">
            <img src="<?php echo $emp_image; ?>" alt="Employee Image" class="profile-img">
            <div>
                <h2><?php echo htmlspecialchars($employee['name']); ?></h2>
                <div class="emp-tags">
                    <div class="emp-tag">ID: <?php echo htmlspecialchars($employee['emp_id']); ?></div>
                    <div class="emp-tag department"><?php echo htmlspecialchars($employee['department']); ?></div>
                </div>
            </div>
        </div>

        Personal Info - Compact Grid 
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value"><?php echo htmlspecialchars($employee['email']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone</div>
                <div class="info-value"><?php echo htmlspecialchars($employee['phone_no']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Join Date</div>
                <div class="info-value"><?php echo htmlspecialchars($employee['join_date']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Basic Salary</div>
                <div class="info-value">₹ <?php echo number_format($employee['salary'], 2); ?></div>
            </div>
        </div>
        
        <div class="info-item">
            <div class="info-label">Address</div>
            <div class="info-value"><?php echo htmlspecialchars($employee['address']); ?></div>
        </div>

        Stats Summary 
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Leave Summary</h3>
                <div class="stat-item">
                    <span class="stat-label">Total Approved Leaves:</span>
                    <span class="stat-value"><?php echo $leave_data['total_leave_count'] ?: 0; ?></span>
                </div>
                <div class="penalty-badge">
                    Leave Penalty: ₹ <?php echo number_format($leave_data['total_penalty'] ?: 0, 2); ?>
                </div>
            </div>

            <div class="stat-card">
                <h3>Attendance Summary</h3>
                <div class="stat-item">
                    <span class="stat-label">Total Absents (A):</span>
                    <span class="stat-value"><?php echo $total_absents; ?></span>
                </div>
                <div class="penalty-badge">
                    Attendance Penalty: ₹ <?php echo number_format($total_att_penalty, 2); ?>
                </div>
            </div>
        </div>

        Attendance History - Only 5 rows 
        <div class="section-title">Recent Attendance (Last 5)</div>
        <div class="table-wrapper">
            <table class="compact-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Penalty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Only fetch 5 records
                    $att_stmt = $conn->prepare("SELECT * FROM attendance WHERE emp_id = :emp_id ORDER BY attendance_date DESC LIMIT 5");
                    $att_stmt->execute(['emp_id' => $emp_id]);
                    if ($att_stmt->rowCount() > 0) {
                        while($att_row = $att_stmt->fetch(PDO::FETCH_ASSOC)) {
                            $badge = ($att_row['status'] == 'P' || $att_row['status'] == 'Present') ? 'status-approved' : 'status-pending';
                            echo "<tr>
                                <td>" . date('d M, Y', strtotime($att_row['attendance_date'])) . "</td>
                                <td><span class='status-badge $badge'>{$att_row['status']}</span></td>
                                <td>₹" . number_format($att_row['penalty'], 2) . "</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center;'>No attendance history found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

         Leave History - Only 3 rows 
        <div class="section-title">Recent Leaves (Last 3)</div>
        <div class="table-wrapper">
            <table class="compact-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Reason</th>
                        <th>Penalty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Only fetch 3 records
                    $history_stmt = $conn->prepare("SELECT * FROM leaves WHERE emp_id = :emp_id AND status = 'Approved' ORDER BY from_date DESC LIMIT 3");
                    $history_stmt->execute(['emp_id' => $emp_id]);
                    if ($history_stmt->rowCount() > 0) {
                        while($row = $history_stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>
                                <td>" . date('d M', strtotime($row['from_date'])) . "</td>
                                <td>" . date('d M', strtotime($row['to_date'])) . "</td>
                                <td style='max-width:150px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;' title='" . htmlspecialchars($row['reason']) . "'>" . htmlspecialchars($row['reason']) . "</td>
                                <td>₹" . number_format($row['penalty'], 2) . "</td>
                            </tr>";          
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center;'>No leave history found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html> -->
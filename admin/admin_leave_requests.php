<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}
$admin_id = $_SESSION['admin_id'];

// 1. Database Connection
$conn = mysqli_connect("localhost", "root", "", "employee_management_system");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// --- ACTION LOGIC ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $leave_id = mysqli_real_escape_string($conn, $_GET['id']);
    $action = $_GET['action'];
    
    $new_status = ($action == 'approve') ? 'Approved' : 'Rejected';

    $update_query = "UPDATE leaves SET status = '$new_status' WHERE id = '$leave_id'";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: admin_leave_requests.php?msg=success");
        exit;
    }
}

// 2. Admin Data Fetch
$admin_name = "Admin"; 
$admin_query = "SELECT * FROM admin WHERE admin_id = '$admin_id'";
$admin_res = mysqli_query($conn, $admin_query);
if($admin_res && mysqli_num_rows($admin_res) > 0) {
    $admin_data = mysqli_fetch_assoc($admin_res);
    $admin_name = $admin_data['full_name'];
}

// 3. Leave Requests Fetch (Make sure 'reason' column exists in your table)
$leave_query = "SELECT * FROM leaves ORDER BY id DESC";
$leave_res = mysqli_query($conn, $leave_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request Management | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/admin_leave_requests.css">
</head>

<body>

    <div class="container">
        <?php include('../include/admin_navbar.php'); ?>

        <main class="main-content">
            <header>
                <h2>Leave Request Management</h2>
                <div class="user-info"><span>Welcome, <strong>
                            <?php echo $admin_name; ?>
                        </strong></span></div>
            </header>

            <div class="card">
                <h3>All Leave Requests</h3>
                <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">

                <div class="table-responsive">
                    <table id="leaverTable" class="myTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Emp ID</th>
                                <th>Employee Name</th>
                                <th>Leave Type</th>
                                <th>Dates</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(mysqli_num_rows($leave_res) > 0) {
                                while($row = mysqli_fetch_assoc($leave_res)) {
                                    $status_class = strtolower($row['status']); 
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['emp_id']}</td>
                                        <td><strong>{$row['employee_name']}</strong></td>
                                        <td>{$row['leave_name']}</td>
                                        <td>{$row['from_date']} to {$row['to_date']}</td>
                                        <td class='reason-cell'>{$row['reason']}</td> <td><span class='badge {$status_class}'>{$row['status']}</span></td>
                                        <td style='text-align: center;'>";
                                        
                                        if($row['status'] == 'Pending') {
                                            // Approve Button
                                                echo "<a href='javascript:void(0);' 
                                                        class='btn-approve' 
                                                        onclick='confirmLeaveAction({$row['id']}, \"approve\")'>
                                                        <i class='fas fa-check'></i> Approve
                                                    </a>";

                                            // Reject Button
                                                echo "<a href='javascript:void(0);' 
                                                        class='btn-reject' 
                                                        onclick='confirmLeaveAction({$row['id']}, \"reject\")'>
                                                        <i class='fas fa-times'></i> Reject
                                                    </a>";
                                        } else {
                                            echo "<span class='processed-text'>Action Taken</span>";
                                        }

                                    echo "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align:center;'>No records found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <!-- modal -->
    <div id="leaveActionModal" class="modal">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <div style="font-size: 50px; color: #f39c12; margin-bottom: 15px;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h2 id="modalTitle">Are you sure?</h2>
            <p id="modalText">Do you want to process this leave request?</p>
            <div style="margin-top: 25px; display: flex; justify-content: center; gap: 10px;">
                <button onclick="closeActionModal()" class="btn-secondary"
                    style="padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; background: #95a5a6; color: white;">Cancel</button>
                <a id="confirmBtn" href="#" class="btn-primary"
                    style="padding: 10px 20px; border-radius: 4px; text-decoration: none; color: white; font-weight: bold;">Confirm</a>
            </div>
        </div>
    </div>

    <script src="//cdn.datatables.net/2.3.6/js/dataTables.min.js"></script>
<script>
    let table = new DataTable('#myTable');
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#leaverTable').DataTable();
    });
</script>

    <script>
        function confirmLeaveAction(id, type) {
            const modal = document.getElementById('leaveActionModal');
            const title = document.getElementById('modalTitle');
            const text = document.getElementById('modalText');
            const confirmBtn = document.getElementById('confirmBtn');

            if (type === 'approve') {
                title.innerText = "Approve Leave?";
                text.innerText = "Kya aap is leave request ko approve karna chahte hain?";
                confirmBtn.style.backgroundColor = "#27ae60";
                confirmBtn.href = `admin_leave_requests.php?action=approve&id=${id}`;
            } else {
                title.innerText = "Reject Leave?";
                text.innerText = "Kya aap is leave request ko reject karna chahte hain?";
                confirmBtn.style.backgroundColor = "#e74c3c";
                confirmBtn.href = `admin_leave_requests.php?action=reject&id=${id}`;
            }

            modal.style.display = "flex"; // Modal show karne ke liye
        }

        function closeActionModal() {
            document.getElementById('leaveActionModal').style.display = "none";
        }

        // Modal ke bahar click karne par band ho jaye
        window.onclick = function (event) {
            const modal = document.getElementById('leaveActionModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>
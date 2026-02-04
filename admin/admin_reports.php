<?php
session_start();
if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Database Connection
$conn = new mysqli("localhost", "root", "", "employee_management_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Handle Status Toggle ---
if (isset($_POST['toggle_status'])) {
    $emp_id = $conn->real_escape_string($_POST['emp_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);
    
    // Update employee status
    $update_sql = "UPDATE employees SET is_active = '$new_status' WHERE emp_id = '$emp_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $_SESSION['message'] = "Employee status updated successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating status: " . $conn->error;
        $_SESSION['msg_type'] = "error";
    }
    
    // Redirect to avoid form resubmission
    $redirect_url = "admin_reports.php";
    if (isset($_GET['report_type'])) {
        $redirect_url .= '?report_type=' . $_GET['report_type'];
        if ($_GET['report_type'] == 'date_range' && !empty($_GET['from_date']) && !empty($_GET['to_date'])) {
            $redirect_url .= '&from_date=' . $_GET['from_date'] . '&to_date=' . $_GET['to_date'];
        } elseif ($_GET['report_type'] == 'dept_wise' && !empty($_GET['dept_name'])) {
            $redirect_url .= '&dept_name=' . $_GET['dept_name'];
        }
    }
    header("Location: " . $redirect_url);
    exit;
}

// --- 1. Fetch Departments for Dropdown ---
$dept_query = "SELECT dept_name FROM department";
$dept_result = $conn->query($dept_query);

// Store department result for reuse
$departments = [];
if ($dept_result->num_rows > 0) {
    while($dept_row = $dept_result->fetch_assoc()) {
        $departments[] = $dept_row['dept_name'];
    }
}

// --- 2. Logic for Report Results ---
$query = "SELECT * FROM employees WHERE 1=1"; // Default: All employees
$report_title = "All Employees";
$where_params = [];

if (isset($_GET['report_type'])) {
    if ($_GET['report_type'] == 'date_range' && !empty($_GET['from_date']) && !empty($_GET['to_date'])) {
        $from = $conn->real_escape_string($_GET['from_date']);
        $to = $conn->real_escape_string($_GET['to_date']);
        $query = "SELECT * FROM employees WHERE join_date BETWEEN '$from' AND '$to'";
        $report_title = "Registration Report ($from to $to)";
        $where_params['from_date'] = $from;
        $where_params['to_date'] = $to;
    } 
    elseif ($_GET['report_type'] == 'dept_wise' && !empty($_GET['dept_name'])) {
        $dept = $conn->real_escape_string($_GET['dept_name']);
        if ($dept !== 'all') {
            $query = "SELECT * FROM employees WHERE department = '$dept'";
            $report_title = "Department Report: $dept";
            $where_params['dept_name'] = $dept;
        } else {
            $report_title = "Department Report: All Departments";
        }
    }
}

$result = $conn->query($query);

// --- 3. Fetch employee data for modal if ID is provided ---
$employee_details = null;
if(isset($_GET['view_id'])) {
    $view_id = $conn->real_escape_string($_GET['view_id']);
    $employee_query = "SELECT * FROM employees WHERE emp_id = '$view_id'";
    $employee_result = $conn->query($employee_query);
    if($employee_result->num_rows > 0) {
        $employee_details = $employee_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
   <link rel="stylesheet" href="css/admin_reports.css">
</head>

<body>

    <div class="container">
        <?php include('../include/admin_navbar.php'); ?>

        <main class="main-content">
            <header>
                <h2>System Reports</h2>
                <div style="font-size: 14px; color: #7f8c8d;">
                    <i class="fas fa-info-circle"></i> Toggle switches control employee login access
                </div>
            </header>

            <div class="reports-wrapper">
                <div class="filter-section">
                    <div class="card">
                        <h3>Registration Report</h3>
                        <p style="font-size: 12px; color: #7f8c8d; margin-bottom: 15px;">Filter by join date</p>
                        <form action="" method="GET">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" name="from_date" required
                                    value="<?php echo isset($where_params['from_date']) ? $where_params['from_date'] : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" name="to_date" required
                                    value="<?php echo isset($where_params['to_date']) ? $where_params['to_date'] : ''; ?>">
                            </div>
                            <button type="submit" name="report_type" value="date_range"
                                class="btn-generate">Generate</button>
                        </form>
                    </div>

                    <div class="card">
                        <h3>Department Report</h3>
                        <p style="font-size: 12px; color: #7f8c8d; margin-bottom: 15px;">Filter by department</p>
                        <form action="" method="GET">
                            <div class="form-group">
                                <label>Select Department</label>
                                <select name="dept_name" required>
                                    <option value="all">All Departments</option>
                                    <?php 
                                    foreach ($departments as $dept) {
                                        $selected = (isset($where_params['dept_name']) && $where_params['dept_name'] == $dept) ? 'selected' : '';
                                        echo '<option value="'.$dept.'" '.$selected.'>'.$dept.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" name="report_type" value="dept_wise" class="btn-generate">View
                                Report</button>
                        </form>
                    </div>

                    <a href="admin_reports.php" style="text-decoration:none;">
                        <button class="btn-generate" style="background:#7f8c8d;">Reset All</button>
                    </a>
                </div>

                <div class="result-section">
                    <div class="card">
                        <h3>
                            <?php echo $report_title; ?>
                        </h3>

                        <!-- Status Message Display -->
                        <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['msg_type'] ?? 'success'; ?>">
                            <i
                                class="fas fa-<?php echo ($_SESSION['msg_type'] == 'error') ? 'exclamation-triangle' : 'check-circle'; ?>"></i>
                            <?php 
                                    echo $_SESSION['message']; 
                                    unset($_SESSION['message']);
                                    unset($_SESSION['msg_type']);
                                ?>
                        </div>
                        <?php endif; ?>

                        <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">

                        <div class="table-responsive">
                            <table id="reportTable" class="myTable">
                                <thead>
                                    <tr>
                                        <th>Emp ID</th>
                                        <th>Full Name</th>
                                        <th>Department</th>
                                        <th>Join Date</th>
                                        <th>Email</th>
                                        <th>Login Status</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

        $is_active = isset($row['is_active']) ? $row['is_active'] : '1';
        $status_text = ($is_active == '1') ? 'Active' : 'Inactive';
        $status_class = ($is_active == '1') ? 'status-active' : 'status-inactive';

        echo "<tr>
            <td>{$row['emp_id']}</td>
            <td><strong>{$row['name']}</strong></td>
            <td>{$row['department']}</td>
            <td>{$row['join_date']}</td>
            <td>{$row['email']}</td>
            <td>
                <div class='status-toggle'>
                    <span id='status_badge_{$row['emp_id']}' 
                          class='status-badge $status_class'>
                          $status_text
                    </span>

                    <label class='toggle-switch'>
                        <input type='checkbox'
                               ".($is_active == '1' ? 'checked' : '')."
                               onchange='toggleFromTable(
                                    \"{$row['emp_id']}\",
                                    \"{$is_active}\",
                                    this
                               )'>
                        <span class='toggle-slider'></span>
                    </label>
                </div>
            </td>

            <td>
                <div class='action-buttons'>
                    <button class='btn-view'
                        onclick='openEmployeeModal(
                            \"{$row['emp_id']}\",
                            \"".htmlspecialchars(addslashes($row['name']))."\",
                            \"".htmlspecialchars(addslashes($row['email']))."\",
                            \"".htmlspecialchars(addslashes($row['department']))."\",
                            \"{$row['join_date']}\",
                            \"{$row['phone_no']}\",
                            \"".number_format($row['salary'], 2)."\",
                            \"".htmlspecialchars(addslashes($row['address']))."\",
                            \"{$is_active}\"
                        )'>
                        <i class='fas fa-eye'></i> View
                    </button>
                </div>
            </td>
        </tr>";
    }
} else {
    echo "<tr>
            <td colspan='7' style='text-align:center;'>
                No records found for this criteria.
            </td>
          </tr>";
}
?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Employee Details Modal -->
    <div id="employeeModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-user"></i> Employee Details</h2>
                <button class="modal-close" onclick="closeEmployeeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="employee-profile">
                    <img id="modal_emp_image" src="https://cdn-icons-png.flaticon.com/512/149/149071.png"
                        alt="Employee Image" class="profile-img">
                    <div class="profile-info">
                        <h3 id="modal_emp_name"></h3>
                        <div class="emp-tags">
                            <div class="emp-tag" id="modal_emp_id"></div>
                            <div class="emp-tag department" id="modal_emp_dept"></div>
                            <div class="emp-tag" id="modal_emp_status_badge" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="login-access">
                    <div class="login-access-info">
                        <div class="login-access-label">Login Access Status</div>
                        <div class="login-access-status" id="modal_login_status_text"></div>
                        <div style="font-size: 11px; color: #718096; margin-top: 3px;">
                            <i class="fas fa-info-circle"></i> When disabled, employee cannot login to the system
                        </div>
                    </div>
                    <div class="login-access-toggle">
                        <span id="modal_current_status" style="display:none;"></span>
                        <label class="toggle-switch">
                            <input type="checkbox" id="modal_status_toggle" onchange="toggleModalStatus()">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value" id="modal_emp_email"></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value" id="modal_emp_phone"></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Join Date</div>
                        <div class="info-value" id="modal_emp_join_date"></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Monthly Salary</div>
                        <div class="info-value" id="modal_emp_salary"></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Address</div>
                    <div class="info-value" id="modal_emp_address"></div>
                </div>

                <div class="section-title">Summary Statistics</div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h4>Leave Summary</h4>
                        <div class="stat-item">
                            <span>Total Approved Leaves:</span>
                            <span id="modal_leave_count">Loading...</span>
                        </div>
                        <div class="penalty-badge" id="modal_leave_penalty">
                            Leave Penalty: Loading...
                        </div>
                    </div>

                    <div class="stat-card">
                        <h4>Attendance Summary</h4>
                        <div class="stat-item">
                            <span>Total Absents:</span>
                            <span id="modal_absent_count">Loading...</span>
                        </div>
                        <div class="penalty-badge" id="modal_att_penalty">
                            Attendance Penalty: Loading...
                        </div>
                    </div>
                </div>

                <div class="section-title">Recent Attendance (Last 5)</div>
                <div class="table-wrapper">
                    <table class="compact-table" id="attendance_table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Penalty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section-title">Recent Leaves (Last 3)</div>
                <div class="table-wrapper">
                    <table class="compact-table" id="leaves_table">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Reason</th>
                                <th>Penalty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
        $('#reportTable').DataTable();
    });
</script>

    <script>
        function toggleFromTable(empId, currentStatus, checkbox) {

            const newStatus = checkbox.checked ? '1' : '0';
            const action = newStatus === '1' ? 'enable' : 'disable';

            // if (!confirm(`Are you sure you want to ${action} login access for employee ${empId}?`)) {
            //     checkbox.checked = !checkbox.checked;
            //     return;
            // }

            const formData = new FormData();
            formData.append('emp_id', empId);
            formData.append('new_status', newStatus);
            formData.append('toggle_status', '1');

            fetch('admin_reports.php', {
                method: 'POST',
                body: formData
            })
                .then(() => {

                    // Update badge text + color
                    const badge = document.getElementById('status_badge_' + empId);
                    badge.textContent = newStatus === '1' ? 'Active' : 'Inactive';
                    badge.className = 'status-badge ' +
                        (newStatus === '1' ? 'status-active' : 'status-inactive');

                    // MODAL MESSAGE (no browser alert)
                    showModalMessage(
                        `Login access ${action}d successfully!`,
                        'success'
                    );
                })
                .catch(() => {
                    checkbox.checked = !checkbox.checked;
                    showModalMessage('Error updating login access', 'error');
                });
        }

        let currentEmpId = '';
        let currentEmpStatus = '';

        // Function to open employee modal
        function openEmployeeModal(empId, name, email, dept, joinDate, phone, salary, address, isActive) {
            currentEmpId = empId;
            currentEmpStatus = isActive;

            // Set basic information
            document.getElementById('modal_emp_name').textContent = name;
            document.getElementById('modal_emp_id').textContent = 'ID: ' + empId;
            document.getElementById('modal_emp_dept').textContent = dept;
            document.getElementById('modal_emp_email').textContent = email;
            document.getElementById('modal_emp_phone').textContent = phone;
            document.getElementById('modal_emp_join_date').textContent = joinDate;
            document.getElementById('modal_emp_salary').textContent = '₹ ' + salary;
            document.getElementById('modal_emp_address').textContent = address;

            // Set login access status
            const isActiveBool = isActive == '1';
            const statusText = isActiveBool ? 'Active (Can Login)' : 'Inactive (Login Disabled)';
            document.getElementById('modal_login_status_text').textContent = statusText;
            document.getElementById('modal_current_status').textContent = isActive;

            // Set toggle switch
            document.getElementById('modal_status_toggle').checked = isActiveBool;

            // Update status badge
            const statusBadge = document.getElementById('modal_emp_status_badge');
            statusBadge.textContent = isActiveBool ? 'Active' : 'Inactive';
            statusBadge.className = 'emp-tag ' + (isActiveBool ? 'status-active' : 'status-inactive');
            statusBadge.style.display = 'inline-block';

            // Show modal
            document.getElementById('employeeModal').classList.add('active');

            // Fetch detailed data via AJAX
            fetchEmployeeDetails(empId);
        }

        // Function to close modal
        function closeEmployeeModal() {
            document.getElementById('employeeModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('employeeModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeEmployeeModal();
            }
        });

        // Close with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeEmployeeModal();
            }
        });

        function toggleModalStatus() {
    const toggle = document.getElementById('modal_status_toggle');
    const newStatus = toggle.checked ? '1' : '0';
    const action = newStatus === '1' ? 'enable' : 'disable';

    // Create form data
    const formData = new FormData();
    formData.append('emp_id', currentEmpId);
    formData.append('new_status', newStatus);
    formData.append('toggle_status', '1');

    fetch('admin_reports.php', {
        method: 'POST',
        body: formData
    })
    .then(() => {

        // Update local status
        currentEmpStatus = newStatus;

        // Update modal text
        document.getElementById('modal_login_status_text').textContent =
            newStatus === '1'
                ? 'Active (Can Login)'
                : 'Inactive (Login Disabled)';

        // Update badge
        const badge = document.getElementById('modal_emp_status_badge');
        badge.textContent = newStatus === '1' ? 'Active' : 'Inactive';
        badge.className = 'emp-tag ' +
            (newStatus === '1' ? 'status-active' : 'status-inactive');

        // ✅ MODAL MESSAGE (NO ALERT)
        showModalMessage(
            `Login access ${action}d successfully!`,
            'success'
        );
    })
    .catch(() => {
        toggle.checked = !toggle.checked; // revert
        showModalMessage('Error updating login access', 'error');
    });
}


        // Function to show message in modal
        function showModalMessage(message, type) {
            // Remove existing message
            const existingMsg = document.querySelector('.modal-message');
            if (existingMsg) {
                existingMsg.remove();
            }

            // Create message element
            const msgElement = document.createElement('div');
            msgElement.className = `alert alert-${type} modal-message`;
            msgElement.style.margin = '10px 0';
            msgElement.style.padding = '10px 15px';
            msgElement.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'}"></i> ${message}`;

            // Insert after login access section
            const loginAccess = document.querySelector('.login-access');
            loginAccess.parentNode.insertBefore(msgElement, loginAccess.nextSibling);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (msgElement.parentNode) {
                    msgElement.style.opacity = '0';
                    msgElement.style.transition = 'opacity 0.5s';
                    setTimeout(() => msgElement.remove(), 500);
                }
            }, 3000);
        }

        // Fetch employee details via AJAX
        function fetchEmployeeDetails(empId) {
            // Show loading states
            document.getElementById('modal_leave_count').textContent = 'Loading...';
            document.getElementById('modal_leave_penalty').textContent = 'Leave Penalty: Loading...';
            document.getElementById('modal_absent_count').textContent = 'Loading...';
            document.getElementById('modal_att_penalty').textContent = 'Attendance Penalty: Loading...';

            // Create form data
            const formData = new FormData();
            formData.append('emp_id', empId);
            formData.append('action', 'get_employee_details');

            // Send AJAX request
            fetch('ajax_handler.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update leave summary
                        document.getElementById('modal_leave_count').textContent = data.leave_summary.total_leave_count || 0;
                        document.getElementById('modal_leave_penalty').textContent =
                            'Leave Penalty: ₹ ' + (data.leave_summary.total_penalty || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 });

                        // Update attendance summary
                        document.getElementById('modal_absent_count').textContent = data.attendance_summary.total_absents || 0;
                        document.getElementById('modal_att_penalty').textContent =
                            'Attendance Penalty: ₹ ' + (data.attendance_summary.total_att_penalty || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 });

                        // Update attendance table
                        updateTable('attendance_table', data.recent_attendance, 'attendance');

                        // Update leaves table
                        updateTable('leaves_table', data.recent_leaves, 'leaves');

                        // Update profile image if available
                        if (data.profile_image) {
                            document.getElementById('modal_emp_image').src = data.profile_image;
                        }
                    } else {
                        console.error('Error:', data.message);
                        showModalMessage('Error loading employee details', 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('modal_leave_count').textContent = 'Error';
                    document.getElementById('modal_absent_count').textContent = 'Error';
                    showModalMessage('Error loading employee details', 'error');
                });
        }

        // Function to update table data
        function updateTable(tableId, data, type) {
            const table = document.getElementById(tableId);
            const tbody = table.querySelector('tbody');

            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="' + (type === 'attendance' ? 3 : 4) + '" style="text-align:center;">No records found.</td></tr>';
                return;
            }

            let rows = '';
            if (type === 'attendance') {
                data.forEach(item => {
                    const badgeClass = (item.status === 'P' || item.status === 'Present') ? 'status-approved' : 'status-pending';
                    const date = new Date(item.attendance_date).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
                    rows += `
                        <tr>
                            <td>${date}</td>
                            <td><span class="modal-status-badge ${badgeClass}">${item.status}</span></td>
                            <td>₹ ${parseFloat(item.penalty || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                        </tr>
                    `;
                });
            } else if (type === 'leaves') {
                data.forEach(item => {
                    const fromDate = new Date(item.from_date).toLocaleDateString('en-IN', { day: '2-digit', month: 'short' });
                    const toDate = new Date(item.to_date).toLocaleDateString('en-IN', { day: '2-digit', month: 'short' });
                    const reason = item.reason ? item.reason.substring(0, 30) + (item.reason.length > 30 ? '...' : '') : '';
                    rows += `
                        <tr>
                            <td>${fromDate}</td>
                            <td>${toDate}</td>
                            <td title="${item.reason || ''}">${reason}</td>
                            <td>₹ ${parseFloat(item.penalty || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</td>
                        </tr>
                    `;
                });
            }

            tbody.innerHTML = rows;
        }
    </script>
</body>

</html>
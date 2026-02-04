<?php
session_start();

// 1. Session Check
if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

// 2. Database Connection (mysqli)
$conn = mysqli_connect("localhost", "root", "", "employee_management_system");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 3. Admin Data Fetch (Using mysqli)
$admin_name = "Admin"; // Default name
$admin_query = "SELECT * FROM admin WHERE admin_id = '$admin_id'"; 
$admin_res = mysqli_query($conn, $admin_query);

if($admin_res && mysqli_num_rows($admin_res) > 0) {
    $admin_data = mysqli_fetch_assoc($admin_res);
    $admin_name = $admin_data['full_name'];
}

// Handle update via POST (from modal)
if(isset($_POST['update'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $dept = mysqli_real_escape_string($conn, $_POST['emp_dept']);
    $salary = mysqli_real_escape_string($conn, $_POST['salary']);
    $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $join_date = mysqli_real_escape_string($conn, $_POST['join_date']);
    
    // Yahan check karein ki column names 'dob' aur 'join_date' hi hain ya nahi
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
        $_SESSION['success_msg'] = "Employee Updated Successfully!";
    } else {
        $_SESSION['error_msg'] = "Update Failed: " . mysqli_error($conn);
    }
    header("Location: admin_employee.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

  <link rel="stylesheet" href="css/admin_employee.css">
</head>

<body>

    <div class="container">
        <?php include('../include/admin_navbar.php'); ?>

        <main class="main-content">
            <?php if(isset($_SESSION['success_msg'])): ?>
            <div id="statusMessage"
                style="background-color: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; font-weight: 500; width: 100%;">
                <span><i class="fas fa-check-circle"></i>
                    <?php echo $_SESSION['success_msg']; ?>
                </span>
                <span onclick="this.parentElement.style.display='none'"
                    style="cursor: pointer; font-size: 20px;">&times;</span>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
            <?php endif; ?>
            <header>
                <h2>Employee Management</h2>
                <div class="user-info"><span>Welcome, <strong>
                            <?php echo $admin_name; ?>
                        </strong></span></div>
            </header>

            <div class="emp-wrapper">
                <div class="card">
                    <h3>Add New Employee</h3>
                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                    <form action="add_employee_process.php" method="POST">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="emp_name" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="emp_email" placeholder="john@example.com" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone_no" placeholder="9876543210" required>
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" required>
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <select name="emp_dept" required>
                                <option value="">Select Department</option>
                                <?php
                                $dept_query = "SELECT * FROM department";
                                $dept_result = mysqli_query($conn, $dept_query);
                                while($dept_row = mysqli_fetch_assoc($dept_result)) {
                                    echo "<option value='".$dept_row['dept_name']."'>".$dept_row['dept_name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Joining Date</label>
                            <input type="date" name="join_date" required>
                        </div>
                        <div class="form-group">
                            <label>Monthly Salary (₹)</label>
                            <input type="number" name="emp_salary" placeholder="50000" required>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" rows="2" placeholder="Full Address..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Login Password</label>
                            <input type="password" name="emp_password" placeholder="Set Password" required>
                        </div>
                        <button type="submit" class="btn-save">Register Employee</button>
                    </form>
                </div>

                <div class="card">
                    <h3>Employee List</h3>
                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                    <div class="table-responsive">
                        <table id="employeeTable" class="myTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Employee Info</th>
                                    <th>Contact & DOB</th>
                                    <th>Dept & Join Date</th>
                                    <th>Salary</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM employees ORDER BY id DESC";
                                $result = mysqli_query($conn, $query);

                                if(mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                <tr>
                                    <td>
                                        <?php echo $row['emp_id']; ?>
                                    </td>
                                    <td>
                                        <strong>
                                            <?php echo htmlspecialchars($row['name']); ?>
                                        </strong><br>
                                        <small>
                                            <?php echo htmlspecialchars($row['email']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small><i class="fas fa-phone"></i>
                                            <?php echo htmlspecialchars($row['phone_no']); ?>
                                        </small><br>
                                        <small><i class="fas fa-birthday-cake"></i>
                                            <?php echo $row['dob']; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge">
                                            <?php echo htmlspecialchars($row['department']); ?>
                                        </span><br>
                                        <small>Joined:
                                            <?php echo $row['join_date']; ?>
                                        </small>
                                    </td>
                                    <td>₹
                                        <?php echo number_format($row['salary']); ?>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn-edit" title="Edit"
                                                    onclick='openEditModal(<?php echo json_encode([
                                                                $row["id"],
                                                                $row["name"],
                                                                $row["phone_no"],
                                                                $row["dob"],
                                                                $row["department"],
                                                                $row["join_date"],
                                                                $row["salary"],
                                                                $row["address"],
                                                                $row["emp_id"]
                                                            ]); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- <a href="delete_employee.php?id=<?php echo $row['id']; ?>" class="btn-delete"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </a> -->
                                        <a href="javascript:void(0);" class="btn-delete"
                                            onclick="openDeleteModal(<?php echo $row['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' style='text-align:center;'>No employees found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Employee Details</h3>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>

            <form id="editEmployeeForm" method="POST">
                <input type="hidden" name="id" id="edit_employee_id">

                <div class="form-group">
                    <label>Employee ID</label>
                    <input type="text" id="edit_emp_id" disabled style="background-color: #f5f5f5;">
                </div>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone_no" id="edit_phone_no" required>
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" id="edit_dob" required>
                </div>

                <div class="form-group">
                    <label>Department</label>
                    <select name="emp_dept" id="edit_dept" required>
                        <option value="">Select Department</option>
                        <?php
                        $dept_query = "SELECT * FROM department";
                        $dept_result = mysqli_query($conn, $dept_query);
                        while($dept_row = mysqli_fetch_assoc($dept_result)) {
                            echo "<option value='".$dept_row['dept_name']."'>".$dept_row['dept_name']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Joining Date</label>
                    <input type="date" name="join_date" id="edit_join_date" required>
                </div>

                <div class="form-group">
                    <label>Monthly Salary (₹)</label>
                    <input type="number" name="salary" id="edit_salary" required>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" id="edit_address" rows="3" required></textarea>
                </div>

                <button type="submit" name="update" class="btn-update">Save Changes</button>
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>

    <!-- delete modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <div style="color: #e74c3c; font-size: 50px; margin-bottom: 15px;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h2>Are you sure?</h2>
            <p style="color: #7f8c8d; margin-top: 10px;">Do you really want to delete this Employee? This process cannot
                be undone.</p>

            <div class="btn-container" style="margin-top: 25px; display: flex; gap: 15px; justify-content: center;">
                <a id="confirmDeleteBtn" href="#" class="btn-update"
                    style="background: #e74c3c; color: white; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; flex: 1; display: inline-block;">
                    Delete Now
                </a>

                <button type="button" class="btn-cancel" onclick="closeDeleteModal()"
                    style="background: #7f8c8d; color: white; border: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; flex: 1; cursor: pointer;">
                    Cancel
                </button>
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
        $('#employeeTable').DataTable();
    });
</script>

    <script>
        // Function to open edit modal with employee data
        function openEditModal(data) {
    // Data ab ek array hai: [id, name, phone, dob, dept, join_date, salary, address, emp_id]
    document.getElementById('edit_employee_id').value = data[0];
    document.getElementById('edit_name').value = data[1];
    document.getElementById('edit_phone_no').value = data[2];
    document.getElementById('edit_dob').value = data[3];
    document.getElementById('edit_dept').value = data[4];
    document.getElementById('edit_join_date').value = data[5];
    document.getElementById('edit_salary').value = data[6];
    document.getElementById('edit_address').value = data[7];
    
    // Display ID (e.g., EMP-7729)
    document.getElementById('edit_emp_id').value = data[8];
    
    document.getElementById('editModal').classList.add('active');
}

        // Function to close edit modal
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Handle form submission
        document.getElementById('editEmployeeForm').addEventListener('submit', function (e) {
            // e.preventDefault();

            // // Show confirmation
            // if (confirm('Are you sure you want to update this employee?')) {
            // Submit the form
            this.submit();

        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });

        // delete modal open and close
        // Delete Modal Open karne ke liye
        function openDeleteModal(id) {
            // Delete link ko dynamically set karna
            document.getElementById('confirmDeleteBtn').href = "delete_employee.php?id=" + id;
            document.getElementById('deleteModal').classList.add('active');
        }

        // Delete Modal Close karne ke liye
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        // Outside click se close ho
        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>

</html>
<?php
// admin_departments.php
session_start();
include('../config/db.php'); 

if(!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$database = new Database();
$db = $database->connect();

try {
    // Fetch admin details
    $query = "SELECT * FROM admin WHERE admin_id = :id"; 
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $admin_id]); 
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch departments
    $query = "SELECT * FROM department ORDER BY dept_id DESC"; 
    $stmt = $db->prepare($query);
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!$admin) {
        die("Error: Admin record not found for ID: " . htmlspecialchars($admin_id));
    }

    $admin_name = $admin['full_name'];
} catch (PDOException $e) {
    die("Error fetching departments: " . $e->getMessage());
}

// Fetch department data for edit if ID is provided
$edit_dept = null;
if(isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $db->prepare("SELECT * FROM department WHERE dept_id = :id");
    $stmt->execute([':id' => $edit_id]);
    $edit_dept = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle update via AJAX/POST
if(isset($_POST['update_dept']) && isset($_POST['dept_id'])) {
    try {
        $sql = "UPDATE department SET dept_name = :name, dept_short = :short WHERE dept_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':name'  => $_POST['dept_name'],
            ':short' => $_POST['dept_short'],
            ':id'    => $_POST['dept_id']
        ]);
        
        // Redirect to refresh the page with updated data
        $_SESSION['success_msg'] = "Department Updated Successfully!";
header("Location: admin_departments.php");
exit;
       
    } catch (PDOException $e) { 
        die("Error: " . $e->getMessage()); 
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Management | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin_departments.css">
   
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
                <h2>Department Management</h2>
                <div class="user-info"><span>Welcome, <strong>
                            <?php echo $admin_name; ?>
                        </strong></span></div>
            </header>

            <div class="dept-wrapper">
                <div class="card">
                    <h3>Add New Department</h3>
                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                    <form action="add_dept_process.php" method="POST">
                        <div class="form-group">
                            <label>Department Name</label>
                            <input type="text" name="dept_name" placeholder="e.g. Information Technology" required>
                        </div>
                        <div class="form-group">
                            <label>Short Name</label>
                            <input type="text" name="dept_short" placeholder="e.g. IT" required>
                        </div>
                        <button type="submit" name="add_dept" class="btn-save">Add Department</button>
                    </form>
                </div>

                <div class="card">
                    <h3>Department List</h3>
                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                    <table id="deptTable" class="myTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Short Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($departments) > 0): ?>
                            <?php 
                                 $serial_no = 1; // Loop ke bahar 1 se start karein
                            ?>
                            <?php foreach($departments as $dept): ?>
                            <tr>
                                <td>
                                    <?php echo $serial_no++; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($dept['dept_name']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($dept['dept_short']); ?>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn-edit" title="Edit"
                                        onclick="openEditModal(<?php echo $dept['dept_id']; ?>, '<?php echo htmlspecialchars($dept['dept_name']); ?>', '<?php echo htmlspecialchars($dept['dept_short']); ?>')">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="javascript:void(0);" class="btn-delete"
                                        onclick="openDeleteModal(<?php echo $dept['dept_id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">No departments found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Department Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-edit"></i> Edit Department</h2>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">

            <form id="editDeptForm" method="POST">
                <input type="hidden" name="dept_id" id="edit_dept_id">

                <div class="form-group">
                    <label>Department Name</label>
                    <input type="text" name="dept_name" id="edit_dept_name" required>
                </div>

                <div class="form-group">
                    <label>Short Name (Code)</label>
                    <input type="text" name="dept_short" id="edit_dept_short" required>
                </div>

                <div class="btn-container">
                    <button type="submit" name="update_dept" class="btn-update">Update Now</button>
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
                </div>
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
            <p style="color: #7f8c8d; margin-top: 10px;">Do you really want to delete this Departments type? This
                process
                cannot be undone.</p>

            <div class="btn-container" style="margin-top: 25px;">
                <a id="confirmDeleteBtn" href="#" class="btn-update"
                    style="background: #e74c3c; text-decoration: none;">Delete Now</a>
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
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
        $('#deptTable').DataTable();
    });
</script>
    <script>
        // 1. Function to open edit modal with department data
        function openEditModal(id, name, shortName) {
            document.getElementById('edit_dept_id').value = id;
            document.getElementById('edit_dept_name').value = name;
            document.getElementById('edit_dept_short').value = shortName;
            document.getElementById('editModal').classList.add('active');
        }

        // 2. Function to close edit modal
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // 3. Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // 4. Handle form submission (ALERT REMOVED)
        document.getElementById('editDeptForm').addEventListener('submit', function (e) {
            // Confirmation alert ko hata diya gaya hai taaki seedha update ho
            this.submit();
        });

        // 5. Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });

        // delete modal open and close
        // Delete Modal Open karne ke liye
        function openDeleteModal(id) {
            // Delete link ko dynamically set karna
            document.getElementById('confirmDeleteBtn').href = "delete_dept.php?id=" + id;
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
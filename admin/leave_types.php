<?php
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
    
    // Database se leave types fetch karna
    $query = "SELECT * FROM leave_type ORDER BY leave_type_id DESC"; 
    $stmt = $db->prepare($query);
    $stmt->execute();
    $leave_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(!$admin) {
        die("Error: Admin record not found for ID: " . htmlspecialchars($admin_id));
    }

    $admin_name = $admin['full_name'];
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle update via POST (from modal)
if(isset($_POST['update_leave']) && isset($_POST['leave_type_id'])) {
    $id = $_POST['leave_type_id'];
    $name = $_POST['leave_name'];
    $desc = $_POST['description'];

    try {
        $sql = "UPDATE leave_type SET 
                leave_name = :name, 
                description = :desc 
                WHERE leave_type_id = :id";
                
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':name' => $name,
            ':desc' => $desc,
            ':id'   => $id
        ]);

        // if($result) {
        //     echo "<script>alert('Leave Type Updated Successfully!'); window.location.href = 'leave_types.php';</script>";
        //     exit;
        // }
         $_SESSION['success_msg'] = "Leave Type Updated Successfully!";
            header("Location: leave_types.php");
            exit;
    } catch (PDOException $e) { 
        die("Database Error: " . $e->getMessage()); 
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Type Management | Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/leave_types.css">
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
                <h2>Leave Type Management</h2>
                <div class="user-info"><span>Welcome, <strong>
                            <?php echo $admin_name; ?>
                        </strong></span></div>
            </header>

            <div class="leave-wrapper">
                <div class="card">
                    <h3>Add New Type</h3>
                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                    <form action="add_leave_process.php" method="POST">
                        <div class="form-group">
                            <label>Leave Type Name</label>
                            <input type="text" name="leave_name" placeholder="e.g. Sick Leave" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3" placeholder="Short description..."></textarea>
                        </div>
                        <button type="submit" name="add_leave" class="btn-save">Save Leave Type</button>
                    </form>
                </div>

                <div class="card">
                    <h3>Current Leave Types</h3>
                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                    <table id="leaveTable" class="myTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Leave Type</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($leave_types) > 0): ?>
                                <?php 
                                 $serial_no = 1; // Loop ke bahar 1 se start karein
                            ?>
                            <?php foreach($leave_types as $type): ?>
                            <tr>
                                <td>
                                    <?php echo $serial_no++; ?>
                                </td>
                                <td><strong>
                                        <?php echo htmlspecialchars($type['leave_name']); ?>
                                    </strong></td>
                                <td>
                                    <?php echo htmlspecialchars($type['description']); ?>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="btn-edit" title="Edit"
                                        onclick="openEditModal(<?php echo $type['leave_type_id']; ?>, '<?php echo htmlspecialchars(addslashes($type['leave_name'])); ?>', '<?php echo htmlspecialchars(addslashes($type['description'])); ?>')">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                   
                                    <a href="javascript:void(0);" class="btn-delete"
                                        onclick="openDeleteModal(<?php echo $type['leave_type_id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align:center;">No Leave Types Found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Leave Type Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-list-alt"></i> Edit Leave Type</h2>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <hr style="margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">

            <form id="editLeaveForm" method="POST">
                <input type="hidden" name="leave_type_id" id="edit_leave_id">

                <div class="form-group">
                    <label>Leave Type Name</label>
                    <input type="text" name="leave_name" id="edit_leave_name" required placeholder="e.g. Sick Leave">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description"
                        placeholder="Write description here..."></textarea>
                </div>

                <div class="btn-container">
                    <button type="submit" name="update_leave" class="btn-update">Update Leave Type</button>
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
            <p style="color: #7f8c8d; margin-top: 10px;">Do you really want to delete this leave type? This process
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
        $('#leaveTable').DataTable();
    });
</script>

    <script>
        // Function to open edit modal with leave type data
        function openEditModal(id, name, description) {
            document.getElementById('edit_leave_id').value = id;
            document.getElementById('edit_leave_name').value = name;
            document.getElementById('edit_description').value = description;
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
        document.getElementById('editLeaveForm').addEventListener('submit', function (e) {
            // e.preventDefault();
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
            document.getElementById('confirmDeleteBtn').href = "delete_leave.php?id=" + id;
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
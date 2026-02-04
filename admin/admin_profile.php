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
    $query = "SELECT * FROM admin WHERE admin_id = :id"; 
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $admin_id]); 
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$admin) {
        die("Error: Admin record not found.");
    }

    $admin_name = $admin['full_name']; 
    $admin_email = $admin['email'];
    $admin_phone = isset($admin['phone']) ? $admin['phone'] : "Not Provided";
    
    // Admin Image Logic
    $admin_img = !empty($admin['profile_image']) ? "../uploads/admin_pics/" . $admin['profile_image'] : "https://cdn-icons-png.flaticon.com/512/149/149071.png";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile | Admin Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/admin_profile.css">
</head>

<body>

    <div class="container">
        <?php include('../include/admin_navbar.php'); ?>
        <main class="main-content">
            <header>
                <h2>Account Settings</h2>
                <div class="user-info"><span>Admin Profile</span></div>
            </header>
            <?php if(isset($_SESSION['success_msg'])): ?>
            <div id="statusMessage"
                style="background-color: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; font-weight: 500;">
                <span><i class="fas fa-check-circle"></i>
                    <?php echo $_SESSION['success_msg']; ?>
                </span>
                <span onclick="this.parentElement.style.display='none'"
                    style="cursor: pointer; font-size: 20px;">&times;</span>
            </div>
            <?php unset($_SESSION['success_msg']); // Refresh karne par message gayab ho jaye ?>

            
            <?php endif; ?>

            <div class="profile-wrapper">
                <div class="card profile-info-card">
                    <div class="admin-img-container">
                        <img src="<?php echo $admin_img; ?>" id="adminDisplay">
                        <label for="adminImageInput" class="upload-icon">
                            <i class="fas fa-plus"></i>
                        </label>
                        <input type="file" id="adminImageInput" accept="image/*">
                    </div>

                    <h2>
                        <?php echo $admin_name; ?>
                    </h2>
                    <p>System Administrator</p>
                    <div style="margin-top: 20px; text-align: left;">
                        <div class="detail-row"><strong>Email:</strong> <span>
                                <?php echo $admin_email; ?>
                            </span></div>
                        <div class="detail-row"><strong>Phone:</strong> <span>
                                <?php echo $admin_phone; ?>
                            </span></div>
                        <div class="detail-row"><strong>Role:</strong> <span>Full Access</span></div>
                    </div>
                </div>

                <div class="card">
                    <h3>Edit Profile Details</h3>
                    <hr style="margin: 15px 0; border: 0; border-top: 1px solid #eee;">
                    <form action="admin_update_profile.php" method="POST">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="admin_name" value="<?php echo $admin_name; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="admin_email" value="<?php echo $admin_email; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="admin_phone" value="<?php echo $admin_phone; ?>">
                        </div>
                        <div class="form-group">
                            <label>Change Password</label>
                            <input type="password" name="admin_password" placeholder="New Password">
                        </div>
                        <button type="submit" class="btn-save">Update Profile</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
    document.getElementById('adminImageInput').addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('admin_image', file);

            fetch('upload_profile_pic.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        const newPath = "../uploads/admin_pics/" + data.filename + "?t=" + new Date().getTime();
                        document.getElementById('adminDisplay').src = newPath;
                        
                        // --- YE RAHA NAYA SWEETALERT ---
                        Swal.fire({
                            title: 'Updated!',
                            text: 'Your profile Photo is Update.',
                            icon: 'success',
                            confirmButtonColor: '#3498db'
                        });

                    } else {
                        // Error ke liye Red Alert
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                } catch (e) {
                    console.error("Server Error Response:", text);
                    Swal.fire({
                        title: 'Server Error',
                        text: 'Database se response nahi mil raha.',
                        icon: 'warning'
                    });
                }
            })
            .catch(err => {
                console.error("AJAX Error:", err);
                Swal.fire('Oops!', 'Kuch galat ho gaya.', 'error');
            });
        }
    });
</script>

<?php if(isset($_SESSION['success_msg'])): ?>
<script>
    Swal.fire({
        title: 'Profile Updated!',
        text: '<?php echo $_SESSION['success_msg']; ?>',
        icon: 'success',
        confirmButtonColor: '#3498db'
    });
</script>
<?php unset($_SESSION['success_msg']); endif; ?>
</body>

</html>
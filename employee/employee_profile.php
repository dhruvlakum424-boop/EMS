<?php
session_start();
include('../config/db.php');

if(!isset($_SESSION['emp_id'])) {
    header("Location: employee_login.php");
    exit;
}

$emp_id = $_SESSION['emp_id']; 
$database = new Database();
$db = $database->connect();

try {
    $query = "SELECT * FROM employees WHERE emp_id = :id LIMIT 1"; 
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $emp_id]); 
    $employees = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$employees) {
        die("Error: Employee record not found.");
    }

    $employee_name  = $employees['name'] ?? 'N/A'; 
    $employee_email = $employees['email'] ?? 'N/A';
    $employee_phone = $employees['phone_no'] ?? "";
    $dob            = $employees['dob'] ?? "";
    $address        = $employees['address'] ?? "";
    $employee_join  = $employees['join_date'] ?? "N/A";
    $department     = $employees['department'] ?? "IT Dept.";
    
    // Image Path Logic
    // Profile Image Path Logic
$upload_dir = "../uploads/profile_pics/";
$db_image = $employees['profile_image'];
$default_avatar = "https://cdn.vectorstock.com/i/500p/43/98/default-avatar-photo-placeholder-icon-grey-vector-38594398.jpg";

// Check karein ki DB mein image name hai aur file folder mein exist karti hai
if (!empty($db_image) && file_exists($upload_dir . $db_image)) {
    $profile_pic = $upload_dir . $db_image;
} else {
    $profile_pic = $default_avatar;
}

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Employee Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/employee_profile.css">
    
</head>

<body>
    <div class="container">
        <?php include('../include/employee_navbar.php'); ?>

        <main class="main-content">
            <header>
                <h2>Account Settings</h2>
                <div class="user-info">
                    <span>ID: <strong>
                            <?php echo htmlspecialchars($emp_id); ?>
                        </strong></span>
                </div>
            </header>
            <?php if(isset($_SESSION['success_msg'])): ?>
            <div id="statusMessage" style="background-color: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; font-weight: 500;">
                <span><i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_msg']; ?></span>
                <span onclick="this.parentElement.style.display='none'" style="cursor: pointer; font-size: 20px;">&times;</span>
            </div>
            <?php unset($_SESSION['success_msg']); ?>
        <?php endif; ?>
    
    <div class="profile-grid">
        <div class="profile-card">
            <div class="profile-img-container">
                <img src="<?php echo $profile_pic; ?>" alt="Profile" id="profileDisplay">
                <label for="imageInput" class="upload-icon">
                    <i class="fas fa-plus"></i>
                </label>
                <input type="file" id="imageInput" accept="image/*">
            </div>

            <h3>
                <?php echo htmlspecialchars($employee_name); ?>
            </h3>
            <p class="dept-text">
                <?php echo htmlspecialchars($department); ?>
            </p>

            <div class="info-list">
                <div class="info-item"><strong>Emp ID:</strong> <span>
                        <?php echo htmlspecialchars($emp_id); ?>
                    </span></div>
                <div class="info-item"><strong>Join Date:</strong> <span>
                        <?php echo htmlspecialchars($employee_join); ?>
                    </span></div>
                <div class="info-item"><strong>Email:</strong> <span>
                        <?php echo htmlspecialchars($employee_email); ?>
                    </span></div>
            </div>
        </div>

        <div class="update-card">
            <h3>Update Personal Details</h3>
            <form action="employee_update.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($employee_name); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($employee_email); ?>"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($employee_phone); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>">
                </div>
                <div class="form-group">
                    <label>Residential Address</label>
                    <textarea name="address" rows="3"><?php echo htmlspecialchars($address); ?></textarea>
                </div>
                <button type="submit" name="update_details" class="btn-update">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
    </main>
    </div>

    <script>
    document.getElementById('imageInput').addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('profile_image', file);

            fetch('upload_profile_pic.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
    if (data.success) {
        // Agar success ho toh naya path set karein
        const newPath = "../uploads/profile_pics/" + data.filename + "?t=" + new Date().getTime();
        document.getElementById('profileDisplay').src = newPath;
        
        Swal.fire({
            title: 'Success!',
            text: 'Profile photo updated successfully.',
            icon: 'success'
        });
    } else {
        // Agar fail ho jaye toh purani image (ya default) wapas dikhayein
        Swal.fire('Error', data.message, 'error');
    }
})
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Server connection failed!', 'error');
            });
        }
    });
</script>

<?php if(isset($_SESSION['success_msg'])): ?>
<script>
    Swal.fire({
        title: 'Updated!',
        text: '<?php echo $_SESSION['success_msg']; ?>',
        icon: 'success',
        confirmButtonColor: '#27ae60'
    });
</script>
<?php unset($_SESSION['success_msg']); endif; ?>
</body>

</html>
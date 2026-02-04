<?php
session_start();
include('../config/db.php');

if(isset($_POST['apply'])) {
    $database = new Database();
    $db = $database->connect();

    if(empty($_POST['leave_name'])) {
        die("Error: Leave Name select nahi kiya gaya.");
    }

    try {
        $emp_id = $_POST['emp_id'];
        $employee_name = $_POST['employee_name'];
        $leave_name = $_POST['leave_name']; 
        $from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $reason = $_POST['reason'];
        $status = "Pending";
        $applied_at = date('Y-m-d H:i:s');
 
        // --- NAYA LOGIC: Penalty Check ---
        // Pehle count karein ki kitni leaves pehle se hain (Approved ya Pending dono count karni chahiye taaki limit cross hote hi penalty dikhe)
        $count_sql = "SELECT COUNT(id) as current_total FROM leaves WHERE emp_id = :emp_id";
        $count_stmt = $db->prepare($count_sql);
        $count_stmt->execute([':emp_id' => $emp_id]);
        $leave_count_data = $count_stmt->fetch(PDO::FETCH_ASSOC);
        $already_applied = $leave_count_data['current_total'];

        // Agar 18 pehle se hain, toh 19th leave se 500 penalty lagegi
        $penalty = ($already_applied >= 18) ? 500.00 : 0.00;
        // ---------------------------------

        // SQL Query mein 'penalty' column add kiya gaya hai
        $sql = "INSERT INTO leaves (emp_id, employee_name, leave_name, from_date, to_date, reason, status, applied_at, penalty) 
                VALUES (:emp_id, :employee_name, :leave_name, :from_date, :to_date, :reason, :status, :applied_at, :penalty)";
        
        $stmt = $db->prepare($sql);
        
        $result = $stmt->execute([
            ':emp_id' => $emp_id,
            ':employee_name' => $employee_name,
            ':leave_name' => $leave_name,
            ':from_date' => $from_date,
            ':to_date' => $to_date,
            ':reason' => $reason,
            ':status' => $status,
            ':applied_at' => $applied_at,
            ':penalty' => $penalty // Penalty value yahan jayegi
        ]);

        if($result) {
    // Session mein message store karein takay next page pe dikhe
    $_SESSION['msg'] = "Leave Applied Successfully!";
    $_SESSION['penalty'] = $penalty;
    $_SESSION['msg_type'] = "success";
    header("Location: leave_history.php");
    exit;
}

    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}
?>
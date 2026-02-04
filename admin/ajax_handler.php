<?php
session_start();
header('Content-Type: application/json');

// Database connection
$conn = new mysqli("localhost", "root", "", "employee_management_system");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if(isset($_POST['action']) && $_POST['action'] == 'get_employee_details' && isset($_POST['emp_id'])) {
    $emp_id = $conn->real_escape_string($_POST['emp_id']);
    
    $response = [
        'success' => true,
        'leave_summary' => [],
        'attendance_summary' => [],
        'recent_attendance' => [],
        'recent_leaves' => [],
        'profile_image' => ''
    ];
    
    // 1. Fetch employee basic info for profile image
    $emp_query = "SELECT profile_image FROM employees WHERE emp_id = '$emp_id'";
    $emp_result = $conn->query($emp_query);
    if($emp_result->num_rows > 0) {
        $emp_row = $emp_result->fetch_assoc();
        if(!empty($emp_row['profile_image'])) {
            $response['profile_image'] = '../uploads/profile_pics/' . $emp_row['profile_image'];
        }
    }
    
    // 2. Leave summary
    $leave_query = "SELECT 
                        COUNT(id) as total_leave_count, 
                        SUM(penalty) as total_penalty 
                    FROM leaves 
                    WHERE emp_id = '$emp_id' AND status = 'Approved'";
    $leave_result = $conn->query($leave_query);
    if($leave_result->num_rows > 0) {
        $response['leave_summary'] = $leave_result->fetch_assoc();
    }
    
    // 3. Attendance summary
    $att_query = "SELECT 
                    SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as total_absents, 
                    SUM(penalty) as total_att_penalty 
                  FROM attendance 
                  WHERE emp_id = '$emp_id'";
    $att_result = $conn->query($att_query);
    if($att_result->num_rows > 0) {
        $response['attendance_summary'] = $att_result->fetch_assoc();
    }
    
    // 4. Recent attendance (last 5)
    $recent_att_query = "SELECT * FROM attendance 
                         WHERE emp_id = '$emp_id' 
                         ORDER BY attendance_date DESC 
                         LIMIT 5";
    $recent_att_result = $conn->query($recent_att_query);
    while($row = $recent_att_result->fetch_assoc()) {
        $response['recent_attendance'][] = $row;
    }
    
    // 5. Recent leaves (last 3 approved leaves)
    $recent_leaves_query = "SELECT * FROM leaves 
                           WHERE emp_id = '$emp_id' AND status = 'Approved'
                           ORDER BY from_date DESC 
                           LIMIT 3";
    $recent_leaves_result = $conn->query($recent_leaves_query);
    while($row = $recent_leaves_result->fetch_assoc()) {
        $response['recent_leaves'][] = $row;
    }
    
    // Handle NULL values
    $response['leave_summary']['total_leave_count'] = $response['leave_summary']['total_leave_count'] ?? 0;
    $response['leave_summary']['total_penalty'] = $response['leave_summary']['total_penalty'] ?? 0;
    $response['attendance_summary']['total_absents'] = $response['attendance_summary']['total_absents'] ?? 0;
    $response['attendance_summary']['total_att_penalty'] = $response['attendance_summary']['total_att_penalty'] ?? 0;
    
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>
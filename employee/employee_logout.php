<?php
session_start();

if (isset($_SESSION['employee_id'])) {
    unset($_SESSION['employee_id']);
}
header("Location: employee_login.php");
exit;
?>
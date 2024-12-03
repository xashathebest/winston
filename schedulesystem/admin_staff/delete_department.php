<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/department.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database connection
$db = new Database();
$department = new Department($db);

// Check if the 'delete_id' parameter is present in the URL
if (isset($_GET['delete_id'])) {
    $department_id = $_GET['delete_id'];

    // Call the deleteDepartment method to delete the department
    $department->deleteDepartment($department_id);

    // Redirect back to manage_departments.php after deletion
    header("Location: departments.php");
    exit(); // Ensure no further code is executed after redirect
} else {
    // If no department ID is passed, redirect to manage departments page
    header("Location: departments.php");
    exit();
}
?>

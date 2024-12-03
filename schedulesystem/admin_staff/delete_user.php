<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/user.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database connection
$db = new Database();
$user = new User($db);

// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Call the delete method from the User class
    $user->delete($user_id);

    // Redirect back to the user management page
    header("Location: admin.php");
    exit();
} else {
    // Redirect if no ID is provided
    header("Location: admin.php");
    exit();
}
?>

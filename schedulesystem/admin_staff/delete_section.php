<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/section.class.php';

// Check if user is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

$db = new Database();
$section = new Section($db);

// Fetch the section ID from the URL
$sectionId = isset($_GET['id']) ? $_GET['id'] : null;

// If no section ID is provided, redirect to the sections list
if (!$sectionId) {
    header("Location: sections.php");
    exit;
}

// Fetch the section details by ID
$sectionDetails = $section->getSectionById($sectionId);

// If section does not exist, redirect to sections list
if (!$sectionDetails) {
    header("Location: sections.php");
    exit;
}

// Process deletion if confirmed
$deleteSuccess = $section->deleteSection($sectionId);

if ($deleteSuccess) {
    header("Location: sections.php"); // Redirect back to the sections list after deletion
    exit;
} else {
    $error = "Failed to delete the section.";
}
?>


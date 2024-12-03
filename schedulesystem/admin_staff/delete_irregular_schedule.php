<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/schedule.class.php';
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database and schedule classes
$db = new Database();
$schedule = new Schedule($db);

// Check if schedule_id and section_id are passed
if (isset($_GET['schedule_id'], $_GET['section_id'])) {
    $schedule_id = $_GET['schedule_id'];
    $section_id = $_GET['section_id'];

    // Call deleteSchedule function
    if ($schedule->deleteSchedule($schedule_id)) {
        $_SESSION['message'] = 'Schedule deleted successfully.';
    } else {
        $_SESSION['message'] = 'Failed to delete schedule.';
    }

    // Redirect back to the edit page for the section
    header("Location: edit_irregular_schedule.php?section_id=$section_id");
    exit();
} else {
    $_SESSION['message'] = 'Invalid request.';
    header("Location: schedules.php");
    exit();
}

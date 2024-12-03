<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/schedule.class.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
    header('Location: ../accounts/login.php');
    exit;
}

// Database connection
$db = new Database(); // Create an instance of Database class
$scheduleObj = new Schedule($db); // Pass the Database instance to the Schedule class

// Fetch the logged-in user's details
$userId = $_SESSION['user_id'];

// Fetch the student's schedule using the Schedule class
$schedule = $scheduleObj->getStudentSchedule($userId);

setlocale(LC_TIME, 'en_US.UTF-8');  // Set locale for English (US)

function formatTime($time) {
    // Convert the time to a Unix timestamp
    $timestamp = strtotime($time);
    
    // Format it to 12-hour format with AM/PM
    return strftime('%I:%M %p', $timestamp);  // '%I:%M %p' formats to 12-hour time with AM/PM
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sync Sched</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Sync Sched</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="edit_password.php">Edit Password</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-danger text-white" href="../admin_staff/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Schedule Table -->
<div class="container mt-5">
    <h2 class="text-center">Your Schedule</h2>
    <?php if (empty($schedule)): ?>
        <div class="alert alert-warning">No schedule found for you.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Subject</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedule as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['day']) ?></td>
                        <td><?= formatTime($row['start_time']); ?></td>
                        <td><?= formatTime($row['end_time']); ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

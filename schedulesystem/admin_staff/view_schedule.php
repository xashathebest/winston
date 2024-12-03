<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/schedule.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database connection and schedule class
$db = new Database();
$schedule = new Schedule($db);

// Get section ID from the URL
if (isset($_GET['section_id'])) {
    $section_id = $_GET['section_id'];
    // Fetch the section name based on section_id
    $section_name = $schedule->getSectionNameById($section_id);
    if (!$section_name) {
        $_SESSION['message'] = 'Section not found.';
        header('Location: manage_schedules.php');
        exit();
    }
} else {
    $_SESSION['message'] = 'No section selected.';
    header('Location: schedules.php');
    exit();
}

// Fetch schedules for the section
$schedules = $schedule->getSchedulesBySectionId($section_id);

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
    <title>View Schedule</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container mt-5">
        <h2 class="mb-4">Schedule for Section: <?= htmlspecialchars($section_name); ?></h2>  <!-- Display section name -->

        <!-- Display messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info message" role="alert">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Schedule Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Subject</th>
                    <th>Room</th>
                    <th>Teacher</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($schedules): ?>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?= htmlspecialchars($schedule['day']); ?></td>
                            <td><?= formatTime($schedule['start_time']); ?></td>
                            <td><?= formatTime($schedule['end_time']); ?></td>
                            <td><?= htmlspecialchars($schedule['subject']); ?></td>
                            <td><?= htmlspecialchars($schedule['room']); ?></td>
                            <td><?= htmlspecialchars($schedule['teacher_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No schedule found for this section.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Back Button -->
        <a href="schedules.php" class="btn btn-secondary">Back to Manage Schedules</a>
    </div>
</div>

</body>
</html>

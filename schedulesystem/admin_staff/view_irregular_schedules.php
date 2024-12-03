<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/schedule.class.php';
require_once '../classes/user.class.php'; // Assuming student details are in this class

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database connection and classes
$db = new Database();
$schedule = new Schedule($db);
$user = new User($db); // Assuming student details are in the User class

// Get section ID from the URL
if (isset($_GET['section_id'])) {
    $section_id = $_GET['section_id'];
    // Fetch the section name based on section_id
    $section_name = $schedule->getSectionNameById($section_id);
    if (!$section_name) {
        $_SESSION['message'] = 'Section not found.';
        header('Location: schedules.php');
        exit();
    }
} else {
    $_SESSION['message'] = 'No section selected.';
    header('Location: schedules.php');
    exit();
}


// Fetch all students who have schedules for the given section
$students_with_schedules = $schedule->getSchedulesForSection($section_id);

// Group schedules by student
$grouped_schedules = [];
foreach ($students_with_schedules as $schedule_item) {
    $grouped_schedules[$schedule_item['student_id']][] = $schedule_item;
}

// Fetch student details once to avoid redundant queries
$students = [];
foreach (array_keys($grouped_schedules) as $student_id) {
    $students[$student_id] = $user->getUserById($student_id); // Assuming this method fetches student details by ID
}

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
    <title>View Irregular Schedules</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<!-- Main Content -->
<div class="main-content" style="margin-left: 260px; padding: 20px;">
    <div class="container mt-5">
        <h2 class="mb-4">Schedules for Section: <?= htmlspecialchars($section_name); ?></h2>

        <!-- Display messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info" role="alert">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($grouped_schedules)): ?>
            <?php foreach ($grouped_schedules as $student_id => $schedules): ?>
                <?php
                // Fetch student details
                $student = $students[$student_id];
                ?>
                <h3><?= htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']); ?></h3>
                <table class="table table-bordered mb-4">
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
                        <?php foreach ($schedules as $schedule_item): ?>
                            <tr>
                                <td><?= htmlspecialchars($schedule_item['day']); ?></td>
                                <td><?= formatTime($schedule_item['start_time']); ?></td>
                                <td><?= formatTime($schedule_item['end_time']); ?></td>
                                <td><?= htmlspecialchars($schedule_item['subject']); ?></td>
                                <td><?= htmlspecialchars($schedule_item['room']); ?></td>
                                <td>
                                    <?php
                                    $teacher_name = $schedule->getTeacherNameById($schedule_item['teacher']);
                                    echo htmlspecialchars($teacher_name);
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No schedules found for this section.</p>
        <?php endif; ?>

    </div>
</div>

</body>
</html>

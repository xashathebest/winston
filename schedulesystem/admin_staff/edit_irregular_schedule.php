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
$students_with_schedules = $schedule->getIrregularSchedulesBySectionId($section_id);

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

// Fetch all teachers for the dropdown
$teachers = $schedule->getStaffUsers();

function formatTime($time) {
    // Convert the time to a Unix timestamp
    $timestamp = strtotime($time);
    
    // Format it to 12-hour format with AM/PM
    return date('h:i A', $timestamp); // Adjusted for PHP compatibility
}

// Handle schedule update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_schedule'])) {
    $schedule_id = $_POST['schedule_id'];
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $subject = $_POST['subject'];
    $room = $_POST['room'];
    $teacher_id = $_POST['teacher_id'];

    // Update schedule
    $update_result = $schedule->updateSchedule($schedule_id, $section_id, $day, $start_time, $end_time, $subject, $room, $teacher_id);
    $_SESSION['message'] = $update_result ? 'Schedule updated successfully.' : 'Failed to update schedule due to teacher/schedule conflicts.';
    header("Location: schedules.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Irregular Schedules</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule_item): ?>
                            <tr>
                                <form action="" method="POST">
                                    <td><input type="text" name="day" value="<?= htmlspecialchars($schedule_item['day']); ?>" class="form-control"></td>
                                    <td><input type="time" name="start_time" value="<?= date('H:i', strtotime($schedule_item['start_time'])); ?>" class="form-control"></td>
                                    <td><input type="time" name="end_time" value="<?= date('H:i', strtotime($schedule_item['end_time'])); ?>" class="form-control"></td>
                                    <td><input type="text" name="subject" value="<?= htmlspecialchars($schedule_item['subject']); ?>" class="form-control"></td>
                                    <td><input type="text" name="room" value="<?= htmlspecialchars($schedule_item['room']); ?>" class="form-control"></td>
                                    <td>
                                        <select name="teacher_id" class="form-control">
                                            <?php foreach ($teachers as $teacher): ?>
                                                <option value="<?= $teacher['id']; ?>" <?= $teacher['id'] == $schedule_item['teacher'] ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($teacher['full_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                    <input type="hidden" name="schedule_id" value="<?= $schedule_item['id']; ?>">
                                    <button type="submit" name="update_schedule" class="btn btn-primary">Update</button>
                                    <a href="delete_irregular_schedule.php?schedule_id=<?= $schedule_item['id']; ?>&section_id=<?= $section_id; ?>" 
                                    onclick="return confirm('Are you sure you want to delete this schedule?');" 
                                    class="btn btn-danger">
                                    Delete
                                            </a>
                                    </td>
                                </form>
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

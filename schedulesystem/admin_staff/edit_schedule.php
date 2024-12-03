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
$staff_users = $schedule->getStaffUsers();
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

// Fetch schedules for the section
$schedules = $schedule->getSchedulesBySectionId($section_id);


setlocale(LC_TIME, 'en_US.UTF-8');  // Set locale for English (US)

function formatTime($time) {
    // Convert the time to a Unix timestamp
    $timestamp = strtotime($time);
    
    // Format it to 12-hour format with AM/PM
    return strftime('%I:%M %p', $timestamp);  // '%I:%M %p' formats to 12-hour time with AM/PM
}


// Handle editing of schedule
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_schedule'])) {
    $schedule_id = $_POST['schedule_id'];
    $day = $_POST['day'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $subject = $_POST['subject'];
    $room = $_POST['room'];
    $teacher_id = $_POST['teacher_id'];

    // Check for overlap before updating
    if ($schedule->overlapExists($section_id, $start_time, $end_time, $teacher_id ,$schedule_id, $day)) {
        $_SESSION['message'] = 'The schedule overlaps with another schedule. Please adjust the timing.';
        header("Location: edit_schedule.php?section_id=$section_id");
        exit();
    }

    // If no overlap, proceed with the update
    $update_result = $schedule->updateSchedule($schedule_id, $section_id, $day, $start_time, $end_time, $subject, $room, $teacher_id);

    if ($update_result) {
        $_SESSION['message'] = 'Schedule updated successfully.';
        header("Location: edit_schedule.php?section_id=$section_id");
        exit();
    } else {
        $_SESSION['message'] = 'Failed to update schedule.';
    }

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule</title>
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($schedules): ?>
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <form action="" method="POST">
                                <td><input type="text" name="day" value="<?= htmlspecialchars($schedule['day']); ?>" class="form-control" readonly  ></td>
                                <td><input type="time" name="start_time" value="<?= date('H:i', strtotime($schedule['start_time'])); ?>" class="form-control"></td>  <!-- Use time input -->
                                <td><input type="time" name="end_time" value="<?= date('H:i', strtotime($schedule['end_time'])); ?>" class="form-control"></td>  <!-- Use time input -->
                                <td><input type="text" name="subject" value="<?= htmlspecialchars($schedule['subject']); ?>" class="form-control"></td>
                                <td><input type="text" name="room" value="<?= htmlspecialchars($schedule['room']); ?>" class="form-control"></td>
                                <td>
                                    <select name="teacher_id" class="form-control">
                                        <?php foreach ($staff_users as $staff_user): ?>
                                            <option value="<?= $staff_user['id']; ?>" <?= $staff_user['id'] == $schedule['teacher'] ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($staff_user['full_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                            </td>
                            <td>
                                <input type="hidden" name="schedule_id" value="<?= $schedule['id']; ?>">
                                <button type="submit" name="update_schedule" class="btn btn-primary">Update</button>
                                <a href="delete_schedule.php?schedule_id=<?= $schedule['id']; ?>&section_id=<?= $section_id; ?>" 
                                onclick="return confirm('Are you sure you want to delete this schedule?');" 
                                class="btn btn-danger">
                                Delete
                                </a>
                            </td>

                            </form>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No schedule found for this section.</td>
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

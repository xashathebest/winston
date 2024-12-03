<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/section.class.php';
require_once '../classes/schedule.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database connection and classes
$db = new Database();
$section = new Section($db);
$schedule = new Schedule($db);
$staff_users = $schedule->getStaffUsers();
// Get section ID from URL
if (isset($_GET['section_id'])) {
    $section_id = $_GET['section_id'];
} else {
    $_SESSION['message'] = 'No section selected.';
    header('Location: schedules.php');
    exit();
}

// Fetch the section details
$section_details = $section->getSectionById($section_id);

if (!$section_details) {
    $_SESSION['message'] = 'Section not found.';
    header('Location: schedules.php');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Loop through all days
    foreach ($_POST['start_time'] as $day => $start_times) {
        // Loop through all start times for the current day
        foreach ($start_times as $index => $start_time) {
            // Get corresponding values for each day and schedule index
            $end_time = $_POST['end_time'][$day][$index];
            $subject = $_POST['subject'][$day][$index];
            $room = $_POST['room'][$day][$index];
            $teacher_id = $_POST['teacher'][$day][$index];

            // Check if there is a teacher overlap for this day/time
            $teacher_overlap = $schedule->checkTeacherOverlap($teacher_id, $day, $start_time, $end_time);

            if ($teacher_overlap) {
                $_SESSION['message'] = "There is an overlapping schedule for the teacher on $day.";
                header('Location: schedules.php');
                exit();
            }

            // If there's no overlap, check for schedule overlap
            $overlap = $schedule->overlapExists($section_id, $start_time, $end_time, $teacher_id ,$schedule_id, $day);

            if ($overlap) {
                $_SESSION['message'] = "Schedule conflict on $day. There is an overlapping schedule.";
                header('Location: schedules.php');
                exit();
            } else {
                // Add the schedule if no overlap
                if (!empty($start_time) && !empty($end_time) && !empty($subject) && !empty($room) && !empty($teacher_id)) {
                    $result = $schedule->addSchedule($section_id, $day, $start_time, $end_time, $subject, $room, $teacher_id);

                    // Check if insertion was successful for this day
                    if (!$result) {
                        $_SESSION['message'] = "Failed to add schedule for $day. Please try again.";
                        break 2; // Break out of both loops if there's a failure
                    }
                }
            }
        }
    }

    // If no failure, set success message
    if (!isset($_SESSION['message'])) {
        $_SESSION['message'] = 'Schedules added successfully!';
    }

    header('Location: schedules.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Schedule</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<!-- Main Content -->
<div class="main-content" style="margin-left: 260px; padding: 20px;">
    <div class="container mt-5">
        <h2 class="mb-4">Add Schedule for Section: <?= htmlspecialchars($section_details['section_name']); ?></h2>

        <!-- Display messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info" role="alert">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Add Schedule Form -->
        <form method="POST">
            <?php
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day):
            ?>
                <h5><?= $day ?>:</h5>

                <!-- Multiple Schedules for the same day -->
                <div class="schedule-group" id="schedule-group-<?= strtolower($day) ?>">
                    <!-- Loop through multiple schedules for the same day -->
                    <div class="schedule-entry">
                        <div class="form-group">
                            <label for="start_time_<?= strtolower($day) ?>[]">Start Time</label>
                            <input type="time" name="start_time[<?= strtolower($day) ?>][]" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="end_time_<?= strtolower($day) ?>[]">End Time</label>
                            <input type="time" name="end_time[<?= strtolower($day) ?>][]" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="subject_<?= strtolower($day) ?>[]">Subject</label>
                            <input type="text" name="subject[<?= strtolower($day) ?>][]" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="room_<?= strtolower($day) ?>[]">Room</label>
                            <input type="text" name="room[<?= strtolower($day) ?>][]" class="form-control">
                        </div>
                        <div class="form-group">
                    <label for="teacher_<?= strtolower($day) ?>[]">Teacher</label>
                    <select name="teacher[<?= strtolower($day) ?>][]" class="form-control">
                        <option value="">Select a Teacher</option>
                        <?php foreach ($staff_users as $staff): ?>
                            <option value="<?= htmlspecialchars($staff['id']); ?>">
                                <?= htmlspecialchars($staff['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                        <hr>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary add-schedule" data-day="<?= strtolower($day) ?>">Add Another Schedule for <?= $day ?></button>
                <hr>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">Add Schedule</button>
            <a href="schedules.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<!-- JavaScript to dynamically add schedule entries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/schedule.js"></script>
</body>
</html>

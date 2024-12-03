<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

require_once '../classes/database.class.php';
require_once '../classes/section.class.php';
require_once '../classes/schedule.class.php';
require_once '../classes/user.class.php'; 

// Initialize database connection and classes
$db = new Database();
$section = new Section($db);
$schedule = new Schedule($db);
$user = new User($db); // Initialize the User class
$staff_users = $schedule->getStaffUsers();

// Get section ID from URL
if (isset($_GET['section_id']) && is_numeric($_GET['section_id'])) {
    $section_id = intval($_GET['section_id']);
} else {
    $_SESSION['message'] = 'Invalid or missing section ID.';
    header('Location: schedules.php');
    exit();
}

// Fetch the section details
$section_details = $section->getSectionById($section_id);

// Ensure section exists
if (!$section_details) {
    $_SESSION['message'] = 'Section not found.';
    header('Location: schedules.php');
    exit();
}

// Fetch students belonging to the section
$students = $user->getStudentsBySection($section_id);

if (empty($students)) {
    $_SESSION['message'] = 'No students found for this section.';
    header('Location: schedules.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_student_id = $_POST['student_id'];

    foreach ($_POST['start_time'] as $day => $start_times) {
        foreach ($start_times as $index => $start_time) {
            $end_time = $_POST['end_time'][$day][$index];
            $subject = $_POST['subject'][$day][$index];
            $room = $_POST['room'][$day][$index];
            $teacher_id = $_POST['teacher'][$day][$index];
            
            $student_id = $selected_student_id;

            // Check for overlaps
            $teacher_overlap = $schedule->checkTeacherOverlap($teacher_id, $day, $start_time, $end_time);
            if ($teacher_overlap) {
                $_SESSION['message'] = "There is an overlapping schedule for the teacher on $day.";
                header('Location: schedules.php');
                exit();
            }

            $overlap = $schedule->checkScheduleOverlap($section_id, $day, $start_time, $end_time);
            if ($overlap) {
                $_SESSION['message'] = "Schedule conflict on $day. There is an overlapping schedule.";
                header('Location: schedules.php');
                exit();
            } else {
                // Add the schedule
                if (!empty($start_time) && !empty($end_time) && !empty($subject) && !empty($room) && !empty($teacher_id)) {
                    $result = $schedule->addScheduleIrreg($section_id, $day, $start_time, $end_time, $subject, $room, $teacher_id, $student_id);
                    if (!$result) {
                        $_SESSION['message'] = "Failed to add schedule for $day. Please try again.";
                        break 2; 
                    }
                }
            }
        }
    }

    // Success message
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
    <title>Add Irregular Schedule</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<div class="main-content" style="margin-left: 260px; padding: 20px;">
    <div class="container mt-5">
        <h2 class="mb-4">Add Irregular Schedule for Section: <?= htmlspecialchars($section_details['section_name']); ?></h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info" role="alert">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="student_id">Select Student</label>
                <select name="student_id" class="form-control" required>
                    <option value="">Select a Student</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= htmlspecialchars($student['id']); ?>">
                            <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day):
            ?>
                <h5><?= $day ?>:</h5>
                <div class="schedule-group" id="schedule-group-<?= strtolower($day) ?>">
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
            <a href="manage_schedules.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/schedule.js"></script>

</body>
</html>

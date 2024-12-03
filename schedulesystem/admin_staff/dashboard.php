<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/user.class.php';
require_once '../classes/section.class.php';
require_once '../classes/department.class.php';
require_once '../classes/course.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database connection and classes
$db = new Database();
$user = new User($db);
$section = new Section($db);
$department = new Department($db); 
$course = new Course($db);

// Get the counts
$user_counts = $user->getUserCounts();
$section_count = $section->getSectionCount();
$department_count = $department->getDepartmentCount();
$course_count = $course->getCourseCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- Sidebar -->
<?php require_once '../includes/side_nav.php'; ?>
<!-- Main Content -->
<div class="main-content" style="margin-left: 260px; padding: 20px;">
    <div class="container mt-5">
        <h2 class="mb-4">Admin Dashboard</h2>

        <!-- Display counts -->
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Staff Users</h5>
                        <p class="card-text"><?= $user_counts['staff']; ?></p>
                        <input type="hidden" id="staffCount" value="<?= $user_counts['staff']; ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Admin Users</h5>
                        <p class="card-text"><?= $user_counts['admin']; ?></p>
                        <input type="hidden" id="adminCount" value="<?= $user_counts['admin']; ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Student Users</h5>
                        <p class="card-text"><?= $user_counts['student']; ?></p>
                        <input type="hidden" id="studentCount" value="<?= $user_counts['student']; ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Courses</h5>
                        <p class="card-text"><?= $course_count; ?></p>
                        <input type="hidden" id="courseCount" value="<?= $course_count; ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Departments</h5>
                <p class="card-text"><?= $department_count; ?></p>
                <input type="hidden" id="departmentCount" value="<?= $department_count; ?>">
            </div>
        </div>
    </div>


        <!-- User Chart -->
        <div class="row mt-5">
            <div class="col-md-12">
                <canvas id="userCountsChart"></canvas>
            </div>
        </div>

        <!-- Section, Department, and Course Charts -->
        <div class="row mt-5">
            <div class="col-md-12">
                <canvas id="sectionsChart"></canvas>
            </div>
            <div class="col-md-12">
                <canvas id="departmentsChart"></canvas>
            </div>
            <div class="col-md-12">
                <canvas id="coursesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="../js/dashboard.js"></script>

</body>
</html>

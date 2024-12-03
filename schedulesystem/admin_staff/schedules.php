<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/section.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database connection and section class
$db = new Database();
$section = new Section($db);

// Handle search functionality
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
    $sections = $section->searchSectionsforsched($search_query);
} else {
    // Fetch all sections if no search query is provided
    $sections = $section->getAllSections();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<!-- Main Content -->
<div class="main-content">
<div class="container mt-5">
    <h2 class="mb-4">Manage Schedules</h2>

    <!-- Search Form -->
    <form method="GET" action="" class="form-inline mb-4">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search by Section, Course, or Department" 
               value="<?= htmlspecialchars($search_query); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <!-- Display messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info message" role="alert">
            <?= $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>


        <!-- Section Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Course</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sections as $section): ?>
                    <tr>
                        <td><?= htmlspecialchars($section['section_name']); ?></td>
                        <td><?= htmlspecialchars($section['course_code']); ?></td>
                        <td><?= htmlspecialchars($section['department_name']); ?></td>
                        <td>
                            <!-- Action buttons -->
                            <a href="view_schedule.php?section_id=<?= $section['id'] ?>" class="btn btn-sm btn-primary">View Schedule</a>
                            <a href="edit_schedule.php?section_id=<?= $section['id'] ?>" class="btn btn-sm btn-warning">Edit Schedule</a>
                            <a href="add_irregular_schedule.php?section_id=<?= $section['id'] ?>" class="btn btn-sm btn-success">Add Irregular Schedule</a>
                            <a href="add_schedule.php?section_id=<?= $section['id'] ?>" class="btn btn-sm btn-info">Add Schedule</a>
                            <a href="view_irregular_schedules.php?section_id=<?= $section['id'] ?>" class="btn btn-sm btn-secondary">View Irregular Schedules</a>
                            <a href="edit_irregular_schedule.php?section_id=<?= $section['id'] ?>" class="btn btn-sm btn-dark">Edit Irregular Schedules</a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

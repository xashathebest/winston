<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/course.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database connection
$db = new Database();
$course = new Course($db);

// Handle search functionality
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$courses = $course->searchCourses($search_query);

// Fetch all departments
$departments = $course->getAllDepartments();

// Handle course deletion
if (isset($_GET['delete_id'])) {
    $course_id = $_GET['delete_id'];
    $course->deleteCourse($course_id);
    header("Location: courses.php"); // Redirect to avoid form resubmission
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php require_once '../includes/side_nav.php'; ?>

<div class="main-content">
    <h2>Manage Courses</h2>
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search courses" value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <a href="add_course.php" class="btn btn-primary mb-3">Add Course</a>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Course Code</th>
            <th>Description</th>
            <th>Department</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
    <?php if (empty($courses)): ?>
        <tr>
            <td colspan="5" class="text-center">No records found</td>
        </tr>
    <?php else: 
        $i = 1;
        foreach ($courses as $course): 
            // Find the department name for the current course
            $department_name = '';
            foreach ($departments as $department) {
                if ($department['id'] == $course['department_id']) {
                    $department_name = $department['department_name'];
                    break; 
                }
            }
    ?>
        <tr>
            <td><?= htmlspecialchars($i) ?></td>
            <td><?= htmlspecialchars($course['course_code']) ?></td>
            <td><?= htmlspecialchars($course['course_description']) ?></td>
            <td><?= htmlspecialchars($department_name) ?></td> <!-- Display the department name -->
            <td>
                <a href="edit_course.php?id=<?= htmlspecialchars($course['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="courses.php?delete_id=<?= htmlspecialchars($course['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
            </td>
        </tr>
    <?php 
        $i++;
        endforeach; 
    endif; ?>
</tbody>

    </table>
</div>

</body>
</html>

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

// Check if a course ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: courses.php");
    exit();
}

$course_id = $_GET['id'];

// Get the course details by ID
$current_course = $course->getCourseById($course_id);

// If the course doesn't exist, redirect back
if (!$current_course) {
    header("Location: courses.php");
    exit();
}

// Handle form submission to update a course
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_code = $_POST['course_code'];
    $course_description = $_POST['course_description'];
    $department_id = $_POST['department_id'];

    // Update the course and check for duplicates
    if (!$course->updateCourse($course_id, $course_code, $course_description, $department_id)) {
        $error_message = "Error: Course code or description already exists."; // Display error if course exists
    } else {
        header("Location: courses.php"); // Redirect to manage courses page after updating
        exit();
    }
}

// Get all departments for the department dropdown
$departments = $course->getAllDepartments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <h2>Edit Course</h2>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="course_code">Course Code:</label>
            <input type="text" class="form-control" id="course_code" name="course_code" value="<?php echo $current_course['course_code']; ?>" required>
        </div>
        <div class="form-group">
            <label for="course_description">Course Description:</label>
            <textarea class="form-control" id="course_description" name="course_description" required><?php echo $current_course['course_description']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="department_id">Department:</label>
            <select class="form-control" id="department_id" name="department_id" required>
                <option value="">Select Department</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?php echo $department['id']; ?>" <?php echo ($department['id'] == $current_course['department_id']) ? 'selected' : ''; ?>>
                        <?php echo $department['department_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Course</button>
    </form>
</div>

</body>
</html>

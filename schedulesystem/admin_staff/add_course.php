<?php
session_start(); 

require_once '../classes/database.class.php';
require_once '../classes/course.class.php';

// Check if user is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

$db = new Database();
$course = new Course($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_code = $_POST['course_code'];
    $course_description = $_POST['course_description'];
    $department_id = $_POST['department_id'];  // Department selected by the user

    // Add the course
    if ($course->addCourse($course_code, $course_description, $department_id)) {
        header("Location: courses.php"); // Redirect to manage courses page after adding
        exit();
    } else {
        $_SESSION['error_message'] = "Error: Course code or description already exists."; // Store the error message in session
    }
}

$departments = $course->getAllDepartments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <h2>Add New Course</h2>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="course_code">Course Code:</label>
            <input type="text" class="form-control" id="course_code" name="course_code" required>
        </div>
        <div class="form-group">
            <label for="course_description">Course Description:</label>
            <textarea class="form-control" id="course_description" name="course_description" required></textarea>
        </div>
        <div class="form-group">
            <label for="department_id">Department:</label>
            <select class="form-control" id="department_id" name="department_id" required>
                <option value="">Select Department</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?php echo $department['id']; ?>"><?php echo $department['department_name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
        <a href="courses.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>

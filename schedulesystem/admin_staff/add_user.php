<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/user.class.php';

// Check if user is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch departments
$query = "SELECT id, department_name FROM department";
$stmt = $conn->prepare($query);
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user = new User($db);

$courses = $user->getCourses();
$sections = $user->getSections();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect the form data
$last_name = $_POST['last_name'];
$first_name = $_POST['first_name'];
$middle_initial = $_POST['middle_initial'];
$section_id = $_POST['section_id'] ?? null;
$course_id = $_POST['course_id'];
$department_id = $_POST['department_id'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];

// Create the new user and get the result
$userCreated = $user->create($last_name, $first_name, $middle_initial, $section_id, $course_id, $department_id, $email, $password, $role);
if ($userCreated) {
    $_SESSION['success_message'] = "User created successfully.";
    header("Location: admin.php");
} else {
    $_SESSION['error_message'] = "User email already exists.";
    header("Location: add_user.php");
}

exit;
exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<div class="main-content">
    <h2>Add New User</h2>

     <!-- Display Session Messages -->
     <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_message']; ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php elseif (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message']; ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

        
        <form action="add_user.php" method="POST" class="mb-3">
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" name="last_name" id="last_name" required>
        </div>

        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" name="first_name" id="first_name" required>
        </div>

        <div class="form-group">
            <label for="middle_initial">Middle Initial</label>
            <input type="text" class="form-control" name="middle_initial" id="middle_initial" maxlength="1">
        </div>
        <!-- Department Dropdown -->
        <div class="form-group">
            <label for="department_id">Department</label>
            <select class="form-control" name="department_id" id="department_id">
                <option value="">Select a department</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department['id'] ?>"><?= $department['department_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Course Dropdown -->
        <div class="form-group">
            <label for="course_id">Course</label>
            <select class="form-control" name="course_id" id="course_id">
                <option value="">Select a course</option>
            </select>
        </div>

        <!-- Section Dropdown -->
        <div class="form-group">
            <label for="section_id">Section</label>
            <select class="form-control" name="section_id" id="section_id">
                <option value="">Select a section</option>
            </select>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select class="form-control" name="role" id="role" required>
            <?php

                if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            ?>    
            <option value="student">Student</option>
            <?php
                }
                else{
                    ?>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="student">Student</option>
                <?php
                }
                ?>
            ?>
  
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Add User</button>
    </form>
</div>

<script src="../js/user.js"></script>
</body>
</html>

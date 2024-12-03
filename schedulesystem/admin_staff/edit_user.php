<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/user.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database connection and user class
$db = new Database();
$user = new User($db);

// Get the user ID from the query string
$user_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($user_id) {
    // Fetch the user details using the provided ID
    $user_details = $user->read($user_id);
    if (!$user_details) {
        // Redirect or handle if user not found
        header("Location: admin.php");
        exit();
    }
} else {
    // Redirect or handle if no ID is provided
    header("Location: admin.php");
    exit();
}

// Handle form submission for updating the user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect the data from the form
    $data = [
        'last_name' => $_POST['last_name'],
        'first_name' => $_POST['first_name'],
        'middle_initial' => $_POST['middle_initial'],
        'section_id' => $_POST['section_id'],
        'course_id' => $_POST['course_id'],
        'department_id' => $_POST['department_id'],
        'email' => $_POST['email'],
        'role' => $_POST['role']
    ];

    // Only update the password if a new one is provided
    if (!empty($_POST['password'])) {
        // Hash the new password before storing it
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    } else {
        // If no new password is provided, don't include it in the update query
        unset($data['password']);
    }

    // Call the update method to update the user details
    $update_result = $user->update($user_id, $data);

    if ($update_result === true) {
        // Successfully updated, store success message in session
        $_SESSION['success_message'] = "User updated successfully.";
        header("Location: admin.php");  // Redirect to avoid resubmitting the form
        exit();
    } else {
        // Display the error message (email already exists)
        $_SESSION['error_message'] = "The email address is already in use.";
    }
}



// Fetch all available sections, courses, and departments for the form
$sections = $user->getSections();
$courses = $user->getCourses();
$departments = $user->getDepartments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    
<?php require_once '../includes/side_nav.php'; ?>


    <div class="main-content">
        <h2>Edit User</h2>
        <?php
    // Display error message if exists
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
        unset($_SESSION['error_message']);  // Remove the message after displaying it
    }
    ?>

        <form method="POST" class="form-container">
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-control" value="<?= htmlspecialchars($user_details['last_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-control" value="<?= htmlspecialchars($user_details['first_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="middle_initial">Middle Initial</label>
                <input type="text" id="middle_initial" name="middle_initial" class="form-control" value="<?= htmlspecialchars($user_details['middle_initial']) ?>" required>
            </div>

            <div class="form-group">
                <label for="department_id">Department</label>
                <select id="department_id" name="department_id" class="form-control">
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= $department['id'] ?>" <?= $department['id'] == $user_details['department_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($department['department_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="course_id">Course</label>
                <select id="course_id" name="course_id" class="form-control">
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['id'] ?>" <?= $course['id'] == $user_details['course_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['course_code']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="section_id">Section</label>
                <select id="section_id" name="section_id" class="form-control">
                    <?php foreach ($sections as $section): ?>
                        <option value="<?= $section['id'] ?>" <?= $section['id'] == $user_details['section_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($section['section_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user_details['email']) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control">
                <small class="form-text text-muted">Leave blank to keep current password</small>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="form-control">
                    <option value="admin" <?= $user_details['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="staff" <?= $user_details['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                    <option value="student" <?= $user_details['role'] == 'student' ? 'selected' : '' ?>>Student</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update User</button>
        </form>

        <a href="admin.php" class="btn btn-secondary mt-3">Back to Admin Panel</a>
    </div>

<script src="../js/user.js"></script>
</body>
</html>

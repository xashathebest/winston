<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/department.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

// Initialize database connection
$db = new Database();
$department = new Department($db);

// Handle department update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_department'])) {
    $department_id = $_POST['department_id'];
    $department_name = $_POST['department_name'];

    // Attempt to update the department
    $success = $department->updateDepartment($department_id, $department_name);

    if ($success) {
        header("Location: departments.php"); // Redirect after successful update
        exit();
    } else {
        // If the department name already exists, set an error message
        $error_message = "Department name already exists!";
    }
}

// Get the department details for editing
if (isset($_GET['id'])) {
    $department_id = $_GET['id'];
    $departments = $department->getAllDepartments();
    $department_to_edit = null;
    foreach ($departments as $dept) {
        if ($dept['id'] == $department_id) {
            $department_to_edit = $dept;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Department</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    
<?php require_once '../includes/side_nav.php'; ?>

<div class="main-content">
    <h2>Edit Department</h2>

    <!-- Display error message if the department name already exists -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <!-- Edit Department Form -->
    <form method="POST">
        <div class="form-group">
            <input type="hidden" name="department_id" value="<?= htmlspecialchars($department_to_edit['id']) ?>">
            <label for="department_name">Department Name</label>
            <input type="text" class="form-control" id="department_name" name="department_name" value="<?= htmlspecialchars($department_to_edit['department_name']) ?>" required>
        </div>
        <button type="submit" name="update_department" class="btn btn-primary">Update Department</button>
    </form>
</div>

</body>
</html>

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

// Handle department addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_department'])) {
    $department_name = $_POST['department_name'];

    // Attempt to add the department
    $success = $department->addDepartment($department_name);

    if ($success) {
        header("Location: departments.php"); // Redirect after adding
        exit();
    } else {
        // If the department already exists, set an error message
        $error_message = "Department already exists!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Department</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    
<?php require_once '../includes/side_nav.php'; ?>

<div class="main-content">
    <h2>Add Department</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>
    
    <form action="add_department.php" method="POST">
        <div class="form-group">
            <label for="department_name">Department Name</label>
            <input type="text" class="form-control" id="department_name" name="department_name" required>
        </div>
        
        <button type="submit" name="add_department" class="btn btn-primary">Add Department</button>
    </form>
</div>

</body>
</html>

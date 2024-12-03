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

// Handle search functionality
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$departments = $department->searchDepartments($search_query);

// Handle department deletion
if (isset($_GET['delete_id'])) {
    $department_id = $_GET['delete_id'];
    $department->deleteDepartment($department_id);
    header("Location: departments.php"); // Redirect to avoid form resubmission
    exit();
}

// Handle department addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_department'])) {
    $department_name = $_POST['department_name'];
    $department->addDepartment($department_name);
    header("Location: departments.php"); // Redirect after adding
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Departments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php require_once '../includes/side_nav.php'; ?>

<div class="main-content">
    <h2>Manage Departments</h2>
    
    <!-- Search Form -->
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search departments" value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    
    <!-- Button to Add Department -->
    <a href="add_department.php" class="btn btn-primary mb-3">Add Department</a>

    <!-- Department List -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Department Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
    <?php if (empty($departments)): ?>
        <tr>
            <td colspan="3" class="text-center">No records found</td>
        </tr>
    <?php else: 
        $i = 1;
        foreach ($departments as $department): ?>
            <tr>
                <td><?= htmlspecialchars($department['id']) ?></td>
                <td><?= htmlspecialchars($department['department_name']) ?></td>
                <td>
                    <a href="edit_department.php?id=<?= htmlspecialchars($department['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_department.php?delete_id=<?= htmlspecialchars($department['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this department?')">Delete</a>
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

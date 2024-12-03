<?php
session_start(); 
require_once '../classes/database.class.php';
require_once '../classes/user.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

$db = new Database();
$user = new User($db);

// Assuming the user's role is stored in $_SESSION['user_role']
$user_role = $_SESSION['user_role'];
$search = $_GET['search'] ?? '';

// Call the method with the user's role
$users = $user->readAllWithDetails($user_role, $search);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <h2>Manage Users</h2>
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
    <!-- Add User Button -->
    <a href="add_user.php" class="btn btn-primary mb-3">Add User</a>
    <div class="search-bar mb-3">
    <form method="GET" action="">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search users..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div>
    </form>
</div>

    <!-- User List Table -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Middle Initial</th>
            <th>Section</th>
            <th>Course</th>
            <th>Department</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
<?php 
if (empty($users)): ?>
    <tr>
        <td colspan="10" class="text-center">No records found.</td>
    </tr>
<?php else:
    $i = 1;
    foreach ($users as $user): ?>
        <tr>
            <td><?= $i ?></td>
            <td><?= htmlspecialchars($user['last_name']) ?></td>
            <td><?= htmlspecialchars($user['first_name']) ?></td>
            <td><?= htmlspecialchars($user['middle_initial']) ?></td>
            <td><?= htmlspecialchars($user['section_name']) ?></td>
            <td><?= htmlspecialchars($user['course_code']) ?></td>
            <td><?= htmlspecialchars($user['department_name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td>
                <a href="edit_user.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="delete_user.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
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

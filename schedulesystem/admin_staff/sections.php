<?php
session_start();
require_once '../classes/database.class.php';
require_once '../classes/section.class.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'staff') {
    // Redirect unauthorized users to the login page
    header('Location: ../accounts/login.php');
    exit;
}

$db = new Database();
$section = new Section($db);

// Handle search functionality
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$sections = $section->searchSections($search_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Sections</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/section.js"></script>
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container mt-5">
        <h2 class="mb-4">Manage Sections</h2>

        <!-- Search Form -->
        <form method="GET" class="form-inline mb-4">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search sections" value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <!-- Add Section Button -->
        <div class="mb-3">
            <a href="add_section.php" class="btn btn-primary">Add Section</a>
        </div>

        <!-- Sections Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Section Name</th>
                    <th>Course Name</th>
                    <th>Department Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php if (empty($sections)): ?>
        <tr>
            <td colspan="5" class="text-center">No records found</td>
        </tr>
    <?php else: 
        $i = 1;
        foreach ($sections as $section): ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= htmlspecialchars($section['section_name']) ?></td>
                <td><?= htmlspecialchars($section['course_description']) ?></td>
                <td><?= htmlspecialchars($section['department_name']) ?></td>
                <td>
                    <!-- Action buttons for Edit and Delete -->
                    <a href="edit_section.php?id=<?= $section['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <button onclick="confirmDelete(<?= $section['id'] ?>)" class="btn btn-sm btn-danger">Delete</button>
                </td>
            </tr>
        <?php 
        $i++;
        endforeach; 
    endif; ?>
</tbody>

        </table>
    </div>
</div>

</body>
</html>

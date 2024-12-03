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

// Fetch all departments for the dropdown
$departments = $section->getDepartments();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sectionName = $_POST['section_name'];
    $courseId = $_POST['course_id'];
    $departmentId = $_POST['department_id']; // Get department_id from the form

    if (!empty($sectionName) && !empty($courseId) && !empty($departmentId)) {
        // Try adding the section
        $isAdded = $section->addSection($sectionName, $courseId, $departmentId);
        
        if ($isAdded) {
            header("Location: sections.php");
            exit;
        } else {
            $error = "Section with this name already exists for the selected course.";
        }
    } else {
        $error = "Please fill out all fields.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Section</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/section.js"></script>
</head>
<body>

<?php require_once '../includes/side_nav.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container mt-5">
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <h2 class="mb-4">Add New Section</h2>

        <form action="add_section.php" method="POST">
            <div class="form-group">
                <label for="section_name">Section Name</label>
                <input type="text" name="section_name" id="section_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="department_id">Select Department</label>
                <select name="department_id" id="department_id" class="form-control" required onchange="fetchCourses(this.value)">
                    <option value="">--Select Department--</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= htmlspecialchars($department['id']) ?>"><?= htmlspecialchars($department['department_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="course_id">Select Course</label>
                <select name="course_id" id="course_id" class="form-control" required>
                    <option value="">--Select Course--</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Add Section</button>
            <a href="sections.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>

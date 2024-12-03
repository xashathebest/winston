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

// Fetch the section ID from the URL
$sectionId = isset($_GET['id']) ? $_GET['id'] : null;

// If no section ID is provided, redirect to the sections list
if (!$sectionId) {
    header("Location: sections.php");
    exit;
}

// Fetch the section details by ID
$sectionDetails = $section->getSectionById($sectionId);

// If section does not exist, redirect to sections list
if (!$sectionDetails) {
    header("Location: sections.php");
    exit;
}

// Fetch all departments for the dropdown
$departments = $section->getDepartments();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sectionName = $_POST['section_name'];
    $courseId = $_POST['course_id'];

    if (!empty($sectionName) && !empty($courseId)) {
        // Check if the section name already exists
        if ($section->sectionExists($sectionName, $sectionId)) {
            $error = "This section name already exists. Please choose a different name.";
        } else {
            // Update the section
            $updateSuccess = $section->updateSection($sectionId, $sectionName, $courseId);
            
            if ($updateSuccess) {
                header("Location: sections.php");
                exit;
            } else {
                $error = "Failed to update the section.";
            }
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
    <title>Edit Section</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        <h2 class="mb-4">Edit Section</h2>

        <form action="edit_section.php?id=<?= $sectionId ?>" method="POST">
            <div class="form-group">
                <label for="section_name">Section Name</label>
                <input type="text" name="section_name" id="section_name" class="form-control" value="<?= htmlspecialchars($sectionDetails['section_name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="department_id">Select Department</label>
                <select name="department_id" id="department_id" class="form-control" required onchange="fetchCourses(this.value)">
                    <option value="">--Select Department--</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= htmlspecialchars($department['id']) ?>" <?= $department['id'] == $sectionDetails['course_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($department['department_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="course_id">Select Course</label>
                <select name="course_id" id="course_id" class="form-control" required>
                    <option value="">--Select Course--</option>
                    <!-- Course options will be populated by fetchCourses function -->
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Section</button>
        </form>
    </div>
</div>

</body>
</html>

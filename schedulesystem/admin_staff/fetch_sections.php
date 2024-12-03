<?php
require_once '../classes/database.class.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];
    
    // Query to fetch sections based on the course ID
    $query = "SELECT id, section_name FROM section WHERE course_id = :courseId Order By section_name";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
    $stmt->execute();
    
    // Fetch all sections
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Output the sections as options for the dropdown
    foreach ($sections as $section) {
        echo "<option value=\"{$section['id']}\">{$section['section_name']}</option>";
    }
}
?>

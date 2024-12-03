<?php
require_once '../classes/database.class.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['department_id'])) {
    $departmentId = $_GET['department_id'];
    
    $query = "SELECT id, course_code FROM course WHERE department_id = :departmentId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':departmentId', $departmentId, PDO::PARAM_INT);
    $stmt->execute();
    
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($courses as $course) {
        echo "<option value=\"{$course['id']}\">{$course['course_code']}</option>";
    }
}
?>
    
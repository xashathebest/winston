<?php
require_once '../classes/database.class.php';

// Establish the database connection
$db = new Database();
$conn = $db->getConnection();

// Get the department ID from the request
$departmentId = $_GET['department_id'];

// Prepare and execute the query to fetch courses based on department
$query = "SELECT id, course_description FROM course WHERE department_id = :department_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':department_id', $departmentId, PDO::PARAM_INT);
$stmt->execute();

// Fetch the courses
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the courses as JSON
echo json_encode($courses);
?>

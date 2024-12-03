<?php
require_once 'database.class.php';
class Course {
    private $db;
    private $conn;
    
    // Constructor to initialize database connection
    public function __construct($db) {
        $this->db = $db;
        $this->conn = $db->getConnection();  // Get the PDO connection
    }

    // Get all courses
    public function getAllCourses() {
        $query = "SELECT * FROM course";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllDepartments() {
        $query = "SELECT * FROM department";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchCourses($query) {
        $sql = "SELECT c.*, d.department_name 
                FROM course c 
                LEFT JOIN department d ON c.department_id = d.id 
                WHERE c.course_code LIKE :query 
                OR c.course_description LIKE :query 
                OR d.department_name LIKE :query";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    // Add a new course
    public function addCourse($course_code, $course_description, $department_id) {
        // Check if the course code or description already exists
        $query = "SELECT COUNT(*) FROM course WHERE course_code = :course_code OR course_description = :course_description";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_code', $course_code);
        $stmt->bindParam(':course_description', $course_description);
        $stmt->execute();
        
        // If the course code or description already exists, return false
        if ($stmt->fetchColumn() > 0) {
            return false; // Course already exists
        }
        
        // Proceed with inserting the new course if both code and description are unique
        $query = "INSERT INTO course (course_code, course_description, department_id) VALUES (:course_code, :course_description, :department_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_code', $course_code);
        $stmt->bindParam(':course_description', $course_description);
        $stmt->bindParam(':department_id', $department_id);
        return $stmt->execute();
    }
    
    
    public function deleteCourse($course_id) {
        // Disable foreign key checks temporarily
        $this->conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    
        // Delete any dependent rows from the 'section' table (optional)
        $stmt = $this->conn->prepare("DELETE FROM section WHERE course_id = :course_id");
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
    
        // Delete the course itself
        $stmt = $this->conn->prepare("DELETE FROM course WHERE id = :id");
        $stmt->bindParam(':id', $course_id);
        $stmt->execute();
    
        // Re-enable foreign key checks
        $this->conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    }
    
    // Get a course by its ID
    public function getCourseById($course_id) {
        $query = "SELECT * FROM course WHERE id = :course_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update a course
    public function updateCourse($course_id, $course_code, $course_description, $department_id) {
        // Check if a course with the same code or description already exists (excluding the current course)
        $checkQuery = "SELECT COUNT(*) FROM course WHERE (course_code = :course_code OR course_description = :course_description) AND id != :course_id";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':course_code', $course_code);
        $stmt->bindParam(':course_description', $course_description);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        $count = $stmt->fetchColumn();
    
        if ($count > 0) {
            return false;  // Course code or description already exists
        }
    
        // If no existing course found, proceed with the update
        $query = "UPDATE course SET course_code = :course_code, course_description = :course_description, department_id = :department_id WHERE id = :course_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_code', $course_code);
        $stmt->bindParam(':course_description', $course_description);
        $stmt->bindParam(':department_id', $department_id);
        $stmt->bindParam(':course_id', $course_id);
        return $stmt->execute();
    }

    public function getCourseCount() {
        $query = "SELECT COUNT(id) AS course_count FROM course"; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['course_count'];
    }
}
?>

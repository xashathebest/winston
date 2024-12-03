<?php
class Section
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db->getConnection();
    }

    // Get all sections
    public function getAllSections()
    {
        $query = "
            SELECT 
                section.id, 
                section.section_name, 
                course.course_description, 
                department.department_name,
                course.course_code
            FROM section
            LEFT JOIN course ON section.course_id = course.id
            LEFT JOIN department ON course.department_id = department.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function searchSections($query) {
        $sql = "
            SELECT 
                section.id, 
                section.section_name, 
                course.course_description, 
                department.department_name,
                course.course_code
            FROM section
            LEFT JOIN course ON section.course_id = course.id
            LEFT JOIN department ON course.department_id = department.id
            WHERE 
                section.section_name LIKE :query OR 
                course.course_description LIKE :query OR 
                department.department_name LIKE :query";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getDepartments()
    {
        $query = "SELECT id, department_name FROM department";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSectionById($id)
    {
        $query = "SELECT id, section_name, course_id FROM section WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all courses
    public function getCourses() {
        $query = "SELECT id, course_description FROM course";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new section after checking if it already exists
    public function addSection($name, $courseId, $departmentId) {
        // Check if section name already exists for the given course and department
        if ($this->checkSectionExists($name, $courseId, $departmentId)) {
            return false;  // Section already exists, deny entry
        }
    
        // Insert the section along with the department_id
        $query = "INSERT INTO section (section_name, course_id, department_id) VALUES (:name, :courseId, :departmentId)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':courseId', $courseId);
        $stmt->bindParam(':departmentId', $departmentId);
        $stmt->execute();
    
        return true;  // Section added successfully
    }
    

    // Check if a section with the same name and course already exists
    private function checkSectionExists($name, $courseId) {
        $query = "SELECT COUNT(*) FROM section WHERE section_name = :name AND course_id = :courseId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':courseId', $courseId);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result > 0;  // Return true if section exists, false otherwise
    }

    public function sectionExists($sectionName, $excludeId = null)
    {
        $query = "SELECT COUNT(*) FROM section WHERE section_name = :section_name";
        
        // If an ID is provided, exclude it from the check (for update case)
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':section_name', $sectionName);

        // Bind excludeId if provided
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }

        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        return $count > 0;  // Return true if section name exists
    }
    
    // Update an existing section
    public function updateSection($id, $name, $course_id)
    {
        // Check if the section name already exists
        if ($this->sectionExists($name, $id)) {
            return false;  // Return false if section name already exists
        }

        $query = "UPDATE section SET section_name = :name, course_id = :course_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();

        return true;  // Return true if the update was successful
    }
    // Delete a section by ID
    public function deleteSection($id)
    {
        $query = "DELETE FROM section WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();  // Return true if delete is successful
    }

    public function getSectionCount() {
        $query = "SELECT COUNT(id) AS section_count FROM section";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['section_count'];
    }

    public function searchSectionsforsched($query) {
        $sql = "SELECT section.*, course.course_code, department.department_name 
                FROM section
                JOIN course ON section.course_id = course.id
                JOIN department ON course.department_id = department.id
                WHERE section.section_name LIKE :query 
                   OR course.course_code LIKE :query 
                   OR department.department_name LIKE :query
                ORDER BY section.section_name ASC";
        $stmt = $this->conn->prepare($sql);
        $search_term = '%' . $query . '%';
        $stmt->bindParam(':query', $search_term, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
?>

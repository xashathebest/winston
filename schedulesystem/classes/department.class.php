<?php

class Department
{
    private $db;
    private $conn;

    // Constructor
    public function __construct($db) {
        $this->db = $db;
        $this->conn = $db->getConnection();  // Get the PDO connection
    }

    // Function to get all departments
    public function getAllDepartments()
    {
        $query = "SELECT * FROM department"; // Assuming 'departments' table exists
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchDepartments($query) {
        $sql = "SELECT * FROM department WHERE department_name LIKE :query";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    // Function to add a new department
    public function addDepartment($name)
    {
        // Check if the department already exists
        $checkQuery = "SELECT COUNT(*) FROM department WHERE department_name = :name";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $count = $stmt->fetchColumn();
    
        // If the department exists, return an error message
        if ($count > 0) {
            return false; // Department already exists
        }
    
        // Proceed with inserting the new department if it doesn't exist
        $query = "INSERT INTO department (department_name) VALUES (:name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
    
        return true; // Department added successfully
    }
    

    // Function to delete a department
    public function deleteDepartment($department_id)
    {
        $query = "DELETE FROM department WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $department_id);
        $stmt->execute();
    }

    // Function to update a department
    public function updateDepartment($department_id, $name)
{
    // Check if the department name already exists, excluding the current department
    $checkQuery = "SELECT COUNT(*) FROM department WHERE department_name = :name AND id != :id";
    $stmt = $this->conn->prepare($checkQuery);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':id', $department_id);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    // If the department name already exists, return false
    if ($count > 0) {
        return false; // Department name already exists
    }

    // Proceed with the update if the name is unique
    $query = "UPDATE department SET department_name = :name WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $department_id);
    $stmt->bindParam(':name', $name);
    $stmt->execute();

    return true; // Update successful
}

public function getDepartmentCount() {
    $query = "SELECT COUNT(id) AS department_count FROM department";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['department_count'];
}

}
?>

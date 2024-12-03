<?php
require_once 'database.class.php';

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db->getConnection();
    }

    public function create($last_name, $first_name, $middle_initial, $section_id, $course_id, $department_id, $email, $password, $role) {
        // Check if email already exists
        $query = "SELECT COUNT(*) FROM user WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $emailCount = $stmt->fetchColumn();

        if ($emailCount > 0) {
            // Email already exists, return false
            return false; 
        }

        // Proceed with user creation if email is unique
        $query = "INSERT INTO user (last_name, first_name, middle_initial, section_id, course_id, department_id, email, password, role) 
                  VALUES (:last_name, :first_name, :middle_initial, :section_id, :course_id, :department_id, :email, :password, :role)";
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_initial', $middle_initial);
        $stmt->bindParam(':section_id', $section_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':department_id', $department_id);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT)); // Securely hash the password
        $stmt->bindParam(':role', $role);

        // Execute the query and return true if successful
        return $stmt->execute();
    }
    
    
    // Retrieve all users
    public function readAll() {
        $stmt = $this->conn->query("SELECT * FROM User");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readAllWithDetails($user_role, $search = '') {
        // Base SQL query
        $sql = "
            SELECT 
                u.id, u.last_name, u.first_name, u.middle_initial, 
                u.email, u.role, 
                s.section_name, c.course_code, d.department_name
            FROM User u
            LEFT JOIN section s ON u.section_id = s.id
            LEFT JOIN course c ON u.course_id = c.id
            LEFT JOIN department d ON u.department_id = d.id
        ";
    
        // Add a condition to filter students if the role is staff
        $conditions = [];
        if ($user_role === 'staff') {
            $conditions[] = "u.role = 'student'";
        }
    
        // Add a condition for the search input
        if (!empty($search)) {
            $search = '%' . $search . '%'; // Prepare for LIKE clause
            $conditions[] = "(u.last_name LIKE :search OR 
                              u.first_name LIKE :search OR 
                              u.middle_initial LIKE :search OR 
                              u.email LIKE :search OR 
                              u.role LIKE :search OR 
                              s.section_name LIKE :search OR 
                              c.course_code LIKE :search OR 
                              d.department_name LIKE :search)";
        }
    
        // Combine all conditions with WHERE
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
    
        $stmt = $this->conn->prepare($sql);
    
        // Bind the search parameter if it exists
        if (!empty($search)) {
            $stmt->bindParam(':search', $search);
        }
    
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    



    // Retrieve a single user by ID
    public function read($id) {
        $stmt = $this->conn->prepare("SELECT * FROM User WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        // Check if the new email already exists in the database (excluding the current user's email)
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user WHERE email = ? AND id != ?");
        $stmt->execute([$data['email'], $id]);
        $email_exists = $stmt->fetchColumn();
    
        if ($email_exists) {
            // Set error message in session if email is already in use
            $_SESSION['error_message'] = "The email address is already in use.";
            return false;  // Return false to indicate failure
        }
    
        $sql = "UPDATE user SET 
                last_name = :last_name, 
                first_name = :first_name, 
                middle_initial = :middle_initial, 
                section_id = :section_id, 
                course_id = :course_id, 
                department_id = :department_id, 
                email = :email, 
                role = :role";
    
        // Add password to the query if it's present
        if (isset($data['password'])) {
            $sql .= ", password = :password";
        }
    
        $sql .= " WHERE id = :id";
    
        // Prepare and execute the query
        $stmt = $this->conn->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(":id", $id); // Correctly bind the $id parameter
    
        return $stmt->execute();
    }
    
    
    public function getStudentsBySection($section_id) {
        $query = "SELECT * FROM user WHERE role = 'student' AND section_id = :section_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    // Delete a user
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM User WHERE id = ?");
        $stmt->execute([$id]);
    }

    // Retrieve all courses
    public function getCourses() {
        $stmt = $this->conn->query("SELECT id, course_code FROM course");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve all departments
    public function getDepartments() {
        $stmt = $this->conn->query("SELECT id, department_name FROM department");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retrieve all sections
    public function getSections() {
        $stmt = $this->conn->query("SELECT id, section_name FROM section");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($student_id) {
        // Prepare and execute SQL query
        $query = "SELECT * FROM user WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $student_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch and return user details
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserCounts() {
        $query = "SELECT role, COUNT(id) AS count FROM user GROUP BY role";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $counts = ['staff' => 0, 'admin' => 0, 'student' => 0];

        foreach ($result as $row) {
            if ($row['role'] == 'staff') {
                $counts['staff'] = $row['count'];
            } elseif ($row['role'] == 'admin') {
                $counts['admin'] = $row['count'];
            } elseif ($row['role'] == 'student') {
                $counts['student'] = $row['count'];
            }
        }
        
        return $counts;
    }

    public function login($email, $password) {
        // Query to find the user by email
        $query = "SELECT * FROM user WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Fetch the user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and the password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            return $user; // Return user details
        }

        // Login failed
        return false;
    }
}
?>

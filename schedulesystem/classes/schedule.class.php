<?php
    class Schedule {
        private $conn;

    public function __construct($db) {
        $this->conn = $db->getConnection();
    }

    
        public function addSchedule($section_id, $day, $start_time, $end_time, $subject, $room, $teacher_id) {
            $query = "INSERT INTO schedule (section_id, day, start_time, end_time, subject, room, teacher) 
                      VALUES (:section_id, :day, :start_time, :end_time, :subject, :room, :teacher)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':section_id', $section_id);
            $stmt->bindParam(':day', $day);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':room', $room);
            $stmt->bindParam(':teacher', $teacher_id);
            
            return $stmt->execute();
        }

        public function addScheduleIrreg($section_id, $day, $start_time, $end_time, $subject, $room, $teacher_id, $student_id) {
            $query = "INSERT INTO schedule (section_id, day, start_time, end_time, subject, room, teacher, student_id) 
                      VALUES (:section_id, :day, :start_time, :end_time, :subject, :room, :teacher, :student_id)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':section_id', $section_id);
            $stmt->bindParam(':day', $day);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':room', $room);
            $stmt->bindParam(':teacher', $teacher_id);
            $stmt->bindParam(':student_id', $student_id);
            
            return $stmt->execute();
        }

        public function getSchedulesBySectionId($section_id) {
    $query = "
        SELECT 
            schedule.id,
            schedule.teacher,
            schedule.day,
            schedule.start_time,
            schedule.end_time,
            schedule.subject,
            schedule.room,
            CONCAT(user.first_name, ' ', user.last_name) AS teacher_name
        FROM schedule
        LEFT JOIN user ON schedule.teacher = user.id
        WHERE schedule.section_id = :section_id AND student_id IS NULL
        ORDER BY 
            CASE schedule.day
                WHEN 'Monday' THEN 1
                WHEN 'Tuesday' THEN 2
                WHEN 'Wednesday' THEN 3
                WHEN 'Thursday' THEN 4
                WHEN 'Friday' THEN 5
                WHEN 'Saturday' THEN 6
                WHEN 'Sunday' THEN 7
                ELSE 8  
            END,
            schedule.start_time ASC
    ";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':section_id', $section_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



        // In schedule.class.php

        public function getTeacherNameById($teacher_id) {
            $query = "SELECT CONCAT(first_name, ' ', last_name) AS teacher_name FROM user WHERE id = :teacher_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
            $stmt->execute();
            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
            return $teacher ? $teacher['teacher_name'] : 'No Teacher Assigned'; // Default if not found
}


        public function checkScheduleOverlap($section_id, $day, $start_time, $end_time) {
            $query = "SELECT * FROM schedule WHERE section_id = :section_id AND day = :day";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':section_id', $section_id, PDO::PARAM_INT);
            $stmt->bindParam(':day', $day, PDO::PARAM_STR);
            $stmt->execute();
    
            $existing_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Convert the times to timestamps for easier comparison
            $new_start = strtotime($start_time);
            $new_end = strtotime($end_time);
    
            // Check for overlap with existing schedules
            foreach ($existing_schedules as $existing) {
                $existing_start = strtotime($existing['start_time']);
                $existing_end = strtotime($existing['end_time']);
    
                // If the new schedule overlaps with an existing one, return true
                if (($new_start < $existing_end) && ($new_end > $existing_start)) {
                    return true;
                }
            }
    
            // No overlap
            return false;
        }

        public function checkTeacherOverlap($teacher_id, $day, $start_time, $end_time) {
            $query = "
                SELECT * 
                FROM schedule 
                WHERE teacher = :teacher
                  AND day = :day";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':teacher', $teacher_id, PDO::PARAM_INT);
            $stmt->bindParam(':day', $day, PDO::PARAM_STR);
            $stmt->execute();
        
            $existing_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            // Convert the new schedule times to timestamps for easier comparison
            $new_start = strtotime($start_time);
            $new_end = strtotime($end_time);
        
            // Check for overlap with existing teacher schedules
            foreach ($existing_schedules as $existing) {
                $existing_start = strtotime($existing['start_time']);
                $existing_end = strtotime($existing['end_time']);
        
                // If the new schedule overlaps with an existing teacher schedule, return true
                if (($new_start < $existing_end) && ($new_end > $existing_start)) {
                    return true;
                }
            }
        
            // No overlap with teacher
            return false;
        }
        
        
        public function getSectionNameById($section_id) {
            $query = "SELECT section_name FROM section WHERE id = :section_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':section_id', $section_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['section_name'] : null;  // Return section name or null if not found
        }


        public function getSchedulesForSection($section_id) {
            // Query to fetch schedules that have both student_id and section_id
            $query = "SELECT student_id, day, start_time, end_time, subject, room, teacher
                      FROM schedule
                      WHERE section_id = :section_id AND student_id IS NOT NULL";
    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':section_id', $section_id);
            $stmt->execute();
    
            // Fetch all schedules for students in the section
            $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $schedules;
        }

            public function getStaffUsers() {
                $sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS full_name FROM user WHERE role = 'staff'";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            public function overlapExists($section_id, $start_time, $end_time, $teacher_id, $schedule_id = null, $day) {
                // SQL query to find overlapping schedules
                $sql = "SELECT * FROM schedule WHERE 
                        section_id = :section_id 
                        AND day = :day
                        AND (start_time < :end_time AND end_time > :start_time)";
            
                // Exclude the current schedule if this is an update
                if ($schedule_id) {
                    $sql .= " AND id != :schedule_id";
                }
            
                // Prepare the statement
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':section_id', $section_id);
                $stmt->bindParam(':day', $day); 
                $stmt->bindParam(':start_time', $start_time);
                $stmt->bindParam(':end_time', $end_time);
            
                // Bind the schedule_id if it's provided
                if ($schedule_id) {
                    $stmt->bindParam(':schedule_id', $schedule_id);
                }
            
                // Execute the query
                $stmt->execute();
                $overlaps = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                foreach ($overlaps as $overlap) {
                    // Check if the teacher is the same
                    if ($overlap['teacher'] == $teacher_id) {
                        return true; // Overlap exists for the same teacher
                    }
                }
            
                // No conflicting schedule found
                return false;
            }
                
            // Method to update a schedule
            public function updateSchedule($schedule_id, $section_id, $day, $start_time, $end_time, $subject, $room, $teacher_id) {
                // Allow exact start and end times
                if ($start_time === $end_time) {
                    $end_time = date('H:i', strtotime('+1 minute', strtotime($start_time))); // Adjust to 1 minute later
                }
        
                // Check for overlaps
                if ($this->overlapExists($section_id, $start_time, $end_time, $teacher_id, $schedule_id, $day)) {
                    return false; // Overlap exists, don't proceed
                }
        
                // SQL to update the schedule
                $sql = "UPDATE schedule SET 
                        day = :day, 
                        start_time = :start_time, 
                        end_time = :end_time, 
                        subject = :subject, 
                        room = :room, 
                        teacher = :teacher_id 
                        WHERE id = :schedule_id";
        
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':day', $day);
                $stmt->bindParam(':start_time', $start_time);
                $stmt->bindParam(':end_time', $end_time);
                $stmt->bindParam(':subject', $subject);
                $stmt->bindParam(':room', $room);
                $stmt->bindParam(':teacher_id', $teacher_id);
                $stmt->bindParam(':schedule_id', $schedule_id);
        
                return $stmt->execute(); // Return true if update is successful
            }

            public function editIrregularSchedule($schedule_id, $student_id, $section_id, $day, $start_time, $end_time, $subject, $room, $teacher_id) {
                // Allow exact start and end times
                if ($start_time === $end_time) {
                    $end_time = date('H:i', strtotime('+1 minute', strtotime($start_time))); // Adjust to 1 minute later
                }
            
                // Check for overlaps
                if ($this->overlapExists($section_id, $start_time, $end_time, $teacher_id, $schedule_id, $day)) {
                    return false; // Overlap exists, don't proceed
                }
            
                // SQL to update the irregular schedule
                $sql = "UPDATE schedules SET 
                        student_id = :student_id,
                        day = :day, 
                        start_time = :start_time, 
                        end_time = :end_time, 
                        subject = :subject, 
                        room = :room, 
                        teacher = :teacher_id 
                        WHERE id = :schedule_id";
            
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':student_id', $student_id);
                $stmt->bindParam(':day', $day);
                $stmt->bindParam(':start_time', $start_time);
                $stmt->bindParam(':end_time', $end_time);
                $stmt->bindParam(':subject', $subject);
                $stmt->bindParam(':room', $room);
                $stmt->bindParam(':teacher_id', $teacher_id);
                $stmt->bindParam(':schedule_id', $schedule_id);
            
                return $stmt->execute(); // Return true if update is successful
            }
            

            public function getIrregularSchedulesBySectionId($section_id) {
                $sql = "SELECT * FROM schedule WHERE section_id = :section_id AND student_id IS NOT NULL";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':section_id', $section_id);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            public function deleteSchedule($schedule_id) {
                try {
                    $query = "DELETE FROM schedule WHERE id = :schedule_id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':schedule_id', $schedule_id, PDO::PARAM_INT);
                    return $stmt->execute();
                } catch (PDOException $e) {
                    return false;
                }
            }
            
            public function getStudentSchedule($userId) {
                // SQL query to fetch schedule
                $query = "
                    SELECT DISTINCT s.day, s.start_time, s.end_time, s.subject 
                    FROM schedule s
                    LEFT JOIN user u ON u.section_id = s.section_id
                    WHERE (s.section_id = (SELECT section_id FROM user WHERE id = :user_id) AND s.student_id IS NULL)
                    OR s.student_id = :user_id
                ";
        
                // Prepare and execute the query
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                $stmt->execute();
        
                // Fetch and return results
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            

    }
    
?>
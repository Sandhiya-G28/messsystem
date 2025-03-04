<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $attendance_data = isset($_POST['attendance']) ? $_POST['attendance'] : []; // Attendance data from the form
    $date = $_POST['date']; // Date for the attendance

    // Fetch all students
    $fetch_students_sql = "SELECT id, reg_no FROM user_management.users";
    $result = $conn->query($fetch_students_sql);

    // Process each student
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $student_id = $row['id'];
            $reg_no = $row['reg_no'];

            // Check if the student was marked as present
            $status = isset($attendance_data[$student_id]) ? 'Present' : 'Absent';

            // Check if the attendance record for this student and date already exists
            $check_sql = "SELECT * FROM attendance_system.attendance WHERE reg_no = ? AND date = ?";
            $stmt = $conn->prepare($check_sql);
            $stmt->bind_param("ss", $reg_no, $date);
            $stmt->execute();
            $result_check = $stmt->get_result();

            if ($result_check->num_rows > 0) {
                // If record exists, update it
                $update_sql = "UPDATE attendance_system.attendance SET status = ? WHERE reg_no = ? AND date = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("sss", $status, $reg_no, $date);
                $update_stmt->execute();
                $update_stmt->close();
            } else {
                // If no record exists, insert a new one
                $insert_sql = "INSERT INTO attendance_system.attendance (reg_no, date, status) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("sss", $reg_no, $date, $status);
                $insert_stmt->execute();
                $insert_stmt->close();
            }

            $stmt->close();
        }
    }

    echo "success"; // Response for AJAX
}

$conn->close();
?>

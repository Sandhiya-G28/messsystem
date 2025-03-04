<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reg_no = $_POST['reg_no'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    // Get student ID and name based on reg_no
    $sql = "SELECT id, name FROM user_management.users WHERE reg_no=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $reg_no);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($student_id, $student_name);
    $stmt->fetch();
    $stmt->close();

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>View Attendance</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                padding: 20px;
            }
            .attendance-container {
                max-width: 80%;
                margin: 0 auto;
                background-color: white;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background-color: #28a745;
                color: white;
            }
        </style>
    </head>
    <body>

    <?php

    if ($student_id) {
        // Fetch attendance records for the selected month and year
        $sql = "SELECT date, status FROM attendance_system.attendance WHERE reg_no=? AND MONTH(date) = ? AND YEAR(date) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $reg_no, $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<div class='attendance-container'>";
        echo "<h2>Attendance Records for $student_name (Reg No: $reg_no) - $month/$year</h2>";
        if ($result->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['date']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No attendance records found for $month/$year.</p>";
        }
        echo "</div>";
        $stmt->close();
    } else {
        echo "<p>Student with Register Number $reg_no not found.</p>";
    }

    ?>

    </body>
    </html>

    <?php
}

$conn->close();

<?php
// calculate_bill.php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['total_grocery'])) {
    $total_grocery = $_POST['total_grocery'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    // Define the date range for the selected month
    $start_date = "$year-$month-01";
    $total_days_in_month = date("t", strtotime($start_date)); // Get total days in the month
    $end_date = "$year-$month-$total_days_in_month"; // Last day of the month

    // Fetch attendance for each student
    $sql = "SELECT attendance.reg_no, 
                   users.name, 
                   COUNT(CASE WHEN attendance.status = 'Absent' THEN 1 END) AS total_absent_days
            FROM attendance_system.attendance AS attendance
            JOIN user_management.users AS users ON attendance.reg_no = users.reg_no
            WHERE attendance.date BETWEEN '$start_date' AND '$end_date'
            GROUP BY attendance.reg_no";

    $result = $conn->query($sql);
    $total_present_days = 0;
    $students = [];

    while ($row = $result->fetch_assoc()) {
        $reg_no = $row['reg_no'];
        $student['reg_no'] = $row['reg_no'];
        $student['name'] = $row['name'];
        $student['total_absent_days'] = $row['total_absent_days'];
        $student['present_days'] = $total_days_in_month - $row['total_absent_days'];
        $student['reduction_days'] = 0;
        $student['final_present_days'] = $student['present_days'];
        $student['consecutive'] = "No"; // Default

        // If student was absent for the entire month, set bill to ₹0
        if ($student['total_absent_days'] == $total_days_in_month) {
            $student['final_present_days'] = 0;
        } else {
            // Fetch absences sorted by date to analyze periods
            $absent_sql = "SELECT date FROM attendance_system.attendance 
                           WHERE reg_no = '$reg_no' AND status = 'Absent' 
                           AND date BETWEEN '$start_date' AND '$end_date' 
                           ORDER BY date";
            $absent_result = $conn->query($absent_sql);

            $consecutive_absent_days = 0;
            $absent_periods = [];
            $previous_date = null;

            while ($absent_row = $absent_result->fetch_assoc()) {
                $current_date = $absent_row['date'];

                if (!isset($previous_date)) {
                    $previous_date = $current_date;
                    $consecutive_absent_days = 1;
                } else {
                    $days_between = (strtotime($current_date) - strtotime($previous_date)) / (60 * 60 * 24);

                    if ($days_between == 1) {
                        $consecutive_absent_days++;
                    } else {
                        $absent_periods[] = $consecutive_absent_days;
                        $consecutive_absent_days = 1;
                    }
                    $previous_date = $current_date;
                }
            }

            if ($consecutive_absent_days > 0) {
                $absent_periods[] = $consecutive_absent_days;
            }

            // Apply N-2 reduction if absence period is >= 7 days
            $total_absent_days = 0;
            foreach ($absent_periods as $period) {
                if ($period >= 7) {
                    $total_absent_days += $period;
                    $student['reduction_days'] += $period - 2;
                    $student['consecutive'] = "Yes";
                }
            }

            if ($total_absent_days > 0) {
                $student['final_present_days'] = $total_days_in_month - $total_absent_days + 2;
            }
        }

        $students[] = $student;
        $total_present_days += $student['final_present_days'];
    }

    foreach ($students as $student) {
        // Set bill amount to ₹0 for students absent the whole month
        $bill_amount = ($student['final_present_days'] == 0) ? 0 : ($student['final_present_days'] / $total_present_days) * $total_grocery;

        $insert_bill_sql = "INSERT INTO attendance_system.mess_bills (reg_no, name, present_days, absent_days, reduction_days, final_present_days, consecutive, month, year, bill_amount) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE 
                                present_days = VALUES(present_days),
                                absent_days = VALUES(absent_days),
                                reduction_days = VALUES(reduction_days),
                                final_present_days = VALUES(final_present_days),
                                consecutive = VALUES(consecutive),
                                bill_amount = VALUES(bill_amount)";

        $stmt = $conn->prepare($insert_bill_sql);
        $stmt->bind_param("ssiiissiid", 
            $student['reg_no'], 
            $student['name'], 
            $student['present_days'], 
            $student['total_absent_days'], 
            $student['reduction_days'], 
            $student['final_present_days'], 
            $student['consecutive'], 
            $month, 
            $year, 
            $bill_amount
        );
        $stmt->execute();
    }

    // Display the results
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Mess Bill Calculation</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f9f9f9;
                margin: 0;
                padding: 20px;
            }
            h2 {
                text-align: center;
                color: #a52a2a;
            }
            table {
                width: 70%;
                margin: 20px auto;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
                text-align: center;
                border: 1px solid #ddd;
            }
            th {
                background-color: #a52a2a;
                color: white;
            }
        </style>
    </head>
    <body>";

    echo "<h2>Mess Bill Calculation for " . date("F", mktime(0, 0, 0, $month, 1)) . " $year</h2>";
    echo "<h3>Total Grocery Amount: ₹ " . htmlspecialchars($total_grocery) . "</h3>";
    echo "<h3>Bill Division:</h3>";
    echo "<table>
            <tr>
                <th>Register Number</th>
                <th>Name</th>
                <th>Total Absent Days</th>
                <th>Present Days</th>
                <th>Reduction Days</th>
                <th>Final Present Days</th>
                <th>Consecutive Leave</th>
                <th>Bill Amount</th>
            </tr>";

    foreach ($students as $student) {
        echo "<tr>
                <td>" . htmlspecialchars($student['reg_no']) . "</td>
                <td>" . htmlspecialchars($student['name']) . "</td>
                <td>" . htmlspecialchars($student['total_absent_days']) . "</td>
                <td>" . htmlspecialchars($student['present_days']) . "</td>
                <td>" . htmlspecialchars($student['reduction_days']) . "</td>
                <td>" . htmlspecialchars($student['final_present_days']) . "</td>
                <td>" . htmlspecialchars($student['consecutive']) . "</td>
                <td>₹ " . number_format(($student['final_present_days'] == 0 ? 0 : ($student['final_present_days'] / $total_present_days) * $total_grocery), 2) . "</td>
              </tr>";
    }

    echo "</table></body></html>";

    $conn->close();
} else {
    header("Location: enter_grocery.php");
    exit();
}
?>
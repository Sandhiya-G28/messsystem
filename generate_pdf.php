<?php
require('fpdf.php');
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['total_grocery'])) {
    $total_grocery = $_POST['total_grocery'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    // Fetch attendance data again
    $start_date = "$year-$month-01";
    $total_days_in_month = date("t", strtotime($start_date));

    $sql = "SELECT attendance.reg_no, users.name, 
                   COUNT(CASE WHEN attendance.status = 'Absent' THEN 1 END) AS total_absent_days
            FROM attendance_system.attendance AS attendance
            JOIN user_management.users AS users ON attendance.reg_no = users.reg_no
            WHERE attendance.date BETWEEN '$start_date' AND '$year-$month-$total_days_in_month'
            GROUP BY attendance.reg_no";

    $result = $conn->query($sql);
    $students = [];
    $total_present_days = 0;

    while ($row = $result->fetch_assoc()) {
        $present_days = $total_days_in_month - $row['total_absent_days'];
        $final_present_days = ($present_days > 0) ? $present_days : 0;

        $students[] = [
            'reg_no' => $row['reg_no'],
            'name' => $row['name'],
            'total_absent_days' => $row['total_absent_days'],
            'present_days' => $final_present_days
        ];

        $total_present_days += $final_present_days;
    }

    $per_day_bill = ($total_present_days > 0) ? ($total_grocery / $total_present_days) : 0;

    // PDF Generation
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 10, "Mess Bill - " . date("F Y", strtotime($start_date)), 1, 1, 'C');
    $pdf->Cell(190, 10, "Per Day Bill: ₹ " . number_format($per_day_bill, 2), 1, 1, 'C');

    foreach ($students as $s) {
        $bill_amount = $s['present_days'] * $per_day_bill;
        $pdf->Cell(190, 10, "{$s['reg_no']} | {$s['name']} | ₹ " . number_format($bill_amount, 2), 1, 1);
    }

    $pdf->Output();
}
?>

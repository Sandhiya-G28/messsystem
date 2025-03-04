<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer autoload

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'thejaraga09@gmail.com';
        $mail->Password = 'kzah osqm oyaf dswx';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('thejaraga09@gmail.com', 'TPGIT Hostels');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$data = json_decode(file_get_contents("php://input"), true);
$month = intval($data['month']);
$year = intval($data['year']);

$conn = new mysqli('localhost', 'root', '', 'user_management');

if ($conn->connect_error) {
    echo json_encode(['success' => false]);
    exit();
}

$query = "SELECT u.mail, b.name, b.bill_amount, b.month, b.year
          FROM boys_users u
          INNER JOIN attendance_system.boys_mess_bills b ON u.reg_no = b.reg_no
          WHERE b.month = ? AND b.year = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $month, $year);
$stmt->execute();
$result = $stmt->get_result();
$success = true;

while ($row = $result->fetch_assoc()) {
    $email = $row['mail'];
    $subject = "Mess Bill for {$row['month']}/{$row['year']}";
    $body = "<p>Dear {$row['name']},</p><p>Your mess bill for the month of {$row['month']}/{$row['year']} is <strong>Rs. {$row['bill_amount']}</strong>.</p><p>Thank you.</p>";

    if (!sendMail($email, $subject, $body)) {
        $success = false;
    }
}

$stmt->close();
$conn->close();

echo json_encode(['success' => $success]);

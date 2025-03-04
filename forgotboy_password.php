<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = $_POST['mail'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "user_management");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM boys_users WHERE mail = ?");
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate OTP
        $otp = rand(100000, 999999);

        // Save OTP in database
        $stmt = $conn->prepare("INSERT INTO passwordboy_resets (mail, otp, created_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE otp = ?, created_at = NOW()");
        $stmt->bind_param("sis", $mail, $otp, $otp);
        $stmt->execute();

        // Send OTP using PHPMailer
        $mailSender = new PHPMailer(true);
        try {
            // Server settings
            $mailSender->isSMTP();
            $mailSender->Host = 'smtp.gmail.com'; // Gmail SMTP server
            $mailSender->SMTPAuth = true;
            $mailSender->Username = 'udhayarekham1712@gmail.com'; // Your Gmail address
            $mailSender->Password = 'jiby chbj llxt kqua'; // Gmail App Password
            $mailSender->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mailSender->Port = 587;

            // Recipients
            $mailSender->setFrom('your_email@gmail.com', 'TPGIT Mess');
            $mailSender->addAddress($mail);

            // Content
            $mailSender->isHTML(true);
            $mailSender->Subject = 'Password Reset OTP';
            $mailSender->Body = "Your OTP is: <strong>$otp</strong>";

            $mailSender->send();
            echo "OTP sent to your email.";

            // Redirect to verify OTP page after sending OTP
            header("Location: resetboy_password.php");
            exit(); // Make sure no further code is executed after redirection

        } catch (Exception $e) {
            echo "Failed to send OTP: {$mailSender->ErrorInfo}";
        }
    } else {
        echo "Email not registered.";
    }

    $stmt->close();
    $conn->close();
}
?>
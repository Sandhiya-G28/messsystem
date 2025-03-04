<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate that 'mail', 'otp', and 'new_password' exist in the POST data
    if (isset($_POST['mail']) && isset($_POST['otp']) && isset($_POST['new_password'])) {
        $mail = $_POST['mail'];
        $otp = $_POST['otp'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hash the new password

        // Database connection
        $conn = new mysqli("localhost", "root", "", "user_management");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the OTP and email match in password_resets table
        $stmt = $conn->prepare("SELECT otp FROM password_resets WHERE mail = ? AND otp = ?");
        $stmt->bind_param("si", $mail, $otp);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // OTP is correct, now update the user's password
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE mail = ?");
            $update_stmt->bind_param("ss", $new_password, $mail);

            if ($update_stmt->execute()) {
                // Password updated, delete OTP from the password_resets table
                $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE mail = ?");
                $delete_stmt->bind_param("s", $mail);
                $delete_stmt->execute();

                // Redirect to the login page after successful password update
                header("Location: lgirl.html"); // Change to your login page URL
                exit; // Ensure the rest of the code doesn't execute
            } else {
                echo "Error updating password: " . $update_stmt->error;
            }

            $update_stmt->close();
            $delete_stmt->close();
        } else {
            echo "Invalid OTP.";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="reset_password.css">
    <title>Reset Password</title>
    

</head>
<body>
    <div class="forgot-password-container">
    <h2>Reset Password</h2>
    
    <form action="reset_password.php" method="POST">
        <label for="mail">Enter your email address:</label>
        <input type="email" id="mail" name="mail" required><br><br>
        
        <label for="otp">Enter OTP sent to your email:</label>
        <input type="text" id="otp" name="otp" required><br><br>
        
        <label for="new_password">Enter new password:</label>
        <input type="password" id="new_password" name="new_password" required><br><br>
        
        <button type="submit">Reset Password</button>
    </form>
    </div>
</body>
</html>
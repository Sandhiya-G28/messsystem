<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = $_POST['mail'];
    $password = $_POST['password'];

    // Database connection
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "user_management";

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT password FROM users WHERE mail = ?");
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    // Check if a matching record was found and verify the password
    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        // If the login is successful, redirect to home.html
        header("Location: fgirl.html");
        exit(); // Always exit after a header redirect to prevent further script execution
    } else {
        echo "Invalid email or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

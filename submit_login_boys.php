<?php
// Connection settings
$servername = "localhost";
$username = "root";  // Change this if needed
$password = "";  // Add your MySQL Workbench password if set
$dbname = "staff_registration";  // Database where staff information is stored

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to get the stored password for the provided email
    $sql = "SELECT * FROM staff WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the staff data from the database
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        // Directly compare the provided password with the stored password
        if ($password === $stored_password) {
            // Successful login
            header("Location: staff_home_boys.html");
            exit();
        } else {
            // Password mismatch
            echo "Incorrect password.";
        }
    } else {
        // Email not found in the database
        echo "No user found with that email.";
    }
}

// Close the connection
$conn->close();
?>

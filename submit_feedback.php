<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'user_management'); // If using default XAMPP settings


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the form data
$student_name = $_POST['student_name'];
$register_number = $_POST['register_number']; // Updated variable name
$rating = $_POST['rating'];
$comments = $_POST['comments'];

// Prepare and bind the statement
$stmt = $conn->prepare("INSERT INTO feedback (student_name, register_number, rating, comments) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssis", $student_name, $register_number, $rating, $comments); // s = string, i = integer

// Execute the statement
if ($stmt->execute()) {
    echo "Feedback submitted successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
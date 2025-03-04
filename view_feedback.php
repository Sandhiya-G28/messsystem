<?php
// Database connection

$conn = new mysqli('localhost', 'root', '', 'user_management');


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Assuming you have the gender stored in a session or passed in from the form
$gender = $_SESSION['gender']; // or however you manage gender




// Fetch feedback from the database
$sql = "SELECT student_name, register_number, rating, comments,gender FROM feedback";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Details</title>
    <link rel="stylesheet" href="feedback_style.css"> <!-- Optional: Add a stylesheet for styling -->
    
</head>
<body>

<div class="feedback-container">
    <h1>FEEDBACK FROM STUDENTS</h1>

    <?php
    // Check if there are results and display them
    if ($result->num_rows > 0) {
        // Output data for each row
        while ($row = $result->fetch_assoc()) {
            echo "<div class='feedback-item'>";
            echo "<h4>Name: " . htmlspecialchars($row['student_name']) . " (Reg No: " . htmlspecialchars($row['register_number']) . ")</h4>";
            echo "<p>Rating: " . htmlspecialchars($row['rating']) . "/5</p>";
            echo "<p>Comments: " . htmlspecialchars($row['comments']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No feedback available.</p>";
    }

    // Close the connection
    $conn->close();
    ?>
</div>

</body>
</html>
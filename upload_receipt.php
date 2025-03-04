<?php
$servername = "localhost";
$username = "root";
$password = ""; // Your database password
$dbname = "user_management"; // Change to your database

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form data
$reg_no = $_POST['reg_no'];
$transaction_ref = $_POST['transaction_ref'];
$payment_date = $_POST['payment_date'];

// Handle file upload
$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["receipt"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Allow only certain file formats
if($fileType != "pdf" && $fileType != "jpg" && $fileType != "jpeg" && $fileType != "png") {
    echo "error";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "error";
} else {
    if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $target_file)) {
        // Insert data into the database
        $sql = "INSERT INTO payments (reg_no, transaction_ref, payment_date, receipt_path) VALUES ('$reg_no', '$transaction_ref', '$payment_date', '$target_file')";

        if ($conn->query($sql) === TRUE) {
            echo "success"; // Respond with "success" to trigger the success message in the JS
        } else {
            echo "error"; // Respond with "error" if there is an issue with the query
        }
    } else {
        echo "error"; // Respond with "error" if file upload fails
    }
}

$conn->close();
?>

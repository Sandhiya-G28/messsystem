<?php
$host = "localhost";  // Database host
$username = "root";  // Database username
$password = "";  // Database password
$dbname = "user_management";  // Database name

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['reg_no'])) {
    $reg_no = $_POST['reg_no'];
    $name = $_POST['name'];
    $ph_no = $_POST['ph_no'];
    $mail = $_POST['mail'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $acc_type = $_POST['acc_type'];

    // Insert query
    $query = "INSERT INTO boys_users (reg_no, name, ph_no, mail, password, dob, gender, acc_type) 
              VALUES ('$reg_no', '$name', '$ph_no', '$mail', '$password', '$dob', '$gender', '$acc_type')";

    if ($conn->query($query) === TRUE) {
        echo "success";  // Return success message
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
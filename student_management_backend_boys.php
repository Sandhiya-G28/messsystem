<?php
$servername = "localhost";
$username = "root";
$password = ""; // Set your password
$dbname = "user_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert new student
if (isset($_POST['insert'])) {
    $name = $_POST['name'];
    $reg_no = $_POST['reg_no'];
    $mail = $_POST['mail'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $ph_no = $_POST['ph_no'];

    $sql = "INSERT INTO boys_users (name, reg_no, mail, dob, gender, ph_no) VALUES ('$name', '$reg_no', '$mail', '$dob', '$gender', '$ph_no')";
    $conn->query($sql);
}

// Fetch students
$sql = "SELECT * FROM boys_users";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['name'] . "</td>
            <td>" . $row['reg_no'] . "</td>
            <td>" . $row['mail'] . "</td>
            <td>" . $row['dob'] . "</td>
            <td>" . $row['gender'] . "</td>
            <td>" . $row['ph_no'] . "</td>
            <td>
                <a href='edit_student_boys.php?id=" . $row['id'] . "'>Edit</a>
            </td>
        </tr>";
}

$conn->close();
?>

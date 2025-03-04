<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student details for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM boys_users WHERE id=$id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}

// Update student details
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $reg_no = $_POST['reg_no'];
    $mail = $_POST['mail'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $ph_no = $_POST['ph_no'];

    $sql = "UPDATE users SET name='$name', reg_no='$reg_no', mail='$mail', dob='$dob', gender='$gender', ph_no='$ph_no' WHERE id=$id";
    $conn->query($sql);

    header("Location: student_management_boys.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="edit_student.css">
</head>
<body>
    <div class="header">
        <h1>Edit Student</h1>
    </div>

    <div class="container">
        <form action="edit_student_boys.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <input type="text" name="name" value="<?php echo $row['name']; ?>" placeholder="Student Name" required>
            <input type="text" name="reg_no" value="<?php echo $row['reg_no']; ?>" placeholder="Registration Number" required>
            <input type="email" name="mail" value="<?php echo $row['mail']; ?>" placeholder="Email" required>
            <input type="date" name="dob" value="<?php echo $row['dob']; ?>" required>
            <input type="text" name="gender" value="<?php echo $row['gender']; ?>" placeholder="Gender" required>
            <input type="text" name="ph_no" value="<?php echo $row['ph_no']; ?>" placeholder="Phone Number" required>
            <input type="submit" name="update" value="Update Student">
        </form>
    </div>
</body>
</html>

<?php
// Database connection
$connection = new mysqli("localhost", "root", "", "user_management");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Insert student record
if (isset($_POST['insert'])) {
    $name = $_POST['name'];
    $reg_no = $_POST['reg_no'];
    $mail = $_POST['mail'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $ph_no = $_POST['ph_no'];

    $query = "INSERT INTO users (name, reg_no, mail, dob, gender, ph_no) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ssssss", $name, $reg_no, $mail, $dob, $gender, $ph_no);

    if ($stmt->execute()) {
        echo "<script>alert('Student record inserted successfully');</script>";
    } else {
        echo "<script>alert('Error inserting record');</script>";
    }

    $stmt->close();
}

// Update student record
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $reg_no = $_POST['reg_no'];
    $mail = $_POST['mail'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $ph_no = $_POST['ph_no'];

    $query = "UPDATE users SET name = ?, reg_no = ?, mail = ?, dob = ?, gender = ?, ph_no = ? WHERE id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ssssssi", $name, $reg_no, $mail, $dob, $gender, $ph_no, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Student record updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating record');</script>";
    }

    $stmt->close();
}

// Delete student record
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    // Move the deleted record to the deleted_records table
    $query_move = "INSERT INTO deleted_records.deleted_users (id, name, reg_no, mail, dob, gender, ph_no) SELECT id, name, reg_no, mail, dob, gender, ph_no FROM users WHERE id = ?";
    $stmt_move = $connection->prepare($query_move);
    $stmt_move->bind_param("i", $id);
    if ($stmt_move->execute()) {
        // Proceed with deleting the record from the users table
        $query_delete = "DELETE FROM users WHERE id = ?";
        $stmt_delete = $connection->prepare($query_delete);
        $stmt_delete->bind_param("i", $id);

        if ($stmt_delete->execute()) {
            echo "<script>alert('Student record deleted successfully');</script>";
        } else {
            echo "<script>alert('Error deleting record');</script>";
        }

        $stmt_delete->close();
    } else {
        echo "<script>alert('Error moving record to deleted_users');</script>";
    }

    $stmt_move->close();
}

// Fetch all student records or search results
$search_query = "";
if (isset($_GET['search'])) {
    $search_value = $_GET['search'];
    $search_query = "WHERE name LIKE '%$search_value%' OR reg_no LIKE '%$search_value%'";
}

$query = "SELECT * FROM users $search_query";
$result = $connection->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="manage.css">
    <!-- Font Awesome CDN for home icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Additional CSS for home icon in top-left corner */
        .home-icon {
            position: fixed;  /* Position it at the top-left corner */
            top: 25px;        /* Distance from the top */
            left: 20px;       /* Distance from the left */
            font-size: 25px;  /* Adjust font size */
            color: white;     /* Set color to white */
            text-decoration: none;
            background-color: transparent;
            border: none;
            z-index: 9999;    /* Make sure it's on top of other elements */
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Home Icon (white color and positioned at top left) -->
        <a href="staff_home.html" class="home-icon">
        <i class="fa-sharp fa-solid fa-house" style="color: #f4f5f5;"></i>
        </a>
        <h1>Manage Students</h1>
    </div>
    <div class="container">
        <!-- Search Form -->
        <form action="student_management.php" method="GET">
            <input type="text" name="search" placeholder="Search by name or reg number" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <input type="submit" value="Search">
        </form>

        <!-- Form for inserting or updating a student -->
        <form action="student_management.php" method="POST">
            <input type="hidden" name="id" id="studentId">
            <input type="text" name="name" id="studentName" placeholder="Student Name" required>
            <input type="text" name="reg_no" id="studentRegNo" placeholder="Registration Number" required>
            <input type="email" name="mail" id="studentMail" placeholder="Email" required>
            <input type="date" name="dob" id="studentDob" placeholder="DOB" required>
            <input type="text" name="gender" id="studentGender" placeholder="Gender" required>
            <input type="text" name="ph_no" id="studentPhone" placeholder="Phone Number" required>
            <input type="submit" name="insert" id="insertButton" value="Insert Student">
            <input type="submit" name="update" id="updateButton" value="Update Student" style="display: none;">
        </form>

        <!-- Display student list -->
        <h2>Student List</h2>
        <table border="1" id="studentTable">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Reg No</th>
                <th>Email</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['reg_no']}</td>
                        <td>{$row['mail']}</td>
                        <td>{$row['dob']}</td>
                        <td>{$row['gender']}</td>
                        <td>{$row['ph_no']}</td>
                        <td>
                            <button class='editBtn' data-id='{$row['id']}' data-name='{$row['name']}' data-reg_no='{$row['reg_no']}' data-mail='{$row['mail']}' data-dob='{$row['dob']}' data-gender='{$row['gender']}' data-ph_no='{$row['ph_no']}'>Edit</button>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <input type='submit' name='delete' value='Delete' class='deleteBtn'>
                            </form>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No students found</td></tr>";
            }
            ?>
        </table>

        <h2><a href="deleted_records.php">View Deleted Records</a></h2>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        // Handle edit button click
        $(".editBtn").click(function () {
            $("#studentId").val($(this).data("id"));
            $("#studentName").val($(this).data("name"));
            $("#studentRegNo").val($(this).data("reg_no"));
            $("#studentMail").val($(this).data("mail"));
            $("#studentDob").val($(this).data("dob"));
            $("#studentGender").val($(this).data("gender"));
            $("#studentPhone").val($(this).data("ph_no"));

            $("#insertButton").hide();
            $("#updateButton").show();
        });
    </script>
</body>
</html>

<?php
// Database connection to the deleted_records database
$connection = new mysqli("localhost", "root", "", "deleted_records");

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Initialize the search term variable
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';

// Modify the query to include a WHERE clause if a search term is provided
$query = "SELECT * FROM deleted_users";
if ($searchTerm) {
    $query .= " WHERE name LIKE '%$searchTerm%' OR reg_no LIKE '%$searchTerm%' OR mail LIKE '%$searchTerm%'";
}

$result = $connection->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Student Records</title>
    <link rel="stylesheet" href="manage.css">
</head>
<body>
    <div class="header">
        <h1>Deleted Student Records</h1>
    </div>
    <div class="container">
        <h2>List of Deleted Students</h2>

        <!-- Search Form -->
        <form method="POST" action="">
            <input type="text" name="search" placeholder="Search by Name, Reg No, or Email" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Display results if any -->
        <?php if ($result->num_rows > 0): ?>
            <table border="1" id="deletedStudentTable">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Reg No</th>
                    <th>Email</th>
                    <th>DOB</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Deleted On</th>
                </tr>
                <?php
                while ($row = $result->fetch_assoc()) {
                    // Ensure 'deleted_on' column exists in the row
                    $deletedOn = isset($row['deleted_on']) ? $row['deleted_on'] : 'N/A';

                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['reg_no']}</td>
                        <td>{$row['mail']}</td>
                        <td>{$row['dob']}</td>
                        <td>{$row['gender']}</td>
                        <td>{$row['ph_no']}</td>
                        <td>{$deletedOn}</td>
                    </tr>";
                }
                ?>
            </table>
        <?php else: ?>
            <p>No records found for your search.</p>
        <?php endif; ?>

        <br>
        <a href="student_management.php">Back to Student Management</a>
    </div>
</body>
</html>

<?php
// Close the database connection
$connection->close();
?>

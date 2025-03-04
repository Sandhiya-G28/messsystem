<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <style>
        table {
            width: 60%;
            margin: auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #a52a2a;
            color: white;
        }
        input[type="checkbox"] {
            transform: scale(1.5);
        }
        h1 {
            text-align: center;
            color: #a52a2a;
        }
        .success-message {
            text-align: center;
            padding: 10px;
            color: white;
            background-color: green;
            display: none; /* Hidden by default */
            margin-top: 10px;
            width: 50%;
            margin-left: auto;
            margin-right: auto;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>Mark Attendance</h1>

    <form id="attendance-form">
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required>
        
        <table>
            <tr>
                <th>Register Number</th>
                <th>Name</th>
                <th>Present/Absent</th>
            </tr>
            <?php
            include 'db.php';

            // Fetch all students with their name and reg_no
            $sql = "SELECT id, name, reg_no FROM user_management.users";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Loop through each student and create a table row
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['reg_no']) . "</td>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td>
                                <input type='checkbox' name='attendance[" . htmlspecialchars($row['id']) . "]' value='Present' checked='checked'>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No students found.</td></tr>";
            }

            $conn->close();
            ?>
        </table>

        <div class="button-container">
            <button type="submit">Submit Attendance</button>
            <div id="success-message" class="success-message">
                Attendance recorded successfully!
            </div>
        </div>
    </form>

    <script>
        // Function to show success message for 5 seconds
        function showSuccessMessage() {
            const successMessage = document.getElementById('success-message');
            successMessage.style.display = 'block';
            
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000); // Hide after 5 seconds
        }

        // Handle form submission via AJAX
        document.getElementById('attendance-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting the traditional way

            const formData = new FormData(this);

            // Send the form data via AJAX
            fetch('submit_attendance.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    // Show success message
                    showSuccessMessage();
                } else {
                    console.error('Error:', data);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>

</body>
</html>

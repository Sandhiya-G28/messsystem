<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Payment Receipts</title>
    <style>
        body {
            background-image: url('image.png'); /* Add your background image path */
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        h2 {
            color: white;
            text-align: center;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            color: white;
        }
        select {
            padding: 10px;
            margin-right: 10px;
        }
        table {
            width: 80%; /* Reduced table width */
            border-collapse: collapse;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto; /* Center the table */
        }
        th, td {
            padding: 8px; /* Reduced padding */
            text-align: left;
            border: 1px solid maroon;
        }
        th {
            background-color: maroon;
            color: white;
        }
        tr:nth-child(even) {
            background-color: white;
        }
        tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
        .btn {
            background-color: maroon;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        /* Toggle switch styling */
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 25px;
        }
        .toggle-switch input {
            display: none;
        }
        .toggle-switch-label {
            display: block;
            width: 100%;
            height: 100%;
            background-color: gray;
            border-radius: 25px;
            position: relative;
            transition: background-color 0.3s;
        }
        .toggle-switch-label:before {
            content: '';
            position: absolute;
            width: 23px;
            height: 23px;
            border-radius: 50%;
            background-color: white;
            top: 1px;
            left: 1px;
            transition: transform 0.3s;
        }
        input:checked + .toggle-switch-label {
            background-color: green;
        }
        input:checked + .toggle-switch-label:before {
            transform: translateX(25px);
        }
    </style>
</head>
<body>

<h2>Verify Payment Receipts</h2>

<form method="POST" action="">
    <label for="month">Select Month:</label>
    <select name="month" id="month">
        <option value="">Select Month</option>
        <?php
        // Generate month options
        for ($i = 1; $i <= 12; $i++) {
            $selected = (isset($_POST['month']) && $_POST['month'] == $i) ? 'selected' : '';
            echo "<option value='$i' $selected>$i</option>"; // Display month numbers (1-12)
        }
        ?>
    </select>

    <label for="year">Select Year:</label>
    <select name="year" id="year">
        <option value="">Select Year</option>
        <?php
        // Generate year options (e.g., 2020 to current year)
        $current_year = date("Y");
        for ($year = $current_year; $year >= 2020; $year--) {
            $selected = (isset($_POST['year']) && $_POST['year'] == $year) ? 'selected' : '';
            echo "<option value='$year' $selected>$year</option>";
        }
        ?>
    </select>

    <input type="submit" name="show_students" value="Show Students" class="btn">
</form>

<table>
    <thead>
        <tr>
            <th>Reg No</th>
            <th>Name</th>
            <th>Receipt</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'user_management');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle toggle status change
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['current_status'])) {
        $reg_no = $_POST['reg_no'];
        $current_status = $_POST['current_status'];
        $selected_month = $_POST['selected_month']; // Get the selected month
        $selected_year = $_POST['selected_year']; // Get the selected year

        // Update status in the payments table for the selected month and year
        $update_sql = "UPDATE boys_payments SET status='$current_status' WHERE reg_no='$reg_no' AND MONTH(payment_date) = '$selected_month' AND YEAR(payment_date) = '$selected_year'";
        if ($conn->query($update_sql) === TRUE) {
            echo "Status updated successfully.";
        } else {
            echo "Error updating status: " . $conn->error;
        }
    }

    if (isset($_POST['show_students']) && !empty($_POST['month']) && !empty($_POST['year'])) {
        $selected_month = $_POST['month'];
        $selected_year = $_POST['year'];

        // Fetch all students from the 'users' table
        $sql = "SELECT reg_no, name FROM boys_users";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Fetch receipt details from 'payments' table for the selected month and year
                $reg_no = $row['reg_no'];
                $payment_sql = "SELECT receipt_path, status FROM boys_payments WHERE reg_no='$reg_no' AND MONTH(payment_date) = '$selected_month' AND YEAR(payment_date) = '$selected_year'";
                $payment_result = $conn->query($payment_sql);
                $receipt_path = "No Receipt"; // Default if no receipt is found
                $status = "Unpaid";  // Default status

                if ($payment_result->num_rows > 0) {
                    $payment_row = $payment_result->fetch_assoc();
                    $receipt_path = $payment_row['receipt_path'];
                    $status = $payment_row['status'];
                }

                // Display data in table rows
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['reg_no']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                
                // Display the receipt as a link if it exists
                if ($receipt_path !== "No Receipt") {
                    echo "<td><a href='" . htmlspecialchars($receipt_path) . "' target='_blank'>View Receipt</a></td>";
                } else {
                    echo "<td>" . htmlspecialchars($receipt_path) . "</td>"; // Display "No Receipt"
                }
                
                // Toggle button for changing status
                echo "<td>
                        <form method='POST' action='' class='status-form'>
                            <input type='hidden' name='reg_no' value='" . htmlspecialchars($reg_no) . "'>
                            <input type='hidden' name='selected_month' value='" . htmlspecialchars($selected_month) . "'>
                            <input type='hidden' name='selected_year' value='" . htmlspecialchars($selected_year) . "'>
                            <div class='toggle-switch'>
                                <input type='checkbox' id='toggle-". htmlspecialchars($reg_no) ."' name='change_status' ". ($status == 'Paid' ? 'checked' : '') ." data-status='". ($status == 'Paid' ? 'Paid' : 'Unpaid') ."'>
                                <label class='toggle-switch-label' for='toggle-" . htmlspecialchars($reg_no) . "'></label>
                            </div>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No students found</td></tr>";
        }
    } 

    $conn->close();
    ?>
    </tbody>
</table>

<!-- JavaScript for AJAX toggle -->
<script>
    document.querySelectorAll('.toggle-switch input').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const form = this.closest('form');
            const status = this.dataset.status === 'Paid' ? 'Unpaid' : 'Paid';
            
            // Create a FormData object to submit the form via AJAX
            const formData = new FormData(form);
            formData.append('current_status', status);
            
            // Perform AJAX request
            fetch('', {
                method: 'POST',
                body: formData
            }).then(response => response.text())
              .then(data => {
                  // Update status dataset and toggle switch color
                  this.dataset.status = status;
              })
              .catch(error => {
                  console.error('Error:', error);
              });
        });
    });
</script>

</body>
</html>

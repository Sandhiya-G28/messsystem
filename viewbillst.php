<?php
include 'db.php'; // Include your database connection file

// Initialize variables to retain selected values
$selected_month = '';
$selected_year = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_month = $_POST['month'];
    $selected_year = $_POST['year'];

    // Fetch data from mess_bills for the selected month and year
    $sql = "SELECT reg_no, name, present_days, absent_days, reduction_days, final_present_days, bill_amount 
            FROM attendance_system.mess_bills 
            WHERE month = ? AND year = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $selected_month, $selected_year);
    $stmt->execute();
    $result = $stmt->get_result();

    $bills = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bills[] = $row;
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Mess Bills</title>
    <link rel="stylesheet" href="viewst.css">
</head>
<body>
    <div class="navbar">
        <div class="navbar-left">
            <h1>TPGIT Mess</h1>
        </div>
        <!-- Removed logout button from here -->
    </div>

    <div class="container">
        <h2>View Mess Bills</h2>

        <!-- Form to select month and year -->
        <form method="POST" action="">
            <label for="month">Select Month:</label>
            <select id="month" name="month" required>
                <option value="1" <?php if ($selected_month == 1) echo 'selected'; ?>>January</option>
                <option value="2" <?php if ($selected_month == 2) echo 'selected'; ?>>February</option>
                <option value="3" <?php if ($selected_month == 3) echo 'selected'; ?>>March</option>
                <option value="4" <?php if ($selected_month == 4) echo 'selected'; ?>>April</option>
                <option value="5" <?php if ($selected_month == 5) echo 'selected'; ?>>May</option>
                <option value="6" <?php if ($selected_month == 6) echo 'selected'; ?>>June</option>
                <option value="7" <?php if ($selected_month == 7) echo 'selected'; ?>>July</option>
                <option value="8" <?php if ($selected_month == 8) echo 'selected'; ?>>August</option>
                <option value="9" <?php if ($selected_month == 9) echo 'selected'; ?>>September</option>
                <option value="10" <?php if ($selected_month == 10) echo 'selected'; ?>>October</option>
                <option value="11" <?php if ($selected_month == 11) echo 'selected'; ?>>November</option>
                <option value="12" <?php if ($selected_month == 12) echo 'selected'; ?>>December</option>
            </select>

            <label for="year">Select Year:</label>
            <select id="year" name="year" required>
                <?php
                // Generate years dynamically with the selected year
                $current_year = date("Y");
                for ($i = $current_year; $i >= $current_year - 5; $i--) {
                    echo "<option value='$i'" . ($selected_year == $i ? ' selected' : '') . ">$i</option>";
                }
                ?>
            </select>

            <button class="menu-btn" type="submit">View Bills</button>
        </form>

        <!-- Display the bills table if data is available -->
        <?php if (isset($bills) && count($bills) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Register Number</th>
                        <th>Name</th>
                        <th>Present Days</th>
                        <th>Absent Days</th>
                        <th>Reduction Days</th>
                        <th>Final Present Days</th>
                        <th>Bill Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bills as $bill): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($bill['reg_no']); ?></td>
                            <td><?php echo htmlspecialchars($bill['name']); ?></td>
                            <td><?php echo htmlspecialchars($bill['present_days']); ?></td>
                            <td><?php echo htmlspecialchars($bill['absent_days']); ?></td>
                            <td><?php echo htmlspecialchars($bill['reduction_days']); ?></td>
                            <td><?php echo htmlspecialchars($bill['final_present_days']); ?></td>
                            <td>₹ <?php echo number_format($bill['bill_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p>No bills found for the selected month and year.</p>
        <?php endif; ?>
    </div>
</body>
</html>

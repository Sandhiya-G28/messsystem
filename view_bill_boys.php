<?php
include 'db.php';

session_start();  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Mess Bill Receipt</title>
    <link rel="stylesheet" href="styles.css">
    <style>
       body {
    font-family: Arial, sans-serif;
    background-image: url('image.png');
    background-repeat: no-repeat;
    background-position: center;
    background-attachment: fixed;
    background-size: 100% 100%;
    margin: 0;
    padding: 20px;
    color: white; /* Change text color for readability */
}

        h2 {
            text-align: center;
            color: white;
            margin-top:10px;
        }
        
        .form-container input {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .form-container button {
            padding: 10px 20px;
            margin: 65px;
            background-color: #8b1a1a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #8b1a1a;
        }
        .receipt-box {
    display: block; /* Ensure it is visible */
    width: 60%;
    background-color: rgba(255, 255, 255, 0.8);
    margin: 50px auto; /* Adds space above and below */
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
    color: black;
    min-height: 300px; /* Adjust height */
    overflow: auto;
}


.form-container {
    text-align: center;
    margin: 20px auto;
    max-width: 90%;
    margin-bottom:100px;
    justify-content: center; /* Centers the form horizontally */
    align-items: center; /* Centers vertically (if needed) */
    flex-direction: column; 
}
        
        .receipt-box h3 {
            text-align: center;
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .receipt-box .details {
            font-size: 1.1em;
            line-height: 1.6em;
        }
        .receipt-box .total {
            font-size: 1.4em;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
        .no-data {
            text-align: center;
            color: white;
            font-size: 1.2em;
            margin-top: 20px;
        }
        .status {
            font-size: 1.2em;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>View Your Mess Bill</h2>

<div class="form-container">
    <form method="POST" action="">
        <input type="text" name="reg_no" placeholder="Enter Registration Number" required>
        <input type="number" name="month" placeholder="Month (1-12)" min="1" max="12" required>
        <input type="number" name="year" placeholder="Year (e.g. 2024)" required>
        <button type="submit">View Bill</button>
    </form>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reg_no = $_POST['reg_no'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    // Fetch the bill for the specific month and year
    $sql = "SELECT reg_no, name, month, year, present_days, absent_days, reduction_days, final_present_days, bill_amount 
            FROM boys_mess_bills WHERE reg_no = ? AND month = ? AND year = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $reg_no, $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bill_amount = isset($row['bill_amount']) ? number_format($row['bill_amount'], 2) : 'N/A';

            // Connect to user_management database to fetch payment status
            $conn_user_management = new mysqli($servername, $username, $password, "user_management");
            if ($conn_user_management->connect_error) {
                die("Connection failed: " . $conn_user_management->connect_error);
            }

            // Fetch the payment status from user_management database payments table
            $sql_payment = "SELECT status FROM boys_payments WHERE reg_no = ? AND MONTH(payment_date) = ? AND YEAR(payment_date) = ?";
            $stmt_payment = $conn_user_management->prepare($sql_payment);
            $stmt_payment->bind_param("sii", $reg_no, $month, $year);
            $stmt_payment->execute();
            $result_payment = $stmt_payment->get_result();

            $status = "Pending"; // Default status
            if ($result_payment->num_rows > 0) {
                $payment_row = $result_payment->fetch_assoc();
                $status = $payment_row['status'];
            }

            $color = ($status === 'Paid') ? 'green' : 'red';

            // Close the connection to user_management
            $stmt_payment->close();
            $conn_user_management->close();

            echo "<div class='receipt-box'>
                    <h3>Mess Bill Receipt</h3>
                    <div class='details'>
                        <p><strong>Register Number:</strong> " . htmlspecialchars($row['reg_no']) . "</p>
                        <p><strong>Name:</strong> " . htmlspecialchars($row['name']) . "</p>
                        <p><strong>Month:</strong> " . htmlspecialchars($row['month']) . "</p>
                        <p><strong>Year:</strong> " . htmlspecialchars($row['year']) . "</p>
                        <p><strong>Present Days:</strong> " . htmlspecialchars($row['present_days']) . "</p>
                        <p><strong>Absent Days:</strong> " . htmlspecialchars($row['absent_days']) . "</p>
                        <p><strong>Reduction Days:</strong> " . htmlspecialchars($row['reduction_days']) . "</p>
                        <p><strong>Final Present Days:</strong> " . htmlspecialchars($row['final_present_days']) . "</p>
                    </div>
                    <div class='total'>
                        <p><strong>Total Bill Amount:</strong> ₹ {$bill_amount}</p>
                    </div>
                    <div class='status' style='color: {$color};'>
                        <p><strong>Status:</strong> " . htmlspecialchars($status) . "</p>
                    </div>
                  </div>";
        }
    } else {
        echo "<div class='no-data'>No bills found for the given registration number, month, and year.</div>";
    }

    $stmt->close();
}

$conn->close();
?>

</body>
</html>

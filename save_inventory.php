<?php
$conn = new mysqli("localhost", "root", "", "hostel_mess");

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve and sanitize POST data
$year = intval($_POST['year']);
$month = $conn->real_escape_string($_POST['month']);
$week = intval($_POST['week']);
$vegetables = floatval($_POST['vegetables']);
$milk = floatval($_POST['milk']);
$gas = floatval($_POST['gas']);
$groceries = floatval($_POST['groceries']);
$salary = floatval($_POST['salary']);
$egg = floatval($_POST['egg']);
$less_waste_food = floatval($_POST['less_waste_food']);

// Calculate total inventory cost
$total_inventory_cost = $vegetables + $milk + $gas + $groceries + $salary + $egg - $less_waste_food;

// Check if an entry for the same year, month, and week already exists
$sql_check = "SELECT * FROM inventory WHERE year = '$year' AND month = '$month' AND week = '$week'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // Update the existing record
    $sql_update = "UPDATE inventory 
                   SET vegetables = '$vegetables',
                       milk = '$milk',
                       gas = '$gas',
                       groceries = '$groceries',
                       salary = '$salary',
                       egg = '$egg',
                       less_waste_food = '$less_waste_food',
                       total = '$total_inventory_cost'
                   WHERE year = '$year' AND month = '$month' AND week = '$week'";

    if ($conn->query($sql_update) === TRUE) {
        echo "Inventory updated successfully.";
    } else {
        echo "Error updating inventory: " . $conn->error;
    }
} else {
    // Insert a new record
    $sql_insert = "INSERT INTO inventory (year, month, week, vegetables, milk, gas, groceries, salary, egg, less_waste_food, total)
                   VALUES ('$year', '$month', '$week', '$vegetables', '$milk', '$gas', '$groceries', '$salary', '$egg', '$less_waste_food', '$total_inventory_cost')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "Inventory added successfully.";
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

// Update the monthly total in the monthly_inventory_total table
$sql_monthly = "INSERT INTO monthly_inventory_total (year, month, total)
                SELECT year, month, SUM(total) AS total
                FROM inventory
                WHERE year = '$year' AND month = '$month'
                GROUP BY year, month
                ON DUPLICATE KEY UPDATE total = VALUES(total)";

if ($conn->query($sql_monthly) === TRUE) {
    echo "Monthly total updated successfully.";
} else {
    echo "Error updating monthly total: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
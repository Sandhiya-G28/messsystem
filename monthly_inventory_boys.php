<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Inventory Expenses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .main-container {
            width: 80%;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .page-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .inventory-form {
            display: flex;
            gap: 15px;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        .inventory-form label {
            font-size: 16px;
        }
        .inventory-form input,
        .inventory-form select {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .inventory-form button {
            padding: 8px 16px;
            font-size: 14px;
            background-color: #004085;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .inventory-form button:hover {
            background-color: #002752;
        }
        .summary-box {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 15px;
            padding: 15px;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-box h2 {
            font-size: 18px;
            width: 100%;
            margin-bottom: 10px;
            color: #333;
            text-align: center;
        }
        .summary-box .item {
            width: 48%;
            font-size: 14px;
            color: #555;
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .summary-box .item span {
            font-weight: bold;
            color: #004085;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }
        table thead {
            background-color: #004085;
            color: #fff;
        }
        table th, table td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <h1 class="page-title">Monthly Inventory Expenses</h1>
        <form class="inventory-form" method="POST">
            <label for="year">Year:</label>
            <input type="number" id="year" name="year" placeholder="Enter year (e.g., 2024)" required>
            <label for="month">Month:</label>
            <select id="month" name="month" required>
                <option value="">Select</option>
                <option value="January">January</option>
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option>
                <option value="May">May</option>
                <option value="June">June</option>
                <option value="July">July</option>
                <option value="August">August</option>
                <option value="September">September</option>
                <option value="October">October</option>
                <option value="November">November</option>
                <option value="December">December</option>
            </select>
            <button type="submit">View Expenses</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $conn = new mysqli("localhost", "root", "", "hostel_mess");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $year = $_POST['year'];
            $month = $_POST['month'];

            $query_total = "SELECT 
                                SUM(vegetables) AS vegetables, 
                                SUM(egg) AS egg, 
                                SUM(gas) AS gas, 
                                SUM(groceries) AS groceries, 
                                SUM(salary) AS salary, 
                                SUM(milk) AS milk, 
                                SUM(less_waste_food) AS less_waste_food 
                            FROM inventory_boys 
                            WHERE year='$year' AND month='$month'";
            $result_total = $conn->query($query_total);
            $totals = $result_total->fetch_assoc();

            // Calculate the grand total by excluding 'less_waste_food'
            $grand_total = ($totals['vegetables'] + $totals['egg'] + $totals['gas'] + $totals['groceries'] + $totals['salary'] + $totals['milk']) - $totals['less_waste_food'];

            $query_weekly = "SELECT week, vegetables, egg, gas, groceries, salary, milk, less_waste_food 
                             FROM inventory_boys
                             WHERE year='$year' AND month='$month'";
            $result_weekly = $conn->query($query_weekly);

            echo '<div class="summary-box">';
            echo '<h2>Total Monthly Expenses</h2>';
            $fields = ['vegetables', 'egg', 'gas', 'groceries', 'salary', 'milk', 'less_waste_food'];
            foreach ($fields as $index => $field) {
                if ($index % 3 === 0 && $index !== 0) echo '<div style="flex-basis: 100%;"></div>';
                echo '<div class="item">' . ucfirst(str_replace("_", " ", $field)) . ': <span>₹' . ($totals[$field] ?? 0) . '</span></div>';
            }
            echo '</div>';

            echo '<div class="grand-total">Grand Total for the Month : ₹' . $grand_total . '</div>';

            echo '<h2>Weekly Expenses Breakdown</h2>';
            echo '<table>';
            echo '<thead>
                    <tr>
                        <th>Week</th>
                        <th>Vegetables</th>
                        <th>Egg</th>
                        <th>Gas</th>
                        <th>Groceries</th>
                        <th>Salary</th>
                        <th>Milk</th>
                        <th>Less Waste Food</th>
                        <th>Total</th>
                    </tr>
                  </thead>';
            echo '<tbody>';
            while ($row = $result_weekly->fetch_assoc()) {
                // Calculate weekly total excluding 'less_waste_food'
                $weekly_total = ($row['vegetables'] + $row['egg'] + $row['gas'] + $row['groceries'] + $row['salary'] + $row['milk']) - $row['less_waste_food'];
                echo '<tr>';
                echo '<td>' . $row['week'] . '</td>';
                foreach ($fields as $field) {
                    echo '<td>₹' . $row[$field] . '</td>';
                }
                echo '<td>₹' . $weekly_total . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        ?>
    </div>
</body>
</html>
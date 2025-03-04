<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenditure  Entry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('image.png'); /* Background image similar to your design */
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }
        .form-container {
            background-color: rgba(0, 0, 0, 0.7); /* Semi-transparent background for the form */
            padding: 30px;
            border-radius: 15px;
            width: 400px;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: bold;
        }
        label {
            font-size: 18px;
            font-weight: bold;
            display: block;
            margin: 15px 0 5px;
            text-align: left;
        }
        select, input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-bottom: 15px;
            border: none;
            border-radius: 5px;
        }
        button {
            font-size: 18px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .form-container input[type="number"] {
            -moz-appearance: textfield;
        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Enter Grocery Amount</h1>
        <form method="POST" action="calculate_bill_boys.php">
            <label for="month">Select Month:</label>
            <select id="month" name="month">
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>

            <label for="year">Select Year:</label>
            <input type="number" id="year" name="year" value="<?= date('Y') ?>" required>

            <label for="total_grocery">Total Expenditure Amount:</label>
            <input type="number" id="total_grocery" name="total_grocery" required>

            <button type="submit">Calculate Bill</button>
        </form>
    </div>
</body>
</html>

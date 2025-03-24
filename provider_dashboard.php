<?php
$servername = "66.165.248.146";
$username = "car";
$password = "mAmi930!9";
$dbname = "CarProject";
$port = 3306;

session_start();
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'provider') {
    header("Location: login.php");
    exit();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add a new car space
if (isset($_POST['add_car_space'])) {
    $provider_id = $_SESSION['user_id'];
    $location = $_POST['location'];
    $cost_per_hour = $_POST['cost_per_hour'];
    $cost_per_day = $_POST['cost_per_day'];
    $availability = isset($_POST['availability']) ? 1 : 0;
    $details = $_POST['details'];
    $bank_account_details = $_POST['bank_account_details'];

    $sql = "INSERT INTO car_spaces (provider_id, location, cost_per_hour, cost_per_day, availability, details, bank_account_details) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdiss", $provider_id, $location, $cost_per_hour, $cost_per_day, $availability, $details, $bank_account_details);

    if ($stmt->execute()) {
        echo "Car space registered successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Update existing car space
if (isset($_POST['update_car_space'])) {
    $car_space_id = $_POST['car_space_id'];
    $location = $_POST['location'];
    $cost_per_hour = $_POST['cost_per_hour'];
    $cost_per_day = $_POST['cost_per_day'];
    $availability = isset($_POST['availability']) ? 1 : 0;
    $details = $_POST['details'];
    $bank_account_details = $_POST['bank_account_details'];

    $sql = "UPDATE car_spaces SET location = ?, cost_per_hour = ?, cost_per_day = ?, availability = ?, details = ?, bank_account_details = ? 
            WHERE car_space_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdissi", $location, $cost_per_hour, $cost_per_day, $availability, $details, $bank_account_details, $car_space_id);

    if ($stmt->execute()) {
        echo "Car space updated successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

// Fetch car spaces for the logged-in provider
$provider_id = $_SESSION['user_id'];
$sql = "SELECT * FROM car_spaces WHERE provider_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$car_spaces = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Car Space Renting System - Provider Dashboard</title>
	<a href="logout.php" class="logout-btn">Logout</a>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #333;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"], input[type="email"], textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="checkbox"] {
            margin-top: 10px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            margin-top: 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        hr {
            margin: 20px 0;
        }
        .message {
            color: green;
        }
			.logout-btn {
    background-color: #d9534f;
    color: white;
    padding: 8px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
}

.logout-btn:hover {
    background-color: #c9302c;
}
    </style>
</head>
<body>
    <div class="container">
        <h2>Car Space Renting System - Provider Dashboard</h2>

        <!-- Register New Car Space -->
        <h3>Register a New Car Space</h3>
        <form method="POST" action="">
            <label>Location:</label>
            <input type="text" name="location" required>

            <label>Cost per Hour:</label>
            <input type="text" name="cost_per_hour" required>

            <label>Cost per Day:</label>
            <input type="text" name="cost_per_day" required>

            <label>Availability:</label>
            <input type="checkbox" name="availability" checked>

            <label>Details:</label>
            <textarea name="details" required></textarea>

            <label>Bank Account Details:</label>
            <input type="text" name="bank_account_details" required>

            <input type="submit" name="add_car_space" value="Register Car Space">
        </form>

        <!-- Update Existing Car Spaces -->
        <h3>Update Existing Car Spaces</h3>
        <?php if ($car_spaces->num_rows > 0): ?>
            <?php while ($car_space = $car_spaces->fetch_assoc()): ?>
                <form method="POST" action="">
                    <input type="hidden" name="car_space_id" value="<?php echo $car_space['car_space_id']; ?>">

                    <label>Location:</label>
                    <input type="text" name="location" value="<?php echo $car_space['location']; ?>" required>

                    <label>Cost per Hour:</label>
                    <input type="text" name="cost_per_hour" value="<?php echo $car_space['cost_per_hour']; ?>" required>

                    <label>Cost per Day:</label>
                    <input type="text" name="cost_per_day" value="<?php echo $car_space['cost_per_day']; ?>" required>

                    <label>Availability:</label>
                    <input type="checkbox" name="availability" <?php echo $car_space['availability'] ? 'checked' : ''; ?>>

                    <label>Details:</label>
                    <textarea name="details" required><?php echo $car_space['details']; ?></textarea>

                    <label>Bank Account Details:</label>
                    <input type="text" name="bank_account_details" value="<?php echo $car_space['bank_account_details']; ?>" required>

                    <input type="submit" name="update_car_space" value="Update Car Space">
                </form>
                <hr>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You have no registered car spaces.</p>
        <?php endif; ?>
    </div>
</body>
</html>

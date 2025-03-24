<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'consumer') {
    header("Location: login.php");
    exit();
}

$servername = "66.165.248.146";
$username = "car";
$password = "mAmi930!9";
$dbname = "CarProject";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch available car spaces
$sql = "SELECT * FROM car_spaces WHERE availability = 1";
$car_spaces = $conn->query($sql);

// Handle booking
if (isset($_POST['book'])) {
    $car_space_id = $_POST['car_space_id'];
    $consumer_id = $_SESSION['user_id']; // Assuming user_id is stored in session
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    
    $total_cost = 0; // Calculate total cost based on the duration
    $sql_cost = "SELECT cost_per_hour FROM car_spaces WHERE car_space_id = $car_space_id";
    $result_cost = $conn->query($sql_cost);
    if ($result_cost->num_rows > 0) {
        $row_cost = $result_cost->fetch_assoc();
        $duration = (strtotime($end_time) - strtotime($start_time)) / 3600; // Duration in hours
        $total_cost = $row_cost['cost_per_hour'] * $duration;

        // Insert booking
        $conn->query("INSERT INTO bookings (car_space_id, consumer_id, start_time, end_time, total_cost) VALUES ($car_space_id, $consumer_id, '$start_time', '$end_time', $total_cost)");

        // Update car space availability
        $conn->query("UPDATE car_spaces SET availability = 0 WHERE car_space_id = $car_space_id");

        echo "<script>alert('Booking successful! Total cost: $total_cost');</script>";
    }
}

// Handle cancel booking
if (isset($_POST['cancel'])) {
    $booking_id = $_POST['booking_id'];
    $conn->query("DELETE FROM bookings WHERE booking_id = $booking_id");
    echo "<script>alert('Booking cancelled successfully!');</script>";
}

// Handle pay bill
if (isset($_POST['pay'])) {
    $amount = $_POST['amount'];
    $provider_id = $_POST['provider_id']; // Assuming you have the provider_id associated with the booking

    // Calculate payment to provider after service fee
    $payment_to_provider = $amount * 0.8; // 20% service fee
    echo "<script>alert('Payment of $amount processed! Provider will receive: $payment_to_provider');</script>";
}

// Fetch consumer bookings
$consumer_id = $_SESSION['user_id'];
$sql_bookings = "SELECT * FROM bookings WHERE consumer_id = $consumer_id";
$bookings = $conn->query($sql_bookings);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Car Space Renting System - Consumer Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #5cb85c;
            color: white;
        }
        button {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .book, .cancel, .pay {
            background-color: #5cb85c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Consumer Dashboard</h2>

        <h3>Available Car Spaces</h3>
        <table>
            <thead>
                <tr>
                    <th>Car Space ID</th>
                    <th>Location</th>
                    <th>Cost per Hour</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($car_spaces->num_rows > 0) {
                    while ($row = $car_spaces->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['car_space_id']}</td>
                                <td>{$row['location']}</td>
                                <td>{$row['cost_per_hour']}</td>
                                <td>
                                    <form method='POST' action='' style='display:inline;'>
                                        <input type='hidden' name='car_space_id' value='{$row['car_space_id']}'>
                                        <input type='datetime-local' name='start_time' required>
                                        <input type='datetime-local' name='end_time' required>
                                        <button type='submit' name='book' class='book'>Book</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No available car spaces found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h3>Your Bookings</h3>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Car Space ID</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Total Cost</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($bookings->num_rows > 0) {
                    while ($row = $bookings->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['booking_id']}</td>
                                <td>{$row['car_space_id']}</td>
                                <td>{$row['start_time']}</td>
                                <td>{$row['end_time']}</td>
                                <td>{$row['total_cost']}</td>
                                <td>
                                    <form method='POST' action='' style='display:inline;'>
                                        <input type='hidden' name='booking_id' value='{$row['booking_id']}'>
                                        <button type='submit' name='cancel' class='cancel'>Cancel</button>
                                    </form>
                                    <form method='POST' action='' style='display:inline;'>
                                        <input type='number' name='amount' placeholder='Amount' required>
                                        <input type='hidden' name='provider_id' value='1'> <!-- Replace with actual provider_id -->
                                        <button type='submit' name='pay' class='pay'>Pay</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>

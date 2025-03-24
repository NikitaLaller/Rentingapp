<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'admin') {
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

// Initialize variables for update
$car_space_id = $location = $cost_per_hour = $cost_per_day = $availability = "";

// Handle delete action
if (isset($_POST['delete'])) {
    $car_space_id = $_POST['car_space_id'];
    $conn->query("DELETE FROM car_spaces WHERE car_space_id = $car_space_id");
}

// Handle update action
if (isset($_POST['update'])) {
    $car_space_id = $_POST['car_space_id'];
    
    // Fetch current data to pre-fill the form
    $result = $conn->query("SELECT * FROM car_spaces WHERE car_space_id = $car_space_id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $location = $row['location'];
        $cost_per_hour = $row['cost_per_hour'];
        $cost_per_day = $row['cost_per_day'];
        $availability = $row['availability'];
    }
} elseif (isset($_POST['submit_update'])) {
    $car_space_id = $_POST['car_space_id'];
    $location = $_POST['location'];
    $cost_per_hour = $_POST['cost_per_hour'];
    $cost_per_day = $_POST['cost_per_day'];
    $availability = $_POST['availability'];

    // Update the car space
    $conn->query("UPDATE car_spaces SET location = '$location', cost_per_hour = $cost_per_hour, cost_per_day = $cost_per_day, availability = $availability WHERE car_space_id = $car_space_id");
}

// Fetch car spaces
$sql = "SELECT * FROM car_spaces";
$car_spaces_result = $conn->query($sql);

// Fetch users
$sql_users = "SELECT * FROM users";
$users_result = $conn->query($sql_users);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Car Space Renting System - Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .nav {
            text-align: center;
            margin-bottom: 20px;
        }
        .nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
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
        .edit {
            background-color: #5bc0de;
            color: white;
        }
        .delete {
            background-color: #d9534f;
            color: white;
        }
        .form-container {
            margin: 20px 0;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
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
        <h2>Admin Dashboard - Car Spaces</h2>
		<a href="logout.php" class="logout-btn">Logout</a>
        <div class="nav">
            <a href="#add-update">Add or Update Car Space</a>
            <a href="#all-car-spaces">All Car Spaces</a>
            <a href="#registered-users">Registered Users</a>
        </div>
        
        <div id="add-update" class="form-container">
            <h3>Add or Update Car Space</h3>
<form method="POST" action="" class="car-space-form">
    <div class="form-group">
        <label for="car_space_id">Car Space ID:</label>
        <input type="number" name="car_space_id" value="<?php echo $car_space_id; ?>" required>
    </div>

    <div class="form-group">
        <label for="location">Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" required>
    </div>

    <div class="form-group">
        <label for="cost_per_hour">Cost per Hour:</label>
        <input type="number" step="0.01" name="cost_per_hour" value="<?php echo $cost_per_hour; ?>" required>
    </div>

    <div class="form-group">
        <label for="cost_per_day">Cost per Day:</label>
        <input type="number" step="0.01" name="cost_per_day" value="<?php echo $cost_per_day; ?>" required>
    </div>

    <div class="form-group">
        <label for="availability">Availability:</label>
        <select name="availability" required>
            <option value="1" <?php echo $availability ? 'selected' : ''; ?>>Available</option>
            <option value="0" <?php echo !$availability ? 'selected' : ''; ?>>Not Available</option>
        </select>
    </div>

    <div class="button-group">
        <button type="submit" name="submit_update" class="edit">Update</button>
        <button type="submit" name="delete" class="delete">Delete</button>
    </div>
</form>

<style>
    .car-space-form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    label {
        font-weight: bold;
        margin-bottom: 5px;
    }
    input, select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        width: 100%;
    }
    .button-group {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
    }
    button {
        padding: 8px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .edit {
        background-color: #5bc0de;
        color: white;
    }
    .delete {
        background-color: #d9534f;
        color: white;
    }
</style>
        </div>
        
        <h3 id="all-car-spaces">All Car Spaces</h3>
        <table>
            <tr><th>ID</th><th>Location</th><th>Cost/Hour</th><th>Cost/Day</th><th>Availability</th><th>Actions</th></tr>
            <?php while ($row = $car_spaces_result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['car_space_id']}</td>
                        <td>{$row['location']}</td>
                        <td>{$row['cost_per_hour']}</td>
                        <td>{$row['cost_per_day']}</td>
                        <td>" . ($row['availability'] ? 'Available' : 'Not Available') . "</td>
                        <td><form method='POST'><input type='hidden' name='car_space_id' value='{$row['car_space_id']}'><button type='submit' name='update' class='edit'>Update</button></form></td>
                      </tr>";
            } ?>
        </table>
		<div id="registered-users" class="form-container">
		   <h3>Registered Users</h3>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>User Type</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($users_result->num_rows > 0) {
                    while ($user = $users_result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$user['user_id']}</td>
                                <td>{$user['username']}</td>
                                <td>{$user['email']}</td>
                                <td>{$user['user_type']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No users registered.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php $conn->close(); ?>

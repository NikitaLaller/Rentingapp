<?php
$servername = "66.165.248.146";
$username = "car";
$password = "mAmi930!9";
$dbname = "CarProject";
$port = 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];  // Plain text password
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    // Insert user into the database
    $sql = "INSERT INTO users (username, password, email, user_type) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $password, $email, $user_type);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Car Space Renting System - Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Car Space Renting System - Register</h2>
        <form method="POST" action="">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Email:</label>
            <input type="email" name="email">

            <label>User Type:</label>
            <select name="user_type" required>
                <option value="admin">Admin</option>
                <option value="provider">Provider</option>
                <option value="consumer">Consumer</option>
            </select>

            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>

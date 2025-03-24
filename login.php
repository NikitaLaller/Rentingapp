<?php
$servername = "66.165.248.146";
$username = "car";
$password = "mAmi930!9";
$dbname = "CarProject";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; // Plain text password

    $sql = "SELECT user_id, username, user_type FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];

        echo "<script>alert('Login successful! Welcome " . $user['username'] . "');</script>";
        
        if ($user['user_type'] == 'admin') {
            header('Location: admin_dashboard.php');
        } elseif ($user['user_type'] == 'provider') {
            header('Location: provider_dashboard.php');
        } elseif ($user['user_type'] == 'consumer') {
            header('Location: consumer_dashboard.php');
        }
        exit();
    } else {
        echo "<script>alert('Invalid username or password!');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Space Renting System - Login</title>
    <style>
        /* Google Font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #4facfe, #00f2fe);
        }

        .container {
            background: rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: white;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .input-group {
            position: relative;
            margin: 15px 0;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            outline: none;
            background: rgba(255, 255, 255, 0.3);
            color: white;
            font-size: 16px;
            transition: 0.3s;
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .input-group input:focus {
            background: rgba(255, 255, 255, 0.5);
        }

        .input-group label {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: white;
            font-size: 14px;
            transition: 0.3s;
            pointer-events: none;
        }

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: 5px;
            font-size: 12px;
            color: #ffeb3b;
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #ffeb3b;
            color: black;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #ffeb3b;
            box-shadow: 0px 0px 10px rgba(255, 235, 59, 0.7);
        }

        p {
            margin-top: 10px;
            color: white;
        }

        a {
            color: #ffeb3b;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Car Space Renting System</h2>
        <form method="POST" action="">
            <div class="input-group">
                <input type="text" name="username" id="username" required placeholder=" ">
                <label for="username">Username</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="password" required placeholder=" ">
                <label for="password">Password</label>
            </div>

            <button type="submit" class="btn">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('input');

            inputs.forEach(input => {
                input.addEventListener('focus', function () {
                    this.previousElementSibling.style.color = '#ffeb3b';
                });

                input.addEventListener('blur', function () {
                    this.previousElementSibling.style.color = 'white';
                });
            });
        });
    </script>
</body>
</html>

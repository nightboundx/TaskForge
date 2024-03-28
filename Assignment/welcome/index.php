<?php
// Start session
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Database connection parameters
$server_name = "localhost";
$username = "webapp_select";
$password = "lS4x!d4iH(DGeeTs";
$database = "assessment2";

// Create connection
$conn = new mysqli($server_name, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get username and role for the logged-in user
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.username, u.role_status FROM users u WHERE u.user_id = $user_id";
    $result = $conn->query($sql);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $logged_in_username = $row['username'];
        $user_role = $row['role_status'];
    } else {
        echo "No user found for the given user ID.";
    }
} else {
    echo "User ID not set in the session.";
}

// Check if the user is an admin
$is_admin = ($user_role == 'Admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2; /* Light grey background */
        }
        header, footer {
            background-color: #333;
            padding: 10px 0;
            color: #fff;
            text-align: center;
        }
        footer {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .button-container {
            margin-top: 20px;
            text-align: center;
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            margin-right: 10px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
        main {
            text-align: center; /* Center-align the content */
            margin: 0 auto; /* Center the main content horizontally */
            max-width: 600px; /* Set a maximum width for the main content */
            padding: 20px; /* Add some padding for better readability */
        }

        main h1 {
            font-size: 24px; /* Adjust the font size of the heading */
            margin-bottom: 20px; /* Add some spacing below the heading */
        }

        main p {
            font-size: 16px; /* Adjust the font size of the paragraphs */
            margin-bottom: 10px; /* Add some spacing below each paragraph */
        }
    </style>
</head>
<body>
<header>
    <nav>
        <ul>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <?php if ($is_admin): ?>
                    <a href="../admin/index.php" class="btn-custom">Admin Page</a>
                <?php endif; ?>
                <a href="../registration/index.php" class="btn-custom">Registration Page</a>
                <a href="../view/index.php" class="btn-custom">View Tasks</a>
                    <?php if ($user_role !== 'Guest'): ?>
                <a href="../create/index.php" class="btn-custom">Create Tasks</a>
                    <?php endif; ?>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <center><h1>Homepage</h1></center>
    <br>Welcome to Task Forge.</br>
    <br>You can use the navigation links above to access the different functions of the website. </br>
</main>

<footer>
    <form action="../login/index.php" method="post">
        <button type="submit" class="btn-custom" name="signout">Logout</button>
    </form>
</footer>
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0"></script>
</html>
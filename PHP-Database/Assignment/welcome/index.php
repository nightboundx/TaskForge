<?php
// Start session with cookies
ini_set('session.use_cookies', 1);
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
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

// Handle sign-out
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signout"])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: ../login/index.php"); // Redirect to the login page
    exit;
}

// Get username and role for the logged-in user
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT u.username, r.role_id, r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id WHERE u.user_id = $user_id";
    $result = $conn->query($sql);
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $logged_in_username = $row['username'];
        $user_role_id = $row['role_id'];
        $user_role_name = $row['role_name'];
    } else {
        echo "No user found for the given user ID.";
    }
} else {
    echo "User ID not set in the session.";
}

// Role constants
define('ROLE_GUEST', 1);
define('ROLE_USER', 2);
define('ROLE_ADMIN', 3);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif; /* adjust the font  */
            margin: 0; /* adjust the margin  */
            padding: 0;/* adjust the padding  */
            background-color: #f2f2f2; /* Light grey background */
        }
        header, footer {
            background-color: #333; /* adjust the background colour of header and footer  */
            padding: 10px 0;
            color: #fff;
            text-align: center;
        }
        footer {
            margin-top: 20px; /* adjust the margin top space  */
        }
        table {
            width: 100%; /* adjust the width of tables by percentage  */
            border-collapse: collapse; /* border collapse added  */
            margin-top: 20px; /* adjust the margin top space by 20px  */
        }
        th, td {
            border: 1px solid #dddddd; /* adjust the border by 1px solid and add colour  */
            text-align: left; /* adjust the alignment to the left  */
            padding: 8px;
        }
        th {
            background-color: #f2f2f2; /* adjust the colour  */
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
</head>
<body>
<header>
    <nav>
        <ul>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <?php if ($user_role_id === ROLE_ADMIN): ?>
                    <a href="../admin/index.php" class="btn-custom">Admin Page</a>
                    <a href="../registration/index.php" class="btn-custom">Registration Page</a>
                <?php endif; ?>
                <a href="../view/index.php" class="btn-custom">View Tasks</a>
                <?php if ($user_role_id === ROLE_USER || $user_role_id === ROLE_ADMIN): ?>
                    <a href="../settings/index.php" class="btn-custom">Settings</a>
                    <a href="../create/index.php" class="btn-custom">Create Tasks</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="../view/index.php" class="btn-custom">View Tasks</a>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <center><h1>Homepage</h1></center>
    <br>Welcome to Task Forge.</br>
    <br>You can use the navigation links above to access the different functions of the website. </br>
    <br>If this is your first visit to the website then please go to settings to change your password. </br>
</main>

<footer>
    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="" method="post">
            <button type="submit" class="btn btn-danger" name="signout">Sign Out</button>
        </form>
    <?php endif; ?>
</footer>
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0"></script>
</html>
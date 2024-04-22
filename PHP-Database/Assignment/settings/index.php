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

// Create connection for selecting data
$connSelect = new mysqli($server_name, $username, $password, $database);

// Check connection
if ($connSelect->connect_error) {
    die("Connection failed: " . $connSelect->connect_error);
}

// Create connection for updating data
$update_username = "webapp_update";
$update_password = "SauvaFd18[U*omq0";
$connUpdate = new mysqli($server_name, $update_username, $update_password, $database);

// Check connection
if ($connUpdate->connect_error) {
    die("Connection failed: " . $connUpdate->connect_error);
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
    $result = $connSelect->query($sql);
    if (!$result) {
        die("Query failed: " . $connSelect->error);
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

// Check if the user is authorized to access the settings page
$authorized_roles = [ROLE_USER, ROLE_ADMIN];
if (!in_array($user_role_id, $authorized_roles)) {
    echo "You are not authorized to access this page.";
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Verify if the new password and confirm new password match
    if ($new_password !== $confirm_new_password) {
        echo "New password and confirm new password do not match.";
        exit;
    }

    // Retrieve user's current hashed password from the database
    $sql = "SELECT password FROM users WHERE user_id = $user_id";
    $result = $connSelect->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_password_from_database = $row['password'];

        // Verify if the entered current password matches the actual current password
        if (password_verify($current_password, $current_password_from_database)) {
            // Update the user's password in the database with the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_sql = "UPDATE users SET password = '$hashed_password' WHERE user_id = $user_id";
            if ($connUpdate->query($update_sql) === TRUE) {
                echo "Password updated successfully.";
            } else {
                echo "Error updating password: " . $connUpdate->error;
            }
        } else {
            echo "Incorrect current password. Please try again.";
        }
    } else {
        echo "No user found for the given user ID.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
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
                <a href="../settings/index.php" class="btn-custom">Settings</a>
                <?php if ($user_role_id !== ROLE_GUEST): ?>
                    <a href="../create/index.php" class="btn-custom">Create Tasks</a>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <center><h1>Settings</h1></center>
    <h2>Change Password</h2>
    <br>If this is your first time logging into the website then please change your password, it will be "Password123" by default.<br>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" required><br>

        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required><br>

        <label for="confirm_new_password">Confirm New Password:</label>
        <input type="password" name="confirm_new_password" required><br>

        <input type="submit" value="Change Password">
    </form>
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
<?php
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

// SQL query to retrieve tasks data
$sql = "SELECT * FROM tasks";

// Execute query
$result = $conn->query($sql);

// Check if user is not logged in, redirect to login page
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit;
}
// Handle sign-out
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signout"])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: ../login/index.php"); // Redirect to the login page
    exit;
}

// Get user role and status
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT u.username, u.role_status FROM users u WHERE u.user_id = $user_id";
$result_user = $conn->query($sql_user);
if (!$result_user) {
    die("Query failed: " . $conn->error);
}
if ($result_user->num_rows > 0) {
    $row_user = $result_user->fetch_assoc();
    $logged_in_username = $row_user['username'];
    $user_role = $row_user['role_status'];
} else {
    $logged_in_username = "Unknown";
    $user_role = "Unknown";
}

// Check if the user is a guest
$is_guest = ($user_role === 'Guest');

// Check if the user is an admin
$is_admin = ($user_role === 'Admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tasks</title>
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
        .btn {
            background-color: #007bff; // button css style for page
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            margin-right: 10px;
            text-decoration: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3; // change button colour on hover
        }
        .btn-danger {
            background-color: #dc3545 !important; // button wouldnt change to red, this is a workaround to recolour
        }
    </style>
</head>
<body>

<header>
    <h2>View Tasks</h2>
    <div class="user-info">
        <p>Logged in as: <?php echo $logged_in_username; ?> (<?php echo $user_role; ?>)</p>
    </div>
    <div class="button-container">
        <a href="../welcome/index.php" class="btn">Homepage</a>
        <?php if ($is_admin): ?>
            <a href="../admin/index.php" class="btn">Admin Page</a>
        <?php endif; ?>
        <?php if (!$is_guest): ?>
            <a href="../registration/index.php" class="btn">Registration</a>
            <a href="../create/index.php" class="btn">Create Tasks</a>
        <?php endif; ?>
    </div>
</header>

<table>
    <tr>
        <th>Task Name</th>
        <th>Task Date</th>
        <th>Task Completion Date</th>
        <th>Task Description</th>
        <th>Task Assigned</th>
    </tr>
    <?php
    // Check if there are any rows returned
    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            // Fetch the username based on the assigned user ID
            $assignedUserId = $row["TaskAssigned"]; // retrieve the assigned user ID and store in new variable
            $query = "SELECT username FROM users WHERE user_id = ?"; // SQL query to retrieve username based on user ID
            $stmt = $conn->prepare($query); // prepare the SQL query
            $stmt->bind_param("i", $assignedUserId); // bind the parameter to the SQL query to prevent injections
            $stmt->execute(); // execute the SQL query
            $userResult = $stmt->get_result(); // get the result of the SQL query

            if ($userResult->num_rows > 0) { // check if there are any rows returned
                $userRow = $userResult->fetch_assoc(); //   fetch the row
                $assignedUsername = $userRow['username']; // store the username in a new variable
            } else {
                $assignedUsername = "User not found";
            }

            echo "<tr>";
            echo "<td>" . $row["TaskName"] . "</td>";
            echo "<td>" . $row["TaskDate"] . "</td>";
            echo "<td>" . $row["TaskCompletionDate"] . "</td>";
            echo "<td>" . $row["TaskDescription"] . "</td>";
            echo "<td>" . $assignedUsername . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No tasks found</td></tr>";
    }
    ?>
</table>

<footer>
    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="" method="post">
            <button type="submit" class="btn btn-danger" name="signout">Sign Out</button>
        </form>
    <?php endif; ?>
</footer>

</body>
</html>

<?php
// Close connection
$conn->close();
?>

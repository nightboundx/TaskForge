<?php

session_start();

// Handle sign-out
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signout"])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    // Redirect the user to a sign-in page or any other desired location
    header("Location: ../login/index.php");
    exit;
}

$server_name = "localhost";
$username = "webapp_insert";
$password = "E5O-R(n0JJor1BVZ";
$database = "assessment2";

$conn = new mysqli($server_name, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $forename = $_POST["forename"];
    $surname = $_POST["surname"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Assign default role and user status
    $default_role_id = 1; // Guest role
    $default_user_status = 'Guest';

    // Insert user into users table with default role and user status
    $sql = "INSERT INTO assessment2.users (username, forename, surname, email, password, role_id, user_status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssis", $username, $forename, $surname, $email, $password, $default_role_id, $default_user_status);

    if ($stmt->execute()) {
        echo "Registration Completed Successfully<br>";
        echo '<a href="../login/index.php" class="btn btn-primary">Login</a>'; // Added link to login page with button styling
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        /* Custom CSS styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            padding: 10px 0;
            color: #fff;
        }
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        nav ul li {
            display: inline;
            margin-right: 10px;
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<header>
    <nav>
        <ul>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <?php if ($is_admin): ?>
                    <li><a href="../admin/index.php" class="btn-custom">Admin Page</a></li>
                <?php endif; ?>
                <li><a href="../login/index.php" class="btn-custom">Login Page</a></li>
            <?php endif; ?>
            <li><a href="../view/index.php" class="btn-custom">View Tasks</a></li>
            <?php if ($is_admin): ?>
                <li><a href="../create/index.php" class="btn-custom">Create Tasks</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <h1>Registration Form</h1>
    <form action="" method="post">
        <table>
            <tr>
                <td>Username:</td>
                <td><input type="text" name="username" required></td>
            </tr>
            <tr>
                <td>Forename:</td>
                <td><input type="text" name="forename" required></td>
            </tr>
            <tr>
                <td>Surname:</td>
                <td><input type="text" name="surname" required></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><input type="email" name="email" required></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="password" required></td>
            </tr>
        </table>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</main>

<footer>
    <?php if ($is_admin): ?>
        <form action="" method="post">
            <button type="submit" name="signout" class="btn btn-danger">Sign Out</button>
        </form>
    <?php endif; ?>
</footer>
</body>
</html>

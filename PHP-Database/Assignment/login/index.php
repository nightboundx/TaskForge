<?php
ini_set('session.use_cookies', 1);
session_start();

$server_name = "localhost";
$username = "webapp_select";
$password = "lS4x!d4iH(DGeeTs";
$database = "assessment2";

$conn = new mysqli($server_name, $username, $password, $database);

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

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Retrieve user information from the database
    $sql = "SELECT user_id, role_id, password FROM assessment2.users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the entered password against the stored hashed password
        if (password_verify($password, $row["password"])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role_id'] = $row['role_id'];

            // Redirect the user based on their role_id
            switch ($row['role_id']) {
                case 1: // Guest
                    header("Location: ../welcome/index.php");
                    break;
                case 2: // User
                    header("Location: ../welcome/index.php");
                    break;
                case 3: // Admin
                    header("Location: ../admin/index.php");
                    break;
                default:
                    header("Location: ../welcome/index.php");
            }
            exit;
        } else {
            echo "Invalid password";
        }
    } else {
        echo "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;/* Light grey background */
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
        .container {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .button-cell {
            text-align: right;
            margin-bottom: 10px;
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 5px;
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
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="../login/index.php" class="btn btn-custom">Login</a>
            <?php else: ?>
                <a href="../welcome/index.php" class="btn btn-custom">Welcome</a>
                <?php if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] === 2 || $_SESSION['role_id'] === 3)): ?>
                    <a href="../registration/index.php" class="btn btn-custom">Registration</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] === 3): ?>
                    <a href="../admin/index.php" class="btn btn-custom">Admin</a>
                <?php endif; ?>
                <a href="../view/index.php" class="btn btn-custom">View Tasks</a>
                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] !== 1): ?>
                    <a href="../create/index.php" class="btn btn-custom">Create Tasks</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] === 2): ?>
                    <a href="../settings/index.php" class="btn btn-custom">Settings</a>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT username FROM assessment2.users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $logged_in_username = $row['username'];
                echo '<div>Welcome, ' . $logged_in_username . '</div>';
            } else {
                echo '<div>Welcome</div>';
            }
            ?>
        <?php endif; ?>
    </nav>
</header>
<form class="row g-3" action="" method="post">
    <div class="col-auto">
        <label for="inputUsername" class="visually-hidden">Username</label>
        <input type="text" class="form-control" id="inputUsername" name="username" placeholder="Username">
    </div>
    <div class="col-auto">
        <label for="inputPassword2" class="visually-hidden">Password</label>
        <input type="password" class="form-control" id="inputPassword2" name="password" placeholder="Password">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary mb-3">Submit</button>
    </div>
</form>
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

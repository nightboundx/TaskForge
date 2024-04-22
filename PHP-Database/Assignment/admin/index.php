<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Administration Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        /* css styles for the entire page */
        body {
            font-family: Arial, sans-serif; /* change font */
            margin: 0; /* change margin space */
            padding: 0; /* change the padding for the body */
        }
        header {
            background-color: #333; /* adds colour to the header background  */
            padding: 10px 0; /* change the padding for the footer */
            color: #fff; /* adds colour to the header */
            text-align: center; /* Center align the text */
            margin-bottom: 20px; /* Adds margin bottom for spacing */
        }
        footer {
            background-color: #333; /* adds colour to the background for footer  */
            padding: 10px 0; /* change the padding for the footer */
            color: #fff; /* change the colour for the footer */
            text-align: center; /* Center align the text */
            margin-top: 20px; /* Add margin top for spacing */
        }
        .container {
            padding: 20px; /* Add padding for content */
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
            text-align: right; /* Align content to the right */
            margin-bottom: 10px; /* Add margin bottom for spacing */
        }
        .btn-custom {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 5px; /* Add margin between buttons */
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<header>
    <h1>Administration Page</h1>
    <div class="container">
        <nav>
            <ul class="d-flex justify-content-start"> <!-- bootstrap classes -->
                <a href="../welcome/index.php" class="btn btn-custom me-2">Homepage</a>
                <a href="../registration/index.php" class="btn btn-custom me-2">Registration</a>
                <a href="../view/index.php" class="btn btn-custom">View Tasks</a>
                <a href="../create/index.php" class="btn btn-custom">Create Task</a>
                <a href="../settings/index.php" class="btn btn-custom">Settings</a>

            </ul>
        </nav>
</header>

<?php
// Establishing a connection to the database for SELECT privileges
$server_name = "localhost";
$username = "webapp_select";
$password = "lS4x!d4iH(DGeeTs";
$database = "assessment2";
$conn_select = new mysqli($server_name, $username, $password, $database);
if ($conn_select->connect_error) {
    die("Connection failed: " . $conn_select->connect_error);
}

// Establishing a connection to the database for UPDATE privileges
$update_username = "webapp_update";
$update_password = "SauvaFd18[U*omq0";
$conn_update = new mysqli($server_name, $update_username, $update_password, $database);
if ($conn_update->connect_error) {
    die("Connection failed: " . $conn_update->connect_error);
}

// Handling the form submission to restore a user
if (isset($_POST['restore_user'])) {
    $restore_user_id = $_POST['restore_user_id'];
    if (!empty($restore_user_id)) {
        $update_query = "UPDATE assessment2.users SET status = 'active' WHERE user_id = ?";
        $stmt = $conn_update->prepare($update_query);
        $stmt->bind_param("i", $restore_user_id);
        if ($stmt->execute()) {
            echo "User restored successfully";
        } else {
            echo "Error updating record: " . $conn_update->error;
        }
        $stmt->close();
    } else {
        echo "Please select a user to restore.";
    }
}

// Handling the form submission to remove a user
if (isset($_POST['remove_user'])) {
    $remove_user_id = $_POST['remove_user_id'];
    $update_query = "UPDATE assessment2.users SET status = 'inactive' WHERE user_id = ?";
    $stmt = $conn_update->prepare($update_query);
    $stmt->bind_param("i", $remove_user_id);
    if ($stmt->execute()) {
        echo "User removed successfully";
    } else {
        echo "Error updating record: " . $conn_update->error;
    }
    $stmt->close();
}
// Handling the form submission to modify a user's role
if (isset($_POST['modify_role'])) {
    $user_id = $_POST['user_id'];
    $role_id = $_POST['role_id'];

    // Update the user's role in the users table
    $update_query = "UPDATE users SET role_id = ?, role_status = ? WHERE user_id = ?";
    $stmt = $conn_update->prepare($update_query);

    $role_status = '';
    switch ($role_id) {
        case 1:
            $role_status = 'Guest';
            break;
        case 2:
            $role_status = 'User';
            break;
        case 3:
            $role_status = 'Admin';
            break;
    }

    $stmt->bind_param("isi", $role_id, $role_status, $user_id);
// Execute the query and echo if successful or not
    if ($stmt->execute()) {
        echo "User role updated successfully";
    } else {
        echo "Error updating user role: " . $stmt->error;
    }

    $stmt->close();
}

?>

<table>
    <tr>
        <th>Administration Tools</th>
    </tr>
    <tr>
        <td colspan="2">Restore User</td>
    </tr>
    <tr>
        <td>
            <form method="POST">
                <select name="restore_user_id">
                    <?php
                    // Display all inactive users for restoration
                    $query = "SELECT user_id, username FROM assessment2.users WHERE status = 'inactive'";
                    $result = $conn_select->query($query); // Use $conn_select for SELECT queries
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="'.$row['user_id'].'">'.$row['username'].'</option>';
                        }
                    }
                    ?>
                </select>
        </td>
        <td>
            <input type="submit" name="restore_user" value="Restore User">
            </form>
        </td>
    </tr>
    <tr>
        <td colspan="2">Remove User</td>
    </tr>
    <tr>
        <td>
            <form method="POST">
                <select name="remove_user_id">
                    <?php
                    // Display all active users for removal
                    $query = "SELECT user_id, username FROM assessment2.users WHERE status = 'active'";
                    $result = $conn_select->query($query); // Use $conn_select for SELECT queries
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="'.$row['user_id'].'">'.$row['username'].'</option>';
                        }
                    }
                    ?>
                </select>
        </td>
        <td>
            <input type="submit" name="remove_user" value="Remove User">
            </form>
        </td>
    </tr>
    <tr>
        <td colspan="2">List Users</td>
    </tr>
    <tr>
        <td>
            <form method="POST">
                <select name="user_id">
                    <?php
                    // Display all users for listing
                    $query = "SELECT user_id, username, forename, surname, email FROM assessment2.users";
                    $result = $conn_select->query($query); // Use $conn_select for SELECT queries
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="'.$row['user_id'].'">'.$row['username'].'</option>';
                        }
                    }
                    ?>
                </select>
                <input type="submit" name="show_credentials" value="Show Credentials">
            </form>
        <td>
            <div>
                <?php
                // Display the selected user's credentials
                if(isset($_POST['show_credentials'])) {
                    $selected_user_id = $_POST['user_id'];
                    $query = "SELECT username, forename, surname, email FROM assessment2.users WHERE user_id = $selected_user_id";
                    $result = $conn_select->query($query); // Use $conn_select for SELECT queries
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo "Username: " . $row['username'] . "<br>";
                        echo "Forename: " . $row['forename'] . "<br>";
                        echo "Surname: " . $row['surname'] . "<br>";
                        echo "Email: " . $row['email'] . "<br>";
                    } else {
                        echo "User not found.";
                    }
                }
                ?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2">Modify User Role</td>
    </tr>
    <tr>
        <td>
            <form method="POST">
                <select name="user_id">
                    <?php
                    // Retrieve all users
                    $query = "SELECT user_id, username FROM assessment2.users";
                    $result = $conn_select->query($query);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row['user_id'] . '">' . $row['username'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <select name="role_id">
                    <option value="1">Guest</option>
                    <option value="2">User</option>
                    <option value="3">Admin</option>
                </select>
        </td>
        <td>
            <input type="submit" name="modify_role" value="Modify Role">
            </form>
        </td>
    </tr>
</table>

<?php
$conn_select->close();
$conn_update->close();
?>
</body>
<footer>
    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="" method="post">
            <button type="submit" class="btn btn-danger" name="signout">Sign Out</button>
        </form>
    <?php endif; ?>
</footer>
</html>

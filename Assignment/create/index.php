<?php
// Start the session
session_start();

//These values are for the connection to the SQL server
$server_name = "localhost";
$select_username = "webapp_select";
$select_password = "lS4x!d4iH(DGeeTs";
$insert_username = "webapp_insert";
$insert_password = "E5O-R(n0JJor1BVZ";
$database = "assessment2";

// Establishing a new connection to the server for selecting data
$connSelect = new mysqli($server_name, $select_username, $select_password, $database);

if ($connSelect->connect_error) {
    die("Connection failed: " . $connSelect->connect_error);
}

// Fetch users from the database using the select connection
$sql = "SELECT user_id, username FROM assessment2.users";
$result = $connSelect->query($sql);

// Initialize an array to store user options
$user_options = [];

//Iterate through the results and store users in the array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Access the user_id and username from the $row array
        $userid = htmlspecialchars($row['user_id']);
        $username = htmlspecialchars($row['username']);
        $user_options[$userid] = $username;
    }
}

// Establishing a new connection to the server for inserting data
$connInsert = new mysqli($server_name, $insert_username, $insert_password, $database);

if ($connInsert->connect_error) {
    die("Connection failed: " . $connInsert->connect_error);
}

// Check if the submit button has been clicked on the form
if (isset($_POST['submit']) && isset($_SESSION['role'])) {
    // Check if the user is logged in and has the appropriate role
    $allowed_roles = [2, 3]; // Users and admins
    if (in_array($_SESSION['role'], $allowed_roles)) {
        // Retrieve form data
        $TaskName = $_POST['TaskName'];
        $TaskDescription = $_POST['TaskDescription'];
        $TaskCompletionDate = $_POST['TaskCompletionDate'];
        $TaskAssigned = $_POST['TaskAssigned'];
        $UserID = $_SESSION['user_id']; // Retrieve the UserID from the session

        // Insert the task into the database
        $sql = "INSERT INTO assessment2.tasks (UserID, TaskName, TaskCompletionDate, TaskDescription, TaskAssigned) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connInsert->prepare($sql);
        $stmt->bind_param('issss', $UserID, $TaskName, $TaskCompletionDate, $TaskDescription, $TaskAssigned);
        if ($stmt->execute()) {
            echo "Task added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "You are not authorised to create tasks.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
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
    </style>
</head>
<body>
<header>
    <h1>Create Task</h1>
    <div class="button-container">
        <a href="../welcome/index.php" class="btn-custom">Homepage</a>
        <a href="../admin/index.php" class="btn-custom">Admin Page</a>
        <a href="../view/index.php" class="btn-custom">View Tasks</a>
        <a href="../create/index.php" class="btn-custom">Create Tasks</a>

    </div>
</header>
<main>
    <div class="container">
        <form action="" method="post">
            <div class="mb-3">
                <label for="TaskName" class="form-label">Task Name</label>
                <input type="text" class="form-control" id="TaskName" name="TaskName" required>
            </div>
            <div class="mb-3">
                <label for="TaskDescription" class="form-label">Task Description</label>
                <input type="text" class="form-control" id="TaskDescription" name="TaskDescription" required>
            </div>
            <div class="mb-3">
                <label for="TaskCompletionDate" class="form-label">Completion Date</label>
                <input type="date" class="form-control" id="TaskCompletionDate" name="TaskCompletionDate" required>
            </div>
            <div class="mb-3">
                <label for="TaskAssigned" class="form-label">Assigned To</label>
                <select class="form-select" id="TaskAssigned" name="TaskAssigned">
                    <?php
                    // Iterate through the user options array and generate dropdown options
                    foreach ($user_options as $userid => $username) {
                        echo '<option value="'.$userid.'">'.$username.'</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Submit</button>
        </form>
    </div>
</main>
<footer>
    <form action="../login/index.php" method="post">
        <button type="submit" class="btn-custom" name="signout">Logout</button>
    </form>
</footer>
</body>
</html>


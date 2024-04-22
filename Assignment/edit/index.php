<?php
// Start the session
session_start();

// Connect to the database
$server_name = "localhost";
$select_username = "webapp_select";
$select_password = "lS4x!d4iH(DGeeTs";
$insert_username = "webapp_insert"; // New insert user
$insert_password = "E5O-R(n0JJor1BVZ"; // New insert password
$update_username = "webapp_update"; // New update user
$update_password = "SauvaFd18[U*omq0"; // New update password
$database = "assessment2";

$conn = new mysqli($server_name, $select_username, $select_password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// this is why it isnt working
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Fetch task details from the database
    $sql = "SELECT * FROM assessment2.tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Task found, fetch details
        $task = $result->fetch_assoc();
    } else {
        // Task not found
        die("Task not found");
    }
} else {
    // Task ID not provided
    die("Task ID not provided");
}
// Handle sign-out
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signout"])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: ../login/index.php"); // Redirect to the login page
    exit;
}

// Check if form is submitted for updating task
if(isset($_POST['submit'])){
    $TaskName = $_POST['TaskName'];
    $TaskDescription = $_POST['TaskDescription'];
    $TaskCompletionDate = $_POST['TaskCompletionDate'];
    $TaskPriority = $_POST['TaskPriority'];
    $TaskStatus = $_POST['TaskStatus'];
    $TaskAssigned = $_POST['TaskAssigned']; // Added TaskAssigned field

    // Update task details in the database
    $conn_update = new mysqli($server_name, $update_username, $update_password, $database);
    if ($conn_update->connect_error) {
        die("Connection failed: " . $conn_update->connect_error);
    }

    $sql_update = "UPDATE assessment2.tasks SET TaskName = ?, TaskDescription = ?, TaskCompletionDate = ?, TaskPriority = ?, TaskStatus = ?, TaskAssigned = ? WHERE id = ?";
    $stmt_update = $conn_update->prepare($sql_update);
    $stmt_update->bind_param('sssssii', $TaskName, $TaskDescription, $TaskCompletionDate, $TaskPriority, $TaskStatus, $TaskAssigned, $task_id);

    if($stmt_update->execute()){
        echo "Task updated successfully!";
    } else {
        echo "Error updating task: " . $conn_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
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
    <!-- Add header content if needed -->
</header>

<main>
    <div class="container">
        <h1>Edit Task</h1>
        <form class="row g-3" action="" method="post">
            <div class="col-md-6">
                <label for="TaskName" class="form-label">Task Name</label>
                <input type="text" class="form-control" id="TaskName" name="TaskName" value="<?php echo $task['TaskName']; ?>" required>
            </div>
            <div class="col-md-6">
                <label for="TaskDescription" class="form-label">Task Description</label>
                <input type="text" class="form-control" id="TaskDescription" name="TaskDescription" value="<?php echo $task['TaskDescription']; ?>" required>
            </div>
            <div class="col-md-6">
                <label for="TaskCompletionDate" class="form-label">Completion Date</label>
                <input type="date" class="form-control" id="TaskCompletionDate" name="TaskCompletionDate" value="<?php echo $task['TaskCompletionDate']; ?>" required>
            </div>
            <div class="col-md-6">
                <label for="TaskPriority" class="form-label">Priority</label>
                <input type="text" class="form-control" id="TaskPriority" name="TaskPriority" value="<?php echo $task['TaskPriority']; ?>" required>
            </div>
            <div class="col-md-6">
                <label for="TaskStatus" class="form-label">Status</label>
                <input type="text" class="form-control" id="TaskStatus" name="TaskStatus" value="<?php echo $task['TaskStatus']; ?>" required>
            </div>
            <div class="col-md-6">
                <label for="TaskAssigned" class="form-label">Assigned To</label>
                <input type="text" class="form-control" id="TaskAssigned" name="TaskAssigned" value="<?php echo $task['TaskAssigned']; ?>" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="submit">Update</button>
            </div>
        </form>
    </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</html>

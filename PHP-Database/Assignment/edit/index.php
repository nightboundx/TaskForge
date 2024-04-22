<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php
// Start the session
session_start();

// different users have different privileges for the various operations
$server_name = "localhost";
$select_username = "webapp_select";
$select_password = "lS4x!d4iH(DGeeTs";
$insert_username = "webapp_insert";
$insert_password = "E5O-R(n0JJor1BVZ";
$update_username = "webapp_update";
$update_password = "SauvaFd18[U*omq0";
$database = "assessment2";

// Create a connection for SELECT queries
$conn_select = new mysqli($server_name, $select_username, $select_password, $database);

// Check the SELECT connection
if ($conn_select->connect_error) {
    die("SELECT connection failed: " . $conn_select->connect_error);
}

// Create a connection for UPDATE queries
$conn_update = new mysqli($server_name, $update_username, $update_password, $database);

// Check the UPDATE connection
if ($conn_update->connect_error) {
    die("UPDATE connection failed: " . $conn_update->connect_error);
}

// Handle sign-out
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signout"])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: ../login/index.php"); // Redirect to the login page
    exit;
}

// Check if the user is logged in and get the user ID and role
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$user_role_id = $_SESSION['role_id'];

// Check if the task ID is provided in the URL
if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];

    // Retrieve the task details from the database based on the task ID
    $sql = "SELECT * FROM tasks WHERE TaskID = ?";
    $stmt = $conn_select->prepare($sql);
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $task_name = $row['TaskName'];
        $task_date = $row['TaskDate'];
        $task_completion_date = $row['TaskCompletionDate'];
        $task_description = $row['TaskDescription'];
        $task_assigned = $row['TaskAssigned'];
        $task_user_id = $row['UserID'];
        $task_status = $row['TaskStatus']; // Retrieve TaskStatus

        // Check if the user is allowed to edit the task
        if ($task_user_id == $user_id || $user_role_id == 3) {
            // Display the task editing form
            ?>
            <h2>Edit Task</h2>
            <a href="../view/index.php" class="btn">Back to View Tasks</a>
            <form method="POST" action="">
                <label for="task_name">Task Name:</label>
                <input type="text" name="task_name" value="<?php echo $task_name; ?>" required><br>

                <label for="task_date">Task Date:</label>
                <input type="date" name="task_date" value="<?php echo $task_date; ?>" required><br>

                <label for="task_completion_date">Task Completion Date:</label>
                <input type="date" name="task_completion_date" value="<?php echo $task_completion_date; ?>" required><br>

                <label for="task_description">Task Description:</label>
                <textarea name="task_description" required><?php echo $task_description; ?></textarea><br>

                <label for="task_assigned">Assigned To:</label>
                <select name="task_assigned" required>
                    <?php
                    // Retrieve the list of users from the database
                    $sql_users = "SELECT user_id, username FROM users";
                    $result_users = $conn_select->query($sql_users);
                    while ($row_user = $result_users->fetch_assoc()) {
                        $selected = ($row_user['user_id'] == $task_assigned) ? 'selected' : '';
                        echo "<option value='" . $row_user['user_id'] . "' $selected>" . $row_user['username'] . "</option>";
                    }
                    ?>
                </select><br>
                <label for="task_status">Task Status:</label>
                <select name="task_status" required>
                    <option value="Done" <?php if ($task_status === 'Done') echo 'selected'; ?>>Done</option>
                    <option value="In Progress" <?php if ($task_status === 'In Progress') echo 'selected'; ?>>In Progress</option>
                    <option value="Not Completed" <?php if ($task_status === 'Not Completed') echo 'selected'; ?>>Not Completed</option>
                </select><br>

                <input type="submit" name="update" value="Update Task">
                <a href="../edit/delete_task.php?task_id=<?php echo $task_id; ?>" class="btn btn-danger">Delete Task</a>
            </form>
            <?php
        } else {
            echo "You are not authorized to edit this task.";
        }
    } else {
        echo "Task not found.";
    }
} else {
    echo "Invalid task ID.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        // Retrieve the updated task details from the form
        $task_name = $_POST['task_name'];
        $task_date = $_POST['task_date'];
        $task_completion_date = $_POST['task_completion_date'];
        $task_description = $_POST['task_description'];
        $task_assigned = $_POST['task_assigned'];
        $task_status = $_POST['task_status'];

        // Update the task details in the database using the webapp_update user
        $sql = "UPDATE tasks SET TaskName = ?, TaskDate = ?, TaskCompletionDate = ?, TaskDescription = ?, TaskAssigned = ?, TaskStatus = ? WHERE TaskID = ?";
        $stmt = $conn_update->prepare($sql);
        $stmt->bind_param("ssssssi", $task_name, $task_date, $task_completion_date, $task_description, $task_assigned, $task_status, $task_id);
        $stmt->execute();

        if ($stmt->affected_rows === 1) {
            echo "Task updated successfully.";
        } else {
            echo "Error updating task: " . $conn_update->error;
        }
    } elseif (isset($_POST['delete'])) {
        // Delete the task from the database using the webapp_update user
        $sql = "DELETE FROM tasks WHERE TaskID = ?";
        $stmt = $conn_update->prepare($sql);
        $stmt->bind_param("i", $task_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows === 1) {
                echo "Task deleted successfully.";
                // Redirect to the view tasks page after deletion
                header("Location: ../view/index.php");
                exit;
            } else {
                echo "No task was deleted. Please try again.";
            }
        } else {
            echo "Error deleting task: " . $conn_update->error;
        }
    }
}

// Close the database connections
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

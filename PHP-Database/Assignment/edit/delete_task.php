<?php
// Start the session
session_start();

// Check if the user is logged in and has the appropriate role
if (!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: ../login/index.php");
    exit;
}

// Check if the task ID is provided in the URL
if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];

    // Connect to the database
    $server_name = "localhost";
    $select_username = "webapp_select";
    $select_password = "lS4x!d4iH(DGeeTs";
    $delete_username = "webapp_delete";
    $delete_password = "IX59ohwNQ@)4X5yl";
    $database = "assessment2";

    // Create a connection for SELECT queries
    $conn_select = new mysqli($server_name, $select_username, $select_password, $database);

    // Check the SELECT connection
    if ($conn_select->connect_error) {
        die("SELECT connection failed: " . $conn_select->connect_error);
    }

    // Create a connection for DELETE queries
    $conn_delete = new mysqli($server_name, $delete_username, $delete_password, $database);

    // Check the DELETE connection
    if ($conn_delete->connect_error) {
        die("DELETE connection failed: " . $conn_delete->connect_error);
    }

    // Handle sign-out
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signout"])) {
        session_unset(); // Unset all session variables
        session_destroy(); // Destroy the session
        header("Location: ../login/index.php"); // Redirect to the login page
        exit;
    }

    // Delete the task from the database using the webapp_delete user
    $sql = "DELETE FROM tasks WHERE TaskID = ?";
    $stmt = $conn_delete->prepare($sql);
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
        echo "Error deleting task: " . $conn_delete->error;
    }

    // Close the database connections
    $conn_select->close();
    $conn_delete->close();
} else {
    echo "Task ID not provided.";
}
?>
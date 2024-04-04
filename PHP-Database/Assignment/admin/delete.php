<?php
// Database connection parameters
$server_name = "localhost";
$del_username = "webapp_delete";
$del_password = ")/5cReIzOV3FVuDS";
$database = "assessment2";

// Connect to the database
$conn_delete = new mysqli($server_name, $del_username, $del_password, $database);
if ($conn_delete->connect_error) {
    die("Connection failed: " . $conn_delete->connect_error);
}

// Query to select inactive users who have been inactive for more than two weeks
$query = "SELECT user_id FROM assessment2.users WHERE status = 'inactive' AND last_updated < NOW() - INTERVAL 2 WEEK";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Iterate through the inactive users and delete them
    while ($row = $result->fetch_assoc()) {
        $user_id = $row['user_id'];
        $delete_query = "DELETE FROM assessment2.users WHERE user_id = $user_id";
        if ($conn->query($delete_query) === TRUE) {
            echo "User with ID $user_id deleted successfully.<br>";
            // Optionally, log the deletion process
            // Example: echo "User with ID $user_id deleted successfully.<br>";
        } else {
            echo "Error deleting user with ID $user_id: " . $conn->error . "<br>";
            // Optionally, log the error
            // Example: echo "Error deleting user with ID $user_id: " . $conn->error . "<br>";
        }
    }
} else {
    echo "No inactive users to delete.<br>";
}

// Close the database connection
$conn_delete->close();
?>


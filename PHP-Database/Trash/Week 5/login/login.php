<?php
$server_name = "localhost";
$username = "webapp_select";
$password = "lS4x!d4iH(DGeeTs";

$conn = new mysqli($server_name, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Retrieve user information from the database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the entered password against the stored hashed password
        if (password_verify($password, $row["password"])) {
            echo "Login successful!";
            // You can redirect the user to another page after successful login
            // header("Location: welcome.php");
            // exit;
        } else {
            echo "Invalid password";
        }
    } else {
        echo "User not found";
    }
}

// Close the database connection
$conn->close();
?>
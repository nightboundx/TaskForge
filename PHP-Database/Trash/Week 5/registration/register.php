<?php
$server_name = "localhost";
$username = "webapp_select";
$password = "lS4x!d4iH(DGeeTs";

$conn = new mysqli($server_name, $username, $password);

if ($conn->connect_error)   {
    die("Connection failed: " . $conn->connect_error);
}
else{
    echo("connection successful");
}

if ($_SERVER["REQUEST_METHOD"]) == "POST") {
    $username = $_POST["webapp_select"];
    $password = password_hash($_POST["lS4x!d4iH(DGeeTs"], PASSWORD_DEFAULT;

    if (!filter_var($username, FILTER_VALIDATE_USERNAME)) {
        echo "invalid username";
        exit
    }
}

$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";

if ($conn->query($sql) ==== TRUE) {
    echo "Registation Completed Successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
    }
$conn->close();
?>

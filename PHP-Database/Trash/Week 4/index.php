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

$sql = "SELECT * FROM world.Country WHERE code=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $code);
$result = $conn->query($sql);

if ($stmt->execute()) {
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $data = array();
        while ($data[] = $res->fetch_assoc()) ;
        return
    }
}
return null;
$conn->close();


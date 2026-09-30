<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "messenger_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connectionn
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['userId'];
$friendId = $_GET['friendId'];

// Check if the friendship already exists
$sql_check = "SELECT * FROM friends WHERE user_id='$userId' AND friend_id='$friendId'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows == 0) {
    // Add friend
    $sql = "INSERT INTO friends (user_id, friend_id) VALUES ('$userId', '$friendId')";
    if ($conn->query($sql) === TRUE) {
        header("Location: addfriend.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    header("Location: addfriend.php");
}

$conn->close();
?>

<?php
$servername = "localhost";
$username = "root";  // default username for localhost is root
$password = "";  // default password is empty
$dbname = "messenger_db";  // replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email and password from form
$email = $_POST['email'];  // Changed from 'username' to 'email'
$pass = $_POST['password'];

// SQL query to check if account exists
$sql = "SELECT * FROM users WHERE email='$email'";  // Updated column name to 'email'
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($pass, $row['password'])) {
        // Correct password, redirect to chat list page
        header("Location: http://localhost/ChatConnect/chat.php");
        exit();
    
    }
} else {
    // Account does not exist
    echo "<script>alert('Your account is not created. Please sign up your account.');</script>";
    echo "<script>window.location.href = 'http://localhost/ChatConnect/signup.php';</script>";
}

$conn->close();
?>

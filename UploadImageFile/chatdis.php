<?php
// Database connection
require 'connection.php';

// Insert data into the database
if(isset($_POST["submit"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];
    $time = date("Y-m-d H:i:s");
    $image = ""; // Initialize image variable

    if($_FILES["image"]["error"] == 4) {
        echo "<script>alert('Image Does Not Exist');</script>";
    } else {
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = explode('.', $fileName);
        $imageExtension = strtolower(end($imageExtension));
        
        if (!in_array($imageExtension, $validImageExtension)) {
            echo "<script>alert('Invalid Image Extension');</script>";
        } elseif ($fileSize > 1000000) {
            echo "<script>alert('Image Size Is Too Large');</script>";
        } else {
            $newImageName = uniqid();
            $newImageName .= '.' . $imageExtension;

            move_uploaded_file($tmpName, 'img/' . $newImageName);
            $image = $newImageName;

            // Insert data into database
            $query = "INSERT INTO chats (name, email, time, message, image) 
                      VALUES ('$name', '$email', '$time', '$message', '$image')";
            mysqli_query($conn, $query);

            echo "<script>alert('Message Sent Successfully');</script>";
        }
    }
}

// Fetch and display data from the database
$rows = mysqli_query($conn, "SELECT * FROM chats ORDER BY time DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messenger Group Chat</title>
<style>
    body {
        font-family: Arial, sans-serif;
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        background-color: #4267B2;
        color: white;
    }

    header h1 {
        margin: 0;
        font-size: 24px;
    }

    .header-buttons a {
        margin-left: 15px;
        text-decoration: none;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        border: 1px solid white;
    }

    .header-buttons a:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    form {
        max-width: 400px;
        margin: 20px auto;
        background: #fff; /* Background color for the form box */
        padding: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    input[type="text"],
    input[type="email"],
    input[type="file"],
    textarea,
    input[type="submit"] {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
        text-align: left;
    }

    input[type="submit"] {
        background-color: #0084FF;
        border: none;
        cursor: pointer;
        color: white; /* Text color for the submit button */
    }

    h2 {
        text-align: center;
    }

    footer {
        background-color: #333;
        color: white;
        text-align: center;
        padding: 10px 0;
        width: 100%;
        position: relative;
        bottom: 0;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    th {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>

<header>
    <h1>Facebook Messenger Database Management System</h1>
    <div class="header-buttons">
        <a href="#">Home</a>
        <a href="#">Sign In</a>
        <a href="#">Sign Up</a>
        <a href="#">Admin</a>
        <a href="#">Ads</a>
        <a href="#">About</a>
    </div>
</header>

<form method="POST" enctype="multipart/form-data">
    <label for="name">Your Name:</label>
    <input type="text" id="name" name="name" required><br><br>
    <label for="email">Your Email:</label>
    <input type="email" id="email" name="email" required><br><br>
    <label for="message">Your Message:</label>
    <textarea id="message" name="message" rows="4" required></textarea><br><br>
    <label for="image">Upload Image:</label>
    <input type="file" id="image" name="image"><br><br>
    <input type="submit" name="submit" value="Send">
</form>

<h2>Chat History</h2>
<table>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Time</th>
        <th>Message</th>
        <th>Image</th>
    </tr>

    <?php $i = 1; ?>
    <?php foreach ($rows as $row) : ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $row["name"]; ?></td>
            <td><?php echo $row["email"]; ?></td>
            <td><?php echo $row["time"]; ?></td>
            <td><?php echo $row["message"]; ?></td>
            <td><?php if (!empty($row["image"])) echo "<img src='img/{$row["image"]}' width='100'>"; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<footer>
    Developed by Fahim, Rakib, Sajjad
</footer>

</body>
</html>

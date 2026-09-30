<?php
// Database connection
$servername = "localhost"; // Assuming your database server is localhost
$username = "root"; // Assuming your database username is root
$password = ""; // Assuming your database password is empty
$database = "messenger_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert data into the database
if(isset($_POST["submit"])) {
    $message = $_POST["message"];
    $call = $_POST["call"];
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
            $sql = "INSERT INTO chats (image, message, call) 
                    VALUES ('$image', '$message', '$call')";
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Message Sent Successfully');</script>";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

// Fetch and display data from the database
$rows = $conn->query("SELECT * FROM chats ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messenger Group Chat</title>
<style>
    /* Your CSS styles */
</style>
</head>
<body>

<form method="POST" enctype="multipart/form-data">
    <label for="message">Your Message:</label>
    <textarea id="message" name="message" rows="4" required></textarea><br><br>
    <label for="call">Call:</label>
    <input type="text" id="call" name="call"><br><br>
    <label for="image">Upload Image:</label>
    <input type="file" id="image" name="image"><br><br>
    <input type="submit" name="submit" value="Send">
</form>

<h2>Chat History</h2>
<table>
    <tr>
        <th>#</th>
        <th>Image</th>
        <th>Message</th>
        <th>Call</th>
    </tr>

    <?php $i = 1; ?>
    <?php while ($row = $rows->fetch_assoc()) : ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td><?php echo $row["image"]; ?></td>
            <td><?php echo $row["message"]; ?></td>
            <td><?php echo $row["call"]; ?></td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>


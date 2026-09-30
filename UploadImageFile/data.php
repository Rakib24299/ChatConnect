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
    $person_name = $_POST["person_name"];
    $email = $_POST["email"];
    $name = $_POST["name"];
    $price = $_POST["price"];
    $fees = $_POST["fees"];
    $date = date("Y-m-d");
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
            $query = "INSERT INTO tb_ads (person_name, email, name, price, fees, Date, image) 
                      VALUES ('$person_name', '$email', '$name', '$price', '$fees', '$date', '$image')";
            if ($conn->query($query) === TRUE) {
                echo "<script>alert('Successfully Added');</script>";
            } else {
                echo "Error: " . $query . "<br>" . $conn->error;
            }
        }
    }
}

// Fetch and display data from the database
$rows = $conn->query("SELECT * FROM tb_ads ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Messenger Account</title>
    <style>
        /* Your CSS styles */
    </style>
</head>

<body>
    <!-- Your HTML content -->
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>





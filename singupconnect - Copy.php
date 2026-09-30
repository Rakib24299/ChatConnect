<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "messenger_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];
$gender = $_POST['gender'];
$birthdate = $_POST['birthdate'];

// Check if account already exists
$sql_check = "SELECT * FROM users WHERE email='$email'";
$result_check = $conn->query($sql_check);

if ($result_check->num_rows > 0) {
    echo "<!DOCTYPE html>
          <html lang='en'>
          <head>
              <meta charset='UTF-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1.0'>
              <title>Messenger Account</title>
              <style>
                  body {
                      font-family: Arial, sans-serif;
                      display: flex;
                      justify-content: center;
                      align-items: center;
                      height: 100vh;
                      background-color: #f4f4f4;
                  }

                  .message {
                      text-align: center;
                      padding: 20px;
                      background-color: #ffffff;
                      border-radius: 5px;
                      box-shadow: 0 0 10px rgba(0,0,0,0.1);
                  }

                  .btn {
                      background-color: #0084FF;
                      color: white;
                      padding: 10px 20px;
                      border: none;
                      border-radius: 5px;
                      cursor: pointer;
                      text-decoration: none;
                  }
              </style>
          </head>
          <body>
              <div class='message'>
                  <h2>Your ID is already created.</h2>
                  <a href='http://localhost/ChatConnect/singin.php' class='btn'>Sign In</a>
              </div>
          </body>
          </html>";
} else {
    // Create new account
    $sql = "INSERT INTO users (first_name, last_name, email, password, gender, birthdate) 
            VALUES ('$firstName', '$lastName', '$email', '$password', '$gender', '$birthdate')";

    if ($conn->query($sql) === TRUE) {
        echo "<!DOCTYPE html>
              <html lang='en'>
              <head>
                  <meta charset='UTF-8'>
                  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                  <title>Messenger Account</title>
                  <style>
                      body {
                          font-family: Arial, sans-serif;
                          display: flex;
                          justify-content: center;
                          align-items: center;
                          height: 100vh;
                          background-color: #f4f4f4;
                      }

                      .message {
                          text-align: center;
                          padding: 20px;
                          background-color: #ffffff;
                          border-radius: 5px;
                          box-shadow: 0 0 10px rgba(0,0,0,0.1);
                      }

                      .btn {
                          background-color: #0084FF;
                          color: white;
                          padding: 10px 20px;
                          border: none;
                          border-radius: 5px;
                          cursor: pointer;
                          text-decoration: none;
                      }
                  </style>
              </head>
              <body>
                  <div class='message'>
                      <h2>Welcome to our Messenger.</h2>
                      <p>Now you can sign in and enjoy your moments.</p>
                      <a href='http://localhost/ChatConnect/singin.php' class='btn'>Sign In</a>
                  </div>
              </body>
              </html>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

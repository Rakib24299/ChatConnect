<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Messenger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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

        .container {
            padding: 20px;
            text-align: center;
            border: 1px solid #e0e0e0; /* Box border */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Shadow */
            max-width: 400px; /* Max width for the container */
            margin: 40px auto; /* Center the container */
        }

        form {
            text-align: left; /* Align form elements to the left */
        }

        form label,
        form input {
            margin-bottom: 15px; /* Spacing between form elements */
            display: block; /* Display elements as block */
        }

        form input[type="text"],
        form input[type="password"] {
            width: 100%; /* Full width input fields */
            padding: 8px; /* Padding for input fields */
            border-radius: 4px; /* Rounded corners for input fields */
            border: 1px solid #ccc; /* Border for input fields */
        }

        form input[type="submit"] {
            background-color: #4267B2; /* Button background color */
            color: white; /* Button text color */
            border: none; /* Remove border */
            padding: 10px 20px; /* Padding for the button */
            border-radius: 4px; /* Rounded corners for the button */
            cursor: pointer; /* Cursor style */
            transition: background-color 0.3s; /* Smooth transition for button hover effect */
        }

        form input[type="submit"]:hover {
            background-color: #2a4a7b; /* Darker background color on hover */
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>

    <header>
        <h1>Facebook Messenger Database Management System</h1>
        <div class="header-buttons">
            <a href="#">Sign In</a>
            <a href="http://localhost/ChatConnect/singup.php">Sign Up</a>
            <a href="#">Admin</a>
            <a href="#">About</a>
        </div>
    </header>

    <div class="container">
        <h2>Sign In to Messenger</h2>
        <form action="http://localhost/ChatConnect/loginconect.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <input type="submit" value="Sign In">
        </form>
    </div>

    <footer>
        Made by Fahim, Rakib, Sajjad
    </footer>

</body>

</html>

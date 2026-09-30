<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Messenger Account</title>
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

        form {
            max-width: 400px;
            margin: 20px auto;
            background: #fff; /* Background color for the form box */
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select,
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
            position: absolute;
            bottom: 0;
        }
    </style>
</head>

<body>

<header>
    <h1>Facebook Messenger Database Management System</h1>
    <div class="header-buttons">
        <a href="#">Sign In</a>
        <a href="#">Sign Up</a>
        <a href="#">Admin</a>
        <a href="#">About</a>
    </div>
</header>

<form action="http://localhost/ChatConnect/singupconnect.php" method="POST">
    <h2>Create Messenger Account</h2>
    <label for="firstName">First Name:</label>
    <input type="text" id="firstName" name="firstName" required><br>

    <label for="lastName">Last Name:</label>
    <input type="text" id="lastName" name="lastName" required><br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <label for="gender">Gender:</label>
    <select id="gender" name="gender">
        <option value="male">Male</option>
        <option value="female">Female</option>
        <option value="other">Other</option>
    </select><br>

    <label for="birthdate">Birthdate:</label>
    <input type="date" id="birthdate" name="birthdate" required><br>

    <input type="submit" value="Create Account">
</form>

<footer>
    Made by Fahim, Rakib, Sajjad
</footer>

</body>

</html>

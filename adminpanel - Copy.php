<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333333;
        }
        .nav {
            background-color: #e0e0e0;
            padding: 10px 0;
            border-radius: 5px;
        }
        .nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        .nav ul li {
            margin: 0 15px;
        }
        .nav ul li a {
            text-decoration: none;
            color: #333333;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }
        .nav ul li a:hover {
            background-color: #d0d0d0;
        }
        .content {
            padding: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #777777;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Admin Panel</h1>
            <div class="nav">
                <ul>
                    <li><a href="#">Dashboard</a></li>
                    <li><a href="#">Users</a></li>
                    <li><a href="#">Products</a></li>
                    <li><a href="#">Settings</a></li>
                </ul>
            </div>
        </div>
        <div class="content">
            <!-- Admin panel content will go here -->
            <img src="path_to_your_image.jpg" alt="Admin Image" style="max-width: 100%; height: auto;">
        </div>
        <div class="footer">
            Made by Fahim, Rakib, and Sajjad
        </div>
    </div>
</body>
</html>
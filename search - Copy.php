<!DOCTYPE html>
<html>
<head>
    <title>Search Page</title>
    <style>
        table, td, th {
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <center>
        <h1>Search Messenger Login Member</h1><br/>

        <form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            Username: <input type="text" name="username"><br/><br/>
            <input type="submit" value="Search">
        </form>
        <br><br><br>

        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "messenger_db";

        $conn = mysqli_connect($servername, $username, $password, $dbname);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $sql = "SELECT * FROM users";
        if (isset($_GET['username']) && !empty($_GET['username'])) {
            $username = $_GET['username'];
            $sql .= " WHERE username='$username'";
        }

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<table>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Password</th>
                    </tr>";

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>".$row["id"]."</td>
                        <td>".$row["username"]."</td>
                        <td>*****</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "0 results";
        }

        mysqli_close($conn);
        ?>
    </center>
</body>
</html>

<?php
session_start();
include("hash.php");
include("connection.php");

if(isset($_POST['button'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $status = $_POST['status'];

    $sql = "INSERT INTO Users (UserName, UserEmail, UserPassword, UserStatus)
    VALUES ('$username', '$email', '$hashed', '$status')";

    if ($conn->query($sql) === TRUE) {
        echo "Account Added";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        
        <title>Sign up | Unify</title>
        <link rel="stylesheet" href="style.css">

    </head>

    <body>

        <main>
            <h1>Welcome to Unify</h1>
            <p class="intro">Create an account to get started.</p>
            <form action="" method="post">
                <div class="field">
                    <label for="name">Name</label>
                    <input id="username" name="username" type="name" placeholder="name" required>
                </div>
                <div class="field">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email" placeholder="email" required>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="password" required>
                </div>

                <select name="status" id="status">
                    <option value="">--- User Type: ---</option>
                    <option value="1">Student</option>
                    <option value="2">Staff</option>
                    <option value="3">Company</option>
                </select>
                
                <button type="submit" name="button">Sign up</button>
            </form> 
            
            <p class="signup">Already have an account? <a href="login.php">Log in</a></p>
        </main>
        
    </body>
</html>


<!-- Add entry to accounts table when signed up -->
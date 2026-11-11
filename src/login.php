<?php
session_start();
include("hash.php");
include("connection.php");

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : ''; 

    if (empty($email) || empty($password)) {

        $error = 'Please provide email and password.';
    }
    else {
        if (isset($conn)) {
            $stmt = $conn->prepare('SELECT UserID, UserName, UserPassword FROM users WHERE UserEmail = ? LIMIT 1');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
                
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $verified = passwordVerify($password, $user['UserPassword']);
                if ($verified) {
                    $_SESSION['user_id'] = $user['UserID'];
                    $_SESSION['username'] = $user['UserName'];
                    setcookie('login_user', $user['UserName'], time() + 604800, '/', '', false, true);
                    setcookie('login_user_id', $user['UserID'], time() + 604800, '/', '', false, true);

                    echo "Log In Complete";

                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'Invalid email or password.';
            }
            $stmt->close();
        }
        else {
            $error = 'Database connection not available.';
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
	<html lang="en">
		<head>
			
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">

			
			<title>Sign in | Unify</title>
			<link rel="stylesheet" href="style.css">

		</head>

		<!--Top bar (navigation) -->
		<div class="top-bar">
            <nav class="nav-navbar">
                <a href="index.html" class="logo-link">
                <img src="img/logo.png" alt="Unify Logo" style="width:80px; height:80px;">
                </a>
                <div class="logo">Unify</div>
                
                <ul class="nav-links">
                    <li><a href="#">1</a></li>
                    <li><a href="#">2</a></li>
                    <li><a href="login.html">Login</a></li>
                </ul>
            </nav>
        </div>

		<!-- main form-->
		<body>
			<main>
				<h1>Sign in to continue to Unify.</h1>
				<form action="" method="post">
					<div class="field">
						<label for="email">Email address</label>
						<input id="email" name="email" type="email" placeholder="email" required>
					</div>
					<div class="field">
						<label for="password">Password</label>
						<input id="password" name="password" type="password" placeholder="password" required>
					</div>
					
					<p><a href="#">Forgot password?</a></p>

					<button type="submit">Sign in</button>
				</form>
				
				<p class="signup">New to Unify? <a href="signup.html">Create an account</a></p>
			</main>
			
		</body>
	</html>
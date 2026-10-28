<?php
include 'connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// assuming the database exists - cant test this until the database is created and populated with data
// will need editing to work with the new database, this is just ripped from an old project

$error = '';
    // read button data
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : ''; // hash is ignored for simplicity

        if (empty($username) || empty($password)) {

            $error = 'Please provide username and password.';
        }
        else {
            // $con is provided by connection.php
            if (isset($con)) {
                $stmt = $con->prepare('SELECT STUDENTID, username, password FROM STUDENTS WHERE username = ? LIMIT 1');
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    if ($user['password'] === $password) {
                        $_SESSION['user_id'] = $user['STUDENTID'];
                        $_SESSION['username'] = $user['username'];
                        // sets the cookie for login
                        setcookie('login_user', $user['username'], time() + 604800, '/', '', false, true);

                        header('Location: StudentPage.php');
                        exit;
                    } else {
                        $error = 'Invalid username or password.';
                    }
                } else {
                    $error = 'Invalid username or password.';
                }
                $stmt->close();
            }
            else {
                $error = 'Database connection not available.';
            }
        }
    }

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
    
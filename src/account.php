<?php
include("connection.php"); // provides $con

// this only works for viewing the account of the currently logged in user
$userID = filter_input(INPUT_COOKIE, 'login_user_id', FILTER_VALIDATE_INT);
$user = null;
$loadError = false;

if ($userID !== false && $userID !== null && $userID > 0) {
    try {
        // fetch basic details from Users
        $query = $con->prepare( 
            'SELECT UserName, UserEmail, UserStatus, profile_picture FROM Users WHERE UserID = ? LIMIT 1'
        );
        $query->bind_param('i', $userID);
        $query->execute();
        $result = $query->get_result();
        $user = $result->fetch_assoc();
        $query->close();


        // fetch bio from account table if it exists
        if ($user) {
            $bioQuery = $con->prepare(
                'SELECT Bio FROM Account WHERE UserID = ? LIMIT 1'
            );
            $bioQuery->bind_param('i', $userID);
            $bioQuery->execute();
            $bioResult = $bioQuery->get_result();
            $bio = $bioResult->fetch_assoc();
            $user['Bio'] = $bio['Bio'] ?? '';
            $bioQuery->close();
        }
    } catch (Throwable $exception) {
        $loadError = true;
    }
}

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
</head>

<body>



    <!--Top bar -->
    <header>
        <h1>My App</h1>
        <nav class="nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="account.php">Account</a></li>
            </ul>
        </nav>
    </header>



    <?php if ($loadError): ?>
        <p>Unable to load account information.</p>
    <?php elseif (!$user): ?>
        <p><?= empty($userID) ? '<a href="login.php">Log in</a> to view your account.' : 'Account not found.' ?></p> 
    <?php else: ?>
        <!-- Account details are loaded using the userID cookie set at login. -->
        <div class="details">
        
            <div class="pfp">
                <?php
                // adds a fallback image if the profile picture is not set or the file does not exist
                $profilePicture = $user['profile_picture'] ?? '';
                if ($profilePicture === '' || !is_file(__DIR__ . '/' . ltrim($profilePicture, '/\\'))) {
                    $profilePicture = 'img/account_default.png';
                }
                ?>
                <img src="<?= escapeHtml($profilePicture) ?>" alt="Profile Picture" width="526" height="526" style="width:526px; height:526px; object-fit:cover;">
            </div>
            <div class="info">
                <h2><?= escapeHtml($user['name']) ?></h2>
                <p>Email: <?= escapeHtml($user['email']) ?></p>
                <p>Account Type: <?= escapeHtml($user['account_type']) ?></p>
            </div>
        </div>

        <div class="bio">
            <h3>Bio</h3>
            <p><?= nl2br(escapeHtml($user['Bio'] ?? '')) ?></p>
        </div>
    <?php endif; ?>
</body>
</html>
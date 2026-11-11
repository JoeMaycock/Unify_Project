<?php
include("connection.php"); // provides $con

$userID = filter_input(INPUT_COOKIE, 'login_user_id', FILTER_VALIDATE_INT);
$user = null;
$loadError = false;

if ($userID !== false && $userID !== null && $userID > 0) {
    try {
        $query = $con->prepare(
            ''
        );
        $query->bind_param('i', $userID);
        $query->execute();
        $result = $query->get_result();
        $user = $result->fetch_assoc();
        $query->close();
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
                <img src="<?= escapeHtml($user['profile_picture'] ?: 'img/pfp.png') ?>" alt="Profile Picture" style="width:100px; height:100px;">
            </div>
            <div class="info">
                <h2><?= escapeHtml($user['name']) ?></h2>
                <p>Email: <?= escapeHtml($user['email']) ?></p>
                <p>Account Type: <?= escapeHtml($user['account_type']) ?></p>
            </div>
        </div>

        <div class="bio">
            <h3>Bio</h3>
            <p><?= nl2br(escapeHtml($user['bio'])) ?></p>
        </div>
    <?php endif; ?>
</body>
</html>
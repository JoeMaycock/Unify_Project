<?php
session_start();

$conn = null;
if (file_exists(__DIR__ . '/connection.php')) {
    require_once __DIR__ . '/connection.php';
}

$userID = filter_input(INPUT_COOKIE, 'login_user_id', FILTER_VALIDATE_INT);
$user = null;
$loadError = false;

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if ($conn === null) {
    $loadError = true;
} elseif ($userID !== false && $userID !== null && $userID > 0) {
    try {
        $query = $conn->prepare(
            'SELECT UserName, UserEmail, UserStatus FROM Users WHERE UserID = ? LIMIT 1'
        );
        $query->bind_param('i', $userID);
        $query->execute();
        $result = $query->get_result();
        $user = $result->fetch_assoc();
        $query->close();

        if ($user) {
            $bioQuery = $conn->prepare(
                'SELECT Bio FROM Accounts WHERE UserID = ? LIMIT 1'
            );
            $bioQuery->bind_param('i', $userID);
            $bioQuery->execute();
            $bioResult = $bioQuery->get_result();
            $bio = $bioResult->fetch_assoc();
            $user['Bio'] = $bio['Bio'] ?? '';
            $bioQuery->close();

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {
                $newUsername = trim($_POST['username'] ?? '');
                $newEmail = trim($_POST['email'] ?? '');
                $newBio = trim($_POST['bio'] ?? '');

                if ($newUsername === '' || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                    $loadError = true;
                } else {
                    if (strlen($newBio) > 1024) {
                        $newBio = substr($newBio, 0, 1024);
                    }

                    $updateUserQuery = $conn->prepare(
                        'UPDATE Users SET UserName = ?, UserEmail = ? WHERE UserID = ?'
                    );
                    $updateUserQuery->bind_param('ssi', $newUsername, $newEmail, $userID);
                    $updateUserQuery->execute();
                    $updateUserQuery->close();

                    $updateAccountQuery = $conn->prepare(
                        'UPDATE Accounts SET Bio = ? WHERE UserID = ?'
                    );
                    $updateAccountQuery->bind_param('si', $newBio, $userID);
                    $updateAccountQuery->execute();
                    $updateAccountQuery->close();

                    header('Location: account.php');
                    exit;
                }
            }
        }
    } catch (Throwable $exception) {
        error_log('Account edit error: ' . $exception->getMessage());
        $loadError = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
</head>
<body>
    <div class="account-edit">
        <h1>Edit Account</h1>

        <?php if ($loadError): ?>
            <p>Unable to load or update account information. Please check the database connection and try again.</p>
        <?php elseif (!$user): ?>
            <p><?= empty($userID) ? '<a href="login.php">Log in</a> to view your account.' : 'Account not found.' ?></p>
        <?php else: ?>
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <label for="fileToUpload">Select image to upload:</label>
                <input type="file" name="fileToUpload" id="fileToUpload">
                <input type="submit" value="Upload Image" name="submit">
            </form>

            <form action="account_edit.php" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= escapeHtml($user['UserName']) ?>" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= escapeHtml($user['UserEmail']) ?>" required><br><br>

                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio" maxlength="1024" oninput="updateBioRemaining()"><?= escapeHtml($user['Bio']) ?></textarea>
                <span id="bio-remaining" aria-live="polite">1024 characters remaining</span><br><br>

                <input type="submit" name="save_changes" value="Save Changes">
            </form>

            <script>
                function updateBioRemaining() {
                    const bio = document.getElementById('bio');
                    const remaining = 1024 - bio.value.length;
                    document.getElementById('bio-remaining').textContent =
                        remaining + ' characters remaining';
                }

                updateBioRemaining();
            </script>
        <?php endif; ?>
    </div>
</body>
</html>

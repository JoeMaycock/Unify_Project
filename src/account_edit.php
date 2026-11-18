<?php
session_start();
include("connection.php"); // provides $conn



// this only works for viewing the account of the currently logged in user
$userID = filter_input(INPUT_COOKIE, 'login_user_id', FILTER_VALIDATE_INT);
$user = null;
$loadError = false;

if ($userID !== false && $userID !== null && $userID > 0) 
    try {
        // fetch basic details from Users
        $query = $conn->prepare( 
            'SELECT UserName, UserEmail, UserStatus FROM Users WHERE UserID = ? LIMIT 1'
        );
        $query->bind_param('i', $userID);
        $query->execute();
        $result = $query->get_result();
        $user = $result->fetch_assoc();
        $query->close();


        // fetch bio from account table if it exists
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
        }

    if(isset($_POST['save_changes'])) { {
        $newUsername = $_POST['username'];
        $newEmail = $_POST['email'];
        $newBio = $_POST['bio'];

        // Update Users table
        $updateUserQuery = $conn->prepare(
            'UPDATE Users SET UserName = ?, UserEmail = ? WHERE UserID = ?'
        );
        $updateUserQuery->bind_param('ssi', $newUsername, $newEmail, $userID);
        $updateUserQuery->execute();
        $updateUserQuery->close();

        // Update Accounts table
        $updateAccountQuery = $conn->prepare(
            'UPDATE Accounts SET Bio = ? WHERE UserID = ?'
        );
        $updateAccountQuery->bind_param('si', $newBio, $userID);
        $updateAccountQuery->execute();
        $updateAccountQuery->close();

        // Refresh user data after update
        header("Location: account.php");
        exit;
    }
    } catch (Throwable $exception) {
        echo '<p>' . escapeHtml($exception->getMessage()) . '</p>'; // debug
        $loadError = true;
    }
}

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); 
}
?>

    <?php if ($loadError): ?>
        <p>Unable to load account information.</p>
    <?php elseif (!$user): ?>
        <p><?= empty($userID) ? '<a href="login.php">Log in</a> to view your account.' : 'Account not found.' ?></p> 
    <?php else: ?>

<html>
    <head>
        <title>Edit Account</title>
    </head>




    <div class="account-edit">
        <h1>Edit Account</h1>

<!-- Image Upload Form -->
        <form action="upload.php" method="post" enctype="multipart/form-data">
            Select image to upload:
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Upload Image" name="submit">
        </form>
        <!-- Account Edit Form -->
        <form action="account_edit.php" method="post">


            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['UserName']); ?>" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['UserEmail']); ?>" required><br><br>



            <label for="bio">Bio:</label> 
            <textarea id="bio" name="bio" maxlength="1024" oninput="updateBioRemaining() "><?php echo htmlspecialchars($user['Bio']); ?></textarea>
            <span id="bio-remaining" aria-live="polite">1024 characters remaining</span><br><br>
            <script>
                function updateBioRemaining() {
                    const bio = document.getElementById('bio');
                    const remaining = 1024 - bio.value.length;
                    document.getElementById('bio-remaining').textContent = remaining + ' characters remaining';
                }

                updateBioRemaining();
            </script>




            <input type="submit" value="Save Changes">

        </form>
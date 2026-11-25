<?php
session_start();

$conn = null;
if (file_exists('connection.php')) {
    require_once 'connection.php';
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
                    // Update the profile picture if a new one was uploaded
                    if (isset($newProfilePicture)) {
                        $updateprofilePictureQuery = $conn->prepare(
                            'UPDATE Accounts SET ProfilePicture = ? WHERE UserID = ?'
                        );
                        $updateprofilePictureQuery->bind_param('si', $newProfilePicture, $userID);
                        $updateprofilePictureQuery->execute();
                        $updateprofilePictureQuery->close();
                    }
                    header('Location: account.php');
                    exit;
                    
                }

              
                }
                // Handle profile picture upload
                if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['Upload_Image'])) {
                    $targetDir = 'img/';
                    $targetFile = $targetDir . basename($_FILES['fileToUpload']['name']);
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                    // Check if image file is a actual image or fake image
                    $check = getimagesize($_FILES['fileToUpload']['tmp_name']);
                    if ($check !== false) {
                        echo 'File is an image - ' . $check['mime'] . '.';
                        $uploadOk = 1;
                    } else {
                        echo 'File is not an image.';
                        $uploadOk = 0;
                    }

                    // Check if file already exists, replaces the existing file
                    if (file_exists($targetFile)) {
                        // Optionally, you can delete the existing file or handle it as needed
                        unlink($targetFile); // Deletes the existing file
                    }

                    // Check file size
                    if ($_FILES['fileToUpload']['size'] > 500000) {
                        echo 'Sorry, your file is too large.';
                        $uploadOk = 0;
                    }

                    // Allow certain file formats
                    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg'])) {
                        echo 'Sorry, only JPG, JPEG, PNG files are allowed.';
                        $uploadOk = 0;
                    }

                    // Check if $uploadOk is set to 0 by an error
                    if ($uploadOk === 0) {
                        echo 'Sorry, your file was not uploaded.';
                    } else {
                        // changes the display photo, but doesnt update the actual image until the user clicks save changes
                        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile)) {
                            echo 'The file ' . htmlspecialchars(basename($_FILES['fileToUpload']['name'])) . ' has been uploaded.';
                            $newProfilePicture = $targetFile;
                        } else {
                            echo 'Sorry, there was an error uploading your file.';
                        }
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

            <!-- Display the current profile picture -->
            <div class="current-profile-picture">
                <?php
                if (isset($newProfilePicture)) {
                    $profilePicture = $newProfilePicture;
                } else {
                    $profilePicture = $user['ProfilePicture'] ?? '';
                }
                if ($profilePicture === '' || !is_file(__DIR__ . '/' . ltrim($profilePicture, '/\\'))) {
                    $profilePicture = 'img/account_default.png';
                }
                ?>
                <img src="<?= escapeHtml($profilePicture) ?>" alt="Current Profile Picture" width="150" height="150">


                <div class="upload-form">
                    <form action="account_edit.php" method="post" enctype="multipart/form-data">
                        <label for="fileToUpload">Select image to upload:</label>
                        <input type="file" name="fileToUpload" id="fileToUpload">
                        <input type="submit" value="Upload Image" name="Upload_Image">
                    </form>
                </div>
            </div>
            

            <form action="account_edit.php" method="post">
                <div class="name-and-email">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?= escapeHtml($user['UserName']) ?>" required><br><br>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= escapeHtml($user['UserEmail']) ?>" required><br><br>
                </div>
                <div class="bio-section">
                    <label for="bio">Bio:</label>
                    <textarea id="bio" name="bio" maxlength="1024" oninput="updateBioRemaining()"><?= escapeHtml($user['Bio']) ?></textarea>
                    <span id="bio-remaining" aria-live="polite">1024 characters remaining</span><br><br>
                </div>
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

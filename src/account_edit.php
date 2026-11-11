<?php
session_start();
include("connection.php"); // provides $con

if ($userID !== false && $userID !== null && $userID > 0) {
    try {
        // fetch basic details from Users
        $getinfoquery = $con->prepare( 
            'SELECT UserName, UserEmail, UserStatus FROM Users WHERE UserID = ? LIMIT 1'
        );
        $getinfoquery->bind_param('i', $userID);
        $getinfoquery->execute();
        $result = $getinfoquery->get_result();
        $user = $result->fetch_assoc();
        $getinfoquery->close();


        $getbioquery = $con->prepare(
            'SELECT Bio FROM Account WHERE UserID = ? LIMIT 1'
        );
        $getbioquery->bind_param('i', $userID);
        $getbioquery->execute();
        $bioResult = $getbioquery->get_result();
        $bio = $bioResult->fetch_assoc();
        $user['Bio'] = $bio['Bio'] ?? '';
        $getbioquery->close();
        
            
    } catch (Throwable $exception) {
        $loadError = true;
    }

?>



<html>
    <head>
        <title>Edit Account</title>
    </head>




    <div class="account-edit">
        <h1>Edit Account</h1>
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
<html>
    <body>
        <?php

        function passwordHash($password) {
            return password_hash($password, PASSWORD_DEFAULT);
        }

        function passwordVerify($password, $hash){
            return password_verify($password, $hash);
        }

        $placeholder = "placeholder password";

        $hash = passwordHash($placeholder);

        if (passwordVerify($placeholder, $hash)) {
            echo "Verified";
        } else {
            echo "Incorrect";
        }

        ?>
    </body>
</html>

<!-- 
phpMyAdmin details

$conn=mysqli_connect('localhost', 'charlie.burton', '54H4PDBC', 'charlieburton_Unify');
->
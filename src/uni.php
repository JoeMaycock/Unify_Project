<?php
session_start();
include("connection.php");


$id = $_GET['id'];

$sql = "SELECT UniName, UniDescription, UkRank FROM University WHERE UniID = '$id'"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles.css">
    </head>
    
    <body>
        <main>
            <?php
                echo"<h1>" . $row['UniName'] . "</h1>";
                echo"<p>" . $row['UniDescription'] . "</p>";
                echo"<p>" . $row['UniName'] . " is ranked as the number " . $row['UkRank'] . " university in the Uk by Times Higher Education.</p>";
                echo'<a href="review.php"> see reviews for ' . $row['UniName'] . " university here</a>";
            ?>
        </main>
    </body>
</html>

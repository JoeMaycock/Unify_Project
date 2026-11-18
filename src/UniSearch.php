<?php
session_start();
include("connection.php");

if (isset($_POST['university'])) {

	$university = $_POST['university'];

	$sql = "SELECT UniID, UniName FROM University WHERE UniName LIKE '%$university%'";
	$result = $conn->query($sql);
	
	// www.w3schools.com/php/php_mysql_select.asp
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
        // https://stackoverflow.com/questions/14179765/display-data-from-database-and-link-it-to-new-page
		echo "<a href='uni.php?id=" . $row['UniID'] . "'>". $row['UniName'] . "</a><br>";
		}
	} else {
	echo "0 results";
	}
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" href="style.css">

    </head>

    <body>

        <main>
            <p class="intro">Search Universities</p>
            
            <form action="" method="post">
                <div class="field">
                    <label for="university">University</label>
                    <input id="university" name="university" type="text" placeholder="university" required>
                </div>
                <button type="submit" name="search">Search</button>
            </form> 
        </main>
        
    </body>
</html>

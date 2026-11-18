<?php
session_start();
include("connection.php");

$sql = "SELECT EventID, title FROM Events";
$result = $conn->query($sql);

if(isset($_POST['button'])) {
    $name = $_POST['name'];
    $eventID = $_POST['events'];

    $sql2 = "INSERT INTO EventAttendance (name, EventID)
    VALUES ('$name', '$eventID')";

    if ($conn->query($sql2) === TRUE) {
        echo "Attendance Noted";
    } else {
        echo "Error: " . $sql2 . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <body>
        <form action="" method="post">
            <!-- https://www.phptutorial.net/php-tutorial/php-select-option/ -->
            <label for="events">Look for events:</label>
            <select name="events" id="events">
                <option value="">--- Select an event ---</option>

                <?php
                // https://www.w3schools.com/php/php_mysql_select.asp
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<option value=" . $row['EventID'] . ">" . $row['title'] . "</option>";
                        }
                    } else {
                        echo "No events";
                    }
                ?>
            </select>

            <label for="name">Enter your name:</label>
            <input id="name" name="name" type="name" placeholder="name" required>

            <button type="submit" name="button">Submit</button>
        </form>
    </body>
</html>

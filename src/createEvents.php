<?php
session_start();
include("connection.php");

if(isset($_POST['button'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $capacity = $_POST['capacity'];
    $time = $_POST['time'];
    $tags = $_POST['tags'];

    $sql = "INSERT INTO events (title, description, capacity, time)
    VALUES ('$title', '$description', '$capacity', '$time')";

    if ($conn->query($sql) === TRUE) {
        echo "Event Added";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <body>
        <form action="" method="post">
                <label for="name">Event Name</label>
                <input id="title" name="title" type="title" placeholder="Event Name" required>

                <label for="description">Event Description</label>
                <input id="description" name="description" type="description" placeholder="Event Description" required>

                <label for="capacity">Event Capacity</label>
                <input id="capacity" name="capacity" type="capacity" placeholder="Capacity" required>

                <label for="time">Event Date & Time</label>
                <input id="time" name="time" type="datetime-local" placeholder="Date & Time" required>

                <label for="tags">Tags</label>
                <input id="tags" name="tags" type="tags" placeholder="tags" required>
                
            <button type="submit" name="button">Create New Event</button>
        </form> 
    </body>
</html>
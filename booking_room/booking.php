<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room | Student Hub</title>
    <link rel="stylesheet" href="booking.css">
</head>
<body>
    <div class="container">
        <h2>Book Accommodation</h2>
        <p>Reserve a room online before your visit.</p>

        <div class="form-container">
            <form id="bookingForm">
                <label for="roomType"><b>Room Type</b></label>
                <select id="roomType" name="roomType" required>
                    <option value="">-- Select a Room --</option>
                    <option value="Single">Single Room (£50/night)</option>
                    <option value="Double">Double Room (£80/night)</option>
                    <option value="Accessible">Accessible Room (£50/night)</option>
                </select>

                <label for="checkIn"><b>Check-in Date</b></label>
                <input type="date" id="checkIn" name="checkIn" required>

                <label for="checkOut"><b>Check-out Date</b></label>
                <input type="date" id="checkOut" name="checkOut" required>

                <button type="submit" class="btn">Confirm Booking</button>
            </form>
        </div>
    </div>

    <script src="booking.js"></script>
</body>
</html>

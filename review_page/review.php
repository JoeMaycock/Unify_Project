<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Reviews | Student Hub</title>
    <link rel="stylesheet" href="review.css">
</head>
<body>
    <div class="container">
        <h2>University Reviews</h2>

        <div class="form-container">
            <form id="reviewForm">
                <label for="uniName"><b>University Name</b></label>
                <select id="uniName" name="uniName" required>
                    <option value="">-- Select a University --</option>
                    <option value="York St John University">York St John University</option>
                    <option value="University of Leeds">University of Leeds</option>
                </select>

                <label for="rating"><b>Rating (1-5)</b></label>
                <input type="number" id="rating" name="rating" min="1" max="5" placeholder="Enter rating 1-5" required>

                <label for="reviewText"><b>Your Review</b></label>
                <textarea id="reviewText" name="reviewText" rows="4" placeholder="Share your experience..." required></textarea>

                <button type="submit" class="btn">Submit Review</button>
            </form>
        </div>

        <h3>Recent Reviews</h3>
        <div id="reviewsContainer">
            <!-- Default Review -->
            <div class="review-card">
                <h4>York St John University</h4>
                <p><strong>Rating:</strong> 5/5</p>
                <p>"Great facilities and helpful staff!"</p>
                <button class="btn-report" onclick="reportReview(this)">Report this review</button>
            </div>
        </div>
    </div>

    <script src="review.js"></script>
</body>
</html>

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('reviewForm');
    const container = document.getElementById('reviewsContainer');

    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault(); 

            const uni = document.getElementById('uniName').value;
            const rating = document.getElementById('rating').value;
            const text = document.getElementById('reviewText').value;

            if (!uni || rating < 1 || rating > 5 || text.trim() === '') {
                alert("Please fill out all fields correctly.");
                return;
            }

            const newReview = document.createElement('div');
            newReview.className = 'review-card';
            newReview.innerHTML = `
                <h4>${uni}</h4>
                <p><strong>Rating:</strong> ${rating}/5</p>
                <p>"${text}"</p>
                <button class="btn-report" onclick="reportReview(this)">Report this review</button>
            `;

            container.insertBefore(newReview, container.firstChild);

            form.reset();
            alert("Review submitted successfully!");
        });
    }
});

function reportReview(button) {
    button.disabled = true;
    button.textContent = 'Reported to Moderators';
    button.style.backgroundColor = '#ccc';
}

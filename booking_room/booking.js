document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('bookingForm');
    const checkIn = document.getElementById('checkIn');
    const checkOut = document.getElementById('checkOut');

    const today = new Date().toISOString().split('T')[0];
    checkIn.min = today;

    if (checkIn) {
        checkIn.addEventListener('change', () => {
            checkOut.min = checkIn.value;
            if (checkOut.value && checkOut.value < checkIn.value) {
                checkOut.value = '';
            }
        });
    }

    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const room = document.getElementById('roomType').value;
            const inDate = new Date(checkIn.value);
            const outDate = new Date(checkOut.value);

            if (!room || !checkIn.value || !checkOut.value) {
                alert("Please fill in all fields.");
                return;
            }
            if (outDate <= inDate) {
                alert("Error: Check-out date must be after check-in date.");
                return;
            }

            alert(`Success! Your ${room} room is booked from ${checkIn.value} to ${checkOut.value}.`);
            form.reset();
        });
    }
});

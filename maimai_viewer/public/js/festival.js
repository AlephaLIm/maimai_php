$("document").ready(function () {
    // Get a reference to the festival form and the festival button
    const festivalForm = document.getElementById('festival_form');
    const festivalButton = document.querySelector('.festivalsubmit');

    // Define a function to send the GET request
    function sendFestivalRequest() {
        // Construct the query string parameter
        const festivalParam = 'festival=festival';

        // Build the URL for the GET request
        const url = `${festivalForm.action}?${festivalParam}`;

        // Send the GET request
        fetch(url)
            .then(response => {
                if (response.ok) {
                    console.log('GET request sent successfully!');
                } else {
                    console.error('Failed to send GET request!');
                }
            })
            .catch(error => {
                console.error('Failed to send GET request!', error);
            });
    }

    // Define a function to toggle the festival button
    function toggleFestivalButton() {
        // Check if the festival button is currently active
        const isFestivalActive = festivalButton.classList.contains('active');

        // Send or remove the GET request as appropriate
        if (!isFestivalActive) {
            // Activate the festival button
            festivalButton.classList.add('active');
            sendFestivalRequest();
        } else {
            // Deactivate the festival button
            festivalButton.classList.remove('active');
            window.history.replaceState({}, '', festivalForm.action);
        }
    }

    // Attach a click event listener to the festival button
    festivalButton.addEventListener('click', toggleFestivalButton);

});
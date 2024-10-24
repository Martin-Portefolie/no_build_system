import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    navigate(event) {
        event.preventDefault(); // Prevent full page reload

        let weekNumber = parseInt(event.currentTarget.dataset.week); // Get the target week number
        let year = parseInt(event.currentTarget.dataset.year); // Get the current year

        // Handle week boundaries and year transitions
        if (weekNumber < 1) {
            weekNumber = 53; // Move to week 53 of the previous year
            year--;
        } else if (weekNumber > 53) {
            weekNumber = 1; // Move to week 1 of the next year
            year++;
        }

        // Update the URL dynamically without reloading the page
        this.updateUrl(weekNumber, year);
        this.fetchWeekData(weekNumber, year);
    }

    // Update the URL with week and year without reloading the page
    updateUrl(weekNumber, year) {
        const url = `/weeks/${weekNumber}-${year}`;
        history.pushState({ path: url }, '', url);
    }

    // Fetch the data for the specific week and year using an AJAX request
    fetchWeekData(weekNumber, year) {
        fetch(`/weeks/${weekNumber}-${year}`)
            .then(response => response.text()) // Fetch the HTML content
            .then(html => {
                document.querySelector('#week-frame').innerHTML = html; // Update the content of the turbo frame
            })
            .catch(error => console.error('Error fetching week data:', error));
    }
}

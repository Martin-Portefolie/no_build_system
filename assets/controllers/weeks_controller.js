import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    navigate(event) {
        event.preventDefault(); // Prevent default link behavior

        // Get the link URL
        const url = event.currentTarget.getAttribute("href");

        // Update the Turbo Frame content without reloading the page
        this.loadFrame(url);

        // Update the URL in the address bar
        history.pushState({}, '', url);
    }

    loadFrame(url) {
        // Load the content into the Turbo Frame
        fetch(url, {
            headers: {
                "Turbo-Frame": "week-frame" // Ensure only the Turbo Frame content is returned
            }
        })
            .then(response => response.text())
            .then(html => {
                document.querySelector('#week-frame').innerHTML = html;
            })
            .catch(error => console.error("Error loading week data:", error));
    }
}

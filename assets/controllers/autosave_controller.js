// assets/controllers/autosave_controller.js

import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["hours", "minutes", "id"];

    connect() {
        this.timeout = null;
        // Add an event listener for the "keydown" event
        document.addEventListener("keydown", this.handleKeydown.bind(this));
    }

    disconnect() {
        // Clean up the event listener when the controller is disconnected
        document.removeEventListener("keydown", this.handleKeydown.bind(this));
    }
    change() {
        clearTimeout(this.timeout);
        this.timeout = setTimeout(() => this.save(), 500); // Debounce to avoid rapid requests
    }

    handleKeydown(event) {
        // Check if the pressed key is "H" (either uppercase or lowercase)
        if (event.key.toLowerCase() === "h") {
            event.preventDefault(); // Prevent any default behavior
            this.hoursTarget.focus(); // Focus on the hours input field
        }
    }

    save() {
        const hours = parseInt(this.hoursTarget.value) || 0;
        const minutes = parseInt(this.minutesTarget.value) || 0;
        const id = this.idTarget.value || null;

        fetch('/testautosave', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ hours, minutes, id })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    this.updateStatus(`Saved: ${data.hours} hours, ${data.minutes} minutes`);
                    if (!id) {
                        // Update the hidden ID input if this was a new save
                        this.idTarget.value = data.id;
                    }
                } else {
                    this.updateStatus(`Error: ${data.error}`);
                }
            })
            .catch(error => {
                this.updateStatus(`Error: ${error}`);
            });
    }

    updateStatus(message) {
        const statusElement = document.getElementById("status");
        if (statusElement) {
            statusElement.textContent = message;
        }
    }
}

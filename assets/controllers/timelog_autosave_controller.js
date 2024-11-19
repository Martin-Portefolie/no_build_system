import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    connect() {
        this.inputs = this.element.querySelectorAll(".autosave-input");
        this.inputs.forEach((input) => {
            input.addEventListener("change", this.autoSave.bind(this));
        });
    }

    async autoSave(event) {
        const input = event.target;
        const todoId = input.dataset.todoId;
        const date = input.dataset.date;
        const [hours, minutes] = input.value.split(":").map((val) => parseInt(val, 10));

        const payload = {
            todoId,
            date,
            hours: isNaN(hours) ? 0 : hours,
            minutes: isNaN(minutes) ? 0 : minutes,
        };

        try {
            const response = await fetch("/profile/todo-handler/save-time", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error("Failed to save data.");
            }

            const result = await response.json();
            console.log("Save successful:", result);
        } catch (error) {
            console.error("Auto-save error:", error);
        }
    }
}

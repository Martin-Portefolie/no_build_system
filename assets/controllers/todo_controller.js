import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["select"];

    connect() {
        console.log("todo controller connected");
        this.element.addEventListener('change', this.addTodoToTable.bind(this));
    }

    addTodoToTable(event) {
        // Parse weeklyDates from the data attribute, or fall back to an empty array
        const weeklyDatesRaw = this.data.get("weeklyDates");
        const weeklyData = weeklyDatesRaw ? JSON.parse(weeklyDatesRaw) : [];
        console.log("Weekly Data:", weeklyData);

        if (!weeklyData.length) {
            console.error("No weekly data available");
            return;
        }

        const selectedOption = this.selectTarget.selectedOptions[0];
        const todoName = selectedOption.value;
        const todoId = selectedOption.dataset.id;

        if (!todoName || !todoId) return;

        // Check if the todo is already in the table
        const existingRow = document.getElementById(`row_${todoId}`);
        if (existingRow) {
            alert("This todo is already in the table.");
            return;
        }

        // Rest of the logic
        const tableBody = this.element.querySelector("table tbody");
        const newRow = document.createElement("tr");
        newRow.id = `row_${todoId}`;

        // Todo name cell
        const nameCell = document.createElement("td");
        nameCell.className = "py-3 px-4 border-b text-sm bg-gray-50";
        const nameLink = document.createElement("a");
        nameLink.href = "#";
        nameLink.className = "text-black hover:underline";
        nameLink.textContent = todoName;
        nameCell.appendChild(nameLink);

        newRow.appendChild(nameCell);

        // Iterate over weeklyData
        for (let i = 0; i < weeklyData.length; i++) {
            const timeCell = document.createElement("td");
            timeCell.className = "py-3 px-4 border-b text-center text-sm bg-green-50"; // Updated to green

            const labelForInput = document.createElement("label");

            const timeInput = document.createElement("input");
            timeInput.type = "time";
            timeInput.name = `time_${weeklyData[i]}_${todoId}`;
            timeInput.value = "00:00";
            timeInput.className = "block mx-auto border-gray-300 rounded-md shadow-sm autosave-input";
            timeInput.dataset.todoId = `${todoId}`;
            timeInput.dataset.date = weeklyData[i];

            labelForInput.appendChild(timeInput);
            timeCell.appendChild(labelForInput);
            newRow.appendChild(timeCell);
        }

        // Add the Total cell
        const totalCell = document.createElement("td");
        totalCell.className = "py-3 px-4 border-b text-center text-sm bg-gray-50";
        totalCell.id = `total_${todoId}`;
        totalCell.textContent = "0h 0m"; // Initial total
        newRow.appendChild(totalCell);

        tableBody.appendChild(newRow);

        // Remove the selected option
        this.selectTarget.removeChild(selectedOption);

        console.log(`Added todo: ${todoName} with ID: ${todoId}`);
    }
}

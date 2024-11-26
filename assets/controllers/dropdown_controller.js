import { Controller } from "@hotwired/stimulus";

/**
 * DropdownController handles the behavior of custom dropdowns
 */
export default class extends Controller {
    static targets = ["select", "customDropdown", "menu", "search", "item", "selected", "tableBody"];

    connect() {
        this.createCustomDropdown();
    }

    createCustomDropdown() {
        const select = this.selectTarget;
        const options = Array.from(select.options);

        // Create Custom Dropdown
        const customDropdown = document.createElement("div");
        customDropdown.classList.add("dropdown");
        this.element.appendChild(customDropdown);

        // Selected Element
        const selected = document.createElement("div");
        selected.classList.add("dropdown-select");
        selected.textContent = options[0].textContent;
        customDropdown.appendChild(selected);

        // Menu Element
        const menu = document.createElement("div");
        menu.classList.add("dropdown-menu", "hidden");
        customDropdown.appendChild(menu);

        // Search Input
        const search = document.createElement("input");
        search.type = "text";
        search.placeholder = "Search...";
        search.classList.add("dropdown-menu-search");
        menu.appendChild(search);

        // Items Wrapper
        const menuInnerWrapper = document.createElement("div");
        menuInnerWrapper.classList.add("dropdown-menu-inner");
        menu.appendChild(menuInnerWrapper);

        // Add Items
        options.forEach((option) => {
            const item = document.createElement("div");
            item.classList.add("dropdown-menu-item");
            item.dataset.value = option.value;
            item.textContent = option.textContent;
            menuInnerWrapper.appendChild(item);
        });

        // Add Event Listeners
        selected.addEventListener("click", () => menu.classList.toggle("hidden"));
        search.addEventListener("input", this.filterItems.bind(this, menuInnerWrapper));
        menuInnerWrapper.addEventListener("click", (e) => {
            if (e.target.classList.contains("dropdown-menu-item")) {
                this.setSelected(e.target, selected, select, menu);
            }
        });

        // Hide the original select
        select.style.display = "none";
    }

    filterItems(wrapper, e) {
        const value = e.target.value.toLowerCase();
        Array.from(wrapper.children).forEach((item) => {
            const text = item.textContent.toLowerCase();
            item.style.display = text.includes(value) ? "block" : "none";
        });
    }

    setSelected(item, selected, select, menu) {
        selected.textContent = item.textContent;
        select.value = item.dataset.value;

        // Close the menu
        menu.classList.add("hidden");
    }

    addTodoToTable(event) {
        console.log("addTodoToTable triggered");
        console.log("Selected value:", this.selectTarget.value);

        const selectedOption = this.selectTarget.selectedOptions[0];
        const todoName = selectedOption.textContent.trim();
        const todoId = selectedOption.getAttribute("data-id");

        console.log("Selected Todo Name:", todoName);
        console.log("Selected Todo ID:", todoId);

        // Prevent adding the placeholder
        if (!todoId) return;

        // Check if the Todo is already in the table
        const existingRow = this.tableBodyTarget.querySelector(`[data-todo-id="${todoId}"]`);
        if (existingRow) {
            alert("This Todo is already in the timetable.");
            return;
        }

        console.log("Creating new row for Todo:", todoName);

        // Create a new row for the timetable
        const newRow = document.createElement("tr");
        newRow.setAttribute("data-todo-id", todoId);

        newRow.innerHTML = `
        <td class="py-3 px-4 border-b text-sm bg-gray-50">
            <a href="#" class="text-black hover:underline">${todoName}</a>
        </td>
        ${this.createTimeLogColumns(todoId)}
        <td class="py-3 px-4 border-b text-center text-sm bg-gray-50">0h 0m</td>
    `;

        this.tableBodyTarget.appendChild(newRow);

        // Reset the dropdown
        this.selectTarget.value = "";
    }

    createTimeLogColumns(todoId) {
        // Generate columns for each day in the week (replace `weeklyData` dynamically)
        const days = JSON.parse(this.element.dataset.weeklyData || "[]"); // Assumes weeklyData is passed as JSON
        return days
            .map(
                (day) => `
                    <td class="py-3 px-4 border-b text-center text-sm ${
                    day.isWeekend ? "bg-gray-100" : "bg-blue-50"
                }">
                        <input
                            type="time"
                            name="time_${day.date}_${todoId}"
                            value="00:00"
                            class="block mx-auto border-gray-300 rounded-md shadow-sm text-sm"
                        />
                    </td>
                `
            )
            .join("");
    }


}

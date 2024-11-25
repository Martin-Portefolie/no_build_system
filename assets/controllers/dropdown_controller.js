import { Controller } from "@hotwired/stimulus";

/**
 * DropdownController handles the behavior of custom dropdowns
 */
export default class extends Controller {
    static targets = ["select", "customDropdown", "menu", "search", "item", "selected"];

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
}

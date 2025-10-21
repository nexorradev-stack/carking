document.addEventListener("DOMContentLoaded", () => {
    const configureAddFeature = (addContainerId, listContainerId, itemClass) => {
        const addContainer = document.getElementById(addContainerId);
        const listContainer = document.getElementById(listContainerId);

        const resetAddButton = () => {
            addContainer.classList.remove("editing");
            addContainer.innerHTML = `
                <img src="assets/add.svg" id="add-icon">
                <p>Add</p>
            `;
        };

        const handleSave = (inputValue) => {
            const trimmedValue = inputValue.trim();
            if (trimmedValue) {
                const newItem = document.createElement("div");
                newItem.className = itemClass;
                newItem.innerHTML = `
                    <p>${trimmedValue}</p>
                    <img src="assets/cross.svg" id="cross">
                `;
                listContainer.insertBefore(newItem, addContainer);
                attachRemoveHandler(newItem);
            }
            resetAddButton();
        };

        const attachRemoveHandler = (item) => {
            const cross = item.querySelector("#cross");
            cross.addEventListener("click", () => {
                item.remove();
            });
        };

        addContainer.addEventListener("click", (event) => {
            // If clicked inside the input field, prevent click from triggering the container click event
            if (event.target.tagName === "INPUT") {
                return;
            }

            if (!addContainer.classList.contains("editing")) {
                addContainer.classList.add("editing");
                addContainer.innerHTML = `
                    <input type="text" id="${addContainerId}-input" placeholder="Enter name">
                `;
                const inputField = document.getElementById(`${addContainerId}-input`);
                
                inputField.focus();

                inputField.addEventListener("keydown", (event) => {
                    if (event.key === "Enter") {
                        handleSave(inputField.value);
                    } else if (event.key === "Escape") {
                        resetAddButton();
                    }
                });

                // Close the input if clicked outside
                document.addEventListener("click", (event) => {
                    if (!addContainer.contains(event.target)) {
                        resetAddButton();
                    }
                }, { once: true });
            }
        });

        // Prevent the input click from propagating to the add button
        addContainer.addEventListener("click", (event) => {
            if (event.target.tagName === "INPUT") {
                event.stopPropagation();
            }
        });

        listContainer.querySelectorAll(`.${itemClass}`).forEach((item) => {
            attachRemoveHandler(item);
        });
    };

    configureAddFeature("add-tag", "tags", "tag-option");
    configureAddFeature("add-cat", "categories", "cat-option");
});

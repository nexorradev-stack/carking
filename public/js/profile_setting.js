document.addEventListener("DOMContentLoaded", () => {
    const clearButtons = document.querySelectorAll(".remove-btn");

    clearButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const inputField = button.previousElementSibling;

            if (inputField && inputField.tagName === "INPUT") {
                inputField.value = "";
            }
        });
    });
});
document.addEventListener("DOMContentLoaded", () => {
    const urlInputs = document.querySelectorAll('input[type="url"]');

    urlInputs.forEach((inputField) => {
        inputField.addEventListener("blur", () => {
            const urlPattern = /^(https?:\/\/)?([\w\d\-_]+\.+[A-Za-z]{2,})+(\/.*)?$/;
            if (!urlPattern.test(inputField.value.trim())) {
                inputField.style.border = "2px solid red";
            } else {
                inputField.style.border = "0.5px solid #C5C5C5";
            }
        });
    });
});

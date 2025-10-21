document.addEventListener("DOMContentLoaded", () => {
    const imgContainer = document.getElementById("img-container");
    const addImageButton = document.getElementById("add-image-button");
    const uploadButtons = document.querySelectorAll(".upload-button");

    addImageButton.addEventListener("click", () => {
        const input = document.createElement("input");
        input.type = "file";
        input.accept = "image/*";

        input.addEventListener("change", (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    const newDiv = document.createElement("div");
                    newDiv.classList.add("brand-image");

                    const newImg = document.createElement("img");
                    newImg.src = e.target.result;
                    newImg.alt = "Brand Image";

                    newDiv.appendChild(newImg);
                    imgContainer.insertBefore(newDiv, document.getElementById("add-image"));

                    applyHoverAndDeleteLogic(newImg);
                };

                reader.readAsDataURL(file);
            }
        });

        input.click();
    });

    uploadButtons.forEach((uploadButton) => {
        const input = document.createElement("input");
        input.type = "file";
        input.accept = "image/*";
        uploadButton.appendChild(input);

        input.style.display = "none";

        uploadButton.addEventListener("click", () => input.click());

        input.addEventListener("change", (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = () => {
                    const fileName = document.createElement("p");
                    fileName.textContent = file.name;
                    fileName.style.margin = 0;
                    fileName.style.textAlign = "center";

                    uploadButton.innerHTML = "";
                    uploadButton.appendChild(fileName);
                };

                reader.readAsDataURL(file);
            }
        });
    });

    const existingImages = document.querySelectorAll("#img-container .brand-image img");
    existingImages.forEach((img) => applyHoverAndDeleteLogic(img));

    function applyHoverAndDeleteLogic(img) {
        const originalSrc = img.src;

        img.addEventListener("mouseenter", () => {
            img.dataset.originalSrc = img.src;
            img.src = "assets/delete.png";
        });

        img.addEventListener("mouseleave", () => {
            img.src = img.dataset.originalSrc;
        });

        img.addEventListener("click", () => {
            const parentDiv = img.parentElement;
            parentDiv.remove();
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('add-category').addEventListener('click', function () {
        const categoryInputGroup = document.getElementById('category-input-group');

        if (categoryInputGroup) {
            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.className = 'new-category-input';
            newInput.placeholder = 'Add another Category';
            categoryInputGroup.appendChild(newInput);
        } else {
            console.error('Category input group not found!');
        }
    });
});

document.getElementById('media-upload-label').addEventListener('click', function() {
    document.getElementById('media-upload-input').click();
});

document.getElementById('media-upload-input').addEventListener('change', function(event) {
    const files = event.target.files;
    const fileTypeError = document.getElementById('file-type-error');
    const mediaPreview = document.getElementById('media-preview');

    mediaPreview.innerHTML = ''; // Clear previous previews

    // Allowed file types
    const allowedTypes = ['image/jpeg', 'image/png', 'image/svg+xml', 'application/zip'];

    let invalidFile = false;

    Array.from(files).forEach(file => {
        if (!allowedTypes.includes(file.type)) {
            invalidFile = true;
            return;
        }

        // If the file is an image, display the preview
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'media preview';
                mediaPreview.appendChild(img);
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/zip') {
            // For ZIP files, display the filename
            const fileInfo = document.createElement('p');
            fileInfo.textContent = `Uploaded: ${file.name}`;
            mediaPreview.appendChild(fileInfo);
        }
    });

    // Show error if there is an invalid file type
    if (invalidFile) {
        fileTypeError.style.display = 'block';
    } else {
        fileTypeError.style.display = 'none';
    }
});

// Select elements
const fullImg = document.querySelector('#full-img img');
const carPics = document.querySelectorAll('#imgs .car-pic');
const prevButton = document.getElementById('prev');
const nextButton = document.getElementById('next');
let currentIndex = 0; // Track the currently displayed image index

// Function to update the main image and add border to selected image
function updateMainImage(index) {
    // Remove the stroke from all images
    carPics.forEach(pic => pic.parentElement.style.border = 'none');

    // Update main image source and add stroke to selected image
    fullImg.src = carPics[index].src;
    carPics[index].parentElement.style.border = '1px solid #000';

    // Update the current index
    currentIndex = index;
}

// Event listener for clicking on images in #imgs div
carPics.forEach((pic, index) => {
    pic.addEventListener('click', () => {
        updateMainImage(index);
    });
});

// Event listener for next button
nextButton.addEventListener('click', () => {
    let nextIndex = (currentIndex + 1) % carPics.length; // Loop back to first image if at end
    updateMainImage(nextIndex);
});

// Event listener for prev button
prevButton.addEventListener('click', () => {
    let prevIndex = (currentIndex - 1 + carPics.length) % carPics.length; // Loop to last image if at beginning
    updateMainImage(prevIndex);
});

// Initialize with the first image
updateMainImage(0);




//belw is page java script

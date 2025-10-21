
const faqs = document.querySelectorAll(".faq");

faqs.forEach(faq => {
    faq.addEventListener("click", () => {
        faq.querySelector(".answer").classList.toggle("display");
        faq.classList.toggle("display");
    });
});

document.addEventListener("DOMContentLoaded", () => {
    adjustNavMiddlePosition();
    const toggleButton = document.querySelector(".toggle-menu-button");
    const dropdownMenu = document.querySelector(".dropdown-menu");

    if (toggleButton) {
        toggleButton.addEventListener("click", () => {
            dropdownMenu.classList.toggle("hidden");
        });
    }
});



function adjustNavMiddlePosition() {
    const logo = document.querySelector('.logo');
    const iconsRight = document.querySelector('.icons-right');
    const navMiddle = document.querySelector('.nav-middle');

    if (logo && iconsRight && navMiddle) {
        // Get the widths of logo and icons-right
        const logoWidth = logo.offsetWidth;
        const iconsRightWidth = iconsRight.offsetWidth;

        // Calculate the adjustment value
        const adjustment = (iconsRightWidth - logoWidth) / 2;

        // Apply the adjustment
        navMiddle.style.transform = `translateX(${adjustment}px)`;
    }
}


// Adjust on window resize
window.addEventListener("resize", adjustNavMiddlePosition);

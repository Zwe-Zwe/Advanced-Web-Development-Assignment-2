document.addEventListener('DOMContentLoaded', function() {
    const hamburgerCheckbox = document.getElementById('hamburger-checkbox');
    const fullWidthMenu = document.getElementById('full-width-menu');

    hamburgerCheckbox.addEventListener('change', function() {
        if (this.checked) {
            fullWidthMenu.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent scrolling when menu is open
        } else {
            fullWidthMenu.classList.remove('show');
            document.body.style.overflow = ''; // Restore scrolling when menu is closed
        }
    });

    // Close menu when clicking a link
    const menuLinks = fullWidthMenu.querySelectorAll('a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function() {
            hamburgerCheckbox.checked = false;
            fullWidthMenu.classList.remove('show');
            document.body.style.overflow = '';
        });
    });
});

let mybutton = document.getElementById("btn-back-to-top");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
    scrollFunction();
};

function scrollFunction() {
    if (
        document.body.scrollTop > 20 ||
        document.documentElement.scrollTop > 20
    ) {
        mybutton.style.display = "block";
    } else {
        mybutton.style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
mybutton.addEventListener("click", backToTop);

function backToTop() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE, and Opera
}

document.addEventListener('DOMContentLoaded', function() {
    const nav = document.querySelector('nav');
    const defaultLogo = document.getElementById('default-logo');
    const scrolledLogo = document.getElementById('scrolled-logo');
    const userContent = document.querySelectorAll('.user-content');

    // Debugging to check the pathname
    console.log("window.location.pathname:", window.location.pathname);

    // Get the current path without the query string or hash
    const currentPath = window.location.pathname.split('/').pop().toLowerCase() || 'index.php'; // Default to 'index.php' if empty

    console.log("Current path after split:", currentPath); // Debugging

    // Check if the current page is index.php, login.php, or registration.php
    const isIndexPage = currentPath === 'index.php';

    if (isIndexPage) {
        // Apply scroll behavior only for index.php
        window.addEventListener('scroll', function() {
            if (window.scrollY > window.innerHeight) {
                nav.style.backgroundColor = 'rgba(242, 242, 240, 0.8)';
                nav.style.color = 'rgba(74, 74, 74, 1)';
                defaultLogo.style.display = 'none';
                scrolledLogo.style.display = 'block';
                userContent.forEach(function(element) {
                    element.style.backgroundColor = 'transparent';
                });

                // Change hamburger bar color when scrolled
                document.querySelectorAll('.bar').forEach(function(bar) {
                    bar.style.backgroundColor = 'rgba(74, 74, 74, 1)'; // Color for scrolled state
                });
            } else {
                nav.style.color = 'rgba(242, 242, 240, 1)';
                nav.style.backgroundColor = 'transparent';
                defaultLogo.style.display = 'block';
                scrolledLogo.style.display = 'none';

                // Reset hamburger bar color when at the top
                document.querySelectorAll('.bar').forEach(function(bar) {
                    bar.style.backgroundColor = 'rgba(242, 242, 240, 1)'; // Color for transparent navbar
                });
            }
        });

        // Initial check in case the page is already scrolled
        if (window.scrollY > window.innerHeight) {
            nav.style.backgroundColor = 'rgba(242, 242, 240, 0.8)';
            nav.style.color = 'rgba(74, 74, 74, 1)';
            defaultLogo.style.display = 'none';
            scrolledLogo.style.display = 'block';
            userContent.forEach(function(element) {
                element.style.backgroundColor = 'transparent';
            });

            // Set hamburger bar color for initial scroll
            document.querySelectorAll('.bar').forEach(function(bar) {
                bar.style.backgroundColor = 'rgba(74, 74, 74, 1)';
            });
        } else {
            nav.style.color = 'rgba(242, 242, 240, 1)';
            nav.style.backgroundColor = 'transparent';
            defaultLogo.style.display = 'block';
            scrolledLogo.style.display = 'none';

            // Set hamburger bar color for initial top state
            document.querySelectorAll('.bar').forEach(function(bar) {
                bar.style.backgroundColor = 'rgba(242, 242, 240, 1)';
            });
        }
    } else {
        // Apply static navbar design for all other pages
        nav.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
        nav.style.color = 'rgba(74, 74, 74, 1)';

        userContent.forEach(function(element) {
            element.style.backgroundColor = 'transparent';
        });

        if (defaultLogo && scrolledLogo) {
            scrolledLogo.style.display = 'block';
            defaultLogo.style.display = 'none';
        }

        // Set hamburger bar color for static navbar
        document.querySelectorAll('.bar').forEach(function(bar) {
            bar.style.backgroundColor = 'rgba(74, 74, 74, 1)';
        });
    }

    // Hamburger menu toggle functionality
    document.getElementById("hamburger-btn").addEventListener("click", function() {
        var fullWidthMenu = document.getElementById("full-width-menu");
        fullWidthMenu.classList.toggle("show"); // Toggle the "show" class
    });
});




let slideIndex = 0;
autoSlide();

function autoSlide(){
    let i;
    let slides = document.getElementsByClassName("slide");

    // Hide all slides by removing 'active' class
    for (i = 0; i < slides.length; i++){
        slides[i].classList.remove("active");  // Remove active class
    }

    slideIndex++;

    if (slideIndex > slides.length) {
        slideIndex = 1;
    }

    // Show the current slide by adding 'active' class
    slides[slideIndex - 1].classList.add("active");  // Add active class

    setTimeout(autoSlide, 5000); // Change slide every 3 seconds
}

document.addEventListener('scroll', function() {
    const scrollPosition = window.scrollY;
    const hideThreshold = window.innerHeight * 0.1; // 50% of viewport height

    const encircleElement = document.querySelector('.encircle');

    if (scrollPosition > hideThreshold) {
        encircleElement.classList.add('hidden');
    } else {
        encircleElement.classList.remove('hidden');
    }
});

window.addEventListener('scroll', function() {
    let scrollPosition = window.scrollY;

    // Select all images that need the effect
    let images = document.querySelectorAll('.index-content-1-photo, .index-content-2-photo');

    // Maximum upward movement (in vh)
    let maxMoveUp = -5;  // Move up by 5vh
    let scrollLimit = 300; // The scroll distance over which the image moves (adjust as needed)

    // Apply the scroll effect to each image
    images.forEach(image => {
        // Calculate how much the image should translate upwards based on the scroll position
        let translateYValue = Math.min(0, (scrollPosition / scrollLimit) * maxMoveUp);

        // Apply the translation using transform
        image.style.transform = `translateY(${translateYValue}vh)`;
    });
});

function clearErrors() {
    // Get all elements with the class 'error-message'
    const errorMessages = document.querySelectorAll('.error-message');

    // Loop through each error message element and clear its content
    errorMessages.forEach(function(error) {
        error.innerText = '';  // Clear the error message
    });
}









// loading page
const loader = document.querySelector('#loader');


window.addEventListener("load", function () {
    
    loader.classList.add('fondu-out');
    this.setTimeout(() => {
        loader.style.display = "none";
    }, 400);


    /* nav-bar (hamburger) */
    const hamburgerToggler = document.querySelector(".hamburger");
    const navLinksContainer = document.querySelector(".navlinks-container");

    let startX; // Position X au début du touch
    let distance; // Distance de glissement nécessaire pour fermer le menu

    const toggleNav = () => {
        hamburgerToggler.classList.toggle("open");

        const ariaToggle = hamburgerToggler.getAttribute("aria-expanded") === "true" ? "false" : "true";
        hamburgerToggler.setAttribute("aria-expanded", ariaToggle);

        navLinksContainer.classList.toggle("open");
    }
    hamburgerToggler.addEventListener("click", toggleNav);


    /* fermer le menu aux slides vers la gauche */
    document.addEventListener("touchstart", function (e) {
        startX = e.touches[0].clientX;
        distance = 150; // Vous pouvez ajuster cette valeur selon vos besoins
    });
    document.addEventListener("touchmove", function (e) {
        if (startX) {
            const currentX = e.touches[0].clientX;
            const deltaX = startX - currentX;
            // Si le glissement dépasse la distance définie, fermez le menu
            if (deltaX > distance) {
                navLinksContainer.classList.remove("open");
                hamburgerToggler.classList.remove("open");
                startX = null; // Réinitialisez la position de départ
            }

            // Si le glissement dépasse la distance définie dans l'autre sens, ouvrez le menu
            if (deltaX < -distance) {
                navLinksContainer.classList.add("open");
                hamburgerToggler.classList.add("open");
                startX = null; // Réinitialisez la position de départ
            }
        }
    });
    // Réinitialisez la position de départ lorsque le doigt est levé
    document.addEventListener("touchend", function () {
        startX = null;
    });

    
    /* fermer le menu si clic n'importe où sur le document */
    document.addEventListener("click", function (event) {
        // Vérifiez si le clic a eu lieu à l'extérieur du menu
        if (!navLinksContainer.contains(event.target) && !hamburgerToggler.contains(event.target)) {
            // Si c'est le cas, fermez le menu en retirant la classe "open"
            navLinksContainer.classList.remove("open");
            hamburgerToggler.classList.remove("open");
        }
    });

    new ResizeObserver(entries => { //gérer la transition du menu hamburger
        if(entries[0].contentRect.width <= 800){
            navLinksContainer.style.transition = "transform 0.3s ease-out";
        } else {
            navLinksContainer.style.transition = "none";
        }
    }).observe(document.body);


    /* button of scroll to top and navbar animatation */
    const header = document.querySelector('#navbar');
    const toTopBtn = document.querySelector("#to-top-btn");
    window.addEventListener("scroll", () => {
        header.classList.toggle("sticky", window.scrollY > 0);

        if(document.documentElement.scrollTop > window.innerHeight * 0.7)
            toTopBtn.classList.add("active");
        else 
            toTopBtn.classList.remove("active");
    });
    toTopBtn.addEventListener("click", () => {
        if (toTopBtn.classList.contains("active")) {
            window.scrollTo({
                top: 0
            });
        }
    });
});
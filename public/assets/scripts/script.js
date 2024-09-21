/* ========================================================================
   File Name: script.js
   Author: CUINET Antoine
   Version: 1.0
   Date: [Creation or Last Update Date]
   
   Note: This code was developed by CUINET Antoine, see https://acuinet.fr
======================================================================== */

document.addEventListener('DOMContentLoaded', function() {

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
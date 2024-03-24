const mobileNavToggler = document.querySelector('.mobile-nav-toggle');
const navbar = document.querySelector('#navbar');


mobileNavToggler.addEventListener('click', function() {
    navbar.classList.toggle('navbar-mobile');
    this.classList.toggle('fa-bars');
    this.classList.toggle('fa-close');
});
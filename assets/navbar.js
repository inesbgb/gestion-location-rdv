// Gestion du menu mobile
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const closeMenu = document.getElementById('close-menu');

    if (menuToggle && mobileMenu && closeMenu) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.remove('hidden');
            mobileMenu.classList.add('active');
        });

        closeMenu.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            mobileMenu.classList.add('hidden');
        });
    }
}); 
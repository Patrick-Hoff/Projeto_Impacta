(function () {
    'use strict';

    var STORAGE_KEY = 'auto-dealer-theme';
    var htmlEl = document.documentElement;
    var toggleBtn = document.getElementById('themeToggle');
    var iconDark = document.getElementById('themeIconDark');
    var iconLight = document.getElementById('themeIconLight');

    function getCurrentTheme() {
        return htmlEl.getAttribute('data-bs-theme') === 'light' ? 'light' : 'dark';
    }

    function updateIcon(theme) {
        if (!iconDark || !iconLight) return;
        if (theme === 'light') {
            iconDark.classList.add('d-none');
            iconLight.classList.remove('d-none');
        } else {
            iconLight.classList.add('d-none');
            iconDark.classList.remove('d-none');
        }
    }

    function applyTheme(theme) {
        htmlEl.setAttribute('data-bs-theme', theme);
        localStorage.setItem(STORAGE_KEY, theme);
        updateIcon(theme);
    }

    // Sincroniza o ícone com o tema já aplicado no <head> (evita flash)
    updateIcon(getCurrentTheme());

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            var next = getCurrentTheme() === 'dark' ? 'light' : 'dark';
            applyTheme(next);
        });
    }
})();
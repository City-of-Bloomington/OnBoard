"use strict";
(function () {
    var closeMenus = function () {
        var openLaunchers = document.querySelectorAll('.dropdown [aria-expanded="true"]'),
            len = openLaunchers.length,
            i   = 0,
            id  = '';

        for (i=0; i<len; i++) {
            id = openLaunchers[i].getAttribute('id');

            openLaunchers[i].setAttribute("aria-expanded", "false");
            openLaunchers[i].parentElement.querySelector('[aria-labeledby="'+ id +'"]').setAttribute('aria-hidden', 'true');
        }
    },
    launcherClick = function(e) {
        var launcher = e.target,
            id       = launcher.getAttribute('id'),
            menu     = launcher.parentElement.querySelector('.dropdown .links');
        e.preventDefault();
        launcher.blur();
        closeMenus();
        launcher.setAttribute("aria-expanded", "true");
        launcher.parentElement.querySelector('[aria-labeledby="'+ id +'"]').setAttribute('aria-hidden', 'false');

        e.stopPropagation();
        menu.focus();
    },
    launchers = document.querySelectorAll('.dropdown .launcher'),
    len   = launchers.length,
    i = 0;

    closeMenus();
    for (i=0; i<len; i++) {
        launchers[i].addEventListener('click', launcherClick);
    }
    document.addEventListener('click', closeMenus);
})();

"use strict";
(function () {
    var buttons = document.querySelectorAll('.changeLogEntry button'),
        len     = buttons.length,
        i       = 0,
        toggle  = function (e) {
            var button = e.target;

            e.preventDefault();
            if (button.getAttribute('aria-expanded') == 'true') {
                button.setAttribute('aria-expanded', 'false');
                document.getElementById(button.getAttribute('aria-controls')).setAttribute('aria-hidden', 'true');
            }
            else {
                button.setAttribute('aria-expanded', 'true');
                document.getElementById(button.getAttribute('aria-controls')).setAttribute('aria-hidden', 'false');
            }
            e.stopPropagation();
        };

    for (i=0; i<len; i++) {
        document.getElementById(buttons[i].getAttribute('aria-controls')).setAttribute('aria-hidden', 'true');
        buttons[i].setAttribute('aria-expanded', 'false');
        buttons[i].addEventListener('click', toggle, false);
    }
})();


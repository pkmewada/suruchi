document.addEventListener('DOMContentLoaded', function() {
        var track = document.querySelector('.process-steps-track');
        if (!track || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        Array.from(track.children).forEach(function(card) {
            var copy = card.cloneNode(true);
            copy.setAttribute('aria-hidden', 'true');
            track.appendChild(copy);
        });
    });

document.addEventListener('DOMContentLoaded', function() {
        var viewport = document.querySelector('.team-carousel-viewport');
        var track = viewport && viewport.querySelector('.team-carousel-track');
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        if (!track || track.children.length < 2 || reduceMotion.matches) return;

        var timer;
        var sliding = false;

        function makeTailCopy() {
            var copy = track.firstElementChild.cloneNode(true);
            copy.setAttribute('aria-hidden', 'true');
            track.appendChild(copy);
        }

        function advance() {
            if (sliding || viewport.matches(':hover') || viewport.contains(document.activeElement)) return;
            var firstCard = track.firstElementChild;
            if (!firstCard) return;

            sliding = true;
            track.style.transform = 'translateX(-' + firstCard.getBoundingClientRect().width + 'px)';
            track.classList.add('is-sliding');
        }

        track.addEventListener('transitionend', function(event) {
            if (event.target !== track || event.propertyName !== 'transform') return;

            track.classList.remove('is-sliding');
            track.removeChild(track.firstElementChild);
            track.style.transform = 'translateX(0)';
            makeTailCopy();
            sliding = false;
        });

        function start() {
            if (timer || reduceMotion.matches) return;
            timer = window.setInterval(advance, 100);
        }

        function stop() {
            window.clearInterval(timer);
            timer = null;
        }

        viewport.addEventListener('mouseenter', stop);
        viewport.addEventListener('mouseleave', start);
        viewport.addEventListener('focusin', stop);
        viewport.addEventListener('focusout', function(event) {
            if (!viewport.contains(event.relatedTarget)) start();
        });
        reduceMotion.addEventListener('change', function() {
            if (reduceMotion.matches) stop();
            else start();
        });

        makeTailCopy();
        start();
    });
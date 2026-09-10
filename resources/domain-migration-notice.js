(function () {
    'use strict';

    function post(url, token) {
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            body: '_csrf=' + encodeURIComponent(token)
        }).catch(function () {
            // The notice must remain useful even when browser storage is unavailable.
        });
    }

    function formatRemaining(seconds) {
        if (seconds <= 0) {
            return 'The deadline has been reached.';
        }

        var days = Math.floor(seconds / 86400);
        var hours = Math.floor((seconds % 86400) / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        return days + 'd ' + hours + 'h ' + minutes + 'm';
    }

    function initialise(notice) {
        var preview = notice.dataset.preview === '1';
        var blocked = notice.dataset.blocked === '1';
        var token = notice.dataset.csrfToken;

        if (!preview && !blocked) {
            post(notice.dataset.seenUrl, token);
        }

        notice.querySelectorAll('[data-dmn-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                notice.remove();
            });
        });

        var snooze = notice.querySelector('[data-dmn-snooze]');
        if (snooze) {
            snooze.addEventListener('click', function () {
                post(notice.dataset.dismissWeekUrl, token);
                notice.remove();
            });
        }

        var countdown = notice.querySelector('[data-dmn-countdown]');
        if (countdown) {
            var deadline = Number(notice.dataset.deadline) * 1000;
            var update = function () {
                countdown.textContent = formatRemaining(Math.ceil((deadline - Date.now()) / 1000));
            };
            update();
            window.setInterval(update, 30000);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-dmn-notice]').forEach(initialise);
    });
}());

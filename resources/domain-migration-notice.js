(function () {
    'use strict';

    function post(url, token) {
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: '_csrf=' + encodeURIComponent(token)
        }).then(function (response) {
            if (!response.ok) {
                return false;
            }

            return response.json().then(function (result) {
                return result && result.ok === true;
            }).catch(function () {
                return false;
            });
        }).catch(function () {
            // The notice remains readable even when state storage is unavailable.
            return false;
        });
    }

    function formatRemaining(seconds, format, deadlineReachedLabel) {
        if (seconds <= 0) {
            return deadlineReachedLabel;
        }

        var days = Math.floor(seconds / 86400);
        var hours = Math.floor((seconds % 86400) / 3600);
        var minutes = Math.floor((seconds % 3600) / 60);
        return format
            .replace('{days}', days)
            .replace('{hours}', hours)
            .replace('{minutes}', minutes);
    }

    function initialise(notice) {
        var preview = notice.dataset.preview === '1';
        var blocked = notice.dataset.blocked === '1';
        var token = notice.dataset.csrfToken;

        if (!preview && !blocked) {
            post(notice.dataset.seenUrl, token);
        }

        var onKeydown = null;
        var closeNotice = function () {
            notice.remove();
            if (onKeydown) {
                document.removeEventListener('keydown', onKeydown);
            }
        };

        notice.querySelectorAll('[data-dmn-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeNotice();
            });
        });

        if (!preview && !blocked) {
            onKeydown = function (event) {
                if (event.key === 'Escape' && notice.isConnected) {
                    closeNotice();
                }
            };
            document.addEventListener('keydown', onKeydown);
        }

        var snooze = notice.querySelector('[data-dmn-snooze]');
        if (snooze) {
            snooze.addEventListener('click', function () {
                snooze.disabled = true;
                post(notice.dataset.dismissWeekUrl, token).then(function (stored) {
                    if (stored) {
                        closeNotice();
                        return;
                    }

                    snooze.disabled = false;
                });
            });
        }

        var countdown = notice.querySelector('[data-dmn-countdown]');
        if (countdown) {
            var deadline = Number(notice.dataset.deadline) * 1000;
            var update = function () {
                countdown.textContent = formatRemaining(
                    Math.ceil((deadline - Date.now()) / 1000),
                    notice.dataset.countdownFormat,
                    notice.dataset.deadlineReachedLabel
                );
            };
            update();
            var timer = window.setInterval(function () {
                if (!notice.isConnected) {
                    window.clearInterval(timer);
                    return;
                }

                update();
            }, 30000);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-dmn-notice]').forEach(initialise);
    });
}());

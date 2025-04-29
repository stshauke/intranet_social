// assets/js/notification.js

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.mark-read-icon').forEach(function (icon) {
        icon.addEventListener('click', function () {
            const notificationId = this.getAttribute('data-id');

            fetch(`/notification/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                if (response.ok) {
                    this.textContent = '✅';
                } else {
                    alert('Erreur lors de la mise à jour.');
                }
            })
            .catch(error => {
                console.error('Erreur AJAX :', error);
            });
        });
    });
});

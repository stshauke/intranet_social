// public/js/message-modal.js

document.addEventListener('DOMContentLoaded', function () {
    const modalPlaceholder = document.createElement('div');
    document.body.appendChild(modalPlaceholder);

    document.querySelectorAll('.btn-message-modal').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const url = this.getAttribute('href');

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                modalPlaceholder.innerHTML = html;

                const modal = new bootstrap.Modal(modalPlaceholder.querySelector('.modal'));
                modal.show();

                // Gestion de l’envoi du formulaire dans la modale
                const form = modalPlaceholder.querySelector('form');
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(form);

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            modal.hide();
                            alert('Message envoyé avec succès 🎉');
                        } else if (data.form) {
                            modalPlaceholder.querySelector('.modal-content').innerHTML = data.form;
                        }
                    })
                    .catch(error => console.error('Erreur envoi message :', error));
                });
            })
            .catch(error => console.error('Erreur chargement modale :', error));
        });
    });
});

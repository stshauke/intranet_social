document.addEventListener('DOMContentLoaded', function () {
    const messageCount = document.getElementById('message-count');
    const messageMenu = document.getElementById('messageDropdownMenu');

    function loadMessages() {
        fetch('/message/ajax/unread')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    messageCount.style.display = 'inline-block';
                    messageCount.textContent = data.count;
                } else {
                    messageCount.style.display = 'none';
                }

                messageMenu.innerHTML = '';

                if (data.messages.length > 0) {
                    data.messages.forEach(message => {
                        const item = document.createElement('li');
                        const link = document.createElement('a');
                        link.classList.add('dropdown-item');
                        link.href = message.url;
                        link.textContent = `De ${message.sender} : ${message.content}`;

                        // ✅ Marquage en AJAX quand on clique
                        link.addEventListener('click', function (event) {
                            event.preventDefault();
                            fetch(`/message/mark-as-read/${message.id}`, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }).then(() => {
                                window.location.href = message.url;
                            });
                        });

                        item.appendChild(link);
                        messageMenu.appendChild(item);
                    });
                } else {
                    const item = document.createElement('li');
                    item.innerHTML = '<span class="dropdown-item text-muted">Aucun nouveau message</span>';
                    messageMenu.appendChild(item);
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des messages :', error);
            });
    }

    if (messageCount && messageMenu) {
        loadMessages();
        setInterval(loadMessages, 30000); // Rafraîchit toutes les 30 secondes
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const notificationCount = document.getElementById('notification-count');
    const notificationMenu = document.getElementById('notificationDropdownMenu');
    const markAllReadButton = document.getElementById('mark-all-read');

    const messageCount = document.getElementById('message-count');
    const messageMenu = document.getElementById('messageDropdownMenu');

    function loadNotifications() {
        fetch('/notification/ajax/unread')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des notifications');
                }
                return response.json();
            })
            .then(data => {
                console.log('🔔 Notifications chargées :', data.notifications);
                notificationMenu.innerHTML = '';

                if (data.count > 0) {
                    notificationCount.style.display = 'inline-block';
                    notificationCount.textContent = data.count;

                    data.notifications.forEach(notification => {
                        const item = document.createElement('li');
                        const link = document.createElement('a');
                        link.classList.add('dropdown-item');
                        link.href = notification.url;
                        link.textContent = notification.message;

                        // Marquer comme lue au clic
                        link.addEventListener('click', function (event) {
                            event.preventDefault();
                            fetch(`/notification/${notification.id}/mark-read`, { method: 'POST' })
                                .then(() => {
                                    window.location.href = notification.url;
                                });
                        });

                        item.appendChild(link);
                        notificationMenu.appendChild(item);
                    });

                    const divider = document.createElement('li');
                    divider.innerHTML = '<hr class="dropdown-divider">';
                    notificationMenu.appendChild(divider);

                    if (markAllReadButton) {
                        const markAllItem = document.createElement('li');
                        markAllItem.appendChild(markAllReadButton);
                        notificationMenu.appendChild(markAllItem);
                    }
                } else {
                    notificationCount.style.display = 'none';
                    const item = document.createElement('li');
                    item.innerHTML = '<span class="dropdown-item text-muted">Aucune nouvelle notification</span>';
                    notificationMenu.appendChild(item);
                }
            })
            .catch(error => {
                console.error('❌ Erreur notifications :', error.message);
            });
    }

    function loadMessages() {
        fetch('/message/ajax/unread')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des messages');
                }
                return response.json();
            })
            .then(data => {
                console.log('✉️ Messages chargés :', data.messages);
                messageMenu.innerHTML = '';

                if (data.count > 0) {
                    messageCount.style.display = 'inline-block';
                    messageCount.textContent = data.count;

                    data.messages.forEach(message => {
                        const item = document.createElement('li');
                        const link = document.createElement('a');
                        link.classList.add('dropdown-item');
                        link.href = `/message/read/${message.id}`;
                        link.textContent = `${message.sender} : ${message.content}`;
                        item.appendChild(link);
                        messageMenu.appendChild(item);
                    });
                } else {
                    messageCount.style.display = 'none';
                    const item = document.createElement('li');
                    item.innerHTML = '<span class="dropdown-item text-muted">Aucun nouveau message</span>';
                    messageMenu.appendChild(item);
                }
            })
            .catch(error => {
                console.error('❌ Erreur chargement messages :', error.message);
            });
    }

    // Chargement initial
    if (notificationCount && notificationMenu) {
        loadNotifications();
        setInterval(loadNotifications, 30000);
    }

    if (markAllReadButton) {
        markAllReadButton.addEventListener('click', function (event) {
            event.preventDefault();
            fetch('/notification/mark-all-read', { method: 'POST' })
                .then(() => loadNotifications());
        });
    }

    if (messageCount && messageMenu) {
        loadMessages();
        setInterval(loadMessages, 30000);
    }
});

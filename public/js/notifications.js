document.addEventListener('DOMContentLoaded', function () {
    const notificationCount = document.getElementById('notification-count');
    const notificationMenu = document.getElementById('notificationDropdownMenu');
    const markAllReadButton = document.getElementById('mark-all-read');

    const messageCount = document.getElementById('message-count');
    const messageMenu = document.getElementById('messageDropdownMenu');

    function loadNotifications() {
        fetch('/notifications/ajax/unread')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    notificationCount.style.display = 'inline-block';
                    notificationCount.textContent = data.count;
                } else {
                    notificationCount.style.display = 'none';
                }

                notificationMenu.innerHTML = '';

                if (data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        const item = document.createElement('li');
                        const link = document.createElement('a');
                        link.classList.add('dropdown-item');
                        link.href = notification.url;
                        link.textContent = notification.message;

                        link.addEventListener('click', function (event) {
                            event.preventDefault();
                            fetch(`/notifications/mark-as-read/${notification.id}`)
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

                    notificationMenu.appendChild(markAllReadButton.parentElement);
                } else {
                    const item = document.createElement('li');
                    item.innerHTML = '<span class="dropdown-item text-muted">Aucune nouvelle notification</span>';
                    notificationMenu.appendChild(item);
                }
            })
            .catch(error => {
                console.error('Erreur notifications :', error);
            });
    }

    if (notificationCount && notificationMenu) {
        loadNotifications();
        setInterval(loadNotifications, 30000);
    }

    if (markAllReadButton) {
        markAllReadButton.addEventListener('click', function (event) {
            event.preventDefault();
            fetch('/notifications/mark-all-as-read')
                .then(() => {
                    loadNotifications();
                });
        });
    }

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
                        link.href = `/message/read/${message.id}`;
                        link.textContent = `${message.sender} : ${message.content}`;

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
                console.error('Erreur chargement messages :', error);
            });
    }

    if (messageCount && messageMenu) {
        loadMessages();
        setInterval(loadMessages, 30000);
    }
});

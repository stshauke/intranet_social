document.addEventListener('DOMContentLoaded', function () {
    const paginationContainer = document.getElementById('pagination-container');
    const messagesContainer = document.getElementById('messages-container');

    if (paginationContainer) {
        paginationContainer.addEventListener('click', function (e) {
            if (e.target.tagName === 'A') {
                e.preventDefault();
                const url = e.target.getAttribute('href');

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newMessages = doc.getElementById('messages-container');
                    const newPagination = doc.getElementById('pagination-container');

                    if (newMessages && newPagination) {
                        messagesContainer.innerHTML = newMessages.innerHTML;
                        paginationContainer.innerHTML = newPagination.innerHTML;
                    }
                })
                .catch(error => console.error('Erreur de pagination AJAX :', error));
            }
        });
    }
});

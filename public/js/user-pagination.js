// public/js/user-pagination.js

document.addEventListener('DOMContentLoaded', function () {
    const userList = document.getElementById('user-list');
    const pagination = document.getElementById('pagination');

    if (!pagination) return;

    pagination.addEventListener('click', function (e) {
        if (e.target.tagName === 'A') {
            e.preventDefault();
            const url = e.target.href;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newUserList = doc.getElementById('user-list');
                const newPagination = doc.getElementById('pagination');

                if (newUserList && newPagination) {
                    userList.innerHTML = newUserList.innerHTML;
                    pagination.innerHTML = newPagination.innerHTML;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            })
            .catch(error => console.error('Erreur pagination :', error));
        }
    });
});

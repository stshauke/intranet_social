document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('messages-container');
    const pagination = document.getElementById('pagination');

    if (container && pagination) {
        pagination.addEventListener('click', function (e) {
            if (e.target.tagName === 'A') {
                e.preventDefault();
                fetch(e.target.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    container.innerHTML = doc.getElementById('messages-container').innerHTML;
                    pagination.innerHTML = doc.getElementById('pagination').innerHTML;
                });
            }
        });
    }
});

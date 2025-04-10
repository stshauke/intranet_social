document.addEventListener('DOMContentLoaded', function () {
    const likeButtons = document.querySelectorAll('.like-comment-btn');

    likeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const url = this.getAttribute('data-url');

            fetch(url, { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.likeCount !== undefined) {
                        const countSpan = this.querySelector('.like-count');
                        countSpan.textContent = data.likeCount;

                        if (data.liked) {
                            this.classList.remove('btn-outline-primary');
                            this.classList.add('btn-primary');
                        } else {
                            this.classList.remove('btn-primary');
                            this.classList.add('btn-outline-primary');
                        }
                    }
                })
                .catch(error => console.error('Erreur:', error));
        });
    });
});

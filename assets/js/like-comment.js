document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.like-comment-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const commentId = this.dataset.commentId;
            const url = `/like-comment/${commentId}/like-ajax`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.innerHTML = `❤️ J'aime (${data.likeCount})`;
                if (data.liked) {
                    this.classList.add('btn-success');
                    this.classList.remove('btn-outline-success');
                } else {
                    this.classList.add('btn-outline-success');
                    this.classList.remove('btn-success');
                }
            })
            .catch(error => {
                console.error('Erreur lors du like du commentaire :', error);
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.like-post-btn').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const postId = this.dataset.postId;
            const url = `/post/${postId}/like-ajax`;

            const originalHTML = this.innerHTML;
            this.innerHTML = '⏳';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.innerHTML = `👍 J'aime (${data.likeCount})`;
                if (data.liked) {
                    this.classList.add('btn-primary');
                    this.classList.remove('btn-outline-primary');
                } else {
                    this.classList.add('btn-outline-primary');
                    this.classList.remove('btn-primary');
                }
            })
            .catch(error => {
                console.error('Erreur lors du like du post :', error);
                this.innerHTML = originalHTML;
            });
        });
    });
});

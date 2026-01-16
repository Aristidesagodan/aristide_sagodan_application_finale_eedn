document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('toggleTheme');
    btn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        document.body.classList.toggle('light-mode');
    });
});

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById('showPassword').addEventListener('change', function() {
        let passwordField = document.getElementById('password');
        passwordField.type = this.checked ? 'text' : 'password';
    });

    document.getElementById('username').addEventListener('input', function() {
        let feedback = document.getElementById('usernameFeedback');
        feedback.textContent = this.value.length < 5 ? 'Username too short!' : '';
    });

    document.getElementById('password').addEventListener('input', function() {
        let feedback = document.getElementById('passwordFeedback');
        feedback.textContent = this.value.length < 8 ? 'Weak password!' : '';
    });

    document.getElementById('email').addEventListener('input', function() {
        let feedback = document.getElementById('emailFeedback');
        feedback.textContent = !this.value.includes('@') ? 'Invalid email!' : '';
    });
});

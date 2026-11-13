const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');

const togglePassword = document.getElementById('togglePassword');
const formMessage = document.getElementById('formMessage');

togglePassword.addEventListener('click', () => {
    const isHidden = passwordInput.type === 'password';

    passwordInput.type = isHidden ? 'text' : 'password';
    togglePassword.textContent = isHidden ? 'HIDE' : 'SHOW';
    togglePassword.setAttribute(
        'aria-label',
        isHidden ? 'Hide password' : 'Show password'
    );
});

loginForm.addEventListener('submit', (event) => {
    event.preventDefault();

    document.getElementById('emailError').textContent = '';
    document.getElementById('passwordError').textContent = '';
    formMessage.textContent = '';

    const email = emailInput.value.trim();
    const password = passwordInput.value;

    let valid = true;

    if (!email) {
        document.getElementById('emailError').textContent =
            'Please enter your email address.';
        valid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('emailError').textContent =
            'Please enter a valid email address.';
        valid = false;
    }

    if (!password) {
        document.getElementById('passwordError').textContent =
            'Please enter your password.';
        valid = false;
    }

    if (!valid) return;

    formMessage.textContent =
        'Login interface is ready. Secure authentication will be added in Commit 3.';
});

document.getElementById('forgotLink').addEventListener('click', (event) => {
    event.preventDefault();

    formMessage.textContent =
        'Password recovery will be available in a future authentication update.';
});
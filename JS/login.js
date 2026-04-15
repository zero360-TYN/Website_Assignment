const wrapper = document.querySelector('.wrapper');
const signUpLink = document.querySelector('.register-link');
const loginLink = document.querySelector('.login-link');

if (signUpLink) signUpLink.onclick = () => wrapper.classList.add('active');
if (loginLink) loginLink.onclick = () => wrapper.classList.remove('active');
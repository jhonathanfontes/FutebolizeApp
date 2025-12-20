const loginCard = document.getElementById('loginCard');
const registerCard = document.getElementById('registerCard');
const toRegister = document.getElementById('toRegister');
const toLogin = document.getElementById('toLogin');

toRegister.addEventListener('click', (e) => {
    e.preventDefault();
    loginCard.classList.add('hidden');
    setTimeout(() => {
        registerCard.classList.remove('hidden');
    }, 100);
});

toLogin.addEventListener('click', (e) => {
    e.preventDefault();
    registerCard.classList.add('hidden');
    setTimeout(() => {
        loginCard.classList.remove('hidden');
    }, 100);
});
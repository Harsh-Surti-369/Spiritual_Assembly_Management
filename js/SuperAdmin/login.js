// function validateForm() {
//     let password = document.getElementById('password').value;
//     let emailError = document.getElementById('emailError');
//     let passwordError = document.getElementById('passwordError');
//     let email = document.getElementById('email').value;

//     // Email validation
//     var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//     if (!emailRegex.test(email)) {
//         emailError.innerText = "Please enter a valid email address";
//         return false;
//     } else {
//         emailError.innerText = "";
//     }

//     // Password validation
//     // var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
//     // if (!passwordRegex.test(password)) {
//     //     passwordError.innerText = "Password must be at least 8 characters long, contain at least one lowercase letter, one uppercase letter, and one digit";
//     //     return false;
//     // } else {
//     //     passwordError.innerText = "";
//     // }

//     return true;
// }

function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

const emailForm = document.getElementById('emailForm');
const emailInput = document.getElementById('email');
const emailError = document.getElementById('emailError');

emailForm.addEventListener('submit', function (event) {
    event.preventDefault();

    const email = emailInput.value.trim();

    if (email === '') {
        emailError.textContent = 'Email is required';
    } else if (!validateEmail(email)) {
        emailError.textContent = 'Invalid email format';
    } else {
        // Reset error message
        emailError.textContent = '';
        // Submit the form (or perform other actions)
        emailForm.submit();
    }
});
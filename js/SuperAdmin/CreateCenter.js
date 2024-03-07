// // Password validation function
// function validatePassword() {
//     var passwordInput = document.getElementById('leaderPassword');
//     // var passwordFeedback = document.querySelector('.password-feedback');
//     var password = passwordInput.value;

//     // Password validation rules
//     var minLength = 8;
//     var uppercaseRegex = /[A-Z]/;
//     var lowercaseRegex = /[a-z]/;
//     var numericRegex = /[0-9]/;

//     if (password.length < minLength || !uppercaseRegex.test(password) || !lowercaseRegex.test(password) || !numericRegex.test(password)) {
//         passwordFeedback.style.display = 'block';
//         return false;
//     } else {
//         passwordFeedback.style.display = 'none';
//         return true;
//     }
// }

// // Form submission validation
// document.getElementById('createCenter').addEventListener('submit', function (event) {
//     if (!validatePassword()) {
//         event.preventDefault();
//     }
// });

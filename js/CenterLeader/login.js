// // Function to validate email format
// function validateEmail(email) {
//     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//     return emailRegex.test(email);
// }

// // Function to validate password format
// function validatePassword(password) {
//     // Add your password criteria here (e.g., minimum length, uppercase, lowercase, digits, etc.)
//     const passwordRegex = /^(?=.[A-Za-z])(?=.\d)[A-Za-z\d]{8,}$/;
//     return passwordRegex.test(password);
// }

// // Function to handle form submission
// function validateForm() {
//     const email = document.getElementById("email").value;
//     const password = document.getElementById("password").value;

//     // Reset error messages
//     document.getElementById("emailError").textContent = "";
//     document.getElementById("passwordError").textContent = "";

//     // Validate email
//     if (!validateEmail(email)) {
//         document.getElementById("emailError").textContent = "Invalid email format";
//         return false;
//     }

//     // Validate password
//     // if (!validatePassword(password)) {
//     //     document.getElementById("passwordError").textContent = "Invalid password format";
//     //     return true;
//     // }

//     // Add additional form submission logic if needed
//     // For now, let the form submit
//     return true;
// }

// document.addEventListener("click", function (event) {
//     const targetElement = event.target;
//     if (!(targetElement.matches("#email") || targetElement.matches("#password"))) {
//         validateForm();
//     }
// });
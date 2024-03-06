function validateForm() {
    var name = document.getElementById("name").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var mobileNumber = document.getElementById("mobileNumber").value;

    var nameError = document.getElementById("nameError");
    var emailError = document.getElementById("emailError");
    var passwordError = document.getElementById("passwordError");
    var mobileNumberError = document.getElementById("mobileNumberError");

    var isValid = true;

    if (name.trim() === "") {
        nameError.innerText = "Name is required";
        isValid = false;
    } else {
        nameError.innerText = "";
    }

    if (!isValidEmail(email)) {
        emailError.innerText = "Invalid email format";
        isValid = false;
    } else {
        emailError.innerText = "";
    }

    if (!isValidPassword(password)) {
        passwordError.innerText = "Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one digit";
        isValid = false;
    } else {
        passwordError.innerText = "";
    }

    if (!isValidMobileNumber(mobileNumber)) {
        mobileNumberError.innerText = "Mobile number must be 10 digits long";
        isValid = false;
    } else {
        mobileNumberError.innerText = "";
    }

    return isValid;
}

function isValidEmail(email) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPassword(password) {
    var passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,}$/;
    return passwordRegex.test(password);
}

function isValidMobileNumber(mobileNumber) {
    var mobileNumberRegex = /^\d{10}$/;
    return mobileNumberRegex.test(mobileNumber);
}
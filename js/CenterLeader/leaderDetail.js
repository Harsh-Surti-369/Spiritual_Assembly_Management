function validateForm(event) {
    var name = document.getElementById("name").value;
    var mobile = document.getElementById("mobile").value;
    var dob = document.getElementById("dob").value;
    var gender = document.querySelector('input[name="gender"]:checked');

    if (name === "" || mobile === "" || dob === "" || !gender) {
        event.preventDefault();
        alert("Please fill in all fields.");
        return false;
    }

    // Validate mobile number length
    if (mobile.length !== 10) {
        event.preventDefault();
        alert("Mobile number must be 10 digits.");
        return false;
    }

    // Additional validation can be added here if needed

    return true;
}
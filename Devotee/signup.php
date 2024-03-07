<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp as devotee</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/Devotee/signup.css">
</head>

<body>
    <div class="container">
        <div class="form-container">
            <img src="https://yt3.googleusercontent.com/h-7oh6u7mXrnxy_9BKLdg2CA2pjIx_jADK5ocj6Y4-T60yFPoLlRNgH3bFK2Vu__GRysIMcnaEI=s176-c-k-c0x00ffffff-no-rj" alt="Logo" class="logo rounded-circle">
            <h2 class="text-center mb-4">Prabodham Weekly Assembly</h2>
            <h3 class="text-center mb-4">Devotee Signup</h3>
            <form action="../php/Dsignup.php" method="post" id="signupForm" onsubmit="return validateForm()">
                <div class="form-group row">
                    <label for="name" class="col-sm-3 col-form-label">Name:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="name" placeholder="Enter your name" required>
                        <span id="nameError" class="error"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-3 col-form-label">Email:</label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" id="email" placeholder="Enter your email" required>
                        <span id="emailError" class="error"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-3 col-form-label">Password:</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" id="password" placeholder="Enter your password" required>
                        <span id="passwordError" class="error"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="dob" class="col-sm-3 col-form-label">Date of Birth:</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control" id="dob">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="gender" class="col-sm-3 col-form-label">Gender:</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="gender">
                            <option selected disabled>Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="mobileNumber" class="col-sm-3 col-form-label">Mobile Number:</label>
                    <div class="col-sm-9">
                        <input type="tel" class="form-control" id="mobileNumber" placeholder="Enter your mobile number" required>
                        <span id="mobileNumberError" class="error"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="spiritualCenter" class="col-sm-3 col-form-label">Nearest Spiritual Center:</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="spiritualCenter" required>
                            <option selected disabled>Select</option>
                            <?php
                            // Fetch spiritual centers from the database and populate options
                            // Example: $centers is an array containing center data
                            foreach ($centers as $center) {
                                echo "<option value='{$center['center_id']}'>{$center['center_name']}</option>";
                            }
                            ?>
                        </select>
                        <span id="centerError" class="error"></span>
                    </div>
                </div>
                <button type="submit" class="btn submit btn-block">Sign Up</button>
            </form>
            <p class="text-center mt-3">Already have an account? <a href="login.html">Login</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/Devotee/signup.js"></script>
</body>

</html>
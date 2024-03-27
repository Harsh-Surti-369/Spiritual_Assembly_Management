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
            <img src="../images/Logo.png" alt="Logo" class="logo rounded-circle">
            <h2 class="text-center mb-4">Prabodham Weekly Assembly</h2>
            <h3 class="text-center mb-4">Devotee Signup</h3>
            <form action="../php/Dsignup.php" method="post" id="signupForm" onsubmit="return validateForm()">
                <div class="form-group row">
                    <label for="name" class="col-sm-3 col-form-label">Name:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                        <span id="nameError" class="error"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-3 col-form-label">Email:</label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        <span id="emailError" class="error"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-3 col-form-label">Password:</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        <span id="passwordError" class="error"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="dob" class="col-sm-3 col-form-label">Date of Birth:</label>
                    <div class="col-sm-9">
                        <input type="date" class="form-control" id="dob" name="dob">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="gender" class="col-sm-3 col-form-label">Gender:</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="gender" name="gender" required>
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
                        <input type="tel" class="form-control" id="mobileNumber" name="mobileNumber" placeholder="Enter your mobile number" required>
                        <span id="mobileNumberError" class="error"></span>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="spiritualCenter" class="col-sm-3 col-form-label">Nearest Spiritual Center:</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="spiritualCenter" name="spiritualCenter" required>
                            <option selected disabled>Select</option>
                            <?php
                            require('../php/dbConnect.php');

                            $query = "SELECT center_id, center_name FROM tbl_center";
                            $result = mysqli_query($conn, $query);

                            if (mysqli_num_rows($result) > 0) {
                                // Output data of each row
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['center_id']}'>{$row['center_name']}</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No centers available</option>";
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
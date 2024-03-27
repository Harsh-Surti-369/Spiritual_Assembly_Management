<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to PWA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/Devotee/login.css">
</head>

<body>
    <section class="d-flex my-2 h-80 justify-content-center align-items-center gradient-form">
        <div class="card rounded-3 text-black shadow-lg d3">
            <div class="card-body p-md-5 mx-md-4">
                <div class="text-center">
                    <img src="../images/Logo.png" style="width: 185px;" alt="logo" class="my-1 rounded-circle logo">
                    <h2 class="text-center mb-4">Prabodham Weekly Assembly</h2>
                    <h3 class="text-center mb-4">Devotee Login</h3>
                </div>
                <form id="superAdminLoginForm" action="../php/Dlogin.php" method="post" onsubmit="return validateForm()">
                    <div class="form-group mb-4">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                        <label class="form-label" for="email">Email</label>
                        <span id="emailError" class="text-danger"></span>
                    </div>
                    <div class="form-group mb-4">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                        <label class="form-label" for="password">Password</label>
                        <br><span id="passwordError" class="text-danger"></span>
                    </div>
                    <div class="text-center pt-1 mb-3 pb-1">
                        <button class="btn btn-block fa-lg mb-3 gradient-custom-2 submit" type="submit">Log in</button>
                    </div>
                    <div class="text-center pt-1 mb-5 pb-1">
                        <p>Don't Have an Account?</p> <a href="signup.php">Create Account</a>
                    </div>
                </form>

            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/Spiritual_Assembly_Management/js/Devotee/login.js"></script>
</body>

</html>
<?php
include('../php/dbConnect.php');
session_start();

// Check if form is submitted
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT password FROM super_admin WHERE email = '$email'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['super_admin_email'] = $email;
            header('Location:index.php');
            exit; // Ensure script stops executing after redirection
        } else {
            $errorMessage = "Invalid email or password";
        }
    } else {
        $errorMessage = "Invalid email or password";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin login</title>
    <link rel="shortcut icon" href="../images/Logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-image: url(../images/bgCanada.png);
        }

        h1 {
            color: #0C2D57;
            font-weight: bold;
        }

        section>div {
            width: 40vw;
        }

        .d3 {
            background-color: #EFECEC;
            background-blend-mode: lighten;
        }

        .gradient-form {
            height: 100vh;
        }

        .logo {
            width: 12rem !important;
            height: 9rem !important;
        }

        .submit {
            background-color: #0C2D57;
            color: #EFECEC;
        }

        .error-message {
            color: red;
        }
    </style>
</head>

<body>
    <section class="d-flex my-2 h-80 justify-content-center align-items-center gradient-form">
        <div class="card rounded-3 shadow-lg d3">
            <div class="card-body p-md-5 mx-md-4">
                <div class="text-center">
                    <img src="../images/logo.png" alt="logo" class="m-1 logo">
                    <h1 class="text-center my-3">Admin Login</h1>
                </div>
                <?php if (isset($errorMessage) && !empty($errorMessage)) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $errorMessage; ?>
                    </div>
                <?php } ?>
                <form class="needs-validation" novalidate id="emailForm" action="login.php" method="post" onsubmit="return validateForm()">
                    <div class="form-group mb-4">
                        <input name="email" type="email" id="email" class="form-control" placeholder="Email" required>
                        <div class="invalid-feedback">
                            Please enter a valid email address.
                        </div>
                    </div>
                    <div class="form-group mb-4">
                        <input name="password" type="password" id="password" class="form-control" placeholder="Password" required>
                        <div class="invalid-feedback">
                            Password must be at least 6 characters long.
                        </div>
                    </div>
                    <div class="text-center pt-1 mb-5 pb-1">
                        <button class="btn btn-block fa-lg mb-3 gradient-custom-2 submit" type="submit">Log in</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const email = document.getElementById('email').value;

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('email').classList.add('is-invalid');
                return false;
            } else {
                document.getElementById('email').classList.remove('is-invalid');
            }

            // Password validation
            const passwordRegex = /^.{6,}$/;
            if (!passwordRegex.test(password)) {
                document.getElementById('password').classList.add('is-invalid');
                return false;
            } else {
                document.getElementById('password').classList.remove('is-invalid');
            }

            return true;
        }
    </script>
</body>

</html>
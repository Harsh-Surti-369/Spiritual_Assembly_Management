<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperAdmin login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/SuperAdmin/login.css">
</head>

<body>
    <section class="d-flex my-2 h-80 justify-content-center align-items-center gradient-form">
        <div class="card rounded-3 text-black shadow-lg d3">
            <div class="card-body p-md-5 mx-md-4">
                <div class="text-center">
                    <img src="../images/logo.jpg" style="width: 185px;" alt="logo" class="my-1 rounded-circle logo">
                    <h2 class="text-center mb-4">Prabodham Weekly Assembly</h2>
                    <h3 class="text-center mb-4">Super Admin</h3>
                </div>
                <form id="emailForm" action="login.php" method="post" onsubmit="return validateEmail()">
                    <div class="form-group mb-4">
                        <input name="email" type="email" id="email" class="form-control" placeholder="Email" required>
                        <label class="form-label" for="email">Email</label>
                        <span class="error-message" id="emailError"></span>
                    </div>
                    <div class="form-group mb-4">
                        <input name="password" type="password" id="password" class="form-control" placeholder="Password" required>
                        <label class="form-label" for="password">Password</label>
                    </div>
                    <div class="text-center pt-1 mb-5 pb-1">
                        <button class="btn btn-block fa-lg mb-3 gradient-custom-2 submit" type="submit">Log
                            in</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script src="/Spiritual_Assembly_Management/js/SuperAdmin/login.js"></script>
</body>

</html>
<?php
include('../php/dbConnect.php');
session_start();

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
    } else {
        echo "Invalid email or password";
    }
} else {
    echo "Invalid email or password";
}

$conn->close();
?>
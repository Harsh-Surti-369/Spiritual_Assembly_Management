<?php
session_start();
include('../php/dbConnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT leader_id, email, password FROM tbl_leader WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            // Fetch the center_id associated with the leader_id
            $leader_id = $row['leader_id'];
            $sql_fetch_center_id = "SELECT center_id FROM tbl_leader WHERE leader_id = '$leader_id'";
            $result_center_id = $conn->query($sql_fetch_center_id);

            if ($result_center_id->num_rows > 0) {
                $row_center_id = $result_center_id->fetch_assoc();
                $center_id = $row_center_id['center_id'];
                $_SESSION['leader_id'] = $leader_id;
                $_SESSION['center_id'] = $center_id;

                $sql_check_details = "SELECT name,address,DOB,m_no,gender FROM tbl_leader WHERE leader_id = '$leader_id'";
                $result_check_details = $conn->query($sql_check_details);

                if ($result_check_details->num_rows > 0) {
                    // Details already inserted, redirect to another page
                    header("Location: ../centerleader/index.php");
                    exit();
                } else {
                    exit();
                }
            } else {
            }
        } else {
        }
    } else {
    }
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to PWA as Center Leader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <link rel="stylesheet" href="../css/CenterLeader/login.css">
</head>

<body>
    <section class="d-flex my-2 h-80 justify-content-center align-items-center gradient-form">
        <div class="card rounded-3 text-black shadow-lg d3">
            <div class="card-body p-md-5 mx-md-4">
                <div class="text-center">
                    <img src="../images/logo.jpg" style="width: 185px;" alt="logo" class="my-1 rounded-circle logo">
                    <h2 class="text-center mb-4">Prabodham Weekly Assembly</h2>
                    <h3 class="text-center mb-4">Center Leader</h3>
                </div>
                <form action="login.php" method="post" id="superAdminLoginForm">
                    <div class="form-group mb-4">
                        <input name="email" type="email" id="email" class="form-control" placeholder="Email" required>
                        <label class="form-label" for="email">Email</label>
                        <span id="emailError" class="text-danger"></span>
                    </div>
                    <div class="form-group mb-4">
                        <input name="password" type="password" id="password" class="form-control" placeholder="Password" required>
                        <label class="form-label" for="password">Password</label>
                        <br><span id="passwordError" class="text-danger"></span>
                    </div>
                    <div class="text-center pt-1 mb-5 pb-1">
                        <button class="btn btn-block fa-lg mb-3 gradient-custom-2 submit" type="submit">Log
                            in</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script src="../js/CenterLeader/login.js"></script> -->
</body>

</html>
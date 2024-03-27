<?php session_start();
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create new Center</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/SuperAdmin/login.css">
    <style>
        .bg-primary {
            background-color: #0C2D57 !important;
        }

        .bg-secondary {
            background-color: #EFECEC !important;
        }

        .text-primary {
            color: #0C2D57 !important;
        }

        .text-secondary {
            color: #EFECEC !important;
        }

        .btn-primary {
            background-color: #0C2D57 !important;
            border-color: #0C2D57 !important;
        }

        .btn-primary:hover {
            background-color: #0C2D57 !important;
            border-color: #0C2D57 !important;
        }

        .btn-secondary {
            background-color: #EFECEC !important;
            border-color: #EFECEC !important;
            color: #0C2D57 !important;
        }

        .btn-secondary:hover {
            background-color: #EFECEC !important;
            border-color: #EFECEC !important;
            color: #0C2D57 !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="centers.php">Centers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="devotees.php">Devotees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="content.php">Content</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <section class="d-flex my-2 h-80 justify-content-center align-items-center gradient-form">
        <div class="container py-1">
            <div class="col-xl-6 offset-xl-3">
                <div class="card rounded-3 text-black shadow-lg d3">
                    <div class="card-body p-md-5 mx-md-4">
                        <div class="text-center">
                            <img src="../images/Logo.png" style="width: 185px;" alt="logo" class="my-1 rounded-circle logo">
                            <h3 class="mt-1 mb-5 pb-1 brand">Prabodham Weekly Assembly</h3>
                        </div>
                        <form action="createCenter.php" method="post" id="createCenter" onsubmit="return validateForm()">
                            <div class="form-group mb-4">
                                <input name="name" type="name" id="name" class="form-control" placeholder="Center Name" required>
                                <label class="form-label" for="name">Center Name</label>
                                <span id="name" class="text-danger"></span>
                            </div>

                            <div class="form-group mb-4">
                                <input name="address" type="address" id="address" class="form-control" placeholder="address for center" required>
                                <label class="form-label" for="name">Address for Center</label>
                                <span id="name" class="text-danger"></span>
                            </div>
                            <div class="form-group mb-4">
                                <input name="email" type="email" id="email" class="form-control" placeholder="Email" required>
                                <label class="form-label" for="email">Email of leader</label>
                                <span id="emailError" class="text-danger"></span>
                            </div>
                            <div class="form-group mb-4">
                                <input name="password" type="password" id="leaderPassword" class="form-control" placeholder="Password" required>
                                <label class="form-label" for="password">Password of leader</label>
                                <br><span id="passwordError" class="text-danger"></span>
                            </div>
                            <div class="text-center pt-1 mb-1 pb-1">
                                <button class="btn btn-block fa-lg mb-1 gradient-custom-2 submit" type="submit">Create center</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Toast -->
    <div class="toast toast-success" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="toast-header">
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Center created successfully. Center leader login credentials have been emailed to <?php echo $email; ?>
        </div>
    </div>

    <!-- Failure Toasts -->
    <div class="toast toast-failure" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="toast-header">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Failed to send email.
        </div>
    </div>

    <div class="toast toast-error-update" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="toast-header">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Error updating center with leader ID.
        </div>
    </div>

    <div class="toast toast-error-leader" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="toast-header">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Error creating center leader.
        </div>
    </div>

    <div class="toast toast-error-center" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="toast-header">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close" data-bs dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Error creating center.
        </div>
    </div>

    <div class="toast toast-error-fields" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="toast-header">
            <strong class="me-auto">Error</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            All required fields are not provided.
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>

</html>

<?php
include('../php/dbConnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are present in the $_POST array
    if (isset($_POST['name'], $_POST['address'], $_POST['email'], $_POST['password'])) {
        $center_name = $_POST['name'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql_insert_center = "INSERT INTO tbl_center (center_name, location, starting_date) 
                              VALUES ('$center_name', '$address', NOW())";
        if ($conn->query($sql_insert_center) === TRUE) {
            $center_id = $conn->insert_id;
            $sql_insert_leader = "INSERT INTO tbl_leader (email, password, address, center_id) 
                                  VALUES ('$email', '$hashed_password', '$address', $center_id)";
            if ($conn->query($sql_insert_leader) === TRUE) {
                $leader_id = $conn->insert_id;

                // Update the tbl_center with the leader_id
                $sql_update_center = "UPDATE tbl_center SET leader_id = $leader_id WHERE center_id = $center_id";
                if ($conn->query($sql_update_center) === TRUE) {
                    // Send email with login credentials
                    $to = $email;
                    $subject = "Your Center Leader Account Details";
                    $message = "Dear Center Leader,\n\n";
                    $message .= "Your account has been successfully created.\n";
                    $message .= "Email: $email\n";
                    $message .= "Password: $password\n\n";
                    $message .= "Please use these credentials to log in to your account.\n\n";
                    $message .= "Thank you,\nThe Admin";
                    $headers = 'From: harsurati@gmail.com';
                    if (mail($to, $subject, $message, $headers)) {
                        // Display success message using JavaScript
                        echo '<script>';
                        echo '  $(document).ready(function(){';
                        echo '      $(".toast-success").toast("show");';
                        echo '  });';
                        echo '</script>';
                    } else {
                        // Display failure message using JavaScript
                        echo '<script>';
                        echo '  $(document).ready(function(){';
                        echo '      $(".toast-failure").toast("show");';
                        echo '  });';
                        echo '</script>';
                    }
                } else {
                    // Display error message using JavaScript
                    echo '<script>';
                    echo '  $(document).ready(function(){';
                    echo '      $(".toast-error-update").toast("show");';
                    echo '  });';
                    echo '</script>';
                }
            } else {
                // Display error message using JavaScript
                echo '<script>';
                echo '  $(document).ready(function(){';
                echo '      $(".toast-error-leader").toast("show");';
                echo '  });';
                echo '</script>';
            }
        } else {
            // Display error message using JavaScript
            echo '<script>';
            echo '  $(document).ready(function(){';
            echo '      $(".toast-error-center").toast("show");';
            echo '  });';
            echo '</script>';
        }
    } else {
        // Display error message using JavaScript
        echo '<script>';
        echo '  $(document).ready(function(){';
        echo '      $(".toast-error-fields").toast("show");';
        echo '  });';
        echo '</script>';
    }
} else {
}

$conn->close();
?>
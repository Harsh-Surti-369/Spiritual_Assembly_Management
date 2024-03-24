<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create new Center</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/SuperAdmin/login.css">
</head>

<body>
    <section class="d-flex my-2 h-80 justify-content-center align-items-center gradient-form">
        <div class="container py-1">
            <div class="col-xl-6 offset-xl-3">
                <div class="card rounded-3 text-black shadow-lg d3">
                    <div class="card-body p-md-5 mx-md-4">
                        <div class="text-center">
                            <img src="https://yt3.googleusercontent.com/h-7oh6u7mXrnxy_9BKLdg2CA2pjIx_jADK5ocj6Y4-T60yFPoLlRNgH3bFK2Vu__GRysIMcnaEI=s176-c-k-c0x00ffffff-no-rj" style="width: 185px;" alt="logo" class="my-1 rounded-circle logo">
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
                            <div class="text-center pt-1 mb-5 pb-1">
                                <button class="btn btn-block fa-lg mb-3 gradient-custom-2 submit" type="submit">Log
                                    in</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- <script src="../js/SuperAdmin/CreateCenter.js"></script> -->
</body>

</html>

<?php
session_start();
include('dbConnect.php');

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
                        echo "Center created successfully. Center leader login credentials have been emailed to $email";
                    } else {
                        echo "Failed to send email.";
                    }
                } else {
                    echo "Error updating center with leader ID: " . $conn->error;
                }
            } else {
                echo "Error creating center leader: " . $conn->error;
            }
        } else {
            echo "Error creating center: " . $conn->error;
        }
    } else {
        echo "All required fields are not provided.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
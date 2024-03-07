<?php

include('dbConnect.php');

// $center_name = $_POST['name'];
// $address = $_POST['address'];
// $email = $_POST['email'];
// $password = $_POST['password'];

// if ($_SERVER["REQUEST_METHOD"] == "POST") {


//     $hashed_password = password_hash($password, PASSWORD_DEFAULT);

//     $sql_insert_center = "INSERT INTO tbl_center (center_name, location, starting_date) 
//                           VALUES ('$center_name', '$address', NOW())";

//     if ($conn->query($sql_insert_center) === TRUE) {
//         $center_id = $conn->insert_id;

//         $sql_insert_leader = "INSERT INTO tbl_leader (name, email, password, address, center_id) 
//                               VALUES ('$center_name', '$email', '$hashed_password', '$address', $center_id)";

//         if ($conn->query($sql_insert_leader) === TRUE) {
//             echo "Center created successfully. Center leader login credentials have been emailed to $email ";
//         } else {
//             echo "Error: " . $sql_insert_leader . "<br>" . $conn->error;
//         }
//     } else {
//         echo "Error: " . $sql_insert_center . "<br>" . $conn->error;
//     }
// }

// $to = $_POST['email'];
// $subject = "Your Center Leader Account Details";
// $message = "Dear Center Leader,\n\n";
// $message .= "Your account has been successfully created.\n";
// $message .= "Email: " . $_POST['email'] . "\n";
// $message .= "Password: " . $_POST['password'] . "\n\n";
// $message .= "Please use these credentials to log in to your account.\n\n";
// $message .= "Thank you,\nThe Admin";

// // Send email using PHP's mail function
// $headers = 'From: harsurati@gmail.com';
// if (mail($to, $subject, $message, $headers)) {
//     echo "Email sent successfully.";
// } else {
//     echo "Failed to send email.";
// }

// $conn->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are present in the $_POST array
    if (isset($_POST['name'], $_POST['address'], $_POST['email'], $_POST['password'])) {
        $center_name = $_POST['name'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute SQL query to insert center information
        $sql_insert_center = "INSERT INTO tbl_center (center_name, location, starting_date) 
                              VALUES ('$center_name', '$address', NOW())";
        if ($conn->query($sql_insert_center) === TRUE) {
            $center_id = $conn->insert_id;

            // Prepare and execute SQL query to insert leader information
            $sql_insert_leader = "INSERT INTO tbl_leader (name, email, password, address, center_id) 
                                  VALUES ('$center_name', '$email', '$hashed_password', '$address', $center_id)";
            if ($conn->query($sql_insert_leader) === TRUE) {
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

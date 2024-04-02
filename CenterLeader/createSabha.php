<?php
session_start();
require('../php/dbConnect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['title'], $_POST['description'], $_POST['speaker'], $_POST['sabhaType'], $_POST['timingFrom'], $_POST['timingTo'], $_POST['location'], $_POST['date'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $speaker = $_POST['speaker'];
        $sabhaType = $_POST['sabhaType'];
        $timingFrom = $_POST['timingFrom'];
        $timingTo = $_POST['timingTo'];
        $location = $_POST['location'];
        $date = $_POST['date'];
        $center_id = $_SESSION['center_id'];

        $currentDate = date('Y-m-d');

        if ($date < $currentDate) {
            echo '<div class="toast gb" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Invalid Date</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">Please select a date that is today or in the future.</div>
          </div>';
            exit(); 
        }

        $sql_insert_sabha = "INSERT INTO tbl_sabha (title, description, speaker, sabha_type, timing_from, timing_to, location, date, center_id)
                             VALUES ('$title', '$description', '$speaker', '$sabhaType', '$timingFrom', '$timingTo', '$location', '$date', '$center_id')";

        if ($conn->query($sql_insert_sabha) === TRUE) {
            header("Location: sabhalist.php");
            exit();
        } else {
            echo '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Error</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">' . "Error creating Sabha: " . $conn->error . '</div>
                  </div>';
        }
    } else {
        echo '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Incomplete Form</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">All required fields are not provided.</div>
              </div>';
    }
} else {
    echo '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Invalid Request</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">Invalid request method.</div>
          </div>';
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <title>Create Sabha Event</title>
    <style>
        :root {
            --primary-color: #0C2D57;
            --secondary-color: #FC6736;
            --accent-color: #FFB0B0;
            --background-color: #EFECEC;
        }

        body {
            color: var(--primary-color);
            background-image: url(../images/bgCanada.png);
            padding-top: 40px;
        }

        h2 {
            font-weight: bolder;
        }

        form {
            background-color: var(--background-color);
            padding: 5%;
            border-radius: 1rem;
        }

        .form-control {
            border-color: var(--primary-color);
            margin: 5px;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 .1rem rgba(252, 103, 54, .25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            margin: 1rem 150px 1rem 290px;
            width: 10rem;
            height: 3rem;
        }

        .btn-primary:hover {
            background-color: #0a2548;
            border-color: #092140;
        }

        label {
            font-weight: 700;
        }

        .toast-container {
            z-index: 1056;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .toast {
            background-color: var(--background-color);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .toast-header {
            background-color: var(--primary-color);
            color: var(--background-color);
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="text-center mb-4">Create Sabha</h2>
                <form action="createSabha.php" method="post" class="p-4 rounded" id="createSabhaForm">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input name="title" type="text" class="form-control" id="title" placeholder="Enter title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea name="description" class="form-control" id="description" placeholder="Enter description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="speaker">Speaker:</label>
                        <input name="speaker" type="text" class="form-control" id="speaker" placeholder="Enter speaker's name" required>
                    </div>
                    <div class="form-group">
                        <label for="sabhaType">Sabha Type:</label>
                        <select name="sabhaType" class="form-control" id="sabhaType" required>
                            <option value="">Select Sabha Type</option>
                            <option value="Weekly">Weekly Sabha</option>
                            <option value="Monthly">Monthly Sabha</option>
                            <option value="Special">Special Sabha</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-5 mx-4" style="display: inline-block;">
                            <label for="timingFrom">Timing (From):</label>
                            <input name="timingFrom" type="time" class="form-control" id="timingFrom" required>
                        </div>
                        <div class="form-group col-md-5 mx-4" style="display: inline-block;">
                            <label for="timingTo">Timing (To):</label>
                            <input name="timingTo" type="time" class="form-control" id="timingTo" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="location">Location:</label>
                        <input name="location" type="text" class="form-control" id="location" placeholder="Enter location" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date:</label>
                        <input name="date" type="date" class="form-control" id="date" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create New Sabha</button>
                </form>
            </div>
        </div>
    </div>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="dateValidationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Invalid Date</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Please select a date that is today or in the future.
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('createSabhaForm');
        const dateInput = document.getElementById('date');

        form.addEventListener('submit', function(event) {
            // Get the selected date value
            const selectedDate = new Date(dateInput.value);
            // Get today's date
            const today = new Date();

            // Check if the selected date is before today
            if (selectedDate < today) {
                // Prevent the default form submission
                event.preventDefault();

                // Show the date validation toast
                const toastElement = document.getElementById('dateValidationToast');
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }
        });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>

</html>
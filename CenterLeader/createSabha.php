<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <title>Create Sabha Event</title>
    <style>
        body {
            color: #0C2D57;
            background-image: url('../images/bgCanada.png');
        }

        h2 {
            font-weight: bolder;
        }

        form {
            background-color: #EFECEC;
            padding: 5%;
            border-radius: 1rem;
        }

        .form-control {
            border-color: #0C2D57;
            margin: 5px;
        }

        .form-control:focus {
            border-color: #FC6736;
            box-shadow: 0 0 0 0.1rem rgba(252, 103, 54, 0.25);
        }

        .btn-primary {
            background-color: #0C2D57;
            border-color: #0C2D57;
            margin: 1rem 150px 1rem 290px;
            width: 10rem;
            height: 3rem;
        }

        .btn-primary:hover {
            background-color: #0a2548;
            border-color: #092140;
        }

        label {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="text-center">Create Sabha</h2>
                <form action="../php/createSabha.php" method="post">
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
                        <div class="form-group col-md-4">
                            <label for="timingFrom">Timing (From):</label>
                            <input name="timingFrom" type="time" class="form-control" id="timingFrom" required>
                        </div>
                        <div class="form-group col-md-4">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Center</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0C2D57;
            --secondary-color: #FC6736;
            --accent-color: #FFB0B0;
            --background-color: #EFECEC;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--background-color);
            color: var(--primary-color);
        }

        .container {
            margin-top: 20px;
        }

        .card {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">My Center</h1>

        <!-- Center Details Card -->
        <div class="card">
            <div class="card-header">
                <h3>Center Details</h3>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> Spiritual Center</p>
                <p><strong>Center Leader:</strong> John Doe</p>
                <p><strong>Contact:</strong> john.doe@example.com | +1234567890</p>
            </div>
        </div>

        <!-- List of Devotees -->
        <div class="card">
            <div class="card-header">
                <h3>List of Devotees</h3>
            </div>
            <div class="card-body">
                <!-- Display the logged in devotee's details -->
                <h4>Me</h4>
                <p><strong>Name:</strong> Jane Smith</p>
                <p><strong>Email:</strong> jane.smith@example.com</p>
                <p><strong>Mobile Number:</strong> +9876543210</p>
                <!-- Add a horizontal line for separation -->
                <hr>

                <!-- Display other devotees' details -->
                <h4>John Wick</h4>
                <p><strong>Email:</strong> john.wick@example.com</p>
                <p><strong>Mobile Number:</strong> +1231231234</p>
                <!-- Add a horizontal line for separation -->
                <hr>

                <h4>Alice Johnson</h4>
                <p><strong>Email:</strong> alice.johnson@example.com</p>
                <p><strong>Mobile Number:</strong> +5555555555</p>
                <!-- Add a horizontal line for separation -->
                <hr>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Center Leader Q&A Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        body {
            background-color: #EFECEC;
            color: #0C2D57;
        }

        .container {
            padding-top: 50px;
            margin-top: 50px;
        }

        .question-table {
            background-color: #FFFFFF;
            border: 1px solid #0C2D57;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .question-table th,
        .question-table td {
            vertical-align: middle;
        }

        .btn-primary {
            background-color: #0C2D57;
            border-color: #0C2D57;
        }

        .btn-primary:hover {
            background-color: #FC6736;
            border-color: #FC6736;
        }
    </style>
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php'); ?>
    </header>
    <div class="container">
        <h1 class="mb-4">Question by Devotees</h1>

        <!-- Question table -->
        <table class="table question-table">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Type</th>
                    <th>Asking Time</th>
                    <th>Devotee Name</th>
                    <th>Anonymous</th>
                    <th>Attachment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include('../php/dbConnect.php');

                // Check if devotee_id is set in session
                if (isset($_SESSION['devotee_id'])) {
                    $devotee_id = $_SESSION['devotee_id'];

                    // Fetch center_id from tbl_devotee
                    $sql_center = "SELECT center_id FROM tbl_devotee WHERE devotee_id = ?";
                    $stmt_center = $conn->prepare($sql_center);
                    $stmt_center->bind_param("i", $devotee_id);
                    $stmt_center->execute();
                    $result_center = $stmt_center->get_result();

                    // Check if center_id is fetched
                    if ($result_center->num_rows > 0) {
                        $row_center = $result_center->fetch_assoc();
                        $center_id = $row_center['center_id'];

                        // Retrieve questions from the database for the center's leader
                        $sql = "SELECT QID, Question, question_type, Asking_Time, Anonymous, Attachment_Path FROM tbl_qna WHERE devotee_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $devotee_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Check if there are any questions
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['QID'] . "</td>";
                                echo "<td>" . $row['Question'] . "</td>";
                                echo "<td>" . $row['question_type'] . "</td>";
                                echo "<td>" . $row['Asking_Time'] . "</td>";
                                echo "<td>" . ($row['Anonymous'] == 1 ? "Anonymous" : "Devotee Name") . "</td>";
                                echo "<td>" . ($row['Anonymous'] == 1 ? "Yes" : "No") . "</td>";
                                echo "<td><a href='" . $row['Attachment_Path'] . "'>Attachment</a></td>";
                                echo "<td><a href='answer.php?qid=" . $row['QID'] . "' class='btn btn-primary'>Answer</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No questions found.</td></tr>";
                        }
                    } else {
                        echo "Center ID not found for the current devotee.";
                    }

                    $stmt_center->close();
                } else {
                }

                $conn->close(); // Close the database connection
                ?>

            </tbody>
        </table>
    </div>
</body>

</html>
<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Center Leader Q&A</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" defer></script>
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        body {
            background-color: #EFECEC;
            color: #0C2D57;
        }

        .container {
            padding-top: 50px;
        }

        .question-card {
            background-color: #FFFFFF;
            border: 1px solid #0C2D57;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .question-details {
            margin-bottom: 20px;
        }

        .question-answer-input {
            width: 100%;
            min-height: 100px;
            resize: vertical;
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
    <?php include('header.php'); ?>
    <div class="container">
        <?php
        include('../php/dbConnect.php');
        // Check if QID is provided in the URL
        if (isset($_GET['qid'])) {
            $qid = $_GET['qid'];

            $sql = "SELECT q.QID, q.Question, q.question_type, q.Asking_Time, d.name, q.Anonymous, q.Attachments
            FROM tbl_qna q
            JOIN tbl_devotee d ON q.devotee_id = d.devotee_id
            WHERE q.QID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $qid);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
        ?>
                <div class="question-card">
                    <div class="question-details">
                        <p><strong>Question Text:</strong> <?php echo $row['Question']; ?></p>
                        <p><strong>Question Type:</strong> <?php echo $row['question_type']; ?></p>
                        <p><strong>Asking Time:</strong> <?php echo $row['Asking_Time']; ?></p>
                        <?php if ($row['Anonymous'] == 'No') : ?>
                            <p><strong>Devotee Name:</strong> <?php echo $row['name']; ?></p>
                        <?php endif; ?>
                        <p><strong>Anonymous Status:</strong> <?php echo ($row['Anonymous'] == 1 ? "Yes" : "No"); ?></p>
                        <?php if (isset($row['Attachment_Path'])) : ?>
                            <p><strong>Attachment:</strong> <a href="<?php echo $row['Attachment_Path']; ?>"><?php echo $row['Attachment_Path']; ?></a></p>
                        <?php endif; ?>

                    </div>
                    <form action="../php/submit-answer.php" method="post">
                        <div class="form-group">
                            <label for="answerInput">Your Answer:</label>
                            <textarea class="form-control question-answer-input" id="answerInput" name="answer" rows="4" placeholder="Enter your answer here..."></textarea>
                        </div>
                        <input type="hidden" name="qid" value="<?php echo $qid; ?>">
                        <button type="submit" class="btn btn-primary">Submit Answer</button>
                    </form>
                </div>
        <?php
            } else {
                echo "<p>No question found with the provided ID.</p>";
            }

            $stmt->close(); // Close the prepared statement
        } else {
            echo "<p>Question ID not provided.</p>";
        }
        ?>
    </div>

</body>

</html>
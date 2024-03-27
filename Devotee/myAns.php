<?php
include('../php/dbConnect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['devotee_id'])) {
    header("Location: login.php");
    exit;
}

$devoteeId = $_SESSION['devotee_id'];

// Initialize alert message variables
$alertType = "";
$alertMessage = "";

// Check if form is submitted to answer a question
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $answer = mysqli_real_escape_string($conn, $_POST['answer']);
    $qid = $_POST['qid'];
    $answerTime = date("Y-m-d H:i:s");

    // Prepare SQL statement to update answer in the database
    $sql = "UPDATE tbl_qna SET Answer = ?, Answer_Time = ? WHERE QID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $answer, $answerTime, $qid);

    // Execute SQL statement
    if ($stmt->execute()) {
        $alertType = "success";
        $alertMessage = "Answer submitted successfully.";
    } else {
        $alertType = "danger";
        $alertMessage = "Error: " . $conn->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Retrieve questions and answers for the logged-in devotee
$sql = "SELECT q.QID, q.question, q.asking_time, q.attachment_path, q.anonymous, q.question_type, q.Answer, q.Answer_Time
        FROM tbl_qna q
        WHERE q.devotee_id = '$devoteeId'
        ORDER BY q.asking_time DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Asked Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #EFECEC;
            color: #0C2D57;
        }

        header {
            margin: 0;
            background-color: transparent;
            width: 100%;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            flex-direction: row;
        }

        .u-logo {
            margin-top: 20px;
        }

        .logo {
            height: 100px;
            width: 100px;
            margin: 10px 20px;
            border-radius: 20px;
        }

        .bg-primary {
            background-color: #0C2D57 !important;
        }

        .text-primary {
            color: #0C2D57 !important;
        }

        .btn-primary {
            background-color: #FC6736;
            border-color: #FC6736;
        }

        .btn-primary:hover {
            background-color: #FFB0B0;
            border-color: #FFB0B0;
        }
    </style>
</head>

<body>
    <?php
    include('header.php');
    ?>
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">My Asked Questions</h1>
            <a href="askquestion.php" class="btn btn-primary">Ask New Question</a>
        </div>

        <?php if (!empty($alertMessage)) : ?>
            <div class="alert alert-<?php echo $alertType; ?>" role="alert">
                <?php echo $alertMessage; ?>
            </div>
        <?php endif; ?>

        <div class="accordion" id="questionsAccordion">
            <?php if ($result->num_rows > 0) : ?>
                <?php $i = 1; ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $i; ?>">
                            <button class="accordion-button <?php echo ($i === 1) ? 'bg-primary text-white' : 'collapsed bg-primary text-white'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i; ?>" aria-expanded="<?php echo ($i === 1) ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $i; ?>">
                                <?php echo $row['question']; ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse <?php echo ($i === 1) ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $i; ?>" data-bs-parent="#questionsAccordion">
                            <div class="accordion-body">
                                <p><strong>Question Type:</strong> <?php echo $row['question_type']; ?></p>
                                <p><strong>Asking Time:</strong> <?php echo $row['asking_time']; ?></p>
                                <?php if (!empty($row['attachment_path'])) : ?>
                                    <p><strong>Attachment:</strong> <a href="<?php echo $row['attachment_path']; ?>" target="_blank">View Attachment</a></p>
                                <?php endif; ?>
                                <?php if (!empty($row['Answer'])) : ?>
                                    <p><strong>Answer:</strong> <?php echo $row['Answer']; ?></p>
                                    <p><strong>Answer Time:</strong> <?php echo $row['Answer_Time']; ?></p>
                                <?php else : ?>
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <div class="mb-3">
                                            <label for="answer" class="form-label">Answer:</label>
                                            <textarea class="form-control" id="answer" name="answer" rows="3" required></textarea>
                                        </div>
                                        <input type="hidden" name="qid" value="<?php echo $row['QID']; ?>">
                                        <button type="submit" class="btn btn-primary">Submit Answer</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php $i++; ?>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No questions asked yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
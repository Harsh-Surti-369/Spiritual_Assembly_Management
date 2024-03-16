<?php
include('../php/dbConnect.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = mysqli_real_escape_string($conn, $_POST['question']);
    $questionType = mysqli_real_escape_string($conn, $_POST['questionType']);
    $devoteeId = $_SESSION['devotee_id'];


    $attachmentPath = "";
    $anonymous = 0;

    if (!empty($_FILES['attachment']['name'])) {
        $attachmentPath = "../q_attach/";
    }

    if (isset($_POST['anonymous'])) {
        $anonymous = 1;
    }

    if (empty($question) || empty($questionType)) {
        echo "Error: Question and question type are required.";
    } else {
        $sql = "INSERT INTO tbl_qna (question, question_type, devotee_id, asking_time, attachment_path, anonymous) 
                VALUES ('$question', '$questionType', '$devoteeId', NOW(), '$attachmentPath', '$anonymous')";

        if ($conn->query($sql) === TRUE) {
            echo "Question submitted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

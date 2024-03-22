<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask a Question</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('../images/bgCanada.png');
        }

        .container {
            margin-top: 50px;
        }

        .form-container {
            background-color: #EFECEC;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: bold;
            color: #0C2D57;
            /* Dark blue */
        }

        .form-control {
            border-color: #FC6736;
        }

        .btn-primary {
            background-color: #FC6736;
            border-color: #FC6736;
        }

        .btn-primary:hover {
            background-color: #FFB0B0;
            border-color: #FFB0B0;
        }

        .form-check-input:checked {
            background-color: #FC6736;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <h2 class="text-center mb-4" style="color: #0C2D57;">Ask a Question</h2>
                    <form action="../php/submitQuestion.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="question">Your Question</label>
                            <textarea class="form-control" id="question" name="question" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="questionType" style="color: #0C2D57;">Question Type</label>
                            <select class="form-control" id="questionType" name="questionType">
                                <option value="Select youe question type" disabled>Select youe question type</option>
                                <option value="financial">Financial</option>
                                <option value="social">Social</option>
                                <option value="mental">Mental</option>
                                <option value="spiritual">Spiritual</option>
                                <option value="personal">Personal</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="attachment" style="color: #0C2D57;">Attachment</label>
                            <input type="file" class="form-control-file" id="attachment" name="attachment">
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="anonymous" name="anonymous">
                            <label class="form-check-label" for="anonymous" style="color: #0C2D57;">Ask
                                Anonymously</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php
session_start();
include('../../php/dbConnect.php');

$devotee_id = $_SESSION['devotee_id'];

$joinDateQuery = "SELECT joining_date FROM tbl_devotee WHERE devotee_id = ?";
$joinDateStmt = $conn->prepare($joinDateQuery);
$joinDateStmt->bind_param("i", $devotee_id);
$joinDateStmt->execute();
$joinDateResult = $joinDateStmt->get_result();

if ($joinDateRow = $joinDateResult->fetch_assoc()) {
  $startDate = $joinDateRow['joining_date'];
  $endDate = date('Y-m-d'); // Today's date
} else {
  // Default to empty string if joining date not found
  $startDate = "";
  $endDate = date('Y-m-d'); // Today's date
}

// Fetch attendance data from the database for lifetime
$sql = "SELECT COUNT(*) AS total_sabhas, 
            SUM(CASE WHEN attendance_status = 'Present' THEN 1 ELSE 0 END) AS present_count
        FROM tbl_attendance
        WHERE devotee_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $devotee_id);

if ($stmt->execute()) {
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    $totalSabhas = $row['total_sabhas'];
    $presentCount = $row['present_count'];

    // Calculate the attendance percentage
    if ($totalSabhas > 0) {
      $attendancePercentage = round(($presentCount / $totalSabhas) * 100, 2);
    } else {
      $attendancePercentage = 0;
    }
  }
} else {
  // Log or display the error
  error_log("Error executing SQL query: " . $stmt->error);
}

// Prepare chart data
$dataPoints = array(
  array("label" => "Present", "y" => $attendancePercentage),
  array("label" => "Absent", "y" => 100 - $attendancePercentage)
);
?>
<!DOCTYPE html>
<html style="font-size: 16px;" lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="utf-8">
  <title>Page 1</title>
  <link rel="stylesheet" href="nicepage.css" media="screen">
  <link rel="stylesheet" href="Page-1.css" media="screen">
  <script class="u-script" type="text/javascript" src="nicepage.js" defer=""></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <link id="u-theme-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i|Open+Sans:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i">
  <link id="u-page-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald:200,300,400,500,600,700|Playfair+Display:400,400i,500,500i,600,600i,700,700i,800,800i,900,900i|Roboto+Slab:100,200,300,400,500,600,700,800,900|Lato:100,100i,300,300i,400,400i,700,700i,900,900i">

  <style>
    /* Custom CSS for styling */
    #chartContainer {
      width: 60%;
      margin: 50px auto;
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
    }
  </style>
</head>

<body data-home-page="Page-1.html" data-home-page-title="Page 1" data-path-to-root="./" data-include-products="false" class="u-body u-overlap u-overlap-transparent u-xl-mode" data-lang="en">
  <header class="u-align-center-xs u-header u-sticky-6247 u-valign-bottom u-header" id="sec-990f" data-animation-name="" data-animation-duration="0" data-animation-delay="0" data-animation-direction="">
    <a href="https://nicepage.com" class="u-image u-logo u-image-1" data-image-width="512" data-image-height="201">
      <img src="images/yds-canada-logo-white-bg.png" class="u-logo-image u-logo-image-1" data-image-width="80">
    </a>
    <nav class="u-menu u-menu-one-level u-offcanvas u-menu-1" data-responsive-from="MD">
      <div class="menu-collapse" style="font-size: 1rem; letter-spacing: 0px; font-weight: 700;">
        <a class="u-button-style u-custom-active-border-color u-custom-border u-custom-border-color u-custom-borders u-custom-hover-border-color u-custom-left-right-menu-spacing u-custom-text-active-color u-custom-text-color u-custom-text-hover-color u-custom-top-bottom-menu-spacing u-nav-link" href="#">
          <img src="svg/svg5.svg" alt="">
          <img src="svg/svg6.svg" alt="">
        </a>
      </div>
      <div class="u-custom-menu u-nav-container">
        <ul class="u-custom-font u-font-lato u-nav u-spacing-30 u-unstyled u-nav-1">
          <li class="u-nav-item"><a class="u-border-2 u-border-active-palette-1-light-2 u-border-no-left u-border-no-right u-border-no-top u-button-style u-nav-link u-radius u-text-active-white u-text-hover-custom-color-2" href="#" style="--radius:8px; padding: 6px 38px;">Home</a>
          </li>
          <li class="u-nav-item"><a class="u-border-2 u-border-active-palette-1-light-2 u-border-no-left u-border-no-right u-border-no-top u-button-style u-nav-link u-radius u-text-active-white u-text-hover-custom-color-2" style="--radius:8px; padding: 6px 38px;">Notification</a>
          </li>
          <li class="u-nav-item">
            <div class="dropdown">
              <a class=" dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false" style="color:black;">
                Pravachan
              </a>

              <div class="dropdown-menu">
                <a class="dropdown-item" href="vpravachan.php">Video Pravachan</a>
                <a class="dropdown-item" href="apravachan.php">Audio Pravachan</a>
              </div>
            </div>
          </li>
          <li class="u-nav-item">
            <div class="dropdown">
              <a class=" dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false" style="color:black;">
                Bhajan
              </a>

              <div class="dropdown-menu">
                <a class="dropdown-item" href="vbhajan.php">Video Bhajan</a>
                <a class="dropdown-item" href="../abhajan.php">Audio Bhajan</a>
              </div>
            </div>
          </li>
          <li class="u-nav-item"><a class="u-border-2 u-border-active-palette-1-light-2 u-border-no-left u-border-no-right u-border-no-top u-button-style u-nav-link u-radius u-text-active-white u-text-hover-custom-color-2" style="--radius:8px; padding: 6px 38px;" href="../devoteeAttendance.php">Attendence</a>
          </li>
        </ul>
      </div>
      <div class="u-custom-menu u-nav-container-collapse">
        <div class="u-black u-container-style u-inner-container-layout u-opacity u-opacity-95 u-sidenav">
          <div class="u-inner-container-layout u-sidenav-overflow">
            <div class="u-menu-close"></div>
            <ul class="u-align-center u-nav u-popupmenu-items u-unstyled u-nav-2">
              <li class="u-nav-item"><a class="u-button-style u-nav-link" href="#">Home</a>
              </li>
              <li class="u-nav-item"><a class="u-button-style u-nav-link">Notification</a>
              </li>
              <li class="u-nav-item">
                <div class="dropdown">
                  <a class=" dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false" style="color:white; ">
                    Pravachan
                  </a>

                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="vpravachan.php">Video Pravachan</a>
                    <a class="dropdown-item" href="apravachan.php">Audio Pravachan</a>
                  </div>
                </div>
              </li>
              <div class="dropdown">
                <a class=" dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false" style="color:white; ">
                  Bhajan
                </a>

                <div class="dropdown-menu">
                  <a class="dropdown-item" href="vbhajan.php">Video Bhajan</a>
                  <a class="dropdown-item" href="../abhajan.php">Audio Bhajan</a>
                </div>
              </div>
              </li>
              <li class="u-nav-item"><a class="u-button-style u-nav-link" href="../devoteeAttendance.php">Attendence</a>
              </li>
            </ul>
          </div>
        </div>
        <div class="u-black u-menu-overlay u-opacity u-opacity-70"></div>
      </div>
    </nav><img class="custom-expanded u-image u-image-default u-image-2" src="images/1000011660.png" alt="" data-image-width="3840" data-image-height="2160" data-animation-name="customAnimationIn" data-animation-duration="2250" data-animation-delay="0" data-animation-direction="">
  </header>

  <section class="u-carousel u-carousel-duration-500 u-carousel-right u-slide u-block-886e-1" id="carousel-4eaf" data-interval="5000" data-u-ride="carousel">
    <ol class="u-absolute-hcenter u-carousel-indicators u-block-886e-2">
      <li data-u-target="#carousel-4eaf" class="u-active u-grey-30" data-u-slide-to="0"></li>
    </ol>
    <div class="u-carousel-inner" role="listbox">
      <div class="custom-expanded u-active u-carousel-item u-clearfix u-image u-section-1-1" data-image-width="1920" data-image-height="1080">
        <div class="u-clearfix u-sheet u-sheet-1">
          <h1 class="u-align-left u-custom-font u-font-oswald u-text u-text-custom-color-2 u-text-1" data-animation-name="customAnimationIn" data-animation-duration="2250" data-animation-delay="0" data-animation-direction=""> Prabodham<br>Weekly&nbsp;<br>Assembly&nbsp;<br> &nbsp;
          </h1>
          <div class="custom-expanded u-shape u-shape-svg u-text-custom-color-5 u-shape-1" data-animation-name="customAnimationIn" data-animation-duration="2250" data-animation-delay="0" data-animation-direction="X">
            <svg class="u-svg-link" preserveAspectRatio="none" viewBox="0 0 160 80">
              <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-2307"></use>
            </svg>
            <svg class="u-svg-content" viewBox="0 0 160 80" x="0px" y="0px" id="svg-2307">
              <path d="M80,0C35.8,0,0,35.8,0,80h160C160,35.8,124.2,0,80,0z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <a class="u-absolute-vcenter u-carousel-control u-carousel-control-prev u-hidden u-text-grey-30 u-block-886e-3" href="#carousel-4eaf" role="button" data-u-slide="prev">
      <span aria-hidden="true">
        <img src="svg/svg3.svg" alt="">
      </span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="u-absolute-vcenter u-carousel-control u-carousel-control-next u-hidden u-text-grey-30 u-block-886e-4" href="#carousel-4eaf" role="button" data-u-slide="next">
      <span aria-hidden="true">
        <img src="svg/svg4.svg" alt="">
      </span>
      <span class="sr-only">Next</span>
    </a>
  </section>
  <section class="u-clearfix u-grey-5 u-section-2" id="sec-c5ed">
    <div class="u-clearfix u-sheet u-valign-middle u-sheet-1">
      <div class="u-list u-list-1">
        <div class="u-repeater u-repeater-1">
          <div class="u-align-center u-container-style u-list-item u-repeater-item u-shape-rectangle">
            <div class="u-container-layout u-similar-container u-container-layout-1">
              <div class="u-hover-feature u-image u-image-circle u-preserve-proportions u-image-1" alt="" data-image-width="5000" data-image-height="5000">
                <img src="svg/seva.svg" alt="" style="margin-top: 2rem;">
              </div>
              <h3 class="u-custom-font u-font-oswald u-text u-text-1">seva </h3>
              <h6>
                Aapdi seva nu 100% fad made, e rite seva thaay, enu naam "Samjanpurvak Seva".
              </h6>
            </div>
          </div>
          <div class="u-align-center u-container-style u-list-item u-repeater-item u-shape-rectangle">
            <div class="u-container-layout u-similar-container u-container-layout-2">
              <div class="u-hover-feature u-image u-image-circle u-preserve-proportions u-image-2" alt="" data-image-width="5000" data-image-height="5000">
                <img src="svg/smruti.svg" alt="" style="width: 10rem; height: 5rem; margin-top: 2rem;">
              </div>
              <h3 class="u-custom-font u-font-oswald u-text u-text-2">Smruti </h3>
              <h6>
                Swamiji ma nirantar dubine jeevvu, enu naam "Smruti".
              </h6>
            </div>
          </div>
          <div class="u-align-center u-container-style u-list-item u-repeater-item u-shape-rectangle">
            <div class="u-container-layout u-similar-container u-container-layout-3">
              <div class="u-hover-feature u-image u-image-circle u-preserve-proportions u-image-3" alt="" data-image-width="5000" data-image-height="5000">
                <img src="svg/suhradbhav.svg" alt="" style="margin-top: 2rem;">
              </div>
              <h3 class="u-custom-font u-font-oswald u-text u-text-3">Suhradbhav&nbsp;</h3>
              <h6>
                Dil thi sambandhno Mahima joi ane rasbas thavu, e "Suhradhbhav".
              </h6>
            </div>
          </div>
          <div class="u-align-center u-container-style u-list-item u-repeater-item u-shape-rectangle">
            <div class="u-container-layout u-similar-container u-container-layout-4">
              <div class="u-hover-feature u-image u-image-circle u-preserve-proportions u-image-4" alt="" data-image-width="5000" data-image-height="5000">
                <img src="svg/swadharm.svg" alt="" style="margin-top: 2rem;">
              </div>
              <h3 class="u-custom-font u-font-oswald u-text u-text-4">Swadharm </h3>
              <h6>Vyakti, Padharth ane Prasang ma na bandhavaay, e "Aadhyatmik Swadharm".</h6>
            </div>
          </div>
        </div>
      </div>
    </div>




  </section>
  <section class="u-clearfix u-image u-section-3" id="carousel_4db1" data-image-width="1920" data-image-height="1080">
    <div class="u-clearfix u-sheet u-sheet-1">
      <div class="data-layout-selected u-clearfix u-expanded-width u-layout-wrap u-layout-wrap-1">
        <div class="u-layout">
          <div class="u-layout-row">
            <div class="u-container-align-left u-container-style u-layout-cell u-size-30 u-layout-cell-1">
              <div class="u-container-layout u-valign-middle u-container-layout-1">
                <p class="u-align-left u-text u-text-default u-text-1"> We are a team of tourist specialists passionate
                  about exceeding all expectations with all your travel needs. </p>
                <ul class="u-align-left u-custom-list u-spacing-10 u-text u-text-default u-text-2">
                  <li>
                    <div class="u-list-icon u-text-palette-1-light-1">
                      <div>●</div>
                    </div> A lacus vestibulum sed arcu non
                  </li>
                  <li>
                    <div class="u-list-icon u-text-palette-1-light-1">
                      <div>●</div>
                    </div>Dolor magna eget est lorem ipsum&nbsp;
                  </li>
                  <li>
                    <div class="u-list-icon u-text-palette-1-light-1">
                      <div>●</div>
                    </div>Dolor sit amet consectetur
                  </li>
                  <li>
                    <div class="u-list-icon u-text-palette-1-light-1">
                      <div>●</div>
                    </div>Mauris pellentesque pulvinar pellentesque
                  </li>
                </ul>
                <p class="u-align-left u-text u-text-default u-text-3">Image from <a href="https://freepik.com" class="u-active-none u-border-none u-btn u-button-link u-button-style u-hover-none u-none u-text-palette-1-light-1 u-btn-1">Freepik</a>
                </p>
              </div>
            </div>
            <div class="u-container-style u-layout-cell u-shape-rectangle u-size-30 u-layout-cell-2">
              <div class="u-container-layout u-valign-middle u-container-layout-2">
                <div class="u-image u-image-circle u-image-1" alt="" data-image-width="3840" data-image-height="2160" data-animation-name="customAnimationIn" data-animation-duration="1000" data-animation-delay="0"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <h1>Lifetime Sabha Attendance</h1>
  <div id="chartContainer"></div>

  <section class="u-align-center u-clearfix u-container-align-center u-image u-section-6" id="sec-ab10" data-image-width="1920" data-image-height="1080">
    <div class="u-align-left u-clearfix u-sheet u-valign-middle u-sheet-1">
      <div class="u-expanded-width u-list u-list-1">
        <div class="u-repeater u-repeater-1">
          <div class="u-container-align-center u-container-style u-list-item u-repeater-item">
            <div class="u-container-layout u-similar-container u-valign-top u-container-layout-1"><span class="u-align-center u-border-2 u-border-palette-1-base u-file-icon u-hover-feature u-icon u-icon-circle u-text-palette-1-base u-icon-1"><img src="images/29302-df30e304.png" alt=""></span>
              <h3 class="u-align-center u-text u-text-default u-text-1">Book's </h3>
              <p class="u-align-center u-text u-text-2">Sample text. Click to select the text box. Click again or double
                click to start editing the text.</p>
              <a href="" class="u-active-none u-align-center u-border-2 u-border-hover-palette-2-base u-border-no-left u-border-no-right u-border-no-top u-border-palette-2-light-1 u-btn u-button-style u-hover-none u-none u-text-body-color u-btn-1">learn
                more</a>
            </div>
          </div>
          <div class="u-container-align-center u-container-style u-list-item u-repeater-item">
            <div class="u-container-layout u-similar-container u-valign-top u-container-layout-2"><span class="u-align-center u-border-2 u-border-palette-1-base u-file-icon u-hover-feature u-icon u-icon-circle u-text-palette-1-base u-icon-2"><img src="images/109190-c72aaee0.png" alt=""></span>
              <h3 class="u-align-center u-text u-text-default u-text-3">Audio </h3>
              <p class="u-align-center u-text u-text-4">Sample text. Click to select the text box. Click again or double
                click to start editing the text.</p>
              <a href="" class="u-active-none u-align-center u-border-2 u-border-hover-palette-2-base u-border-no-left u-border-no-right u-border-no-top u-border-palette-2-light-1 u-btn u-button-style u-hover-none u-none u-text-body-color u-btn-2">learn
                more</a>
            </div>
          </div>
          <div class="u-container-align-center u-container-style u-list-item u-repeater-item">
            <div class="u-container-layout u-similar-container u-valign-top u-container-layout-3"><span class="u-align-center u-border-2 u-border-palette-1-base u-file-icon u-hover-feature u-icon u-icon-circle u-text-palette-1-base u-icon-3"><img src="images/2991195-5fbb1bcb.png" alt=""></span>
              <h3 class="u-align-center u-text u-text-default u-text-5">Video </h3>
              <p class="u-align-center u-text u-text-6">Sample text. Click to select the text box. Click again or double
                click to start editing the text.</p>
              <a href="" class="u-active-none u-align-center u-border-2 u-border-hover-palette-2-base u-border-no-left u-border-no-right u-border-no-top u-border-palette-2-light-1 u-btn u-button-style u-hover-none u-none u-text-body-color u-btn-3">learn
                more</a>
            </div>
          </div>
        </div>
      </div>
    </div>



  </section>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js" integrity="sha512-igl8WEUuas9k5dtnhKqyyld6TzzRjvMqLC79jkgT3z02FvJyHAuUtyemm/P/jYSne1xwFI06ezQxEwweaiV7VA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        exportEnabled: true,
        title: {
          text: "Lifetime Sabha Attendance Percentage"
        },
        subtitles: [{
          text: "Pie Chart"
        }],
        data: [{
          type: "pie",
          showInLegend: true,
          legendText: "{label}",
          indexLabelFontSize: 16,
          indexLabel: "{label} - #percent%",
          yValueFormatString: "#0.#",
          dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
        }]
      });
      chart.render();
    });
  </script>
</body>

</html>
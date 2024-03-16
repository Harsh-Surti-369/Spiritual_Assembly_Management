<?php
session_start();
include('../../php/dbConnect.php');
// Get the devotee ID from the session or any other means
$devoteeId = $_SESSION['devotee_id'];

// Calculate total number of sabhas conducted in the devotee's center
$sqlTotalSabhas = "SELECT COUNT(DISTINCT s.sabha_id) AS total_sabhas
                   FROM tbl_sabha s
                   INNER JOIN tbl_attendance a ON s.sabha_id = a.sabha_id
                   WHERE s.center_id = (SELECT center_id FROM tbl_devotee WHERE devotee_id = ?)";
$stmtTotalSabhas = $conn->prepare($sqlTotalSabhas);
$stmtTotalSabhas->bind_param("i", $devoteeId);
$stmtTotalSabhas->execute();
$resultTotalSabhas = $stmtTotalSabhas->get_result();
$rowTotalSabhas = mysqli_fetch_assoc($resultTotalSabhas);
$totalSabhas = $rowTotalSabhas['total_sabhas'];

// Calculate number of present records for the devotee in the center
$sqlPresentRecords = "SELECT COUNT(*) AS present_records
                      FROM tbl_attendance a
                      INNER JOIN tbl_sabha s ON a.sabha_id = s.sabha_id
                      WHERE a.devotee_id = ? AND a.attendance_status = 'Present'
                      AND s.center_id = (SELECT center_id FROM tbl_devotee WHERE devotee_id = ?)";
$stmtPresentRecords = $conn->prepare($sqlPresentRecords);
$stmtPresentRecords->bind_param("ii", $devoteeId, $devoteeId);
$stmtPresentRecords->execute();
$resultPresentRecords = $stmtPresentRecords->get_result();
$rowPresentRecords = mysqli_fetch_assoc($resultPresentRecords);
$presentRecords = $rowPresentRecords['present_records'];

// Calculate percentage of present devotees
$percentagePresent = ($presentRecords / $totalSabhas) * 100;

// echo "Total Sabhas: " . $totalSabhas . "<br>";
// echo "Present Records: " . $presentRecords . "<br>";
// echo "Percentage of Present Devotees: " . $percentagePresent . "%<br>";

$stmtTotalSabhas->close();
$stmtPresentRecords->close();
?>
<!DOCTYPE html>
<html style="font-size: 16px;" lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="utf-8">
  <title>Page 1</title>
  <link rel="stylesheet" href="nicepage.css" media="screen">
  <link rel="stylesheet" href="Page-1.css" media="screen">
  <script class="u-script" type="text/javascript" src="jquery.js" defer=""></script>
  <script class="u-script" type="text/javascript" src="nicepage.js" defer=""></script>
  <link id="u-theme-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i|Open+Sans:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i">
  <link id="u-page-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald:200,300,400,500,600,700|Playfair+Display:400,400i,500,500i,600,600i,700,700i,800,800i,900,900i|Roboto+Slab:100,200,300,400,500,600,700,800,900|Lato:100,100i,300,300i,400,400i,700,700i,900,900i">
</head>

<body data-home-page="Page-1.html" data-home-page-title="Page 1" data-path-to-root="./" data-include-products="false" class="u-body u-overlap u-overlap-transparent u-xl-mode" data-lang="en">
  <header class="u-align-center-xs u-header u-sticky-6247 u-valign-bottom u-header" id="sec-990f" data-animation-name="" data-animation-duration="0" data-animation-delay="0" data-animation-direction="">
    <div class="u-social-icons u-spacing-10 u-social-icons-1">
      <a class="u-social-url" title="instagram" target="_blank" href=""><span class="u-icon u-social-icon u-social-instagram u-icon-1"><svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 112 112" style="">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-cf0d"></use>
          </svg><svg class="u-svg-content" viewBox="0 0 112 112" x="0" y="0" id="svg-cf0d">
            <circle fill="currentColor" cx="56.1" cy="56.1" r="55"></circle>
            <path fill="#FFFFFF" d="M55.9,38.2c-9.9,0-17.9,8-17.9,17.9C38,66,46,74,55.9,74c9.9,0,17.9-8,17.9-17.9C73.8,46.2,65.8,38.2,55.9,38.2
      z M55.9,66.4c-5.7,0-10.3-4.6-10.3-10.3c-0.1-5.7,4.6-10.3,10.3-10.3c5.7,0,10.3,4.6,10.3,10.3C66.2,61.8,61.6,66.4,55.9,66.4z"></path>
            <path fill="#FFFFFF" d="M74.3,33.5c-2.3,0-4.2,1.9-4.2,4.2s1.9,4.2,4.2,4.2s4.2-1.9,4.2-4.2S76.6,33.5,74.3,33.5z"></path>
            <path fill="#FFFFFF" d="M73.1,21.3H38.6c-9.7,0-17.5,7.9-17.5,17.5v34.5c0,9.7,7.9,17.6,17.5,17.6h34.5c9.7,0,17.5-7.9,17.5-17.5V38.8
      C90.6,29.1,82.7,21.3,73.1,21.3z M83,73.3c0,5.5-4.5,9.9-9.9,9.9H38.6c-5.5,0-9.9-4.5-9.9-9.9V38.8c0-5.5,4.5-9.9,9.9-9.9h34.5
      c5.5,0,9.9,4.5,9.9,9.9V73.3z"></path>
          </svg></span>
      </a>
      <a class="u-social-url" target="_blank" data-type="YouTube" title="YouTube" href=""><span class="u-icon u-social-icon u-social-youtube u-icon-2"><svg class="u-svg-link" preserveAspectRatio="xMidYMin slice" viewBox="0 0 112 112" style="">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svg-b91c"></use>
          </svg><svg class="u-svg-content" viewBox="0 0 112 112" x="0" y="0" id="svg-b91c">
            <circle fill="currentColor" cx="56.1" cy="56.1" r="55"></circle>
            <path fill="#FFFFFF" d="M74.9,33.3H37.3c-7.4,0-13.4,6-13.4,13.4v18.8c0,7.4,6,13.4,13.4,13.4h37.6c7.4,0,13.4-6,13.4-13.4V46.7 C88.3,39.3,82.3,33.3,74.9,33.3L74.9,33.3z M65.9,57l-17.6,8.4c-0.5,0.2-1-0.1-1-0.6V47.5c0-0.5,0.6-0.9,1-0.6l17.6,8.9 C66.4,56,66.4,56.8,65.9,57L65.9,57z">
            </path>
          </svg></span>
      </a>
    </div><a href="https://nicepage.com" class="u-image u-logo u-image-1" data-image-width="512" data-image-height="201">
      <img src="images/yds-canada-logo-white-bg.png" class="u-logo-image u-logo-image-1" data-image-width="80">
    </a>
    <nav class="u-menu u-menu-one-level u-offcanvas u-menu-1" data-responsive-from="MD">
      <div class="menu-collapse" style="font-size: 1rem; letter-spacing: 0px; font-weight: 700;">
        <a class="u-button-style u-custom-active-border-color u-custom-border u-custom-border-color u-custom-borders u-custom-hover-border-color u-custom-left-right-menu-spacing u-custom-text-active-color u-custom-text-color u-custom-text-hover-color u-custom-top-bottom-menu-spacing u-nav-link" href="#">
          <svg class="u-svg-link" viewBox="0 0 24 24">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#menu-hamburger"></use>
          </svg>
          <svg class="u-svg-content" version="1.1" id="menu-hamburger" viewBox="0 0 16 16" x="0px" y="0px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg">
            <g>
              <rect y="1" width="16" height="2"></rect>
              <rect y="7" width="16" height="2"></rect>
              <rect y="13" width="16" height="2"></rect>
            </g>
          </svg>
        </a>
      </div>
      <div class="u-custom-menu u-nav-container">
        <ul class="u-custom-font u-font-lato u-nav u-spacing-30 u-unstyled u-nav-1">
          <li class="u-nav-item"><a class="u-border-2 u-border-active-palette-1-light-2 u-border-no-left u-border-no-right u-border-no-top u-button-style u-nav-link u-radius u-text-active-white u-text-hover-custom-color-2" href="#" style="--radius:8px; padding: 6px 38px;">Home</a>
          </li>
          <li class="u-nav-item"><a class="u-border-2 u-border-active-palette-1-light-2 u-border-no-left u-border-no-right u-border-no-top u-button-style u-nav-link u-radius u-text-active-white u-text-hover-custom-color-2" style="--radius:8px; padding: 6px 38px;">Notification</a>
          </li>
          <li class="u-nav-item"><a class="u-border-2 u-border-active-palette-1-light-2 u-border-no-left u-border-no-right u-border-no-top u-button-style u-nav-link u-radius u-text-active-white u-text-hover-custom-color-2" style="--radius:8px; padding: 6px 38px;">Contact us</a>
          </li>
          <li class="u-nav-item"><a class="u-border-2 u-border-active-palette-1-light-2 u-border-no-left u-border-no-right u-border-no-top u-button-style u-nav-link u-radius u-text-active-white u-text-hover-custom-color-2" style="--radius:8px; padding: 6px 38px;">Bhajan</a>
          </li>
          <li class="u-nav-item"><a class="u-border-2 u-border-active-palette-1-light-2 u-border-no-left u-border-no-right u-border-no-top u-button-style u-nav-link u-radius u-text-active-white u-text-hover-custom-color-2" style="--radius:8px; padding: 6px 38px;">Attendence</a>
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
              <li class="u-nav-item"><a class="u-button-style u-nav-link">Contact us</a>
              </li>
              <li class="u-nav-item"><a class="u-button-style u-nav-link">Bhajan</a>
              </li>
              <li class="u-nav-item"><a class="u-button-style u-nav-link">Attendence</a>
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
            <svg class="u-svg-link" preserveAspectRatio="none" viewBox="0 0 160 80" style="">
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
        <svg class="u-svg-link" viewBox="0 0 477.175 477.175">
          <path d="M145.188,238.575l215.5-215.5c5.3-5.3,5.3-13.8,0-19.1s-13.8-5.3-19.1,0l-225.1,225.1c-5.3,5.3-5.3,13.8,0,19.1l225.1,225
                    c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4c5.3-5.3,5.3-13.8,0-19.1L145.188,238.575z"></path>
        </svg>
      </span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="u-absolute-vcenter u-carousel-control u-carousel-control-next u-hidden u-text-grey-30 u-block-886e-4" href="#carousel-4eaf" role="button" data-u-slide="next">
      <span aria-hidden="true">
        <svg class="u-svg-link" viewBox="0 0 477.175 477.175">
          <path d="M360.731,229.075l-225.1-225.1c-5.3-5.3-13.8-5.3-19.1,0s-5.3,13.8,0,19.1l215.5,215.5l-215.5,215.5
                    c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4c3.4,0,6.9-1.3,9.5-4l225.1-225.1C365.931,242.875,365.931,234.275,360.731,229.075z">
          </path>
        </svg>
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
            </div>
          </div>
          <div class="u-align-center u-container-style u-list-item u-repeater-item u-shape-rectangle">
            <div class="u-container-layout u-similar-container u-container-layout-2">
              <div class="u-hover-feature u-image u-image-circle u-preserve-proportions u-image-2" alt="" data-image-width="5000" data-image-height="5000">
                <img src="svg/smruti.svg" alt="" style="width: 10rem; height: 5rem; margin-top: 2rem;">
              </div>
              <h3 class="u-custom-font u-font-oswald u-text u-text-2">Smruti </h3>
            </div>
          </div>
          <div class="u-align-center u-container-style u-list-item u-repeater-item u-shape-rectangle">
            <div class="u-container-layout u-similar-container u-container-layout-3">
              <div class="u-hover-feature u-image u-image-circle u-preserve-proportions u-image-3" alt="" data-image-width="5000" data-image-height="5000">
                <img src="svg/suhradbhav.svg" alt="" style="margin-top: 2rem;">
              </div>
              <h3 class="u-custom-font u-font-oswald u-text u-text-3">Suhradbhav&nbsp;</h3>
            </div>
          </div>
          <div class="u-align-center u-container-style u-list-item u-repeater-item u-shape-rectangle">
            <div class="u-container-layout u-similar-container u-container-layout-4">
              <div class="u-hover-feature u-image u-image-circle u-preserve-proportions u-image-4" alt="" data-image-width="5000" data-image-height="5000">
                <img src="svg/swadharm.svg" alt="" style="margin-top: 2rem;">
              </div>
              <h3 class="u-custom-font u-font-oswald u-text u-text-4">Swadharm </h3>
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
  <section class="u-clearfix u-grey-5 u-section-4" id="sec-5a8e">
    <div class="u-clearfix u-sheet u-sheet-1">
      <h1 class="u-align-center u-custom-font u-font-georgia u-text u-text-1" data-animation-name="customAnimationIn" data-animation-duration="1000" data-animation-delay="0" data-animation-direction="">About Us </h1><span class="u-align-center u-file-icon u-icon u-icon-rectangle u-text-custom-color-3 u-icon-1" data-animation-name="customAnimationIn" data-animation-duration="1000" data-animation-delay="0"><img src="images/4338295-c0d277e4.png" alt=""></span><span class="u-align-right u-file-icon u-icon u-icon-rectangle u-text-custom-color-3 u-icon-2" data-animation-name="customAnimationIn" data-animation-duration="1000" data-animation-delay="0"><img src="images/4338295-c0d277e4.png" alt=""></span>
      <p class="u-align-center u-text u-text-default u-text-2"> We are a team of tourist specialists passionate about
        exceeding all expectations with all your travel needs. </p>
      <h3 class="u-align-center u-text u-text-3">Spiritual Assembly </h3>
    </div>
  </section>
  <section class="u-clearfix u-image u-section-5" id="carousel_2914" data-image-width="1920" data-image-height="1080">
    <div class="u-clearfix u-sheet u-valign-middle u-sheet-1">
      <h1 class="u-align-left u-custom-font u-font-playfair-display u-text u-text-1">Attendance </h1>
      <div class="u-align-center u-custom-color-2 u-preserve-proportions u-shape u-shape-circle u-shape-1" data-animation-name="customAnimationIn" data-animation-duration="1000" data-animation-delay="0"></div>
      <h3 class="u-align-center u-custom-font u-font-roboto-slab u-text u-text-2"><? $percentagePresent ?></h3>
      <img class="u-image u-image-contain u-image-default u-preserve-proportions u-image-1" alt="" data-image-width="512" data-image-height="512" src="images/immigration.png" data-animation-name="customAnimationIn" data-animation-duration="1000" data-animation-delay="0">
    </div>
  </section>


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
  <section class="u-clearfix u-white u-section-7" id="carousel_5278">
    <div class="u-clearfix u-sheet u-sheet-1">
      <div class="data-layout-selected u-clearfix u-expanded-width u-layout-wrap u-layout-wrap-1">
        <div class="u-layout">
          <div class="u-layout-row">
            <div class="u-container-style u-layout-cell u-size-30 u-layout-cell-1">
              <div class="u-container-layout u-container-layout-1">
                <h2 class="u-text u-text-default u-text-1"> We're here to support</h2>
                <p class="u-text u-text-2">Sample text. Click to select the Text Element.<br>Image from <a href="https://freepik.com" class="u-active-none u-border-none u-btn u-button-link u-button-style u-hover-none u-none u-text-palette-1-base u-btn-1">Freepik</a>
                </p>
                <div class="u-image u-image-circle u-image-1" alt="" data-image-width="1280" data-image-height="720">
                </div>
              </div>
            </div>
            <div class="u-container-style u-layout-cell u-size-30 u-layout-cell-2">
              <div class="u-container-layout u-valign-top u-container-layout-2">
                <div class="u-expanded-width u-form u-grey-5 u-form-1">
                  <form action="https://forms.nicepagesrv.com/v2/form/process" class="u-clearfix u-form-spacing-20 u-form-vertical u-inner-form" source="email" name="form" style="padding: 30px;">
                    <div class="u-form-group u-form-name">
                      <label for="name-b064" class="u-label">Name</label>
                      <input type="text" placeholder="Enter your Name" id="name-b064" name="name" class="u-border-none u-input u-input-rectangle" required="">
                    </div>
                    <div class="u-form-email u-form-group">
                      <label for="email-b064" class="u-label">Email</label>
                      <input type="email" placeholder="Enter a valid email address" id="email-b064" name="email" class="u-border-none u-input u-input-rectangle" required="">
                    </div>
                    <div class="u-form-group u-form-message">
                      <label for="message-b064" class="u-label">Message</label>
                      <textarea placeholder="Enter your message" rows="4" cols="50" id="message-b064" name="message" class="u-border-none u-input u-input-rectangle" required=""></textarea>
                    </div>
                    <div class="u-align-left u-form-group u-form-submit">
                      <a href="#" class="u-border-none u-btn u-btn-submit u-button-style u-palette-3-base u-btn-2">Submit</a>
                      <input type="submit" value="submit" class="u-form-control-hidden">
                    </div>
                    <div class="u-form-send-message u-form-send-success"> Thank you! Your message has been sent. </div>
                    <div class="u-form-send-error u-form-send-message"> Unable to send your message. Please fix errors
                      then try again. </div>
                    <input type="hidden" value="" name="recaptchaResponse">
                    <input type="hidden" name="formServices" value="2487f560-3522-f758-c62f-90ef559b12ae">
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</body>

</html>
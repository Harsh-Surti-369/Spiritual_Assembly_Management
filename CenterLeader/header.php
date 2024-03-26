<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="height: 110px;position: fixed;top: 0;
  left: 0;
  right: 0;
  z-index: 1000;">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="../images/Logo.png" class="logo" alt="Logo" style="width:9rem; height:auto;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvas" aria-controls="navbarOffcanvas" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="navbarOffcanvas" aria-labelledby="navbarOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="navbarOffcanvasLabel">Prabodham Weekly Assembly</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="uploadContent.php">Bhajan/Pravachan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="uploadbook.php">Book</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="qlist.php">QnA</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profile</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Manage Sabha
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="sabhaList.php">Sabha List</a></li>
                                <li><a class="dropdown-item" href="createSabha.php">Create new Sabha</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>
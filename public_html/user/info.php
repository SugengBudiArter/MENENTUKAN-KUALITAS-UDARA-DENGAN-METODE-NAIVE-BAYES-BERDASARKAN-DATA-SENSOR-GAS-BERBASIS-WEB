<?php
session_start();

// cek apakah yang mengakses halaman ini sudah login
if ($_SESSION['level'] == "") {
    header("location:../index.php?pesan=login");
}


// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "udara";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Determine sort order
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'desc';
$order = ($sort == 'asc') ? 'ASC' : 'DESC';

$sql = "SELECT id_sensorData, lokasi, waktu, kualitasudara FROM sensordata ORDER BY waktu $order";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/responsive.css" rel="stylesheet" />
</head>
<body class="sub_page">

<div class="hero_area">
<header class="header_section">
      <div class="container-fluid">
        <nav class="navbar navbar-expand-lg custom_nav-container">
          <a class="" href="halaman_user.php">
            <img src="images/logo.png" alt="" width="100" height="50" />
          </a>
          
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav  ">
              <li class="nav-item active">
                <a class="nav-link" href="halaman_user.php">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="view.php">Data Prediksi</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="info.php">Info Prediksi</a>
              </li>
            </ul>
            <div class="user_option">
              <a href="../logout.php" > 
                <span>
                  LogOut
                </span>
              </a>
              <form class="form-inline my-2 my-lg-0 ml-0 ml-lg-4 mb-3 mb-lg-0">
                <button class="btn  my-2 my-sm-0 nav_search-btn" type="submit"></button>
              </form>
            </div>
          </div>
          <div>
            <div class="custom_menu-btn ">
              <button>
                <span class=" s-1">

                </span>
                <span class="s-2">

                </span>
                <span class="s-3">

                </span>
              </button>
            </div>
          </div>

        </nav>
      </div>
    </header>
</div>

<section class="freelance_section layout_padding">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="section_heading">Sensor Gas Data</h2>
                <div class="mb-3">
                    <a href="?sort=desc" class="btn btn-secondary">Newest First</a>
                    <a href="?sort=asc" class="btn btn-secondary">Oldest First</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Lokasi</th>
                                <th>Waktu</th>
                                <th>Kualitas Udara</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>" . $no++ . "</td>
                                        <td>" . $row["lokasi"] . "</td>
                                        <td>" . $row["waktu"] . "</td>
                                        <td>" . $row["kualitasudara"] . "</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No data available</td></tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="info_section">
    <!-- Info section content here -->
</section>

<footer class="container-fluid footer_section">
    <div class="container">
        <p>&copy; <span id="displayDate"></span> All Rights Reserved By <a href="https://github.com/SugengBudiArter">SugengBudi</a></p>
    </div>
</footer>

<script src="js/jquery-3.4.1.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/custom.js"></script>

</body>
</html>

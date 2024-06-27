<?php
session_start();

if ($_SESSION['level'] == "") {
    header("location:../index.php?pesan=login");
}
if ($_SESSION['level'] == "user") {
    header("location:../index.php?pesan=notadmin");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "udara";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $lokasi = sanitize_input($_POST['lokasi']);
  $waktu = sanitize_input($_POST['waktu']);
  $CO = floatval(sanitize_input($_POST['CO']));
  $NOx = floatval(sanitize_input($_POST['NOx']));
  $NO2 = floatval(sanitize_input($_POST['NO2']));
  $C6H6 = floatval(sanitize_input($_POST['C6H6']));
  $PT08S5 = floatval(sanitize_input($_POST['PT08S5']));
  $T = floatval(sanitize_input($_POST['T']));
  $RH = floatval(sanitize_input($_POST['RH']));

    $data = array(
        "lokasi" => $lokasi,
        "waktu" => $waktu,
        "CO" => $CO,
        "NOx" => $NOx,
        "NO2" => $NO2,
        "C6H6" => $C6H6,
        "PT08S5" => $PT08S5,
        "T" => $T,
        "RH" => $RH
    );
    $data_string = json_encode($data);

    $ch = curl_init('http://127.0.0.1:5000/predict');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );

    $result = curl_exec($ch);
    if(curl_errno($ch)) {
        $error_msg = curl_error($ch);
        // Tangani jenis kesalahan tertentu
        if (strpos($error_msg, 'Could not resolve host') !== false) {
            echo 'Error: Could not connect to server. Please try again later.';
        } else {
            echo 'Curl error: ' . $error_msg;
        }
    } else {
        // Tangani response dari server
        $response = json_decode($result, true);
        if (isset($response['status']) && $response['status'] == 'success') {
          header("location: prediksi.php?prediction=" . urlencode($response['prediction']));
          exit();
        }else {
            echo "Error: " . ($response['message'] ?? 'Unknown error');
        }
    }
    
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>Spering</title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />
  
  <style>
    .form-section .form-control {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
    .form-section .form-group {
        margin-bottom: 1rem;
    }
    .form-section button[type="submit"] {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }
    .form-section .heading_container h2 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
  </style>
</head>

<body class="sub_page">
  <div class="hero_area">
    <!-- header section strats -->
    <header class="header_section">
      <div class="container-fluid">
        <nav class="navbar navbar-expand-lg custom_nav-container">
          <a class="" href="halaman_admin.php">
            <img src="images/logo.png" alt="" width="100" height="50" />
          </a>
          
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav  ">
              <li class="nav-item active">
                <a class="nav-link" href="halaman_admin.php">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="prediksi.php"> Input Prediksi</a>
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
    <!-- end header section -->
  </div>

  <!-- form section -->
  <section class="form-section layout_padding-top layout_padding2-bottom">
    <div class="container">
      <div class="heading_container">
        <h2>Sensor Data Input Form</h2>
      </div>
      <form id="sensorForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="lokasi">Location</label>
            <input type="text" class="form-control" id="lokasi" name="lokasi" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="waktu">Date and Time</label>
            <input type="datetime-local" class="form-control" id="waktu" name="waktu" required>
          </div>
          <div class="form-group col-md-6">
            <label for="CO">CO (GT)</label>
            <input type="number" step="0.01" class="form-control" id="CO" name="CO" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="NOx">NOx (GT)</label>
            <input type="number" step="0.01" class="form-control" id="NOx" name="NOx" required>
          </div>
          <div class="form-group col-md-6">
            <label for="NO2">NO2 (GT)</label>
            <input type="number" step="0.01" class="form-control" id="NO2" name="NO2" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="C6H6">C6H6 (GT)</label>
<input type="number" step="0.01" class="form-control" id="C6H6" name="C6H6" required>
</div>
<div class="form-group col-md-6">
<label for="PT08S5">PT08.S5 (O3)</label>
<input type="number" step="0.01" class="form-control" id="PT08S5" name="PT08S5" required>
</div>
</div>
<div class="form-row">
<div class="form-group col-md-6">
<label for="T">T</label>
<input type="number" step="0.01" class="form-control" id="T" name="T" required>
</div>
<div class="form-group col-md-6">
<label for="RH">RH</label>
<input type="number" step="0.01" class="form-control" id="RH" name="RH" required>
</div>
</div>
<button type="submit" class="btn btn-primary">Submit</button>
<button type="button" class="btn btn-secondary" onclick="clearForm()">Clear Form</button>
<button type="button" onclick="location.href='halaman_admin.php'"  class="btn btn-primary">Kembali</button>
</form>
</div>

  </section>
  <!-- end form section -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/custom.js"></script>
  <script>
    function clearForm() {
      document.getElementById("sensorForm").reset();
    }
  </script>
</body>
</html>

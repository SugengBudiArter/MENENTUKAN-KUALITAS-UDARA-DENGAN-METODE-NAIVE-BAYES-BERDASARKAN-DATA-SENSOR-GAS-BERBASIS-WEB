<?php
session_start();

// Redirect users who are not logged in or not admins
if (!isset($_SESSION['level']) || $_SESSION['level'] == "") {
    header("Location: ../index.php?pesan=login");
    exit;
}
if ($_SESSION['level'] == "user") {
    header("Location: ../index.php?pesan=notadmin");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "udara";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $lokasi = sanitize_input($_POST['lokasi']);
    $waktu = sanitize_input($_POST['waktu']);
    $CO = floatval($_POST['CO']);
    $NOx = floatval($_POST['NOx']);
    $NO2 = floatval($_POST['NO2']);
    $C6H6 = floatval($_POST['C6H6']);
    $PT08S5 = floatval($_POST['PT08S5']);
    $T = floatval($_POST['T']);
    $RH = floatval($_POST['RH']);

    // Prepare data for classification
    $data = array(
        "id_sensorData" => $id,
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

    // Send data to the classification API using PUT method
    $ch = curl_init('http://127.0.0.1:5000/update');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)
    ));

    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);


        $response = json_decode($result, true);
        print_r($response);

            if (isset($response['status']) && $response['status'] == 'success'&& $response['message'] == "Data berhasil diperbarui.") {
                if (isset($response['prediction'])) {
                    $kualitasudara = $response['prediction'];

                    // Update the database record
                    $sql = "UPDATE sensordata SET lokasi=?, waktu=?, CO=?, NOx=?, NO2=?, C6H6=?, PT08S5=?, T=?, RH=?, kualitasudara=? WHERE id_sensorData=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssdddddddis", $lokasi, $waktu, $CO, $NOx, $NO2, $C6H6, $PT08S5, $T, $RH, $kualitasudara, $id);

                    if ($stmt->execute()) {
                        header("location: prediksi.php?prediction=" . urlencode($response['message']));
                        exit;
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                    $stmt->close();
                }
            } else {
                echo "Error: " . ($response['message'] ?? 'Unknown error');
            }
            header("location: prediksi.php?prediction=" . urlencode($response['message']));
            exit;

    curl_close($ch);
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM sensordata WHERE id_sensorData = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        header("Location: halaman_admin.php");
        exit;
    }
    $stmt->close();
} else {
    header("Location: halaman_admin.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data</title>
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>Edit Sensor Data</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <?php if (isset($row['id_sensorData'])): ?>
            <input type="hidden" name="id" value="<?php echo $row['id_sensorData']; ?>">
            <div class="form-group">
                <label for="lokasi">Lokasi:</label>
                <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?php echo $row['lokasi']; ?>" required>
            </div>
            <div class="form-group">
                <label for="waktu">Waktu:</label>
                <input type="datetime-local" class="form-control" id="waktu" name="waktu" value="<?php echo date('Y-m-d\TH:i', strtotime($row['waktu'])); ?>" required>
            </div>
            <div class="form-group">
                <label for="CO">CO (GT):</label>
                <input type="number" step="0.01" class="form-control" id="CO" name="CO" value="<?php echo $row['CO']; ?>" required>
            </div>
            <div class="form-group">
                <label for="NOx">NOx (GT):</label>
                <input type="number" step="0.01" class="form-control" id="NOx" name="NOx" value="<?php echo $row['NOx']; ?>" required>
            </div>
            <div class="form-group">
                <label for="NO2">NO2 (GT):</label>
                <input type="number" step="0.01" class="form-control" id="NO2" name="NO2" value="<?php echo $row['NO2']; ?>" required>
            </div>
            <div class="form-group">
                <label for="C6H6">C6H6 (GT):</label>
                <input type="number" step="0.01" class="form-control" id="C6H6" name="C6H6" value="<?php echo $row['C6H6']; ?>" required>
            </div>
            <div class="form-group">
                <label for="PT08S5">PT08.S5 (O3):</label>
                <input type="number" step="0.01" class="form-control" id="PT08S5" name="PT08S5" value="<?php echo $row['PT08S5']; ?>" required>
            </div>
            <div class="form-group">
                <label for="T">T:</label>
                <input type="number" step="0.01" class="form-control" id="T" name="T" value="<?php echo $row['T']; ?>" required>
            </div>
            <div class="form-group">
                <label for="RH">RH:</label>
                <input type="number" step="0.01" class="form-control" id="RH" name="RH" value="<?php echo $row['RH']; ?>" required>
            </div>
        <?php else: ?>
            <p>Data tidak ditemukan.</p>
        <?php endif; ?>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="prediksi.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>

<?php
session_start();

// cek apakah yang mengakses halaman ini sudah login
if($_SESSION['level'] == "") {
    header("location:../index.php?pesan=login");
    exit(); // Stop further execution
}
if($_SESSION['level'] == "user") {
    header("location:../index.php?pesan=notadmin");
    exit(); // Stop further execution
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

// Cek apakah parameter id_sensorData telah diberikan dalam URL
if(isset($_GET['id']) && !empty($_GET['id'])){
    // Bersihkan data input dari URL
    $id_sensorData = htmlspecialchars($_GET['id']);
    
    // SQL untuk menghapus data berdasarkan id_sensorData
    $sql = "DELETE FROM sensordata WHERE id_sensorData = ?";
    
    // Persiapkan statement
    $stmt = $conn->prepare($sql);
    
    // Bind parameter
    $stmt->bind_param("i", $id_sensorData);
    
    // Eksekusi statement
    if($stmt->execute()){
        // Data berhasil dihapus, arahkan kembali ke halaman sebelumnya
        header("location: prediksi.php");
        exit();
    } else{
        echo "Gagal menghapus data.";
    }
    
    // Tutup statement
    $stmt->close();
}

// Tutup koneksi
$conn->close();
?>

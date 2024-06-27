<?php 
// mengaktifkan session pada php
session_start();

// menghubungkan php dengan koneksi database
include 'koneksi.php';

// menangkap data yang dikirim dari form login
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$password = md5($_POST['password']); // Hash password input dengan MD5

// menyeleksi data user dengan username dan password yang sesuai
$sql = "SELECT * FROM user WHERE username = ? AND password = ?";
$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "ss", $username, $password);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($result);

// cek apakah username dan password di temukan pada database
if($cek > 0) {
    $data = mysqli_fetch_assoc($result);

    // cek level user dan buat session sesuai
    if($data['level'] == "admin") {
        $_SESSION['username'] = $username;
        $_SESSION['level'] = "admin";
        $_SESSION['pesan'] = "Selamat datang, $username!";
        // alihkan ke halaman dashboard admin
        header("Location:admin/halaman_admin.php");
        exit();
    } else if($data['level'] == "user") {
        $_SESSION['username'] = $username;
        $_SESSION['level'] = "user";
        $_SESSION['pesan'] = "Selamat datang, $username!";
        // alihkan ke halaman dashboard user
        header("Location: user/halaman_user.php");
        exit();
    } else {
        // alihkan ke halaman login kembali dengan pesan gagal
        header("Location: index.php?pesan=gagal");
        exit();
    }
} else {
    header("Location: index.php?pesan=gagal");
    exit();
}

// Tutup statement dan koneksi
mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>

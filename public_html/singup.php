<?php

require_once("koneksi.php");

if (isset($_POST['register'])) {
    // filter data yang diinputkan
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // cek apakah email valid
    if ($email === false) {
        die("Email tidak valid.");
    }

    // cek apakah password dan konfirmasi password sesuai
    if ($password !== $confirm_password) {
        die("Password dan konfirmasi password tidak sesuai.");
    }

    // enkripsi password menggunakan md5
    $password_hashed = md5($password);

    // tetapkan level otomatis menjadi 'user'
    $level = 'user';

    // menyiapkan query
    $sql = "INSERT INTO user (username, email, password, level) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);

    // bind parameter ke query
    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password_hashed, $level);

    // eksekusi query untuk menyimpan ke database
    try {
        $saved = mysqli_stmt_execute($stmt);

        // jika query simpan berhasil, maka user sudah terdaftar
        // maka alihkan ke halaman login
        if ($saved) {
            // Redirect to index.php with username and password parameters
            header("Location: index.php?registered=true&username=" . urlencode($username) . "&password=" . urlencode($password));
            exit();
        } else {
            echo "Pendaftaran gagal.";
        }
        
    } catch (Exception $e) {
        echo "Terjadi kesalahan: " . $e->getMessage();
    }

    // Tutup statement
    mysqli_stmt_close($stmt);
}

// Tutup koneksi
mysqli_close($koneksi);
?>

<!DOCTYPE html>
<html>
<head>
<title>Creative Colorlib SignUp Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Custom Theme files -->
<link href="style.css" rel="stylesheet" type="text/css" media="all" />
<!-- //Custom Theme files -->
<!-- web font -->
<link href="//fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i" rel="stylesheet">
<!-- //web font -->


 
</head>
<body>
    <!-- main -->
    <div class="main-w3layouts wrapper">
        <h1>Creative SignUp Form</h1>
        <div class="main-agileinfo">
            <div class="agileits-top">
                <form action="" method="POST">
                    <input class="text" type="text" name="username" placeholder="Username" required="">
                    <input class="text email" type="email" name="email" placeholder="Email" required="">
                    <input class="text" type="password" name="password" placeholder="Password" required="">
                    <input class="text w3lpass" type="password" name="confirm_password" placeholder="Confirm Password" required="">
                    <div class="wthree-text">
                        <label class="anim">
                            <input type="checkbox" class="checkbox" required="">
                            <span>Setuju Untuk Register</span>
                        </label>
                        <div class="clear"> </div>
                    </div>
                    <input type="submit" name="register" value="Register">
                </form>
                <p>Have an Account? <a href="index.php"> Login Now!</a></p>
            </div>
        </div>
        <!-- copyright -->
        <div class="colorlibcopy-agile">
        <p>© 2024 SugengBudi | Design by <a href="https://github.com/SugengBudiArter" target="_blank">SugengBudi</a></p>
        </div>

    </div>
    <!-- //main -->
</body>
</html>

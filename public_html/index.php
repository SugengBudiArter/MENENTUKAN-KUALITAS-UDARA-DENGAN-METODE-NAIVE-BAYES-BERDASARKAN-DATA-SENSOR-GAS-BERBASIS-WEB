<!DOCTYPE html>
<html>
<head>
<title>SignIn</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> 
    addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); 
    function hideURLbar(){ window.scrollTo(0,1); } 
</script>
<!-- Custom Theme files -->
<link href="style.css" rel="stylesheet" type="text/css" media="all" />
<!-- //Custom Theme files -->
<!-- web font -->
<link href="//fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i" rel="stylesheet">
<!-- //web font -->
<style>
    .alert {
        padding: 15px;
        background-color: #f44336;
        color: white;
        margin-bottom: 15px;
    }

    .alertV {
        padding: 15px;
        background-color: #008000;
        color: white;
        margin-bottom: 15px;
    }

    .closebtn {
        margin-left: 15px;
        color: white;
        font-weight: bold;
        float: right;
        font-size: 20px;
        line-height: 20px;
        cursor: pointer;
        transition: 0.3s;
    }

    .closebtn:hover {
        color: black;
    }
</style>


<!-- Notification Popup Script -->
<script>


    window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('registered')) {
        const username = urlParams.get('username');
        const password = urlParams.get('password');
        const message = `Selamat, registrasi berhasil dengan username: ${username} dan password: ${password}`;
        // Mengirim pesan ke PHP untuk ditampilkan sebagai popup
        window.location.href = `index.php?message=${encodeURIComponent(message)}`;
    }
}

</script>

</head>
<body>

    <!-- Notification Popup -->
    

    <!-- main -->
    <div class="main-w3layouts wrapper">
        <h1>SignIn</h1>
        <div class="main-agileinfo">
            <div class="agileits-top">
            
            <?php 


                if(isset($_GET['pesan']) && $_GET['pesan'] == "gagal"){
                    echo "<div class='alert' id='alertMessage'>Username dan Password tidak sesuai !</div>";
                } elseif (isset($_GET['pesan']) && $_GET['pesan'] == "login") {
                    echo "<div class='alert' id='alertMessage'>Silahkan Login Terlebih Dahulu !!!</div>";
                } elseif (isset($_GET['pesan']) && $_GET['pesan'] == "notadmin") {
                    echo "<div class='alert' id='alertMessage'>ANDA BUKAN ADMIN !!!</div>";
                } elseif (isset($_GET['pesan']) && $_GET['pesan'] == "notuser") {
                    echo "<div class='alert' id='alertMessage'>ANDA BUKAN USER !!!</div>";
                } if(isset($_GET['message'])) {
                    $message = $_GET['message'];
                    echo "<div id='alertMessage' class='alertV'>$message</div>";
                }
				
            ?>

                <form action="cek_login.php" method="post">
                    <input class="text" type="text" name="username" placeholder="Username" required="">
                    <input class="text" type="password" name="password" placeholder="Password" required="">
                    <div class="wthree-text">
                        <label class="anim">
                            <input type="checkbox" class="checkbox" required="">
                            <span>Setuju Untuk Masuk</span>
                        </label>
                        <div class="clear"> </div>
                    </div>
                    <input type="submit" value="login">
                </form>
                <p>Don't have an Account? <a href="singup.php">SignUp Now!</a></p>
            </div>
        </div>
        <!-- copyright -->
        <div class="colorlibcopy-agile">
            <p>© 2024 SugengBudi | Design by <a href="https://github.com/SugengBudiArter" target="_blank">SugengBudi</a></p>
        </div>
        <!-- //copyright -->
        <ul class="colorlib-bubbles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>
    <!-- //main -->
    <script>
        // Function to hide the alert message after a few seconds
        setTimeout(function() {
            var alertMessage = document.getElementById('alertMessage');
            if (alertMessage) {
                alertMessage.style.display = 'none';
            }
        }, 3000); // 3000 milliseconds = 3 seconds
    </script>
</body>
</html>

<?php 
    session_start();
    include("../koneksi.php");
    if(isset($_SESSION['user_id'])){
        $id = $_SESSION['user_id'];
        $query = mysqli_query($konek,"select * from users where role='user' and user_id='$id'")or die (mysqli_error($konek));
        while($data=mysqli_fetch_array($query)){
            $_SESSION['email'] = $data['email'];
            $_SESSION['name'] = $data['name'];
            $_SESSION['fullname'] = $data['fullname'];
            $_SESSION['major'] = $data['major'];
            $_SESSION['university'] = $data['university'];
            $_SESSION['profile_picture'] = $data['profile_picture'];
        }
    }else{
        header("Location: ../user/login.php");
    }

    // Proses publish file yang dipilih
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publish'])) {
        if (isset($_POST['selected_files']) && !empty($_POST['selected_files'])) {
            $selected_files = $_POST['selected_files'];
            $user_id = $_SESSION['user_id'];
            
            // Filter file_id untuk keamanan
            $file_ids = implode(',', array_map('intval', $selected_files));

            // Query update hanya untuk file yang dipilih
            $update_query = "UPDATE books SET upload_status = 'publish' WHERE publisher_id = '$user_id' AND book_id IN ($file_ids)";
            if (mysqli_query($konek, $update_query)) {
                echo "<script>alert('File yang dipilih berhasil dipublish!'); window.location.href='uploaded.php';</script>";
            } else {
                echo "<script>alert('Gagal mempublish file.');</script>";
            }
        } else {
            echo "<script>alert('Tidak ada file yang dipilih.');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <title>Profile Pengguna</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
            background-color: #F9F9F9;
        }
        .navbar{
            background-color: #1E5B86;
        }
        .nav-link{
            color:white;
        }
        .profile-nav{
            border-radius: 20%;
            width: 50px;
            height: 38px;
        }
        p{
            color: #ADA7A7;
        }
        .img-top img{
            width: 100%;
            margin-bottom: 10px;
        }
        main{
            width: 90%;
        }
        .btn{
            background-color: #1E5B86;
        }
        .profile-img{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .name-email{
            margin-left: 20px;
        }
        .d-flex .profile-pict{
            width: 80px;
            height: 80px;
            border-radius: 100%;
        }
        .name-email{
            margin-top: 14px;
        }
        .form-section {
            display: flex;
            gap: 20px;
        }
        .form-section .left, .form-section .right {
            flex: 1;
        }
        .form-label {
            color: #555;
        }
        .checkbox-container {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 10px;
    }

    .checkbox-container input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .checkbox-container .custom-checkbox {
        width: 20px;
        height: 20px;
        margin-right: 180px;
        background-color: #f0f0f0;
        border: 2px solid #ccc;
        border-radius: 50%; /* Membuatnya bulat */
        display: flex;
        justify-content: center;
        align-items: center;
        transition: all 0.3s;
    }

    .checkbox-container input[type="checkbox"]:checked + .custom-checkbox {
        background-color: #1E5B86; /* Warna saat dicentang */
        border-color: #1E5B86;
    }

    .checkbox-container input[type="checkbox"]:checked + .custom-checkbox::after {
        content: '';
        width: 10px;
        height: 10px;
        background-color: white;
        border-radius: 50%; /* Membuat centang bulat */
    }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-primary px-3">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="../index.php">Home</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="../search.php">Buy</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="uploaded.php">Sell</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" aria-current="page" href="../monetisasi.php">History</a>
            </li>
        </ul>
        <form class="d-flex" role="search" action="search.php" method="get">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search">
            <?php if($_SESSION['profile_picture'] != "") { ?>
                <div class="dropdown" style="width : 38px;">
                    <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0;">
                        <img class="profile-nav" src="../uploads/<?=$_SESSION['profile_picture']?>" alt="Profile Picture" style="width: 38px;">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
                    </ul>
                </div>
                <?php } else { ?>
                <div class="dropdown" style="width : 38px;">
                    <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0;">
                        <img class="profile-nav" src="default.png" alt="Profile Picture" style="width: 38px;">
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="user/logout.php">Logout</a></li>
                    </ul>
                </div>
                <?php } ?>
            <a href="notifikasi.php" style="margin-left: 8px;"><img src="notif.png" alt="Notifikasi"></a>
            <a href="../cart.php" style="margin-left: 8px; padding:1px; background-color:white; border-radius:8px;"><img src="../cart (2).png" alt="Cart" style="height:36px"></a>
        </form>
        </div>
    </div>
    </nav>

    <main class="m-auto py-5">
    <div class="container">
        <h3 class="mb-4">Uploaded File</h3>
        <div class="row mb-3">
            <?php 
                $user_id = $_SESSION['user_id'];
                $file_query = mysqli_query($konek, "SELECT * FROM books WHERE publisher_id = '$user_id' and upload_status = 'publish'");
                if (mysqli_num_rows($file_query) > 0) {
                    while ($file = mysqli_fetch_array($file_query)) {
                        // Potong judul menjadi maksimal 20 karakter
                        $short_title = strlen($file['title']) > 40 ? substr($file['title'], 0, 40) . '...' : $file['title'];
                
                        echo '
                        <div class="col-3">
                            <div class="card text-center shadow-sm p-3 mb-2 bg-white rounded">
                                <img src="file.png" alt="file" style="width: 80px; margin-left: auto; margin-right:auto;">
                                <i class="bi bi-file-earmark-text fs-1 mb-3"></i>
                                <p class="mb-0">' . htmlspecialchars($short_title) . '</p>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p>Tidak ada file yang diunggah.</p>';
                }                
            ?>
            <!-- Add New File -->
            <div class="col-3">
                <a href="upload.php">
                    <div class="card text-center shadow-sm p-3 mb-2 bg-light rounded">
                        <img src="Plus.png" alt="file" style="width: 80px; margin-left: auto; margin-right:auto;">
                        <i class="bi bi-plus fs-1 text-muted"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="container">
        <h3 class="mb-4">Draft File</h3>
        <form method="POST" action="">
        <div class="row">
            <!-- Draft File -->
            <?php 
                $user_id = $_SESSION['user_id'];
                $file_query = mysqli_query($konek, "SELECT * FROM books WHERE publisher_id = '$user_id' and upload_status = 'draft'");
                if (mysqli_num_rows($file_query) > 0) {
                    while ($file = mysqli_fetch_array($file_query)) {
                        // Potong judul menjadi maksimal 20 karakter
                        $short_title = strlen($file['title']) > 40 ? substr($file['title'], 0, 40) . '...' : $file['title'];
                
                        echo '
                        <div class="col-3">
                            <div class="card text-center shadow-sm p-3 mb-4 bg-white rounded">
                                <div class="checkbox-container">
                                    <input type="checkbox" id="checkbox_'. $file["book_id"] .'" name="selected_files[]" value='. $file["book_id"] .'">
                                    <label for="checkbox_'. $file["book_id"] .'" class="custom-checkbox"></label>
                                </div>
                                <br>
                                <img src="file.png" alt="file" style="width: 80px; margin-left: auto; margin-right:auto;">
                                <i class="bi bi-file-earmark-text fs-1 mb-3"></i>
                                <p class="mb-0">' . htmlspecialchars($short_title) . '</p>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p>Tidak ada file yang diunggah.</p>';
                }                
            ?>
        </div>
        <!-- Tambahkan tombol publish -->
        <button type="submit" name="publish" class="btn btn-primary">Publish</button>
    </form>
    </div>
    </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
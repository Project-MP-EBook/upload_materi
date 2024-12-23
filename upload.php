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

      // Periksa apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $title = mysqli_real_escape_string($konek, $_POST['fileName']);
    $description = mysqli_real_escape_string($konek, $_POST['fileDescription']);
    $price = isset($_POST['shareFree']) ? 0 : (float)$_POST['price'];
    $category = mysqli_real_escape_string($konek, $_POST['category']);
    $publisher_id = $_SESSION['user_id'];
    // Tentukan status berdasarkan tombol yang diklik
    if (isset($_POST['saveAsDraft'])) {
        $status = "draft";
    } elseif (isset($_POST['publish'])) {
        $status = "publish";
    }

    // Proses upload file utama
    $file = $_FILES['file'];
    $uploadDir = '../uploads/';
    $fileName = basename($file['name']);
    $targetFile = $uploadDir . $fileName;

    // Proses upload cover
    $cover = $_FILES['cover'];
    $coverName = basename($cover['name']);
    $targetCover = $uploadDir . $coverName;

    if (move_uploaded_file($file['tmp_name'], $targetFile) && move_uploaded_file($cover['tmp_name'], $targetCover)) {
        // Jika file dan cover berhasil diupload, simpan data ke database
        $query = "INSERT INTO books (title, description, file, cover, price, upload_status, publisher_id, category) 
          VALUES ('$title', '$description', '$fileName', '$coverName', '$price', '$status', '$publisher_id', '$category')";
        $result = mysqli_query($konek, $query);

        if ($result) {
            // Redirect ke halaman sukses atau tampilkan pesan sukses
            echo "<script>alert('File dan cover berhasil diupload dan disimpan ke database!'); window.location.href = 'uploaded.php';</script>";
        } else {
            // Tampilkan pesan error jika gagal menyimpan ke database
            echo "<script>alert('Gagal menyimpan data ke database!'); window.history.back();</script>";
        }
    } else {
        // Tampilkan pesan error jika gagal mengupload file atau cover
        echo "<script>alert('Gagal mengupload file atau cover!'); window.history.back();</script>";
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

    <main class="m-auto">
    <div class="container py-4">
        <h3 class="mb-4">Upload File</h3>
        <form id="uploadForm" enctype="multipart/form-data" method="post">
            <div class="mb-4">
                <div 
                    id="dropZone" 
                    class="border border-2 p-4 text-center" 
                    style="background-color: #F3F3F3; border-radius: 10px;"
                >
                    <img src="file.png" alt="Upload Icon" style="width: 50px; margin-bottom: 10px;">
                    <p id="dropZoneText">Drag and drop or choose file to upload your files.<br>All pdf, doc, and pptx types are supported.</p>
                    <input type="file" id="fileInput" name="file" class="form-control d-none" accept=".pdf,.doc,.docx,.ppt,.pptx">
                </div>
            </div>
            <div class="mb-3">
                <label for="fileName" class="form-label">File Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="fileName" name="fileName" placeholder="your file name">
            </div>
            <div class="mb-3">
                <label for="fileDescription" class="form-label">File Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="fileDescription" name="fileDescription" rows="3" placeholder="your file description"></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="price" name="price" placeholder="price of file">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                <select class="form-select" id="category" name="category" required>
                    <option value="" disabled selected>Select a category</option>
                    <option value="Informatics">Informatics</option>
                    <option value="Industrial Engineering">Industrial Engineering</option>
                    <option value="Information System">Information System</option>
                    <option value="Agriculture">Agriculture</option>
                    <option value="Social and Political Science">Social and Political Science</option>
                    <option value="Economics">Economics</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="cover" class="form-label">Upload Cover <span class="text-danger">*</span></label>
                <input type="file" id="cover" name="cover" class="form-control" accept=".jpg,.jpeg,.png">
            </div>
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="shareFree" name="shareFree">
                    <label class="form-check-label" for="shareFree">Share for Free</label>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-3">
                <button type="submit" name="saveAsDraft" class="btn btn-primary text-white">Draft</button>
                <button type="submit" name="publish" class="btn btn-primary text-white">Publish</button>
            </div>
        </form>
    </div>
</main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const dropZoneText = document.getElementById('dropZoneText');
    const shareFreeCheckbox = document.getElementById('shareFree');
    const priceInput = document.getElementById('price');

    // Event listener untuk checkbox
    shareFreeCheckbox.addEventListener('change', (event) => {
        if (event.target.checked) {
            priceInput.value = 0; // Set harga ke 0
            priceInput.setAttribute('disabled', 'disabled'); // Nonaktifkan input
        } else {
            priceInput.removeAttribute('disabled'); // Aktifkan kembali input
            priceInput.value = ''; // Kosongkan input
        }
    });

    // Klik untuk memilih file
    dropZone.addEventListener('click', () => {
        fileInput.click();
    });

    // Perbarui teks saat file dipilih
    fileInput.addEventListener('change', (event) => {
        const fileName = event.target.files[0]?.name || "No file selected";
        dropZoneText.innerText = `Selected File: ${fileName}`;
    });

    // Drag & Drop Events
    dropZone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropZone.classList.remove('drag-over');

        const files = event.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            dropZoneText.innerText = `Selected File: ${files[0].name}`;
        }
    });
</script>
</body>
</html>
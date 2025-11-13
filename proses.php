<?php
include 'koneksi.php'; 

if (isset($_FILES['pdf_file'])) {
    $file = $_FILES['pdf_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Terjadi kesalahan saat upload.");
    }

    $fileType = mime_content_type($file['tmp_name']);
    if ($fileType !== 'application/pdf') {
        die("Hanya file PDF yang diperbolehkan!");
    }

    $maxSize = 10 * 1024 * 1024; 
    if ($file['size'] > $maxSize) {
        die("Ukuran file maksimal 10 MB!");
    }

    $originalName = basename($file['name']);
    $safeName = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", $originalName);
    $newFileName = "narendra_" . time() . "_" . $safeName;

    $uploadDir = __DIR__ . "/uploads/";
    $uploadPath = $uploadDir . $newFileName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $stmt = $conn->prepare("INSERT INTO uploads (path, name) VALUES (?, ?)");
        $stmt->bind_param("ss", $uploadPath, $originalName);
        $stmt->execute();
        $stmt->close();

        echo "<p style='color:green;'>File berhasil diunggah!</p>";
        echo "<p>Lokasi file: <a href='uploads/$newFileName' target='_blank'>$newFileName</a></p>";
    } else {
        echo "<p style='color:red;'>Gagal menyimpan file ke server.</p>";
    }

    $conn->close();
} else {
    echo "Tidak ada file yang diunggah.";
}
?>

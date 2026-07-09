<?php
// 1. PHP Redirect (Metode utama & tercepat)
if (!headers_sent()) {
    header("Location: /public/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <!-- 2. HTML Meta Refresh Redirect (Pencadangan jika header PHP terhambat) -->
    <meta http-equiv="refresh" content="0;url=/public/index.php">
    <title>Mengalihkan...</title>
    <script>
        // 3. JavaScript Redirect (Pencadangan kedua)
        window.location.href = "/public/index.php";
    </script>
</head>
<body>
    <p style="font-family: sans-serif; text-align: center; margin-top: 50px;">
        Sedang mengalihkan... Jika halaman tidak berpindah, 
        <a href="/public/index.php">klik di sini</a>.
    </p>
</body>
</html>
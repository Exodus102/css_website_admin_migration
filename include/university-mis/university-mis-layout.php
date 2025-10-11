<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$page = $_GET['page'] ?? 'dashboard';
$page_title = ucwords(str_replace(['-', '_'], ' ', $page));

// Handle specific cases for acronyms or special names
if (strtolower($page_title) === 'ncar') $page_title = 'NCAR';
if (strtolower($page_title) === 'qr code') $page_title = 'QR Code';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../Tailwind/src/output.css">
    <title>Customer Satisfaction</title>
</head>

<body class="bg-[#E6E7EC] font-sans leading-normal tracking-normal">
    <div class="h-16 relative">
        <?php include "university-mis-header.php"; ?>
    </div>

    <div class="flex h-[calc(100vh-4rem)]">

        <?php include "university-mis-navigation.php"; ?>

        <main class="flex-1 p-5 w-4/5">
            <h1 class="text-3xl font-bold mb-6">
            </h1>
            <?php
            $filePath = "../../pages/{$page}/{$page}.php";
            if (file_exists($filePath)) {
                include $filePath;
            } else {
                echo "<div>Page not found.</div>";
            }
            ?>
        </main>
    </div>
</body>

</html>
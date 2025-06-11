<?php
// 암호화 함수
function encrypt($data, $key = 'your-secret-key') {
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $ciphertext_raw);
}

$key = 'your-secret-key';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

$url = $data['url'] ?? '';
$rotation = $data['rotation'] ?? 0;

$encryptedUrl = encrypt($url, $key);
$encryptedRotation = encrypt(strval($rotation), $key);

echo json_encode([
    'eurl' => $encryptedUrl,
    'erotation' => $encryptedRotation
]); 
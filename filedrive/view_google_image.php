<?php
// 디버깅을 위한 에러 표시 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 암호화/복호화 함수 정의
function decrypt($data, $key = 'your-secret-key') {
    $c = base64_decode($data);
    $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
    $iv = substr($c, 0, $ivlen);
    $ciphertext_raw = substr($c, $ivlen);
    return openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
}

$key = 'your-secret-key'; // 실제 서비스에서는 안전하게 관리

// 암호화된 파라미터 받기
$encryptedUrl = $_GET['eurl'] ?? '';
$encryptedRotation = $_GET['erotation'] ?? '';

// 복호화
$imageUrl = $encryptedUrl ? decrypt($encryptedUrl, $key) : '';
$rotation = $encryptedRotation ? intval(decrypt($encryptedRotation, $key)) : 0;

// 구글 드라이브 URL 변환
if (strpos($imageUrl, 'drive.google.com') !== false) {
    if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $imageUrl, $matches)) {
        $fileId = $matches[1];
        $imageUrl = "https://drive.google.com/uc?export=view&id=" . $fileId;
    }
}

// 디버깅을 위한 로그
error_log("Original URL: " . $imageUrl);
error_log("Rotation: " . $rotation);
?>
<!DOCTYPE html>
<html>
<head>
    <title>이미지 보기</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f0f0f0;
            overflow: hidden;
        }
        .image-container {
            max-width: 100%;
            max-height: 100vh;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .image-container img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            transform-origin: center center;
            transform: rotate(<?php echo $rotation; ?>deg);
            transition: transform 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="image-container">
        <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="이미지">
    </div>
    <script>
        // 이미지 로드 상태 확인
        window.onload = function() {
            const img = document.querySelector('img');
            if (!img.complete) {
                console.log('이미지 로딩 중...');
            }
        }
    </script>
</body>
</html> 
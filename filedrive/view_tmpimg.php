<?php
$img = isset($_GET['img']) ? $_GET['img'] : '';
$rotation = isset($_GET['rotation']) ? intval($_GET['rotation']) : 0;

// 보안: tmpimg 경로만 허용
if (strpos($img, '/tmpimg/') !== 0) {
    die('잘못된 접근');
}

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
        <img src="<?php echo htmlspecialchars($img); ?>" alt="이미지">
    </div>
</body>
</html> 
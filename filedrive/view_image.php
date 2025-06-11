<?php
// 회전 각도 파라미터 가져오기
$rotation = isset($_GET['rotation']) ? intval($_GET['rotation']) : 0;
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
        }
        .image-container {
            max-width: 100%;
            max-height: 100vh;
            text-align: center;
        }
        .image-container img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="image-container">
        <img src="<?php echo htmlspecialchars($_GET['url'] ?? ''); ?>" 
             style="transform: rotate(<?php echo $rotation; ?>deg);"
             alt="이미지">
    </div>
</body>
</html> 
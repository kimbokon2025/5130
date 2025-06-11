<?php
// 입력값 받기
$fileId = $_POST['fileId'] ?? '';
$rotation = intval($_POST['rotation'] ?? 0);

header('Content-Type: application/json');

if (!$fileId) {
    echo json_encode(['success' => false, 'msg' => 'fileId 누락']);
    exit;
}

// 구글드라이브 이미지 다운로드
$googleUrl = 'https://drive.google.com/uc?export=download&id=' . urlencode($fileId);
$tmpDir = $_SERVER['DOCUMENT_ROOT'] . '/tmpimg/';
if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0777, true);
}
$tmpName = uniqid('img_', true) . '.jpg';
$tmpPath = $tmpDir . $tmpName;
$tmpUrl = '/tmpimg/' . $tmpName;

// 파일 다운로드
$imgData = @file_get_contents($googleUrl);
if ($imgData === false) {
    echo json_encode(['success' => false, 'msg' => '구글드라이브 이미지 다운로드 실패']);
    exit;
}
file_put_contents($tmpPath, $imgData);

// (회전은 프론트에서 CSS로 처리)

echo json_encode([
    'success' => true,
    'imgUrl' => $tmpUrl,
    'rotation' => $rotation
]); 
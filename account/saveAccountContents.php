<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
    $fileName = $_POST['fileName'] ?? 'accountContents.json';
    $backupFileName = $_POST['backupFileName'] ?? '';
    $data = $_POST['data'] ?? '';

    // JSON 폴더 경로 확인 및 생성
    $jsonFolder = $_SERVER['DOCUMENT_ROOT'] . '/account/json/';  //################### 폴더 위치 주의 //
    if (!is_dir($jsonFolder)) {
        mkdir($jsonFolder, 0755, true); // 폴더 생성
    } 

    // 데이터 저장
    if (!empty($backupFileName)) {
        $backupFilePath = $jsonFolder . basename($backupFileName);
        file_put_contents($backupFilePath, $data); // 백업 파일 저장
    }

    $filePath = basename($fileName);
    file_put_contents($filePath, $data); // 원본 파일 저장

    echo json_encode(['message' => '데이터가 저장되었습니다.']);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Invalid request method.']);
}

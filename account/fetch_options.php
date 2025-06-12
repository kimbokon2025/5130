<?php
// JSON 파일 경로
$jsonFile =  $_SERVER['DOCUMENT_ROOT'] . '/account/accountContents.json';

// JSON 파일 읽기
if (file_exists($jsonFile)) {
    $jsonData = json_decode(file_get_contents($jsonFile), true);

    // 수입 옵션 생성
    $incomeOptions = [];
    if (isset($jsonData['수입'])) {
        foreach ($jsonData['수입'] as $key => $value) {
            $incomeOptions[$key] = $value['description'];
        }
    } 
 
    // 지출 옵션 생성
    $expenseOptions = [];
    if (isset($jsonData['지출'])) {
        foreach ($jsonData['지출'] as $key => $value) {
            $expenseOptions[$key] = $value['description'];
        }
    }

    // 결과 출력
    // echo json_encode([
        // 'incomeOptions' => $incomeOptions,
        // 'expenseOptions' => $expenseOptions,
        // 'selectedDetails' => $details
    // ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'error' => 'JSON 파일을 찾을 수 없습니다.'
    ], JSON_UNESCAPED_UNICODE);
}
?>

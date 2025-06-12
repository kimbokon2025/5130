<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON 데이터 받기
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    // PHPExcel 라이브러리 포함
    require_once($_SERVER['DOCUMENT_ROOT'] . '/PHPExcel_1.8.0/Classes/PHPExcel.php');

    // 새로운 PHPExcel 객체 생성
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();

    // 헤더 설정
    $headers = ['번호', '등록일자', '항목', '세부항목', '상세내용', '수입', '지출', '잔액', '적요'];
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        // 헤더 스타일 설정
        $sheet->getStyle($col . '1')->getFont()->setBold(true);
        $sheet->getStyle($col . '1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
        $sheet->getStyle($col . '1')->getFill()->getStartColor()->setRGB('D9D9D9');
        $col++;
    }

    // 데이터 입력
    $row = 2;
    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $item['number']);
        $sheet->setCellValue('B' . $row, $item['registDate']);
        $sheet->setCellValue('C' . $row, $item['content']);
        $sheet->setCellValue('D' . $row, $item['contentSub']);
        $sheet->setCellValue('E' . $row, $item['contentDetail']);
        $sheet->setCellValue('F' . $row, $item['income']);
        $sheet->setCellValue('G' . $row, $item['expense']);
        $sheet->setCellValue('H' . $row, $item['balance']);
        $sheet->setCellValue('I' . $row, $item['memo']);
        $row++;
    }

    // 열 너비 자동 조정
    foreach(range('A','I') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // 파일 저장
    $filename = 'account_' . date('Ymd_His') . '.xlsx';
    $filepath = $_SERVER['DOCUMENT_ROOT'] . '/account/excel/' . $filename;
    
    // 디렉토리가 없으면 생성
    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/account/excel')) {
        mkdir($_SERVER['DOCUMENT_ROOT'] . '/account/excel', 0777, true);
    }

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save($filepath);

    echo json_encode(['success' => true, 'filename' => $filename]);
    exit;
} 
else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['download'])) {
    // 파일 다운로드 처리
    $filename = $_GET['download'];
    $filepath = $_SERVER['DOCUMENT_ROOT'] . '/account/excel/' . $filename;

    if (file_exists($filepath)) {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        readfile($filepath);
        
        // 파일 삭제
        unlink($filepath);
        exit;
    } else {
        echo "File not found";
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

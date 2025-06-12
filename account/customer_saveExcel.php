<?php
// 에러 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // JSON 응답 설정

$response = ['success' => false, 'message' => 'Unknown error'];

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // 요청에서 JSON 데이터 가져오기
        $data = json_decode(file_get_contents('php://input'), true);

        // JSON 오류 확인
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input: ' . json_last_error_msg());
        }

        // 데이터가 비어있는지 확인
        if (empty($data)) {
            throw new Exception('No data received');
        }

        // PHPExcel 라이브러리 포함
        require '../PHPExcel_1.8.0/Classes/PHPExcel.php';

        // 새로운 PHPExcel 객체 생성
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // 헤더 설정
		
        $headers = [
            'number' => '번호',
            'secondord' => '거래처명',
            'lastbalance' => '이월잔액',
            'monthsales' => '당월매출',
            'income' => '수금합계',
            'balances' => '잔액',
            'payday' => '결제일',            
            'memo' => '적요',
            'secondordnum' => '거래처코드'
        ];

        // 헤더를 엑셀에 추가
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            // 셀의 글씨를 굵게 하고 음영을 추가
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet->getStyle($col . '1')->getFill()->getStartColor()->setRGB('D9D9D9');
            $col++;
        }

        // 데이터 채우기
        $rowNumber = 2;
        foreach ($data as $row) {
            $col = 'A';
            foreach ($headers as $key => $header) {
                $value = isset($row[$key]) ? $row[$key] : ''; // 데이터가 있으면 채우고 없으면 공백
                $sheet->setCellValue($col . $rowNumber, $value);
                $col++;
            }
            $rowNumber++;
        }

        // 특정 열의 기본 폭 설정
        $sheet->getColumnDimension('B')->setWidth(40);

        // 나머지 열의 폭을 글씨에 맞추기
        foreach (range('A', 'I') as $columnID) {
            if (!in_array($columnID, ['B'])) {
                $sheet->getColumnDimension($columnID)->setWidth(25);
            }
        }

        // 테두리 설정
        $styleArray = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:I' . ($rowNumber - 1))->applyFromArray($styleArray);

        // 열별 정렬 설정
        $sheet->getStyle('E2:E' . ($rowNumber - 1))
              ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F2:F' . ($rowNumber - 1))
              ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('G2:G' . ($rowNumber - 1))
              ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H2:H' . ($rowNumber - 1))
              ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // 파일 저장
        $filename = 'DH모터(거래처원장)_' . date('YmdHis') . '.xlsx';
        $filePath = '../excelsave/' . $filename; // 파일 경로 확인 필요
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($filePath);

        // 파일이 생성되었는지 확인
        if (file_exists($filePath)) {
            $response = ['success' => true, 'filename' => $filePath];
        } else {
            throw new Exception('Failed to save the Excel file');
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    error_log($e->getMessage()); // 오류 로그 기록
    $response = ['success' => false, 'message' => $e->getMessage()];
}

// JSON 응답 반환
echo json_encode($response);
?>

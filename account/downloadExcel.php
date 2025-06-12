<?php
// 파일 이름이 제공되었는지 확인
if (!isset($_GET['filename']) || empty($_GET['filename'])) {
    die('Filename not specified.');
}

$filename = basename($_GET['filename']);
$filePath = '../excelsave/' . $filename;

// 파일이 존재하는지 확인
if (!file_exists($filePath)) {
    die('File not found.');
}
 
// 파일을 다운로드하도록 설정
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . $filename);
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
?>

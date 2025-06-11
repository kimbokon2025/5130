<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");
header("Content-Type: application/json");

$menu = isset($_REQUEST['menu']) ? $_REQUEST['menu'] : '';

$tablename = 'logdata_menu';

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect(); 

try {
    // log 기록    
    $data = date("Y-m-d H:i:s") . " - " . $menu . " - " . $_SESSION["name"];    
    $pdo->beginTransaction();
    $sql = "INSERT INTO {$DB}.logdata_menu(data) VALUES(?)";
    $stmh = $pdo->prepare($sql); 
    $stmh->bindValue(1, $data, PDO::PARAM_STR);   
    $stmh->execute();
    $pdo->commit(); 
    
    $response = ['status' => 'success', 'message' => 'Log recorded successfully'];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (PDOException $Exception) {
    error_log("오류: " . $Exception->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $Exception->getMessage()]);
} catch (Exception $e) {
    error_log("오류: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>

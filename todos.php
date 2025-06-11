<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
$day = isset($_POST['day']) ? $_POST['day'] : '';
$todo = isset($_POST['todo']) ? $_POST['todo'] : '';

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $mode === 'insert') {
    try {
        $sql = "INSERT INTO todos (day, todo) VALUES (:day, :todo)";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(':day', $day, PDO::PARAM_INT);
        $stmh->bindValue(':todo', $todo, PDO::PARAM_STR);
        $stmh->execute();
        echo json_encode(['status' => 'success']);
    } catch (PDOException $Exception) {
        echo json_encode(['status' => 'error', 'message' => $Exception->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $day = isset($_GET['day']) ? $_GET['day'] : '';
    try {
        $sql = "SELECT todo FROM todos WHERE day = :day";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(':day', $day, PDO::PARAM_INT);
        $stmh->execute();
        $todos = $stmh->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($todos);
    } catch (PDOException $Exception) {
        echo json_encode(['status' => 'error', 'message' => $Exception->getMessage()]);
    }
}
?>

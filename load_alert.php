<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/session_header.php'); 
require_once("./lib/mydb.php");
$pdo = db_connect();
$num=1;
$response = array();

try {
    $sql = "select * from " . $DB . ".alert where num=?";
    $stmh = $pdo->prepare($sql); 
    $stmh->bindValue(1, $num, PDO::PARAM_STR);
    $stmh->execute();

    $row = $stmh->fetch(PDO::FETCH_ASSOC);
    $response = array(
        'voc_alert' => $row["voc_alert"],
        'ma_alert' => $row["ma_alert"],
        'order_alert' => $row["order_alert"]
    );

} catch (PDOException $Exception) {
    $response = array(
        'error' => $Exception->getMessage()
    );
}

echo json_encode($response);
?>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");
$tablename = isset($_REQUEST['tablename']) ? $_REQUEST['tablename'] : 'account';
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

header("Content-Type: application/json");  // Use JSON content type

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

include "_request.php";
 
// Construct the searchtag value
$searchtag = $registDate . ' ' . $inoutsep . ' ' . $content . ' ' . $contentSub . ' ' . $amount . $content_detail ;

if ($mode == "update") {
    $update_log = date("Y-m-d H:i:s") . " - " . $_SESSION["name"] . " " . $update_log . "&#10";
    try {
        $pdo->beginTransaction();
        // Prepare the SQL query for updating the account information
        $sql = "UPDATE " . $tablename . " SET ";
        $sql .= "registDate = ?, inoutsep = ?, content = ?, content_detail = ?, amount = ?, memo = ?, searchtag = ?, update_log = ?, first_writer = ?, bankbook = ?, secondordnum = ?, contentSub=?  ";
        $sql .= "WHERE num = ? LIMIT 1"; // Update only one record matching the 'num'

        $stmh = $pdo->prepare($sql);

        // Bind the variables to the prepared statement as parameters
        $stmh->bindValue(1, $registDate, PDO::PARAM_STR);
        $stmh->bindValue(2, $inoutsep, PDO::PARAM_STR);
        $stmh->bindValue(3, $content, PDO::PARAM_STR);
        $stmh->bindValue(4, $content_detail, PDO::PARAM_STR);
        $stmh->bindValue(5, str_replace(',', '', $amount), PDO::PARAM_STR);  // 숫자안에 콤마제거후 저장
        $stmh->bindValue(6, $memo, PDO::PARAM_STR);
        $stmh->bindValue(7, $searchtag, PDO::PARAM_STR);
        $stmh->bindValue(8, $update_log, PDO::PARAM_STR);
        $stmh->bindValue(9, $first_writer, PDO::PARAM_STR);
        $stmh->bindValue(10, $bankbook, PDO::PARAM_STR);
        $stmh->bindValue(11, $secondordnum, PDO::PARAM_STR);
        $stmh->bindValue(12, $contentSub, PDO::PARAM_STR);
        $stmh->bindValue(13, $num, PDO::PARAM_INT);

        // Execute the statement
        $stmh->execute();
        $pdo->commit();
    } catch (PDOException $Exception) {
        $pdo->rollBack();
        print "오류: " . $Exception->getMessage();
    }
}

if ($mode == "insert" || $mode == '' || $mode == null) {
    $first_writer = date("Y-m-d H:i:s") . " - " . $_SESSION["name"] ;
    $update_log = date("Y-m-d H:i:s") . " - " . $_SESSION["name"] . " " . $update_log . "&#10";
    // Data insertion
    try {
        $pdo->beginTransaction();

        // Updated columns and values to be inserted
        $sql = "INSERT INTO " . $tablename . " (";
        $sql .= "registDate, inoutsep, content, content_detail, amount, memo, searchtag, update_log, first_writer, bankbook, secondordnum, contentSub ";
        $sql .= ") VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmh = $pdo->prepare($sql);

        $stmh->bindValue(1, $registDate, PDO::PARAM_STR);
        $stmh->bindValue(2, $inoutsep, PDO::PARAM_STR);
        $stmh->bindValue(3, $content, PDO::PARAM_STR);
        $stmh->bindValue(4, $content_detail, PDO::PARAM_STR);
        $stmh->bindValue(5, str_replace(',', '', $amount), PDO::PARAM_STR);
        $stmh->bindValue(6, $memo, PDO::PARAM_STR);
        $stmh->bindValue(7, $searchtag, PDO::PARAM_STR);
        $stmh->bindValue(8, $update_log, PDO::PARAM_STR);
        $stmh->bindValue(9, $first_writer, PDO::PARAM_STR);
        $stmh->bindValue(10, $bankbook, PDO::PARAM_STR);
        $stmh->bindValue(11, $secondordnum, PDO::PARAM_STR);
        $stmh->bindValue(12, $contentSub, PDO::PARAM_STR);

        // Execute the statement
        $stmh->execute();
        $pdo->commit();
    } catch (PDOException $Exception) {
        $pdo->rollBack();
        print "오류: " . $Exception->getMessage();
    }
}

if ($mode == "delete") { // Data deletion
    try {
        $pdo->beginTransaction();
        $sql = "UPDATE " . $tablename . " SET is_deleted=1 WHERE num = ?";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, $num, PDO::PARAM_INT);
        $stmh->execute();
        $pdo->commit();
    } catch (PDOException $ex) {
        $pdo->rollBack();
        print "오류: " . $ex->getMessage();
    }
}

$data = [
    'num' => $num,
    'mode' => $mode
];

echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>

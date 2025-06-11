<?php
// 회사 이름을 추출하여 배열로 반환하는 함수
function getCompanyArray($pdo, $tableName) {
// 철판 발주처 DB 불러와서 배열저장 load_company.php 루트에 위치함
require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();	
	
    $sql = "SELECT * FROM mirae8440." . $tableName;
    try {
        $stmh = $pdo->query($sql);
        $companyArray = array();

        while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
            array_push($companyArray, $row["company"]);
        }

        array_push($companyArray, '');
        return array_unique($companyArray);

    } catch (PDOException $Exception) {
        print "오류: " . $Exception->getMessage();
        return array();
    }
}

$suply_company_arr = getCompanyArray($pdo, "steelcompany");
$supplier_arr = getCompanyArray($pdo, "steelsupplier");
?>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

if (!isset($_SESSION["level"]) || $_SESSION["level"] > 5) {
    sleep(1);
    header("Location:" . $WebSite . "login/login_form.php");
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php';
$title_message = '상세 내역 조회';
?>

<link href="css/style.css" rel="stylesheet">
<title> <?=$title_message?> </title>
<style>
    /* 테이블에 테두리 추가 */
    #detailTable, #detailTable th, #detailTable td {
        border: 1px solid black;
        border-collapse: collapse;
    } 

    /* 테이블 셀 패딩 조정 */
    #detailTable th, #detailTable td {
        padding: 8px;
        text-align: center;
    }
</style>

</head>

<body>
<?php

$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y');
$startMonth = isset($_REQUEST['startMonth']) ? $_REQUEST['startMonth'] : 1;
$endMonth = isset($_REQUEST['endMonth']) ? $_REQUEST['endMonth'] : date('m');

$startDate = "$year-$startMonth-01";
$endDate = date("Y-m-t", strtotime("$year-$endMonth-01"));

$tablename = 'account';

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

// 수입 내역 조회
$incomeSql = "
    SELECT content, SUM(amount) as totalAmount
    FROM $tablename
    WHERE inoutsep = '수입'
    AND registDate BETWEEN :startDate AND :endDate
    AND is_deleted = '0'
    GROUP BY content
";
$incomeStmt = $pdo->prepare($incomeSql);
$incomeStmt->bindParam(':startDate', $startDate);
$incomeStmt->bindParam(':endDate', $endDate);
$incomeStmt->execute();
$incomeData = $incomeStmt->fetchAll(PDO::FETCH_ASSOC);

// 지출 내역 조회
$expenseSql = "
    SELECT content, SUM(amount) as totalAmount
    FROM $tablename
    WHERE inoutsep = '지출'
    AND registDate BETWEEN :startDate AND :endDate
    AND is_deleted = '0'
    GROUP BY content
";
$expenseStmt = $pdo->prepare($expenseSql);
$expenseStmt->bindParam(':startDate', $startDate);
$expenseStmt->bindParam(':endDate', $endDate);
$expenseStmt->execute();
$expenseData = $expenseStmt->fetchAll(PDO::FETCH_ASSOC);

// 월수익 계산
$totalIncome = array_sum(array_column($incomeData, 'totalAmount'));
$totalExpense = array_sum(array_column($expenseData, 'totalAmount'));
$netIncome = $totalIncome - $totalExpense;

?>

<div class="container mt-5">
		<div class="card">
			<div class="card-header">
				<h5 class="text-center"><?=$title_message?></h5>
			</div>
			<div class="card-body">
				<div class="row mb-3">					                 
					<div class="d-flex justify-content-center align-items-center">					
						<select id="year" name="year" class="form-select w-auto me-2"  onchange="loadDetails()">
							<?php for ($i = date('Y'); $i >= 2024; $i--): ?>
								<option value="<?=$i?>" <?=($year == $i) ? 'selected' : ''?>><?=$i?>년</option>
							<?php endfor; ?>
						</select>                
					  
						<select id="startMonth" name="startMonth" class="form-select w-auto me-1"   onchange="loadDetails()">
							<?php for ($i = 1; $i <= 12; $i++): ?>
								<option value="<?=$i?>" <?=($startMonth == $i) ? 'selected' : ''?>><?=$i?>월</option>
							<?php endfor; ?>
						</select>
						~               &nbsp;  
						<select id="endMonth" name="endMonth" class="form-select w-auto me-5 "   onchange="loadDetails()">
							<?php for ($i = 1; $i <= 12; $i++): ?>
								<option value="<?=$i?>" <?=($endMonth == $i) ? 'selected' : ''?>><?=$i?>월</option>
							<?php endfor; ?>
						</select>
					</div>
				  </div>

            <div class="row mb-3">
                <div class="d-flex justify-content-center">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <button class="btn btn-outline-primary btn-sm me-1" onclick="loadSpecificMonth('<?=$i?>')">
                            <?=$i?>월
                        </button>
                    <?php endfor; ?>
                </div>
            </div>

            <table class="table table-hover" id="detailTable">
                <thead class="table-info">
                    <tr>
                        <th colspan="2" class="text-center">수입</th>
                        <th colspan="2" class="text-center">지출</th>
                    </tr>
                    <tr>
                        <th class="text-center">항목</th>
                        <th class="text-center">금액</th>
                        <th class="text-center">항목</th>
                        <th class="text-center">금액</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $maxRows = max(count($incomeData), count($expenseData));
                    for ($i = 0; $i < $maxRows; $i++):
                        $incomeContent = isset($incomeData[$i]) ? $incomeData[$i]['content'] : '';
                        $incomeAmount = isset($incomeData[$i]) ? number_format($incomeData[$i]['totalAmount']) : '';

                        $expenseContent = isset($expenseData[$i]) ? $expenseData[$i]['content'] : '';
                        $expenseAmount = isset($expenseData[$i]) ? number_format($expenseData[$i]['totalAmount']) : '';
                    ?>
                    <tr>
                        <td class="text-center"><?=$incomeContent?></td>
                        <td class="text-end text-primary"><?=$incomeAmount?></td>
                        <td class="text-center"><?=$expenseContent?></td>
                        <td class="text-end text-danger"><?=$expenseAmount?></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
				<tfoot class="table-secondary">
					<tr>
						<th class="text-end" > 수입 합계 &nbsp; </th>
						<th class="text-end text-primary"><?=number_format($totalIncome)?></th>					
						<th class="text-end"> 지출 합계 &nbsp; </th>
						<th class="text-end text-danger"><?=number_format($totalExpense)?></th>
					</tr>
					<tr>
						<th class="text-end" colspan="3"> 월수익 &nbsp; </th>
						<th class="text-end"><?=number_format($netIncome)?></th>
					</tr>
				</tfoot>

            </table>
        </div>
    </div>
</div>

</body>
</html>

<script>
// 페이지 로딩
$(document).ready(function(){    
	var loader = document.getElementById('loadingOverlay');
	loader.style.display = 'none';
});

// 기존 loadDetails 함수 유지
function loadDetails() {
    const year = document.getElementById('year').value;
    const startMonth = document.getElementById('startMonth').value;
    const endMonth = document.getElementById('endMonth').value;

    window.location.href = `detail.php?year=${year}&startMonth=${startMonth}&endMonth=${endMonth}`;
}

// 새로운 함수 추가: 특정 월 선택 시 호출
function loadSpecificMonth(month) {
    const year = document.getElementById('year').value;
    window.location.href = `detail.php?year=${year}&startMonth=${month}&endMonth=${month}`;
}
</script>

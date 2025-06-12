<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

if (!isset($_SESSION["level"]) || $_SESSION["level"] > 5) {
    sleep(1);
    header("Location:" . $WebSite . "login/login_form.php");
    exit;
} 
// 에러 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php';
$title_message = '일일 일보'; 
?>

<link href="css/style.css" rel="stylesheet">
<title> <?=$title_message?> </title>
<style>
/* 테이블에 테두리 추가 */
#myTable, #myTable th, #myTable td, #headTable th, #headTable td, #IncomeTable th, #IncomeTable td {
	border: 1px solid black;
	border-collapse: collapse;
}

/* 테이블 셀 패딩 조정 */
#myTable th, #myTable td {
	padding: 8px;
	text-align: center;
}

</style>
</head>
<body>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/myheader.php');
$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';  
$fromdate = isset($_REQUEST['fromdate']) ? $_REQUEST['fromdate'] : '';  
$todate = isset($_REQUEST['todate']) ? $_REQUEST['todate'] : '';  
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';  

// 현재 날짜
$currentDate = date("Y-m-d");

// fromdate 또는 todate가 빈 문자열이거나 null인 경우
if ($fromdate === "" || $fromdate === null || $todate === "" || $todate === null) {
    // 현재 월의 1일을 fromdate로 설정
    // $fromdate = date("Y-m-d");
	$fromdate = date("Y-m-d", strtotime("-1 month", strtotime($currentDate)));
    $todate = $currentDate;
    $Transtodate = $todate;
} else {
    $Transtodate = $todate;
}

function checkNull($strtmp) {
    return $strtmp !== null && trim($strtmp) !== '';
}

$tablename = 'account';

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

$inoutsep_select = isset($_REQUEST['inoutsep_select']) ? $_REQUEST['inoutsep_select'] : '';  
$content_select = isset($_REQUEST['content_select']) ? $_REQUEST['content_select'] : '';

$order = " ORDER BY registDate ASC, num ASC ";

$sql_conditions = [];
$sql_params = [];

if (checkNull($search)) {
    $sql_conditions[] = "searchtag LIKE :search";
    $sql_params[':search'] = "%$search%";
}

$sql_conditions[] = "registDate BETWEEN :fromdate AND :todate";
$sql_params[':fromdate'] = $fromdate;
$sql_params[':todate'] = $todate;

$sql_conditions[] = " (is_deleted = 0 or is_deleted IS NULL) ";

if (checkNull($inoutsep_select)) {
    $sql_conditions[] = "inoutsep = :inoutsep";
    $sql_params[':inoutsep'] = $inoutsep_select;
}

if (checkNull($content_select)) {
    $sql_conditions[] = "content = :content";
    $sql_params[':content'] = $content_select;
}

$sql = "SELECT * FROM " . $tablename . " WHERE " . implode(' AND ', $sql_conditions) . $order;

try {
    $stmh = $pdo->prepare($sql);
    foreach ($sql_params as $param => $value) {
        $stmh->bindValue($param, $value);
    }
    $stmh->execute();
    $total_row = $stmh->rowCount();
    
// 수입, 지출을 기반으로 초기 잔액 계산
$initialBalanceSql = "SELECT 
    SUM(CASE WHEN inoutsep = '수입' THEN REPLACE(amount, ',', '') ELSE 0 END) -
    SUM(CASE WHEN inoutsep = '지출' THEN REPLACE(amount, ',', '') ELSE 0 END) AS balance
    FROM $tablename 
    WHERE is_deleted = '0' AND registDate < :fromdate";
$initialBalanceStmh = $pdo->prepare($initialBalanceSql);
$initialBalanceStmh->bindParam(':fromdate', $fromdate);
$initialBalanceStmh->execute();
$initialBalance = $initialBalanceStmh->fetch(PDO::FETCH_ASSOC)['balance'];

$totalIncomeSql = "SELECT SUM(REPLACE(amount, ',', '')) AS totalIncome 
    FROM $tablename 
    WHERE is_deleted = '0' AND inoutsep = '수입' 
    AND registDate BETWEEN :fromdate AND :todate";
$totalIncomeStmh = $pdo->prepare($totalIncomeSql);
$totalIncomeStmh->bindParam(':fromdate', $fromdate);
$totalIncomeStmh->bindParam(':todate', $todate);
$totalIncomeStmh->execute();
$totalIncome = $totalIncomeStmh->fetch(PDO::FETCH_ASSOC)['totalIncome'];

$totalExpenseSql = "SELECT SUM(REPLACE(amount, ',', '')) AS totalExpense 
    FROM $tablename 
    WHERE is_deleted = '0' AND inoutsep = '지출' 
    AND registDate BETWEEN :fromdate AND :todate";
$totalExpenseStmh = $pdo->prepare($totalExpenseSql);
$totalExpenseStmh->bindParam(':fromdate', $fromdate);
$totalExpenseStmh->bindParam(':todate', $todate);
$totalExpenseStmh->execute();
$totalExpense = $totalExpenseStmh->fetch(PDO::FETCH_ASSOC)['totalExpense'];

} catch (PDOException $Exception) {
	print "오류: ".$Exception->getMessage();
}

$finalBalance = $initialBalance + $totalIncome - $totalExpense;

// Bankbook options
$bankbookOptions = [];
$bankbookFilePath = $_SERVER['DOCUMENT_ROOT'] . "/account/bankbook.txt";
if (file_exists($bankbookFilePath)) {
    $bankbookOptions = file($bankbookFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

// 전체 초기 잔액 계산
$initialBalanceSql = "SELECT 
    SUM(CASE WHEN inoutsep = '수입' THEN REPLACE(amount, ',', '') ELSE 0 END) -
    SUM(CASE WHEN inoutsep = '지출' THEN REPLACE(amount, ',', '') ELSE 0 END) AS balance
    FROM $tablename 
    WHERE is_deleted = '0' AND registDate < :fromdate";
$initialBalanceStmh = $pdo->prepare($initialBalanceSql);
$initialBalanceStmh->bindParam(':fromdate', $fromdate);
$initialBalanceStmh->execute();
$initialBalance = $initialBalanceStmh->fetch(PDO::FETCH_ASSOC)['balance'];

$dailyData = []; // 날짜별 데이터를 저장할 배열
$balance = $initialBalance; // 초기 잔액

// 날짜별로 데이터를 조회
$current = strtotime($todate);
$end = strtotime($fromdate);


$dailyData = []; // 날짜별 데이터를 저장할 배열
$balance = $initialBalance; // 초기 잔액

// 날짜별로 데이터를 조회
$current = strtotime($todate);
$end = strtotime($fromdate);

while ($current >= $end) {
    $currentDate = date("Y-m-d", $current);

    $dailySql = "SELECT * FROM $tablename 
                 WHERE registDate = :currentDate 
                 AND (is_deleted = 0 OR is_deleted IS NULL) 
                 $order";
    $dailyStmh = $pdo->prepare($dailySql);
    $dailyStmh->bindParam(':currentDate', $currentDate);
    $dailyStmh->execute();

    if ($dailyStmh->rowCount() > 0) {
        $dailyRows = $dailyStmh->fetchAll(PDO::FETCH_ASSOC);

        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($dailyRows as $row) {
            $amount = floatval(str_replace(',', '', $row['amount']));
            if ($row['inoutsep'] === '수입') {
                $balance += $amount;
                $totalIncome += $amount;
            } elseif ($row['inoutsep'] === '지출') {
                $balance -= $amount;
                $totalExpense += $amount;
            }
        }

        $dailyData[] = [
            'date' => $currentDate,
            'rows' => $dailyRows,
            'income' => $totalIncome,
            'expense' => $totalExpense,
            'balance' => $balance,
        ];
    }

    $current = strtotime("-1 day", $current); // 하루 전으로 이동
}

// 계좌정보 불러오기
$jsonFile = $_SERVER['DOCUMENT_ROOT'] . "/account/accoutlist.json";
$accounts = [];
if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $accounts = json_decode($jsonContent, true);
}

// 이미지 순서에 맞는 계좌번호 배열 (필요시 수정)
$accountOrder = [
    '796801-00-039630',
    '339-086768-01-011',
    '339-086768-01-028',
    '006090-18-199685',
    '796868-00-003858'
];

// 계좌번호 => 계좌정보 매핑
$accountMap = [];
foreach ($accounts as $acc) {
    $accountMap[$acc['number']] = $acc;
}

// 계좌별 집계
$accountSums = [];
foreach ($accountOrder as $number) {
    $acc = isset($accountMap[$number]) ? $accountMap[$number] : ['company'=>'', 'number'=>$number, 'memo'=>''];
    // 전월이월
    $prevSql = "SELECT 
        SUM(CASE WHEN inoutsep = '수입' THEN REPLACE(amount, ',', '') ELSE 0 END) -
        SUM(CASE WHEN inoutsep = '지출' THEN REPLACE(amount, ',', '') ELSE 0 END) AS balance
        FROM $tablename 
        WHERE (is_deleted = '0' OR is_deleted IS NULL) AND registDate < :fromdate AND bankbook = :bankbook";
    $prevStmh = $pdo->prepare($prevSql);
    $prevStmh->bindParam(':fromdate', $fromdate);
    $bankbookName = $acc['company'] . ' ' . $acc['number'] . ($acc['memo'] ? ' (' . $acc['memo'] . ')' : '');
    $prevStmh->bindParam(':bankbook', $bankbookName);
    $prevStmh->execute();
    $prev = $prevStmh->fetch(PDO::FETCH_ASSOC)['balance'] ?? 0;

    // 수입
    $incomeSql = "SELECT SUM(REPLACE(amount, ',', '')) AS totalIncome 
        FROM $tablename 
        WHERE (is_deleted = '0' OR is_deleted IS NULL) AND inoutsep = '수입' 
        AND registDate BETWEEN :fromdate AND :todate AND bankbook = :bankbook";
    $incomeStmh = $pdo->prepare($incomeSql);
    $incomeStmh->bindParam(':fromdate', $fromdate);
    $incomeStmh->bindParam(':todate', $todate);
    $incomeStmh->bindParam(':bankbook', $bankbookName);
    $incomeStmh->execute();
    $income = $incomeStmh->fetch(PDO::FETCH_ASSOC)['totalIncome'] ?? 0;

    // 지출
    $expenseSql = "SELECT SUM(REPLACE(amount, ',', '')) AS totalExpense 
        FROM $tablename 
        WHERE (is_deleted = '0' OR is_deleted IS NULL) AND inoutsep = '지출' 
        AND registDate BETWEEN :fromdate AND :todate AND bankbook = :bankbook";
    $expenseStmh = $pdo->prepare($expenseSql);
    $expenseStmh->bindParam(':fromdate', $fromdate);
    $expenseStmh->bindParam(':todate', $todate);
    $expenseStmh->bindParam(':bankbook', $bankbookName);
    $expenseStmh->execute();
    $expense = $expenseStmh->fetch(PDO::FETCH_ASSOC)['totalExpense'] ?? 0;

    // 잔액
    $balance = $prev + $income - $expense;

    $accountSums[] = [
        'display' => $acc['company'] . ': ' . $acc['number'],
        'prev'    => $prev,
        'income'  => $income,
        'expense' => $expense,
        'balance' => $balance,
        'memo'    => $acc['memo'],
    ];
}

?>

<form id="board_form" name="board_form" method="post" enctype="multipart/form-data">

<input type="hidden" id="mode" name="mode" value="<?= isset($mode) ? $mode : '' ?>">
<input type="hidden" id="num" name="num" value="<?= isset($num) ? $num : '' ?>">
<input type="hidden" id="tablename" name="tablename" value="<?= isset($tablename) ? $tablename : '' ?>">
 

    <div class="container-fluid">
        <!-- Modal -->
        <div id="myModal" class="modal">
            <div class="modal-content" style="width:800px;">
                <div class="modal-header">
                    <span class="modal-title"> <?=$title_message?> </span>
                    <span class="close">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="custom-card"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card justify-content-center text-center mt-5">
            <div class="card-header">
                <span class="text-center fs-5">  <?=$title_message?> 
					<button type="button" class="btn btn-dark btn-sm me-1" onclick='location.reload()'>  <i class="bi bi-arrow-clockwise"></i> </button>      
				</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center align-items-center mt-2">
						<span>
							▷ <?= $total_row ?> &nbsp;
						</span>			
						<input type="date" id="fromdate" name="fromdate" class="form-control" style="width:110px;" value="<?=$fromdate?>">  &nbsp;   ~ &nbsp;  
						<input type="date" id="todate" name="todate" class="form-control me-1" readonly style="width:110px;" value="<?=$todate?>">  &nbsp;										
						<input type="hidden" id="search" class="form-control" style="width:150px;" name="search" value="<?=$search?>" onKeyPress="if (event.keyCode==13){ enter(); }">					
					&nbsp;&nbsp;
					<button class="btn btn-outline-dark btn-sm" type="button" id="searchBtn"> <i class="bi bi-search"></i> </button> &nbsp;&nbsp;&nbsp;&nbsp;                
				</div>
            </div>			
<div class="container">
    <?php foreach ($dailyData as $daily): ?>
        <div class="row d-flex justify-content-center m-1 mb-2 mt-4">
            <?php
            $tmp = '일자: '; // 일자 텍스트
            $formatter = new IntlDateFormatter(
                'ko_KR',
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                null,
                null,
                'Y년 M월 d일 EEEE'
            );
            $formattedDate = $formatter->format(strtotime($daily['date']));
            ?>
            <div class="alert alert-primary fs-4">
                <?=$tmp?> <?=$formattedDate?> &nbsp; &nbsp; &nbsp; &nbsp; 작성자 : 정미영
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered" id="headTable">
                <thead class="table-secondary">
                    <tr>
                        <th class="text-center" style="width:200px;">구분</th>
                        <th class="text-center" style="width:150px;">전월이월</th>
                        <th class="text-center" style="width:150px;">수입</th>
                        <th class="text-center" style="width:150px;">지출</th>
                        <th class="text-center" style="width:150px;">잔액</th>
                        <th class="text-center" style="width:200px;">비고</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: #ffff00;">
                        <td class="text-center fw-bold">시제금 TOTAL</td>
                        <td class="text-center" colspan="4" style="background: #e2f0d9;">-</td>
                        <td></td>
                    </tr>
                    <tr style="background: #e2f0d9;">
                        <td class="text-center fw-bold">&lt;예금입출금 현황&gt;</td>
                        <td colspan="5"></td>
                    </tr>
                    <?php foreach ($accountSums as $sum): ?>
                    <tr>
                        <td class="text-start"><?= htmlspecialchars($sum['display']) ?></td>
                        <td class="text-end"><?= number_format($sum['prev']) ?></td>
                        <td class="text-end"><?= number_format($sum['income']) ?></td>
                        <td class="text-end"><?= number_format($sum['expense']) ?></td>
                        <td class="text-end"><?= number_format($sum['balance']) ?></td>
                        <td class="text-start"><?= htmlspecialchars($sum['memo']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="myTable">
                <thead class="table-secondary">
                    <tr>
                        <th class="text-center" style="width:200px;">&nbsp;</th>
                        <th class="text-center" style="width:150px;">&nbsp;</th>
                        <th class="text-center" style="width:300px;">출금내역</th>
                        <th class="text-center" style="width:150px;">금액</th>
                        <th class="text-center" style="width:200px;">비고</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daily['rows'] as $row): ?>
                        <?php if ($row['inoutsep'] === '지출'): ?>
                            <tr>
                                <td class="text-center fw-bold"> </td>
                                <td class="text-center fw-bold"> </td>
                                <td class="text-start"><?= htmlspecialchars($row['content']) ?></td>
                                <td class="text-end fw-bold"><?= number_format($row['amount']) ?></td>
                                <td class="text-center fw-bold"><?= htmlspecialchars($row['content_detail'] ?? '') ?> <?= htmlspecialchars($row['memo'] ?? '') ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-end fw-bold orangeBlueBold" colspan="1"> TOTAL </td>
                        <td class="text-end fw-bold orangeBlueBold" colspan="1"> - </td>
                        <td class="text-center fw-bold orangeBlueBold" colspan="1"> 출금 합계 </td>
                        <td class="text-end fw-bold orangeBlueBold" id="totalExpenseAmount"> <?= number_format($daily['expense']) ?> </td>
                    </tr>					
                </tfoot>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="IncomeTable">
                <thead class="table-secondary">
                    <tr>
                        <th class="text-center" style="width:300px;">입금내역</th>
                        <th class="text-center" style="width:150px;">금액</th>
                        <th class="text-center" style="width:200px;">비고</th>
                        <th class="text-center" style="width:200px;">&nbsp;</th>
                        <th class="text-center" style="width:150px;">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daily['rows'] as $row): ?>
                        <?php if ($row['inoutsep'] === '수입'): ?>
                            <tr>
                                <td class="text-start"><?= htmlspecialchars($row['content']) ?></td>
                                <td class="text-end fw-bold"><?= number_format($row['amount']) ?></td>
                                <td class="text-center fw-bold"><?= htmlspecialchars($row['content_detail'] ?? '') ?> <?= htmlspecialchars($row['memo'] ?? '') ?> </td>
                                <td class="text-center fw-bold"> </td>
                                <td class="text-center fw-bold"> </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
					<tr>
                        <td class="text-center fw-bold blueBlackBold" colspan="1"> TOTAL </td>
                        <td class="text-center fw-bold blueBlackBold" colspan="1"> 입금 합계 </td>
                        <td class="text-end fw-bold blueBlackBold" id="totalIncomeAmount"> <?= number_format($daily['income']) ?> </td>
                        <td class="text-end fw-bold blueBlackBold" colspan="1"> - </td>
                        <td class="text-end fw-bold"> </td>
                    </tr>          
						<tr>
							<td class="text-center fw-bold blueBlackBold text-white" colspan="1"> 입금 일계 </td>
							<td class="text-center fw-bold blueBlackBold text-white" colspan="1"> <?= number_format($daily['income'])?>   </td>							
							<td class="text-center fw-bold orangeBlueBold text-white" > 출금 일계  </td>
							<td class="text-end fw-bold orangeBlueBold text-white" > <?= number_format($daily['expense'])?>  </td>
							<td class="text-end fw-bold " >  </td>
							
						</tr>						
                </tfoot>
            </table>
        </div>
					
		
    <?php $finalBalance = $finalBalance - $daily['income'] + $daily['expense'] ?>
    <?php endforeach; ?>
</div>
			

	</div>
</div>
</form>

</body>
</html>

<script>
// 페이지 로딩
$(document).ready(function(){    
    var loader = document.getElementById('loadingOverlay');
	if(loader)
		loader.style.display = 'none';
});


function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

</script>

<script>

let isSaving = false;
var ajaxRequest = null;

document.addEventListener('DOMContentLoaded', function() {
   
    $("#searchBtn").on("click", function() {
        $("#board_form").submit();
    });

});

</script>

<script>

function enter() {
    $("#board_form").submit();
}

$(document).ready(function(){
	saveLogData('회계 일일 일보'); 
});

</script>


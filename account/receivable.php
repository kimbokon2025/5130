<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION["level"]) || $_SESSION["level"] > 5) {
    sleep(1);
    header("Location:" . $WebSite . "login/login_form.php");
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php';
// 첫 화면 표시 문구
$title_message = '미수금 현황';
?>

<link href="css/style.css" rel="stylesheet">
<title> <?=$title_message?> </title> 

<style>
/* 테이블에 테두리 추가 */
#myTable, #myTable th, #myTable td {
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
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/myheader.php'); ?>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/account/fetch_balance.php");

$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
$selectedYearMonth = isset($_REQUEST['yearMonth']) ? $_REQUEST['yearMonth'] : date("Ym");

// 현재 날짜가 속한 연월 계산
$currentYearMonth = date("Ym");

// 선택된 연월의 이전 달을 계산
$previousMonth = date("Ym", strtotime($selectedYearMonth . '01 -1 month'));
$previousMonthDisplay = date("m", strtotime($previousMonth . '01')) . "월";

// 기준년월에 따른 fromdate, todate 설정
$fromdate = $selectedYearMonth . '01';
$todate = date("Y-m-t", strtotime($fromdate));

// 검색 기간 표시를 위해 fromdate와 todate를 Y-m-d 형식으로 변환
$fromdate = date("Y-m-d", strtotime($fromdate));
$todate = date("Y-m-d", strtotime($todate));

$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

function checkNull($strtmp) {
    return $strtmp !== null && trim($strtmp) !== '';
}

$tablename = 'phonebook';

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

// 약속코멘트와 약속일을 가져온다.
$promisedate = [];
$memo = [];

$recordSql = "
    SELECT secondordnum, primisedate, comment 
    FROM recordlist
    WHERE (is_deleted IS NULL or is_deleted = 0) 
    ORDER BY num DESC
";

$recordStmt = $pdo->prepare($recordSql);
$recordStmt->execute();
$recordData = $recordStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($recordData as $row) {
    $secondordnum = $row['secondordnum'];
    $primisedate = $row['primisedate'];
    $comment = $row['comment'];

    // 가장 최신의 약속일과 메모만 저장
    if (!isset($promisedate[$secondordnum])) {
        $promisedate[$secondordnum] = $primisedate;
        $memo[$secondordnum] = $comment;
    }
}

// 이월 잔액을 동적으로 계산하기 위한 로직
$previousMonthFromDate = date("Y-m-01", strtotime($previousMonth . '01'));
$previousMonthToDate = date("Y-m-t", strtotime($previousMonth . '01'));

// echo '<pre>';
// print_r($previousMonthFromDate);
// echo ', ';
// print_r($previousMonthToDate);
// echo '</pre>';

// 전월 매출 및 수금 데이터 가져오기
$previousMonthSalesSql = "
    SELECT o.secondordnum, COALESCE(e.ET_total, 0) AS ET_total
    FROM output o
    LEFT JOIN output_extra e ON o.num = e.parent_num
    WHERE (o.outdate BETWEEN date('$previousMonthFromDate') AND date('$previousMonthToDate')) AND (o.is_deleted IS NULL or o.is_deleted = 0)
";
$previousMonthSalesStmt = $pdo->prepare($previousMonthSalesSql);
$previousMonthSalesStmt->execute();
$previousMonthSalesData = $previousMonthSalesStmt->fetchAll(PDO::FETCH_ASSOC);

$previousMonthSales = [];
foreach ($previousMonthSalesData as $row) {
    $secondordnum = $row['secondordnum'];
    $total_sales_prev = (float)$row['ET_total'];
    if (!isset($previousMonthSales[$secondordnum])) {
        $previousMonthSales[$secondordnum] = 0;
    }
    $previousMonthSales[$secondordnum] += round($total_sales_prev, 2); // ET_total은 부가세 포함
}

// 당월 매출 데이터 가져오기
$salesSql = "
    SELECT o.secondordnum, COALESCE(e.ET_total, 0) AS ET_total
    FROM output o
    LEFT JOIN output_extra e ON o.num = e.parent_num
    WHERE (o.outdate BETWEEN date('$fromdate') AND date('$todate')) AND (o.is_deleted IS NULL or o.is_deleted = 0)
";

$salesStmt = $pdo->prepare($salesSql);
$salesStmt->execute();
$salesData = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

$currentMonthSales = [];
foreach ($salesData as $row) {
    $secondordnum = $row['secondordnum'];
    $total_sales = (float)$row['ET_total'];
    if (!isset($currentMonthSales[$secondordnum])) {
        $currentMonthSales[$secondordnum] = 0;
    }
    $currentMonthSales[$secondordnum] += round($total_sales, 2);
}

// 전월까지의 매출 및 수금 데이터를 기반으로 이월 잔액을 계산
$initialBalances = [];
$lastMonthEnd = date("Y-m-t", strtotime($fromdate . " -1 month"));

$salesBeforeSql = "
    SELECT o.secondordnum, SUM(COALESCE(e.ET_total, 0)) AS total_sales
    FROM output o
    LEFT JOIN output_extra e ON o.num = e.parent_num
    WHERE o.outdate <= :lastMonthEnd AND (o.is_deleted IS NULL or o.is_deleted = 0)
    GROUP BY o.secondordnum
";

$paymentBeforeSql = "
    SELECT secondordnum, SUM(CAST(REPLACE(amount, ',', '') AS SIGNED)) AS total_payment
    FROM account
    WHERE registDate <= :lastMonthEnd AND (is_deleted IS NULL or is_deleted = 0) AND content = '거래처 수금'
    GROUP BY secondordnum
";

$salesBeforeStmt = $pdo->prepare($salesBeforeSql);
$salesBeforeStmt->execute([':lastMonthEnd' => $lastMonthEnd]);
$salesBeforeData = $salesBeforeStmt->fetchAll(PDO::FETCH_ASSOC);

// echo '<pre>';
// print_r($lastMonthEnd);
// echo '</pre>';

$paymentEnddate = date("Y-m-t", strtotime($todate));

$paymentBeforeStmt = $pdo->prepare($paymentBeforeSql);
$paymentBeforeStmt->execute([':lastMonthEnd' => $paymentEnddate]);
$paymentBeforeData = $paymentBeforeStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($salesBeforeData as $row) {
    $secondordnum = $row['secondordnum'];
    $total_sales_before = round((float)$row['total_sales'], 2);
    if (!isset($initialBalances[$secondordnum])) {
        $initialBalances[$secondordnum] = 0;
    }
    $initialBalances[$secondordnum] += $total_sales_before;
}

foreach ($paymentBeforeData as $row) {
    $secondordnum = $row['secondordnum'];
    $total_payment_before = (float)$row['total_payment'];
    if (!isset($initialBalances[$secondordnum])) {
        $initialBalances[$secondordnum] = 0;
    }
    $initialBalances[$secondordnum] -= $total_payment_before;
}

// 거래처별 잔액을 계산
$balances = fetch_balances($DB, $fromdate, $todate);

// 모든 거래처 목록을 생성 (이월 잔액, 전월 매출, 당월 매출, 잔고)
$allResults = array_unique(array_merge(array_keys($initialBalances), array_keys($currentMonthSales), array_keys($previousMonthSales), array_keys($balances)));

// ksort($allResults);

// echo '<pre>';
// print_r($allResults[23]);
// echo '</pre>';	


// 이번달 수금 금액 계산
$currentMonthPayments = [];
$currentMonthStart = date("Y-m-01", strtotime($todate));
$currentMonthEnd = date("Y-m-t", strtotime($todate));

$currentPaymentSql = "
    SELECT secondordnum, SUM(CAST(REPLACE(amount, ',', '') AS SIGNED)) AS total_payment 
    FROM account
    WHERE registDate BETWEEN :currentMonthStart AND :currentMonthEnd 
    AND (is_deleted IS NULL or is_deleted = 0) AND content = '거래처 수금'
    GROUP BY secondordnum
";

$currentPaymentStmt = $pdo->prepare($currentPaymentSql);
$currentPaymentStmt->execute([
    ':currentMonthStart' => $currentMonthStart,
    ':currentMonthEnd' => $currentMonthEnd
]);
$currentPaymentData = $currentPaymentStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($currentPaymentData as $row) {
    $secondordnum = $row['secondordnum'];
    $total_payment_current = (float)$row['total_payment'];

    if (!isset($currentMonthPayments[$secondordnum])) {
        $currentMonthPayments[$secondordnum] = 0;
    }

    $currentMonthPayments[$secondordnum] += $total_payment_current;
}



// 미수금 계산
$receivables = []; // 미수금을 저장할 배열
$vendorNames = []; // 거래처 이름을 저장할 배열
$paydates = []; // 결제일을 저장할 배열

// 거래처 이름을 저장하는 배열 초기화
$vendorNames = [];
$paydates = [];
foreach ($allResults as $secondordnum) {
    // 잔고, 전월매출, 당월매출, 이월 잔액 및 미수금을 계산    
    $previousMonthSale = isset($previousMonthSales[$secondordnum]) ? $previousMonthSales[$secondordnum] : 0;
    $currentMonthSale = isset($currentMonthSales[$secondordnum]) ? $currentMonthSales[$secondordnum] : 0;
    $balance = isset($balances[$secondordnum]) ? $balances[$secondordnum] : 0;
    $currentMonthPayment = isset($currentMonthPayments[$secondordnum]) ? $currentMonthPayments[$secondordnum] : 0;
	
    // 최종 잔고 계산 (이번달 수금액을 차감)
    // $finalBalance = $balance - $currentMonthPayment; // 이코드 보류함 250414 수정 이중으로 차감됨
    $finalBalance = $balance ;

    if ($finalBalance > 0) {
        $receivableAmount = $finalBalance - $currentMonthSale - $previousMonthSale;
		if($receivableAmount > 0)
			$receivables[$secondordnum] = $receivableAmount;
		else
			$receivables[$secondordnum] = 0;
    } else {
        $receivables[$secondordnum] = 0;
    }

    // 거래처 정보 가져오기
    $dueDateSql = "SELECT paydate, vendor_name, note FROM {$DB}.phonebook  
                   WHERE secondordnum = '$secondordnum' AND (is_deleted IS NULL or is_deleted = 0)";
    $dueDateStmt = $pdo->prepare($dueDateSql);
    $dueDateStmt->execute();
    $dueDateRow = $dueDateStmt->fetch(PDO::FETCH_ASSOC);

    // 거래처 이름과 결제일을 저장
    $vendorNames[$secondordnum] = $dueDateRow['vendor_name'] ?? '';        
    $paydates[$secondordnum] = $dueDateRow['paydate'] ?? '';
}


// 거래처 이름으로 정렬
usort($allResults, function($a, $b) use ($vendorNames) {
    return strcmp($vendorNames[$a], $vendorNames[$b]);
});


// echo '<pre>';
// print_r($receivables);
// echo '</pre>';


?>

<!-- 리스트 모달 창 -->
<div class="modal fade" id="recordListModal" tabindex="-1" aria-labelledby="recordListModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordListModalLabel">약속일 및 통화내역</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                 <ul id="recordList" class="list-group" style="max-height: 400px; overflow-y: auto;">
                    <!-- 여기에서 기록 리스트가 동적으로 추가됩니다. -->
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="newRecordButton">신규등록</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
            </div>
        </div>
    </div>
</div>

<!-- 수정/신규등록 모달 창 -->
<div class="modal fade" id="recordEditModal" tabindex="-1" aria-labelledby="recordEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordEditModalLabel">결재 약속일 및 통화내역 수정</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">    
				<form id="recordForm">              
                    <input type="hidden" id="recordNum" name="recordNum">
					<div class="mb-3">
						<label for="record" class="form-label">기록일시</label>
						<input type="datetime-local" class="form-control" style="width:200px;" id="recordTime" name="recordTime">
					</div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">통화내용</label>
                        <textarea class="form-control" id="comment" name="comment"  style="height:150px;"  ></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="primisedate" class="form-label">결재 약속일</label>
                        <input type="date" class="form-control"  style="width:110px;"  id="primisedate" name="primisedate" >
                        <input type="hidden"  id="secondordID" name="secondordID" >
                    </div>
                    <button type="submit" class="btn btn-primary">저장</button>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
					</form>
            </div>
        </div>
    </div>
</div>

<form id="board_form" name="board_form" method="post" enctype="multipart/form-data">    
         
<div class="container mb-5"> 
    <input type="hidden" id="mode" name="mode" value="<?=$mode?>">             
    <input type="hidden" id="num" name="num"> 
    <input type="hidden" id="tablename" name="tablename" value="<?=$tablename?>">                 
    <input type="hidden" id="header" name="header" value="<?=$header?>">                 
    <input type="hidden" id="secondordnum" name="secondordnum" value="<?=$secondordnum?>">                 
    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
	<span class="text-center fs-5 me-4"><?=$title_message?></span>    
        <button type="button" class="btn btn-dark btn-sm me-1" onclick='location.reload();'> 
            <i class="bi bi-arrow-clockwise"></i>
        </button>        
    </div>     
	
    <!-- <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
		<span class="badge bg-danger fs-6 me-4"> 미수금 현황은 <거래처원장> '차기월 이월금확정' 처리 완료 후 계산됩니다. </span>    
    </div>     
	-->
	
    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
			<button type="button" class="btn btn-dark btn-sm me-2" onclick="location.href='../getmoney/list.php?header=header'"><i class="bi bi-journal-x"></i> 수금 </button>    
            <button type="button" class="btn btn-dark btn-sm me-1" onclick="location.href='../account/S_transaction.php?header=header'"> <i class="bi bi-file-earmark-ruled"></i> 거래원장</button>
            <button type="button" class="btn btn-dark btn-sm me-1" onclick="openMonthlyBalancePopup()"> <i class="bi bi-file-earmark-ruled"></i> 이월잔액</button>     
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="location.href='../account/month_sales.php?header=header'"> <i class="bi bi-file-earmark-ruled"></i> 판매일괄회계</button>
    </div>  	
        
<!--		
     <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
        <span class="badge bg-danger fs-3 me-3"> 현재개발중입니다. (미완성) </span>                    
    </div>                  
    -->
    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">                           
          <span class="text-primary fw-bold ms-2 me-3"> * 잔고(거래원장 잔액) - 당월매출 - 전월매출 = 미수금 (계산서 발행 후 결재일 넘기면 미수금)</span>
    </div> 

<div class="card">                               
<div class="card-body">    
	
    <div class="table-responsive">     
     <table class="table table-hover" id="myTable">        
            <thead class="table-danger"> 
                 <th class="text-center ">번호</th>                 
                 <th class="text-center w150px">거래처명</th>                 
                 <th class="text-center ">결제일</th>
                 <th class="text-center ">미수금</th>
                 <th class="text-center ">전월매출</th>                                 
                 <th class="text-center ">당월매출</th>                 
                 <th class="text-center ">잔고</th>
                 <th class="text-center ">결제약속일</th>
                 <th class="text-center ">적요</th>
                 <th class="text-center ">원장</th>
				 <?php if($user_id == 'pro') 
					 print '<th class="text-center w40px">Code</th>';
				 ?>				 
                 <th style="display:none;" ></th>
            </thead>
            <tbody>                 
            <?php  
            try {	
                $start_num = 1;                
                foreach ($allResults as $secondordnum) {
                     if (isset($receivables[$secondordnum]) && intval($receivables[$secondordnum]) > 0  ) {
                    ?>
			
			<tr data-secondordnum="<?=$secondordnum ?>" data-secondord="<?= htmlspecialchars($vendorNames[$secondordnum])?>" 
				data-promisedate="<?= htmlspecialchars($promisedate[$secondordnum] ?? '') ?>" data-memo="<?= htmlspecialchars($memo[$secondordnum] ?? '') ?>">

				<td class="text-center choice"><?= $start_num ?></td>
				
				<td class="text-start text-primary choice" 
					data-secondord="<?= htmlspecialchars($vendorNames[$secondordnum])?>" 
					onclick="redirectToView(this, '<?= $secondordnum ?>')">
					<?= htmlspecialchars($vendorNames[$secondordnum]) ?>
				</td>

				<td class="text-center text-primary fw-bold choice" 
					onclick="redirectToView(this, '<?= $secondordnum ?>')">
					<?= htmlspecialchars($paydates[$secondordnum]) ?>
				</td>
				
				<td class="text-end text-danger fw-bold choice" 
					onclick="redirectToView(this, '<?= $secondordnum ?>')">
					<?= number_format($receivables[$secondordnum]) ?>
				</td>
				
				<td class="text-end text-secondary fw-bold choice" 
					onclick="redirectToView(this, '<?= $secondordnum ?>')">
					<?= number_format(isset($previousMonthSales[$secondordnum]) ? $previousMonthSales[$secondordnum] : 0) ?>
				</td>
				
				<td class="text-end fw-bold choice" 
					onclick="redirectToView(this, '<?= $secondordnum ?>')">
					<?= number_format(isset($currentMonthSales[$secondordnum]) ? $currentMonthSales[$secondordnum] : 0) ?>
				</td>
				
				<td class="text-end fw-bold choice" onclick="redirectToView(this, '<?= $secondordnum ?>')">
					<?= number_format(isset($balances[$secondordnum]) ? $balances[$secondordnum] : 0) ?>
				</td>
				
				<td class="text-center choice" data-promisedate onclick="redirectToView(this, '<?= $secondordnum ?>')" >
					<?= (isset($promisedate[$secondordnum]) && $promisedate[$secondordnum] !== "0000-00-00") ? htmlspecialchars($promisedate[$secondordnum]) : '' ?>
				</td>
												
				<td class="text-start choice" onclick="redirectToView(this, '<?= $secondordnum ?>')" data-memo title="<?= htmlspecialchars($memo[$secondordnum] ?? '') ?>">
					<?php
					if (isset($memo[$secondordnum])) {
						$memoText = htmlspecialchars($memo[$secondordnum]);
						// 한글 처리를 위해 mb_strlen과 mb_substr 사용
						if (mb_strlen($memoText, 'UTF-8') > 40) {
							echo mb_substr($memoText, 0, 40, 'UTF-8') . '...';
						} else {
							echo $memoText;
						}
					} else {
						echo '';
					}
					?>
				</td>
				
				<td class="text-center" onclick="ViewtoCustomerSheet('<?= $secondordnum ?>')">
					<span class="badge bg-secondary"> 보기 </span>
				</td>
				
				<?php if($user_id == 'pro') 
					echo '<td class="text-center w50px"> ' . $secondordnum . ' </td>';
				?>                        
				
				<td style="display:none;"><?= $secondordnum ?></td>                        
			</tr>

                    <?php
                    $start_num++;
                    }
                }
            } catch (PDOException $Exception) {
                print "오류: ".$Exception->getMessage();
            }
            ?>       
          </tbody>
          <tfoot class="table-secondary">
                <tr>
                    <th class="text-end w80px" colspan="3"> 합계 &nbsp; </th>                
					<th class="text-end">
						<?= number_format(array_sum(array_map(function($value) {
							return floatval(str_replace(',', '', $value));
						}, $receivables))) ?>
					</th>

                    <th class="text-end"><?= number_format(array_sum($previousMonthSales)) ?></th>                
                    <th class="text-end"><?= number_format(array_sum($currentMonthSales)) ?></th>
                    <th class="text-end"><?= number_format(array_sum($balances)) ?></th>
                    <th class="text-end ">  </th>
                    <th class="text-end ">  </th>
                    <th class="text-end ">  </th>
					<?php if($user_id == 'pro') 
						echo '<th class="text-center w50px"> </th>';
					?>						
                </tr>
            </tfoot>
         </table>
        </div>
    </div>
    </div>
    </div>
</form>

<!-- 페이지로딩 -->
<script>
$(document).ready(function(){    
	var loader = document.getElementById('loadingOverlay');
	if(loader)
		loader.style.display = 'none';
});

function submitForm() {
	$('#board_form').submit();
}


var dataTable; // DataTables 인스턴스 전역 변수
var bookpageNumber; // 현재 페이지 번호 저장을 위한 전역 변수

$(document).ready(function() {            
    dataTable = $('#myTable').DataTable({
        "paging": true,
        "ordering": true,
        "searching": true,
        "pageLength": 100,
        "lengthMenu": [100, 200, 500, 1000],
        "language": {
            "lengthMenu": "Show _MENU_ entries",
            "search": "Live Search:"
        },
        // "order": [[3, 'desc']], // 잔액기준 내림차순 정렬
        "dom": 't<"bottom"ip>', // search 창과 lengthMenu 숨기기
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            totalInitialReceivable = api.column(4).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            totalSalesAmount = api.column(5).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);            
            totalBalanceDue = api.column(6).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

            $(api.column(4).footer()).html(numberWithCommas(totalInitialReceivable));
            $(api.column(5).footer()).html(numberWithCommas(totalSalesAmount));            
            $(api.column(6).footer()).html(numberWithCommas(totalBalanceDue));
        }
    });

    var savedPageNumber = getCookie('bookpageNumber');
    if (savedPageNumber) {
        dataTable.page(parseInt(savedPageNumber) - 1).draw(false);
    }

    dataTable.on('page.dt', function() {
        var bookpageNumber = dataTable.page.info().page + 1;
        setCookie('bookpageNumber', bookpageNumber, 10);
    });

    $('#myTable_length select').on('change', function() {
        var selectedValue = $(this).val();
        dataTable.page.len(selectedValue).draw();

        savedPageNumber = getCookie('bookpageNumber');
        if (savedPageNumber) {
            dataTable.page(parseInt(savedPageNumber) - 1).draw(false);
        }
    });
});

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

var selectedSecondordnum = null;
var selectedVendorName = null;

$(document).ready(function() {
    // 특정 열(td)에서만 클릭 이벤트를 설정합니다.
    $('#myTable').on('click', '.choice', function() {
        var trElement = $(this).closest('tr');

        // .attr()을 사용하여 속성 값을 가져옵니다.
        selectedSecondordnum = trElement.attr('data-secondordnum');
        selectedVendorName = trElement.attr('data-secondord');

        // 데이터가 잘 가져와지는지 콘솔에 출력해봅니다.
        console.log("Selected Vendor Name:", selectedVendorName);
        console.log("Selected Secondordnum:", selectedSecondordnum);

        if (!selectedSecondordnum || !selectedVendorName) {
            console.error("secondordnum 또는 vendorName이 제대로 설정되지 않았습니다.");
        }
    });

    // "새 기록" 버튼이 클릭되면 저장된 데이터를 사용합니다.
    $('#newRecordButton').on('click', function() {
        console.log("Selected Vendor Name:", selectedVendorName);
        console.log("Selected Secondordnum:", selectedSecondordnum);        
        if (selectedVendorName && selectedSecondordnum) {
            openNewRecordModal(selectedVendorName, selectedSecondordnum);
        } else {
            alert('먼저 거래처를 선택하세요.');
        }
    });
});

function redirectToView(element, secondordnum) {	
    var selectedSecondordnum = secondordnum;
    // 클릭된 <td> 요소의 부모 <tr>에서 data-secondord 속성을 가져옵니다.
    var vendorName = $(element).closest('tr').data('secondord');
    
    // 기존에 같은 div가 있다면 제거    
	$('#recordListModal .modal-header').empty();
    $('#recordListModal .vendor-name').empty();

    // 거래처명을 표시할 div 요소 생성
    var companyNameDiv = $('<div>', {
        class: 'd-flex p-1 fs-4 m-1 mb-1 justify-content-center align-items-center vendor-name',
        text: vendorName
    });
	
	selectedVendorName= vendorName;

    // modal header에 거래처명 추가 (기존 h5 태그 위에)
    $('#recordListModal .modal-header').prepend('(' + selectedSecondordnum + ')');
    $('#recordListModal .modal-header').prepend(companyNameDiv);

    $.ajax({
        url: 'get_records.php',
        type: 'GET',
        data: { secondordnum: selectedSecondordnum },
        success: function(data) {
            try {
                // JSON.parse 제거, data는 이미 JSON 객체임
                var records = Array.isArray(data) ? data : [];

                var recordList = $('#recordList');
                recordList.empty();
							
			if (records.length > 0) {
				records.forEach(function(record) {
					if (!record.primisedate || record.primisedate === '0000-00-00') {
						record.primisedate = '';
					}

					// comment에 줄바꿈이나 특수 문자가 있을 경우 안전하게 변환
					let safeComment = JSON.stringify(record.comment)
						.replace(/\n/g, '\\n')
						.replace(/\r/g, '\\r')
						.replace(/"/g, '&quot;')  // 큰따옴표 이스케이프
						.replace(/'/g, '&#39;');  // 작은따옴표 이스케이프

					recordList.append(
						'<li class="list-group-item d-flex justify-content-between align-items-center">' +
						record.recordTime + ' - ' + safeComment + ' - ' + record.primisedate +
						'<button type="button" class="btn btn-danger btn-sm" onclick="deleteRecord(' + record.num + ')">삭제</button>' +
						'<button type="button" class="btn btn-secondary btn-sm ms-2" onclick="editRecord(' + record.num + ')">수정</button>' +
						'</li>'
					);

				});
			} else {
				recordList.append(
					'<li class="list-group-item text-center">' +
					'기록이 없습니다.' +
					'</li>'
				);
			}


                $('#recordListModal').modal('show');
            } catch (e) {
                console.error("Error processing data: ", e);
            }
        },
        error: function(error) {
            alert('오류 발생: ' + error.responseText);
        }
    });
}

function openNewRecordModal(vendorName, secondordNumber) {
    var today = new Date();

    // UTC 시간을 기반으로 한국 시간을 계산합니다.
    var year = today.getUTCFullYear();
    var month = String(today.getUTCMonth() + 1).padStart(2, '0'); // 월은 0부터 시작하므로 1을 더합니다.
    var day = String(today.getUTCDate()).padStart(2, '0');
    var hours = String(today.getUTCHours() + 9).padStart(2, '0'); // UTC 시간을 기준으로 9시간을 더합니다.
    var minutes = String(today.getUTCMinutes()).padStart(2, '0');

    if (hours >= 24) {
        hours = String(hours - 24).padStart(2, '0');
        day = String(parseInt(day) + 1).padStart(2, '0');
    }

    var formattedDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;

    // 기존에 같은 div가 있다면 제거
    $('#recordEditModal .modal-header').empty();
    $('#recordEditModal .vendor-name').empty();

    // 거래처명을 표시할 div 요소 생성
    var companyNameDiv = $('<div>', {
        class: 'd-flex p-1 fs-4 m-1 mb-1 justify-content-center align-items-center vendor-name',
        text: vendorName // 전달된 거래처 이름을 사용
    });

    // modal header에 거래처명 추가    
    $('#recordEditModal .modal-header').prepend('(' + secondordNumber + ') ');        
    $('#recordEditModal .modal-header').prepend(companyNameDiv);

    // Set form values
    $('#recordNum').val(0);
    // $('#primisedate').val(formattedDateTime.substring(0, 10)); // 오늘 날짜를 기본값으로 설정
    $('#primisedate').val(null); // 오늘 날짜를 기본값으로 설정
    $('#recordTime').val(formattedDateTime); // 현재 시간을 기본값으로 설정
    $('#comment').val('');
    $('#secondordID').val(secondordNumber);

    // Show the modal
    $('#recordEditModal').modal('show');
}

function editRecord(num) {	
    // Ajax 요청으로 고유번호를 이용해 해당 레코드 데이터를 서버에서 가져온다.
    $.ajax({
        url: 'get_record_by_id.php', // 레코드 데이터를 가져오는 서버측 PHP 파일
        type: 'POST',
        data: { recordNum: num },  // 고유번호 전달
       success: function(response) {
    // JSON 데이터를 파싱하여 record 객체로 변환
    let record;
    try {
        record = JSON.parse(response);  // response가 JSON 문자열이면 파싱
    } catch (e) {
        record = response;  // 이미 객체일 경우 그대로 사용
    }

    // 폼 초기화
    $('#recordForm')[0].reset();
    console.log(record);

    if (!record) {
        alert('레코드 데이터를 불러오는 데 실패했습니다.');
        return;
    }

    // 기존에 같은 div가 있다면 제거                        
    $('#recordNum').val(record.num);
    $('#comment').val(decodeURIComponent(record.comment));  // 한글 인코딩 처리
    $('#secondordID').val(record.secondordnum);

    // primisedate가 '0000-00-00'일 경우 공백으로 설정
    if (record.primisedate === '0000-00-00') {
        $('#primisedate').val('');
    } else {
        $('#primisedate').val(record.primisedate);
    }

    // 거래처명을 표시할 div 요소 생성
    var companyNameDiv = $('<div>', {
        class: 'd-flex p-1 fs-4 m-1 mb-1 justify-content-center align-items-center vendor-name',
        text: record.secondordnum
    });

    // modal header에 거래처명 추가
    $('#recordEditModal .modal-header').prepend('(' + record.secondordnum + ') ');
    $('#recordEditModal .modal-header').prepend(companyNameDiv);

    // recordTime이 없을 경우 현재 시간으로 설정
    if (!record.recordTime) {
        var today = new Date();
        today.setHours(today.getHours() + 9);  // UTC+9 시간 설정
        var formattedDateTime = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}T${String(today.getHours()).padStart(2, '0')}:${String(today.getMinutes()).padStart(2, '0')}`;
        $('#recordTime').val(formattedDateTime);
    } else {
        $('#recordTime').val(record.recordTime);
    }

    // 모달 창을 열어 수정 폼을 표시
    $('#recordEditModal').modal('show');
},

        error: function() {
            alert('레코드 데이터를 불러오는 데 오류가 발생했습니다.');
        }
    });
}

$('#recordForm').submit(function(event) {
    event.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: 'save_record.php',
        type: 'POST',
        data: formData,
        success: function(data) {
			
			console.log(data);
           $('#recordEditModal').modal('hide');

            // 테이블에서 해당 행을 찾고 약속일과 메모를 업데이트
            var row = $('tr[data-secondordnum="' + data.secondordnum + '"]');
            if (row.length > 0) {
                row.find('td[data-promisedate]').text(data.primisedate);
                row.find('td[data-memo]').text(data.comment);
                row.find('td[data-recordTime]').text(data.recordTime);
            }

            // 모달 닫은 후 리로드
              $('#recordListModal').modal('hide');
        },
        error: function(error) {
            alert('오류 발생: ' + error.responseText);
        }
    });
});

function deleteRecord(num) {
    if (confirm('정말로 삭제하시겠습니까?')) {
        $.ajax({
            url: 'delete_record.php',
            type: 'POST',
            data: { num: num },
            success: function(data) {
                // 삭제된 기록의 정보가 반환됩니다.
                if (data && data.secondordnum) {
                    // 테이블에서 해당 행을 찾아서 갱신합니다.
                    var row = $('tr[data-secondordnum="' + data.secondordnum + '"]');
                    if (row.length > 0) {
                        // 약속일과 메모를 비워줍니다.
                        row.find('td[data-promisedate]').text('');
                        row.find('td[data-memo]').text('');
                        row.find('td[data-recordTime]').text('');
                    }
                }
				
			// 폼 초기화
			$('#recordForm')[0].reset();

                // 모달의 리스트를 다시 로드합니다.
                redirectToView(this, data.secondordnum);
            },
            error: function(error) {
                alert('오류 발생: ' + error.responseText);
            }
        });
    }
}

function openMonthlyBalancePopup() {
    var url = "monthly_balance_popup.php";
    customPopup(url, '거래원장', 1000, 850);   
}


function ViewtoCustomerSheet(num) {    
    var fromdate = '2024-03-01'; // 고정된 시작 날짜
    var today = new Date(); 
    var year = today.getFullYear();
    var month = String(today.getMonth() + 1).padStart(2, '0');
    var day = String(today.getDate()).padStart(2, '0');
    var todate = year + '-' + month + '-' + day; // 현재 날짜
    
    var url = "customer_sheet.php?num=" + num + "&fromdate=" + fromdate + "&todate=" + todate;
    customPopup(url, '거래원장', 1000, 850);             
}


$(document).ready(function(){
	saveLogData('미수금 현황'); 
});

</script>
</body>
</html>


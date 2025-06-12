<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

if (!isset($_SESSION["level"]) || $_SESSION["level"] > 5) {
    sleep(1);
    header("Location:" . $WebSite . "login/login_form.php"); 
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php';

// 첫 화면 표시 문구
$title_message = '거래처 원장(VAT 포함)'; 
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

$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';  
$fromdate = isset($_REQUEST['fromdate']) ? $_REQUEST['fromdate'] : '';  
$todate = isset($_REQUEST['todate']) ? $_REQUEST['todate'] : '';  
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';  

// 현재 날짜
$currentDate = date("Y-m-d");

// fromdate 또는 todate가 빈 문자열이거나 null인 경우
if ($fromdate === "" || $fromdate === null || $todate === "" || $todate === null) {
    // 현재 월의 1일을 fromdate로 설정
    $fromdate = date("Y-m-01");
    $todate = $currentDate;
    $Transtodate = $todate;
} else {
    $Transtodate = $todate;
}

function checkNull($strtmp) {
    return $strtmp !== null && trim($strtmp) !== '';
}

$tablename = 'phonebook';

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

// 이전 잔액 조회
$lastMonthDate = date("Y-m-t", strtotime($fromdate . " -1 month"));
$initialBalances = [];

$balanceSql = "
    SELECT secondordnum, balance 
    FROM monthly_balances
    WHERE closure_date = :lastMonthDate
";

$balanceStmt = $pdo->prepare($balanceSql);
$balanceStmt->execute([':lastMonthDate' => $lastMonthDate]);
$balanceData = $balanceStmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($balanceData as $row) {
    $secondordnum = $row['secondordnum'];
    $balance = (float)str_replace(',', '', $row['balance']);

    if (!isset($initialBalances[$secondordnum])) {
        $initialBalances[$secondordnum] = 0;
    }
    $initialBalances[$secondordnum] += $balance;
}

// 매출이 발생한 거래처 필터링 및 매출액 계산
$salesSql = "
    SELECT secondordnum, orderlist, accessorieslist, controllerlist, fabriclist, dcadd 
    FROM motor
    WHERE (deadline BETWEEN date('$fromdate') AND date('$Transtodate')) AND is_deleted IS NULL 
";
$salesStmt = $pdo->prepare($salesSql);
$salesStmt->execute();
$salesData = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

$salesResults = [];
foreach ($salesData as $row) {
    $secondordnum = $row['secondordnum'];
    $orderlist = json_decode($row['orderlist'], true);
    $accessorieslist = json_decode($row['accessorieslist'], true);
    $controllerlist = json_decode($row['controllerlist'], true);
    $fabriclist = json_decode($row['fabriclist'], true);

    // 추가할인부분 빼기
    $dcadd = (float)str_replace(',', '', trim($row['dcadd'])) ; // 숫자 외 문자를 제거하고 float로 변환

    $total_sales = 0;
    if (is_array($orderlist)) {
        foreach ($orderlist as $item) {
            $total_sales += isset($item['col12']) ? (float)str_replace(',', '', trim($item['col12'])) : 0;
        }
    }
    if (is_array($accessorieslist)) {
        foreach ($accessorieslist as $item) {
            $total_sales += isset($item['col4']) ? (float)str_replace(',', '', trim($item['col4'])) : 0;
        }
    }
    if (is_array($controllerlist)) {
        foreach ($controllerlist as $item) {
            $total_sales += isset($item['col7']) ? (float)str_replace(',', '', trim($item['col7'])) : 0;
        }
    }
    if (is_array($fabriclist)) {
        foreach ($fabriclist as $item) {
            $total_sales += isset($item['col9']) ? (float)str_replace(',', '', trim($item['col9'])) : 0;
        }
    }

    if (!isset($salesResults[$secondordnum])) {
        $salesResults[$secondordnum] = 0;
    }
    $salesResults[$secondordnum] += ($total_sales - $dcadd) ;
}

// 모든 거래처 목록을 생성 (매출, 기초채권)
$allResults = array_unique(array_merge(array_keys($salesResults), array_keys($initialBalances)));

// rsort($allResults); // 거래처 코드 기준 정렬

// // 매출 금액 기준으로 정렬
// usort($allResults, function($a, $b) use ($salesResults) {
    // $salesA = isset($salesResults[$a]) ? $salesResults[$a] : 0;
    // $salesB = isset($salesResults[$b]) ? $salesResults[$b] : 0;
    // return $salesB - $salesA;
// });

// 매출 금액 기준으로 역순으로 정렬
usort($allResults, function($a, $b) use ($salesResults) {
    $salesA = isset($salesResults[$a]) ? $salesResults[$a] : 0;
    $salesB = isset($salesResults[$b]) ? $salesResults[$b] : 0;
    return $salesA - $salesB;
});

// echo '<pre>';
// print_r($allResults);
// echo '</pre>';

// 합계를 저장할 변수들
$totalInitialReceivable = 0;
$totalSalesAmount = 0;
$totalPaymentAmount = 0;
$totalBalanceDue = 0;

try {	
    $start_num = 1;                
    foreach ($allResults as $secondordnum) {
        // 이월 잔액 설정
        $initialReceivable = isset($initialBalances[$secondordnum]) ? $initialBalances[$secondordnum] : 0;

        // 수금 내역 가져오기
        $paymentSql = "SELECT SUM(CAST(REPLACE(payment, ',', '') AS UNSIGNED)) as total_payment 
                       FROM ".$DB.".getmoney 
                       WHERE secondordnum = '$secondordnum'
                       AND registedate BETWEEN date('$fromdate') AND date('$Transtodate') AND is_deleted IS NULL";

        $paymentStmt = $pdo->prepare($paymentSql);
        $paymentStmt->execute();
        $paymentData = $paymentStmt->fetch(PDO::FETCH_ASSOC);
        $total_payment = isset($paymentData['total_payment']) ? (int)str_replace(',', '', $paymentData['total_payment']) : 0;

        $total_sales = isset($salesResults[$secondordnum]) ? $salesResults[$secondordnum] : 0;

        // 조건: 기초채권이 있거나 매출이 있는 경우만 표시
        if ($initialReceivable != 0 || $total_sales != 0 ) {		  
            $sql = "SELECT * FROM ".$DB.".".$tablename." 
                    WHERE secondordnum = '$secondordnum'
                      AND is_deleted IS NULL 
                      AND represent='아이디부여'";
            if (checkNull($search)) {
                $sql .= " AND (vendor_name LIKE '%$search%' OR representative_name LIKE '%$search%' OR manager_name LIKE '%$search%')";
            }

            $stmh = $pdo->query($sql); 
            while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
                include $_SERVER['DOCUMENT_ROOT'] . '/phonebook/_row.php';        
                if (empty($contact_info))
                    $contact_info = $phone;

                if (intval($secondordnum) > 0)
                    $savenum = $secondordnum;
                else
                    $savenum = $num;

                // VAT를 포함한 총 매출액
                $vat = $total_sales * 0.1;
                $total_amount = $total_sales + $vat;

                // 잔액 계산
                $balance_due = $initialReceivable + $total_amount - $total_payment;

                if ($balance_due != 0) {	
                    // 각 열의 합계 계산
                    $totalInitialReceivable += $initialReceivable;
                    $totalSalesAmount += $total_amount;
                    $totalPaymentAmount += $total_payment;
                    $totalBalanceDue += $balance_due;
                }    
            }
        }
    }
} catch (PDOException $Exception) {
    print "오류: ".$Exception->getMessage();
}

?>  

<form id="board_form" name="board_form" method="post" enctype="multipart/form-data">             
<div class="container mb-5"> 
    <input type="hidden" id="mode" name="mode" value="<?=$mode?>">             
    <input type="hidden" id="num" name="num"> 
    <input type="hidden" id="tablename" name="tablename" value="<?=$tablename?>">                 
    <input type="hidden" id="header" name="header" value="<?=$header?>">                 
    <input type="hidden" id="secondordnum" name="secondordnum" value="<?=$secondordnum?>">                 
    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
        <span class="text-center fs-5 me-4"><?=$title_message?></span>    
        <button type="button" class="btn btn-dark btn-sm me-1" onclick='location.href="customer.php"'> 
            <i class="bi bi-arrow-clockwise"></i>
        </button>        
    </div>     
	
    <div class="card-body">                               
    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
            <i class="bi bi-arrow-right"></i> <?= count($allResults) ?> &nbsp;          

            <!-- 기간부터 검색까지 연결 묶음 start -->
                <button type="button" class="btn btn-outline-dark btn-sm me-1 change_dateRange" onclick='alldatesearch()'>전체</button>  
                <span id="showdate" class="btn btn-dark btn-sm">기간</span>   &nbsp; 
                
                <div id="showframe" class="card" style="width:220px;"> 
                    <div class="card-header" style="padding:2px;">
                        <div class="d-flex justify-content-center align-items-center">  
                            기간 설정
                        </div>
                    </div> 
                    <div class="card-body">										
                        <div class="d-flex justify-content-center align-items-center">                                                              
                            <button type="button" class="btn btn-dark btn-sm me-1 change_dateRange" onclick='pre_month()'>전월</button>                            
							<button type="button" class="btn btn-dark btn-sm me-1 change_dateRange" onclick='this_month()'>당월</button>
                            <button type="button" class="btn btn-dark btn-sm me-1 change_dateRange" onclick='this_year()'>당해년도</button> 
                        </div>
                    </div>
                </div>      

       <input type="date" id="fromdate" name="fromdate" class="form-control" style="width:100px;" value="<?=$fromdate?>">  &nbsp;   ~ &nbsp;  
       <input type="date" id="todate" name="todate" class="form-control me-1" style="width:100px;" value="<?=$todate?>">  &nbsp;     </span> 
            
        <div class="inputWrap">
                <input type="text" id="search" name="search" value="<?=$search?>" onkeydown="if(event.key === 'Enter') submitForm();" autocomplete="off" class="form-control" style="width:150px;"> &nbsp;           
                <button class="btnClear"></button>
        </div>              
          
        <div id="autocomplete-list">                       
        </div>  
          &nbsp;
            <button id="searchBtn" type="button" class="btn btn-dark btn-sm me-2" onclick="submitForm()"> <ion-icon name="search"></ion-icon> </button>                              
			* 출고예정일 기준입니다. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<button type="button" class="btn btn-dark btn-sm me-2" onclick="location.href='../getmoney/list.php?header=header'"><i class="bi bi-journal-x"></i> 수금 </button>    
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="location.href='month_sales.php?header=header'"> <i class="bi bi-file-earmark-ruled"></i> 판매일괄회계</button>
			<button type="button" class="btn btn-primary btn-sm me-1" onclick="saveBalance();"> <i class="bi bi-floppy"></i> 차기월 이월금확정</button>
         </div> 
		
    <div class="d-flex p-1 m-1 mb-1 justify-content-center align-items-center">     
     <table class="table table-hover" id="myTable">        
            <thead class="table-info"> 
                 <th class="text-center w80px">번호</th>                 
                 <th class="text-center w200px">거래처명</th>                 
                 <th class="text-center w140px">이월잔액</th>
                 <th class="text-center w140px">당월매출</th>
                 <th class="text-center w140px">수금합계</th>
                 <th class="text-center w140px">잔액</th>
                 <th class="text-center w60px">결제일</th>
                 <th class="text-center w140px">적요</th>
                 <th style="display:none;" ></th>
            </thead>
         <tbody>                			
<?php  
try {	
	$start_num = 1;                
	foreach ($allResults as $secondordnum) {
        // 이월잔액 설정
        $initialReceivable = isset($initialBalances[$secondordnum]) ? $initialBalances[$secondordnum] : 0;

        // 수금 내역 가져오기
        $paymentSql = "SELECT SUM(CAST(REPLACE(payment, ',', '') AS UNSIGNED)) as total_payment 
                       FROM ".$DB.".getmoney 
                       WHERE secondordnum = '$secondordnum'
                       AND registedate BETWEEN date('$fromdate') AND date('$Transtodate') AND is_deleted IS NULL";

        $paymentStmt = $pdo->prepare($paymentSql);
        $paymentStmt->execute();
        $paymentData = $paymentStmt->fetch(PDO::FETCH_ASSOC);
        $total_payment = isset($paymentData['total_payment']) ? (int)str_replace(',', '', $paymentData['total_payment']) : 0;

        $total_sales = isset($salesResults[$secondordnum]) ? $salesResults[$secondordnum] : 0;
		// print $total_sales;
        // 조건: 이월잔액이 있거나 매출이 있는 경우만 표시
        if ($initialReceivable != 0 || $total_sales != 0 ) {		  
		$sql = "SELECT * FROM ".$DB.".".$tablename." 
				WHERE secondordnum = '$secondordnum'
				  AND is_deleted IS NULL 
				  AND represent='아이디부여'";
		if (checkNull($search)) {
			$sql .= " AND (vendor_name LIKE '%$search%' OR representative_name LIKE '%$search%' OR manager_name LIKE '%$search%')";
		}
		// print_r($sql);
		$stmh = $pdo->query($sql); 
		while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
			include $_SERVER['DOCUMENT_ROOT'] . '/phonebook/_row.php';        
			if (empty($contact_info))
				$contact_info = $phone;      
			if (empty($paydate))
				$paydate = $paydate;
			
			if (intval($secondordnum) > 0)
				$savenum = $secondordnum;
			else
				$savenum = $num;

        // VAT를 포함한 총 매출액
        $vat = $total_sales * 0.1;
        $total_amount = $total_sales + $vat;

        // 잔액 계산
        $balance_due = $initialReceivable + $total_amount - $total_payment;
	    $memo = '';
		// if($balance_due>0 && $initialReceivable>0 )
		?>                     
		<tr onclick="redirectToView('<?= $savenum ?>')">
			<td class="text-start"><?= $start_num ?></td>                
			<td class="text-start text-primary"><?= $vendor_name ?></td>    
			 <td class="text-end text-primary fw-bold"><?= number_format($initialReceivable) ?></td>    
			<td class="text-end text-secondary fw-bold"><?= number_format($total_amount) ?></td>    
			<td class="text-end fw-bold"><?= number_format($total_payment) ?></td>
			<td class="text-end fw-bold"><?= number_format($balance_due) ?></td>
            <td class="text-end text-primary fw-bold">
                <?php if (!empty($paydate)) : ?>
                    매월 <?= htmlspecialchars($paydate) ?>일
                <?php endif; ?>
            </td>
			<td class="text-end"><?= $memo ?></td>
			<td style="display:none;"><?= $savenum ?></td>
		</tr>
		<?php

    $start_num++;
    } 
  }
}
?>         
       
      </tbody>
      <tfoot class="table-secondary">
            <tr>
                <th class="text-end w80px" colspan="2"> 합계 &nbsp; </th>
                <th class="text-end"><?= number_format($totalInitialReceivable) ?></th>
                <th class="text-end"><?= number_format($totalSalesAmount) ?></th>
                <th class="text-end"><?= number_format($totalPaymentAmount) ?></th>
                <th class="text-end"><?= number_format($totalBalanceDue) ?></th>
                <th class="text-end">&nbsp;  </th>
                <th class="text-end w150px">&nbsp;  </th>
            </tr>
        </tfoot>
     </table>
    </div>
</div>
</form>
<?php
} catch (PDOException $Exception) {
    print "오류: ".$Exception->getMessage();
}
?>
<!-- 페이지로딩 -->
<script>
    // 페이지 로딩
    $(document).ready(function(){    
        var loader = document.getElementById('loadingOverlay');
        loader.style.display = 'none';
    });

    function submitForm() {
        $('#board_form').submit();
    }
</script>

<script>
var dataTable; // DataTables 인스턴스 전역 변수
var bookpageNumber; // 현재 페이지 번호 저장을 위한 전역 변수

$(document).ready(function() {            
    // DataTables 초기 설정
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
        "order": [[0, 'desc']], // 잔액기준 내림차순 정렬
        "dom": 't<"bottom"ip>', // search 창과 lengthMenu 숨기기
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            
            // 합계를 계산하는 함수
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
            
            // 합계 계산
            totalInitialReceivable = api.column(2).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            totalSalesAmount = api.column(3).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            totalPaymentAmount = api.column(4).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
            totalBalanceDue = api.column(5).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

            // 합계 출력
            $(api.column(2).footer()).html(numberWithCommas(totalInitialReceivable));
            $(api.column(3).footer()).html(numberWithCommas(totalSalesAmount));
            $(api.column(4).footer()).html(numberWithCommas(totalPaymentAmount));
            $(api.column(5).footer()).html(numberWithCommas(totalBalanceDue));
        }
    });

    // 페이지 번호 복원 (초기 로드 시)
    var savedPageNumber = getCookie('bookpageNumber');
    if (savedPageNumber) {
        dataTable.page(parseInt(savedPageNumber) - 1).draw(false);
    }

    // 페이지 변경 이벤트 리스너
    dataTable.on('page.dt', function() {
        var bookpageNumber = dataTable.page.info().page + 1;
        setCookie('bookpageNumber', bookpageNumber, 10); // 쿠키에 페이지 번호 저장
    });

    // 페이지 길이 셀렉트 박스 변경 이벤트 처리
    $('#myTable_length select').on('change', function() {
        var selectedValue = $(this).val();
        dataTable.page.len(selectedValue).draw(); // 페이지 길이 변경 (DataTable 파괴 및 재초기화 없이)

        // 변경 후 현재 페이지 번호 복원
        savedPageNumber = getCookie('bookpageNumber');
        if (savedPageNumber) {
            dataTable.page(parseInt(savedPageNumber) - 1).draw(false);
        }
    });
});

function redirectToView(num) {    
    var fromdate = document.getElementById('fromdate').value;
    var todate = document.getElementById('todate').value;
    var url = "customer_sheet.php?num=" + num + "&fromdate=" + fromdate + "&todate=" + todate;
    customPopup(url, '거래원장', 1000, 850);             
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function saveBalance() {
    // 테이블 데이터를 수집
    let data = [];
    $('#myTable tbody tr').each(function() {
        let row = $(this);
        let balance = parseFloat(row.find('td:nth-child(6)').text().replace(/,/g, ''));

        // 잔액이 0이 아닌 경우만 데이터 수집
        if (balance !== 0) {
            data.push({
                mode: row.attr('data-mode') || 'insert',  // insert or update mode
                num: row.attr('data-num'),
                secondordnum: row.find('td:nth-child(9)').text().trim(),
                customer_name: row.find('td:nth-child(2)').text().trim(),
                balance: balance,
                closure_date: $('#todate').val(),
                memo: row.find('td:nth-child(8)').text().trim()
            });
        }
    });

    // Ajax로 데이터 전송
    $.ajax({
        url: 'insert_monthly_balance.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
            let result = JSON.parse(response);
            if (result.status === 'success') {
                alert('이월금액이 성공적으로 저장되었습니다.');
            } else {
                alert('오류 발생: ' + result.message);
            }
        },
        error: function(xhr, status, error) {
            alert('오류 발생: ' + error);
        }
    });
}

function detail() {
    // 년도, 시작 월, 종료 월 값을 가져옴
    const year = document.getElementById('year').value;
    const startMonth = document.getElementById('startMonth').value;
    const endMonth = document.getElementById('endMonth').value;

    // detail.php로 이동할 URL 생성
    const url = `detail.php?year=${year}&startMonth=${startMonth}&endMonth=${endMonth}`;

    // customPopup을 사용하여 detail.php를 팝업으로 열기
    customPopup(url, '상세 내역', 900, 700);
}


</script>
</body>
</html>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

session_start();
if (!isset($_SESSION["level"]) || $_SESSION["level"] > 5) {
    sleep(1);
    header("Location: /login/login_form.php"); 
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php';
// 첫 화면 표시 문구
$title_message = '판매일괄회계반영';  
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
    $fromdate = date("Y-m-01", strtotime($currentDate)); // 월의 첫날 설정
    $todate = date("Y-m-t", strtotime($currentDate)); // 현재 달의 마지막 날 설정
} else {
    $fromdate = date("Y-m-01", strtotime($fromdate)); // 선택한 달의 첫날 설정
    $todate = date("Y-m-t", strtotime($todate)); // 선택한 달의 마지막 날 설정
}

function checkNull($strtmp) {
    return $strtmp !== null && trim($strtmp) !== '';
}

$tablename = 'phonebook';

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

// 매출이 발생한 거래처 필터링 및 매출액 계산
$salesSql = "
    SELECT o.secondordnum, COALESCE(e.ET_total, 0) AS ET_total
    FROM output o
    LEFT JOIN output_extra e ON o.num = e.parent_num
    WHERE (o.outdate BETWEEN :fromdate AND :todate) AND o.is_deleted IS NULL
";
$salesStmt = $pdo->prepare($salesSql);
$salesStmt->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
$salesStmt->bindParam(':todate', $todate, PDO::PARAM_STR);
$salesStmt->execute();
$salesData = $salesStmt->fetchAll(PDO::FETCH_ASSOC);

$salesResults = [];
foreach ($salesData as $row) {
    $secondordnum = $row['secondordnum'];
    $et_total = (float)$row['ET_total'];
    if (!isset($salesResults[$secondordnum])) {
        $salesResults[$secondordnum] = 0;
    }
    $salesResults[$secondordnum] += round($et_total, 2);
}

arsort($salesResults);  // 공급가액 내림차순으로 정렬

// 전월 마지막 날짜 계산
$lastMonthDate = date("Y-m-t", strtotime($fromdate . " -1 month"));

// 전월 잔액 조회
$balanceSql = "
    SELECT secondordnum, balance, num, invoice_issued, memo
    FROM monthly_balances
    WHERE closure_date = :lastMonthDate
";

$balanceStmt = $pdo->prepare($balanceSql);
$balanceStmt->execute([':lastMonthDate' => $lastMonthDate]);
$previousBalances = $balanceStmt->fetchAll(PDO::FETCH_ASSOC);

// 전월 잔액을 저장할 배열
$previousBalanceMap = [];
foreach ($previousBalances as $balanceRow) {
    $previousBalanceMap[$balanceRow['secondordnum']] = [
        'balance' => $balanceRow['balance'],
        'invoice_issued' => $balanceRow['invoice_issued'],
        'memo' => $balanceRow['memo'],
        'num' => $balanceRow['num']
    ];
}

// 현재월 잔액 조회
$current_saleSql = "
    SELECT secondordnum, sales, num, invoice_issued, memo
    FROM monthly_sales
    WHERE closure_date BETWEEN :fromdate AND :todate AND is_deleted IS NULL 
";

$balanceStmt = $pdo->prepare($current_saleSql);
$balanceStmt->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
$balanceStmt->bindParam(':todate', $todate, PDO::PARAM_STR);
$balanceStmt->execute();
$currentBalances = $balanceStmt->fetchAll(PDO::FETCH_ASSOC);

// 이번달 잔액을 저장할 배열
$currentBalanceMap = [];
foreach ($currentBalances as $balanceRow) {
    $currentBalanceMap[$balanceRow['secondordnum']] = [
        'sales' => $balanceRow['sales'],
        'invoice_issued' => $balanceRow['invoice_issued'],
        'memo' => $balanceRow['memo'],
        'num' => $balanceRow['num']
    ];
}

// 수금 내역 가져오기
$paymentSql = "SELECT secondordnum, SUM(CAST(REPLACE(payment, ',', '') AS UNSIGNED)) as total_payment 
               FROM getmoney 
               WHERE registedate BETWEEN :fromdate AND :todate AND is_deleted IS NULL 
               GROUP BY secondordnum";
$paymentStmt = $pdo->prepare($paymentSql);
$paymentStmt->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
$paymentStmt->bindParam(':todate', $todate, PDO::PARAM_STR);
$paymentStmt->execute();
$paymentData = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);

// 수금 내역을 저장할 배열
$paymentMap = [];
foreach ($paymentData as $paymentRow) {
    $paymentMap[$paymentRow['secondordnum']] = (int)str_replace(',', '', $paymentRow['total_payment']);
}

?>  

<form id="board_form" name="board_form" method="post" enctype="multipart/form-data">             

    <input type="hidden" id="mode" name="mode" value="<?=$mode?>">             
    <input type="hidden" id="num" name="num"> 
    <input type="hidden" id="tablename" name="tablename" value="<?=$tablename?>">                 
    <input type="hidden" id="header" name="header" value="<?=$header?>">                 
    <input type="hidden" id="secondordnum" name="secondordnum" value="<?=$secondordnum?>">  

<!-- 모달창 가져오기 -->
<?php include 'modal.php'; ?>
    
<div class="container">                               
    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
        <span class="text-center fs-5 me-4"><?=$title_message?></span>    
        <button type="button" class="btn btn-dark btn-sm me-1" onclick='location.href="month_sales.php"'> 
            <i class="bi bi-arrow-clockwise"></i>
        </button>        
    </div>                               
    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
        <span class="text-center fs-6 text-danger"> * 세금계산서 발행일은 매월 말일 기준으로 입력해 주세요! 현재날짜로 입력하면 화면에 안나올수 있습니다.  </span>    
    </div>      

    <div class="card">                               
    <div class="card-body">                               

    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
        ▷ <?= count($salesResults) ?> &nbsp;          
        <!-- 기간부터 검색까지 연결 묶음 start -->
        <button type="button" class="btn btn-secondary btn-sm me-1 change_dateRange" onclick='prepre_month()'>전전월</button>                            
        <button type="button" class="btn btn-secondary btn-sm me-1 change_dateRange" onclick='pre_month()'>전월</button>                            
        <button type="button" class="btn btn-dark btn-sm me-1 change_dateRange" onclick='this_month()'>당월</button>                
     
       <input type="date" id="fromdate" name="fromdate" class="form-control" style="width:100px;" value="<?=$fromdate?>">  &nbsp;   ~ &nbsp;  
       <input type="date" id="todate" name="todate" class="form-control me-1" style="width:100px;" value="<?=$todate?>">  &nbsp;     </span> 
            
        <div class="inputWrap">
                <input type="text" id="search" name="search" value="<?=$search?>" onkeydown="if(event.key === 'Enter') submitForm();" autocomplete="off" class="form-control" style="width:150px;"> &nbsp;           
                <button class="btnClear"></button>
        </div>              
          
        <div id="autocomplete-list">                       
        </div>  
          &nbsp;
            <button id="searchBtn" type="button" class="btn btn-outline-dark btn-sm me-2" onclick="submitForm()"><i class="bi bi-search"></i> 검색</button>                   
            <button type="button" class="btn btn-dark btn-sm me-2" onclick="location.href='../getmoney/list.php?header=header'"><i class="bi bi-journal-x"></i> 수금 </button>    
            <button type="button" class="btn btn-dark btn-sm me-1" onclick="location.href='../account/S_transaction.php?header=header'"> <i class="bi bi-file-earmark-ruled"></i> 거래원장</button>
            <button type="button" class="btn btn-dark btn-sm me-1" onclick="openMonthlyBalancePopup()"> <i class="bi bi-file-earmark-ruled"></i> 이월잔액</button>
			<button type="button" class="btn btn-danger btn-sm me-2" onclick="location.href='../account/receivable.php?header=header'"> <i class="bi bi-journal-x"></i> 미수금 </button>    
         </div> 
        
    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">     
     <table class="table table-hover" id="myTable">        
            <thead class="table-info"> 
                 <th class="text-center w50px">번호</th>                 
                 <th class="text-center w200px">회사명</th>                 
                 <th class="text-center w100px">공급가</th>
                 <th class="text-center w100px">부가세</th>
                 <th class="text-center w100px">당월금액</th>
                 <th class="text-center w50px">상세</th>
                 <th class="text-center w100px">발행금액</th>                 
                 <th class="text-center w100px">세금계산서</th>
                 <th class="text-center w200px">적요</th>
            </thead>
         <tbody>                        
        <?php  
        try {    
            $start_num = 1;   
			$processedSecondOrdnums = []; // 처리된 secondordnum을 추적하는 배열			
            foreach ($salesResults as $secondordnum => $total_sales) {
              if((int)$total_sales!==0 && intval($secondordnum) > 0 )
              {          
				$processedSecondOrdnums[] = $secondordnum;
				
                $sql = "SELECT * FROM ".$DB.".".$tablename." 
                        WHERE secondordnum = '$secondordnum'
                          AND is_deleted IS NULL 
                          AND represent='대표코드'";
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
				}
                    
            // 수금 내역 가져오기
            $payment_collection = 0;
            if (isset($paymentMap[$secondordnum])) {
                $payment_collection = $paymentMap[$secondordnum];
            }

            // 전월 잔액 조회
            $previous_balance = 0;
            $previous_balance_num = '';
            if (isset($previousBalanceMap[$secondordnum])) {
                $previous_balance = $previousBalanceMap[$secondordnum]['balance'];
                $previous_balance_num = $previousBalanceMap[$secondordnum]['num'];                
            }
            
            $vat = round(round($total_sales) * 0.1);
            $total_amount = $total_sales + $vat;
            $balance_due = $total_amount - $payment_collection;
            
            // 현재달 잔액 조회
            $current_sale = 0;
            $current_sale_num = '';
            $invoice_issued = '';
            $memo = '';
            
            // 기존데이터가 존재할때
            if (isset($currentBalanceMap[$secondordnum])) {
                $current_sales = $currentBalanceMap[$secondordnum]['sales'];
                $current_sale_num = $currentBalanceMap[$secondordnum]['num'];
                $invoice_issued = isset($currentBalanceMap[$secondordnum]['invoice_issued']) ? $currentBalanceMap[$secondordnum]['invoice_issued'] : ''; // 기본값을 제공                
                $memo = isset($currentBalanceMap[$secondordnum]['memo']) ? $currentBalanceMap[$secondordnum]['memo'] : ''; // 기본값을 제공       
            }    
            else{
                // 기존데이터가 없을때 신규
                $current_sales =  0;
                $current_sale_num = '';
                $invoice_issued =  ''; 
                $memo = '';
            }
      
			// 마지막 단위 원단위 중 1은 제거하는 로직
			// 당월매출 마지막 1원 삭제
			// 마지막 자리가 1로 끝나는 경우, 0으로 변경
			// print_r($total_payment);
			// $totalSalesAmount -= 5; 
			if (round($total_amount) % 10 === 1) {
				$vat -= 1;  // 1을 빼서 마지막 자리를 0으로 만듭니다.
				$total_amount -= 1;  // 1을 빼서 마지막 자리를 0으로 만듭니다.
			}

			if (round($balance_due) % 10 === 1) {
				$balance_due -= 1;  // 1을 빼서 마지막 자리를 0으로 만듭니다.
			}
			  
		 
	?>           

	<tr data-num="<?= $current_sale_num ?>" data-secondordnum="<?= $secondordnum ?>" data-vendorname="<?= $vendor_name ?>" data-totalamount="<?= $total_amount ?>">
		<td class="text-center"><?= $start_num ?></td>
		<td class="text-start text-primary"  onclick="fetchMonthlyBalanceData('<?= $current_sale_num ?>', '<?= $secondordnum ?>', '<?= $vendor_name ?>', '<?= $total_amount ?>')"><?= $vendor_name ?></td>    
		<td class="text-end "   onclick="fetchMonthlyBalanceData('<?= $current_sale_num ?>', '<?= $secondordnum ?>', '<?= $vendor_name ?>', '<?= $total_amount ?>')"><?= number_format($total_sales) ?></td>
		<td class="text-end "   onclick="fetchMonthlyBalanceData('<?= $current_sale_num ?>', '<?= $secondordnum ?>', '<?= $vendor_name ?>', '<?= $total_amount ?>')"><?= number_format($vat) ?></td>
		<td class="text-end fw-bold text-primary"   onclick="fetchMonthlyBalanceData('<?= $current_sale_num ?>', '<?= $secondordnum ?>', '<?= $vendor_name ?>', '<?= $total_amount ?>')"><?= number_format($total_amount) ?></td>
		<td class="text-center" onclick="redirectToView('<?= $savenum ?>')"><span class="badge bg-secondary"> 상세 </span></td>
		<td class="text-end fw-bold"   onclick="fetchMonthlyBalanceData('<?= $current_sale_num ?>', '<?= $secondordnum ?>', '<?= $vendor_name ?>', '<?= $total_amount ?>')"><?= number_format($current_sales) ?></td>    
		<td class="text-center" onclick="fetchMonthlyBalanceData('<?= $current_sale_num ?>', '<?= $secondordnum ?>', '<?= $vendor_name ?>', '<?= $total_amount ?>')">
			<?php if ($invoice_issued): ?>
				<span class="badge bg-warning"><?= $invoice_issued ?></span>
			<?php else: ?>
				&nbsp;
			<?php endif; ?>
		</td>
		<td class="text-start"  onclick="fetchMonthlyBalanceData('<?= $current_sale_num ?>', '<?= $secondordnum ?>', '<?= $vendor_name ?>', '<?= $total_amount ?>')"><?= $memo ?></td>
	</tr>

			
	<?php
		$start_num++;		
	  }
	}
	?>     
      </tbody>
      <tfoot class="table-secondary">
            <tr>
                <th class="text-end" colspan="2"> 합계 &nbsp; </th>
                <th class="text-end"></th>
                <th class="text-end"></th>
                <th class="text-end"></th>
                <th class="text-end"></th>
                <th class="text-end"></th>
                <th class="text-end"></th>
                <th class="text-end"></th>
            </tr>
        </tfoot>     
     </table>
    </div>
    </div>
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
$(document).ready(function(){    
    var loader = document.getElementById('loadingOverlay');
    loader.style.display = 'none';
// 모달창 닫기
$(document).on('click', '.close', function(e) {
    $("#monthlysaleModal").modal("hide");
});  
  
});

function submitForm() {
    $('#board_form').submit();
}
</script>

<script>
var dataTable; // DataTables 인스턴스 전역 변수
var monthsalepageNumber; // 현재 페이지 번호 저장을 위한 전역 변수

$(document).ready(function() {            
    // DataTables 초기 설정
    dataTable = $('#myTable').DataTable({
        "paging": true,
        "ordering": true,
        "searching": true,
        "pageLength": 50,
        "lengthMenu": [50, 100, 200, 500, 1000],
        "language": {
            "lengthMenu": "Show _MENU_ entries",
            "search": "Live Search:"
        },
        "order": [[2, 'desc']], // 금액 기준 내림차순 정렬        
        "dom": 't<"bottom"ip>', // search 창과 lengthMenu 숨기기        
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api();
            var intVal = function (i) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            var totalInitialReceivable = api
                .column(2)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            var totalSalesAmount = api
                .column(3)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            var totalissued = api
                .column(4)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);
                

            $(api.column(2).footer()).html(numberWithCommas(totalInitialReceivable));
            $(api.column(3).footer()).html(numberWithCommas(totalSalesAmount));            
            $(api.column(4).footer()).html(numberWithCommas(totalissued));                        
        }
    });

    // 페이지 번호 복원 (초기 로드 시)
    var savedPageNumber = getCookie('monthsalepageNumber');
    if (savedPageNumber) {
        dataTable.page(parseInt(savedPageNumber) - 1).draw(false);
    }

    // 페이지 변경 이벤트 리스너
    dataTable.on('page.dt', function() {
        var monthsalepageNumber = dataTable.page.info().page + 1;
        setCookie('monthsalepageNumber', monthsalepageNumber, 10); // 쿠키에 페이지 번호 저장
    });

    // 페이지 길이 셀렉트 박스 변경 이벤트 처리
    $('#myTable_length select').on('change', function() {
        var selectedValue = $(this).val();
        dataTable.page.len(selectedValue).draw(); // 페이지 길이 변경 (DataTable 파괴 및 재초기화 없이)

        // 변경 후 현재 페이지 번호 복원
        savedPageNumber = getCookie('monthsalepageNumber');
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
function openMonthlyBalancePopup() {
    var url = "monthly_balance_popup.php";
    customPopup(url, '거래원장', 1000, 850);   
}

function fetchMonthlyBalanceData(current_sale_num, secondordnum, vendor_name, total_amount) {
    $.ajax({
        type: 'POST',
        url: 'fetch_closure_sale.php',
        data: { num: current_sale_num },
        dataType: 'json',
        success: function(response) {
            console.log(response); // 디버깅을 위해 response를 출력

            var tableBody = $('#monthlysaleModalBody');
            tableBody.empty();

            var currentDate = new Date().toISOString().slice(0, 10);
            var selectedRow = $('#myTable tr[data-num="' + current_sale_num + '"]'); // 현재 선택한 행

            // 선택된 행이 없는 경우 처리
            if (selectedRow.length === 0) {
                console.error('Selected row not found.');
                return;
            }

            var customer_name = vendor_name; // vendor_name을 직접 사용
            var formattedSales = parseFloat(total_amount).toLocaleString(); // total_amount를 3자리마다 콤마를 추가하여 포맷팅

            console.log('Selected Row:', selectedRow);
            console.log('Customer Name:', customer_name);
            console.log('Formatted Sales:', formattedSales);

            if (Array.isArray(response) && response.length !== 1) {
                // 여러 개의 배열이 반환된 경우 또는 데이터가 없는 경우
                var row = '<tr>' +
                    '<td class="text-center"><input type="date" class="form-control text-center" value="' + currentDate + '"></td>' +
                    '<td class="text-center"><input type="text" class="form-control customer_name  text-start " value="' + customer_name + '"></td>'  +
                    '<td class="text-end"><input type="text" class="form-control text-end sales w-75" onkeyup="inputNumberFormat(this)" value="'+ formattedSales +'"></td>' +
                    '<td class="text-center"><input type="checkbox" class="form-check-input invoice_issued"></td>' +
                    '<td class="text-center"><input type="text" class="form-control memo" value=""></td>' +
                    '<td class="text-center"><input type="text" class="form-control w80px text-start num"  readonly  value="' + current_sale_num + '"></td>' +
                    '<td style="display:none;"><input type="hidden" class="text-start secondordnum" value="' + secondordnum + '"></td>' +
                    '</tr>';
                tableBody.append(row);
            } else if (Array.isArray(response) && response.length === 1) {
                // 단일 데이터가 반환된 경우
                response.forEach(function(item) {
                    var formattedsales = parseFloat(item.sales).toLocaleString();
                    var row = '<tr>' +
                        '<td class="text-center"><input type="date" class="form-control text-center" value="' + item.closure_date + '"></td>' +
                        '<td class="text-center"><input type="text" class="form-control customer_name  text-start " value="' + (item.customer_name !== null ? item.customer_name : '') + '"></td>'  +
                        '<td class="text-end"><input type="text" class="form-control text-end w120px sales w-75" onkeyup="inputNumberFormat(this)" value="' + formattedsales + '"></td>' +
                        '<td class="text-center"><input type="checkbox" class="form-check-input invoice_issued" ' + (item.invoice_issued === '발행' ? 'checked' : '') + '></td>' +
                        '<td class="text-center"><input type="text" class="form-control memo" value="' + (item.memo !== null ? item.memo : '') + '"></td>' +
                        '<td class="text-center"><input type="text" class="form-control w80px text-start num"  readonly  value="' + current_sale_num + '"></td>' +
                        '<td style="display:none;"><input type="hidden" class="text-start secondordnum" value="' + secondordnum + '"></td>' +
                        '</tr>';
                    tableBody.append(row);
                });
            } else {
                // 빈 배열이 반환된 경우 (예외 처리)
                var row = '<tr>' +
                    '<td class="text-center"><input type="date" class="form-control text-center" value="' + currentDate + '"></td>' +
                    '<td class="text-center"><input type="text" class="form-control customer_name  text-start" value="' + customer_name + '"></td>'  +
                    '<td class="text-end"><input type="text" class="form-control text-end sales w-75" onkeyup="inputNumberFormat(this)" value="'+ formattedSales +'"></td>' +
                    '<td class="text-center"><input type="checkbox" class="form-check-input invoice_issued"></td>' +
                    '<td class="text-center"><input type="text" class="form-control memo" value=""></td>' +
                    '<td class="text-center"><input type="text" class="form-control w80px text-start num" readonly value="' + current_sale_num + '"></td>' +
                    '<td style="display:none;"><input type="hidden" class="text-start secondordnum" value="' + secondordnum + '"></td>' +
                    '</tr>';
                tableBody.append(row);
            }
			
			console.log('current_sale_num ',current_sale_num);
            $('#monthlysaleModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error("AJAX error: ", status, error);
        }
    });
}

function saveMonthlyBalanceData() {
    var data = [];
    $('#monthlysaleModalBody tr').each(function() {
        var num = $(this).find('.num').val();
        var closure_date = $(this).find('input[type="date"]').val();
        var customer_name = $(this).find('.customer_name').val();
        var sales = $(this).find('.sales').val().replace(/,/g, ''); // 콤마 제거
        var invoice_issued = $(this).find('input[type="checkbox"]').is(':checked');
        var memo = $(this).find('.memo').val();
        var secondordnum = $(this).find('.secondordnum').val();
        var mode = num ? 'update' : 'insert'; // num이 있는 경우 업데이트, 없는 경우 삽입
        data.push({
            mode: mode,
            num: num,
            closure_date: closure_date,
            customer_name: customer_name,
            sales: sales,
            invoice_issued: invoice_issued,
            secondordnum: secondordnum,
            memo: memo
        });
    });

        
	console.log('data',data);

    // 버튼 비활성화
    var saveButton = $('button[onclick="saveMonthlyBalanceData()"]');
    saveButton.prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: 'insert_monthly_sale.php',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
            console.log(response);
            toastAlert('파일저장');
            // 1.5초 후에 모달창 닫고 화면 새로고침
            setTimeout(function(){
                $('#monthlysaleModal').modal('hide');
                location.reload(); // 화면 새로고침
            }, 1500);
        },
        error: function(xhr, status, error) {
            console.error("AJAX error: ", status, error);
            // 에러 발생 시 버튼 다시 활성화
            saveButton.prop('disabled', false);
        }
    });
}

// 삭제처리
function saveMonthlyDelete() {
    Swal.fire({
        title: '계산서 발행 삭제',
        text: "이 작업을 진행하시겠습니까?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '예',
        cancelButtonText: '아니오'
    }).then((result) => {
        if (result.isConfirmed) {
            var data = [];
            $('#monthlysaleModalBody tr').each(function() {
                var num = $(this).find('.num').val();
                var mode = 'delete'; 

                data.push({
                    mode: mode,
                    num: num
                });
            });

            // 버튼 비활성화
            var saveButton = $('button[onclick="saveMonthlyDelete()"]');
            saveButton.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: 'insert_monthly_sale.php',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(response) {            
                    toastAlert('파일삭제');
                    // 1.5초 후에 모달창 닫고 화면 새로고침
                    setTimeout(function(){
                        $('#monthlysaleModal').modal('hide');
                        location.reload(); // 화면 새로고침
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error: ", status, error);
                    // 에러 발생 시 버튼 다시 활성화
                    saveButton.prop('disabled', false);
                }
            });
        } else {
            // 사용자가 '아니오'를 누른 경우
            Swal.fire('취소되었습니다.', '', 'info');
        }
    });
}

// 모달창 닫힐 때 버튼 다시 활성화
$('#monthlysaleModal').on('hidden.bs.modal', function () {
    var saveButton = $('button[onclick="saveMonthlyBalanceData()"]');
    saveButton.prop('disabled', false);
    var saveButton_delete = $('button[onclick="saveMonthlyDelete()"]');
    saveButton_delete.prop('disabled', false);
});

// 화면에 toastAlert() 표시
function toastAlert(Str){
    // 오버레이 활성화
    document.getElementById("overlay").style.display = "block";

    Toastify({
        text: Str,
        duration: 3000,
        close: true,
        gravity: "top",
        position: 'center',           
    }).showToast();

    // 1초 후에 오버레이 비활성화
    setTimeout(function(){
        document.getElementById("overlay").style.display = "none";
    }, 1000);
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

</script>
</body>
</html>

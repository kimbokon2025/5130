<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

if (!isset($_SESSION["level"]) || $_SESSION["level"] > 5) {
    sleep(1);
    header("Location:" . $WebSite . "login/login_form.php"); 
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php';

// 첫 화면 표시 문구
$title_message = '이월잔액 자료'; 
?>

<title> <?=$title_message?> </title>

</head>
<body>

<?php

$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';  
$fromdate = isset($_REQUEST['fromdate']) ? $_REQUEST['fromdate'] : '';  
$todate = isset($_REQUEST['todate']) ? $_REQUEST['todate'] : '';  
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';  

// 현재 날짜
$currentDate = date("Y-m-d");

// fromdate 또는 todate가 빈 문자열이거나 null인 경우
if ($fromdate === "" || $fromdate === null || $todate === "" || $todate === null) {
    // 전월의 첫날과 말일을 계산하여 설정
    $fromdate = date("Y-m-01", strtotime("first day of previous month")); // 전월의 첫날 설정
    $todate = date("Y-m-t", strtotime("last day of previous month")); // 전월의 마지막 날 설정
    $Transtodate = $todate;
} else {
    // 지정된 fromdate와 todate의 월의 첫날과 마지막 날을 계산하여 설정
    $fromdate = date("Y-m-01", strtotime($fromdate)); // 선택한 달의 첫날 설정
    $todate = date("Y-m-t", strtotime($todate)); // 선택한 달의 마지막 날 설정
    $Transtodate = $todate;
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

// 전월 마지막 날짜 계산
// $lastMonthDate = date("Y-m-t", strtotime("last month"));
$lastMonthDate =  $Transtodate ;

// print $lastMonthDate;

// 전월 잔액 조회 (output + output_extra, outdate, ET_total)
$balanceSql = "
    SELECT o.num, o.secondordnum, SUM(COALESCE(e.ET_total, 0)) AS balance, o.secondord AS secondord, '' AS memo, o.outdate AS closure_date
    FROM output o
    LEFT JOIN output_extra e ON o.num = e.parent_num
    WHERE o.outdate = :lastMonthDate AND o.outdate BETWEEN :fromdate AND :todate AND ( o.is_deleted IS NULL or o.is_deleted = 0 )
    GROUP BY o.secondordnum, o.outdate
";

if (!empty($search)) {
    $balanceSql .= " AND o.secondordnum LIKE :search";
}

$balanceStmt = $pdo->prepare($balanceSql);
$balanceStmt->bindValue(':lastMonthDate', $lastMonthDate, PDO::PARAM_STR);
$balanceStmt->bindValue(':fromdate', $fromdate, PDO::PARAM_STR);
$balanceStmt->bindValue(':todate', $todate, PDO::PARAM_STR);

if (!empty($search)) {
    $balanceStmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
}

$balanceStmt->execute();
$previousBalances = $balanceStmt->fetchAll(PDO::FETCH_ASSOC);

?>  

<form id="board_form" name="board_form" method="post" enctype="multipart/form-data">             

    <input type="hidden" id="mode" name="mode" value="<?=$mode?>">             
    <input type="hidden" id="num" name="num"> 
    <input type="hidden" id="tablename" name="tablename" value="<?=$tablename?>">                 
    <input type="hidden" id="header" name="header" value="<?=$header?>">                 
    <input type="hidden" id="secondordnum" name="secondordnum" value="<?=$secondordnum?>">     

<?php include 'modal.php'; ?>

<div class="container">                               	
    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
        <span class="text-center fs-5 me-4"><?=$title_message?></span>    
        <button type="button" class="btn btn-dark btn-sm me-1" onclick='location.reload();'> 
            <i class="bi bi-arrow-clockwise"></i>
        </button>        
    </div>      

    <div class="card">       
    <div class="card-body">       

    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">       
            <ion-icon name="caret-forward-outline"></ion-icon> <?= count($previousBalances) ?> &nbsp;          
           
                <button type="button" class="btn btn-secondary btn-sm me-1 change_dateRange" onclick='prepre_month()'>전전월</button>                            
                <button type="button" class="btn btn-secondary btn-sm me-1 change_dateRange" onclick='pre_month()'>전월</button>    
                
     
       <input type="date" id="fromdate" name="fromdate" class="form-control" style="width:100px;" value="<?=$fromdate?>">  &nbsp;   ~ &nbsp;  
       <input type="date" id="todate" name="todate" class="form-control me-1" style="width:100px;" value="<?=$todate?>">  &nbsp;     </span> 
            
        <div class="inputWrap">
                <input type="text" id="search" name="search" value="<?=$search?>" onkeydown="if(event.key === 'Enter') submitForm();" autocomplete="off" class="form-control me-1" style="width:150px;"> &nbsp;           
                <button class="btnClear"></button>
        </div>   	
		<button id="searchBtn" type="button" class="btn btn-dark btn-sm me-2" onclick="submitForm()"> <i class="bi bi-search"></i>  </button>                  
		<button id="closeBtn" type="button" class="btn btn-secondary btn-sm ms-2" onclick="window.close();"> 창닫기  </button>                  
        </div>   	
		

    <div class="d-flex p-1 m-1 mt-1 mb-1 justify-content-center align-items-center">     
     <table class="table table-hover" id="balanceTable">        
            <thead class="table-info"> 
                 <th class="text-center w50px">번호</th>                 
                 <th class="text-center w100px">기준일자</th>                 
                 <th class="text-center w130px">거래처명</th>                 
                 <th class="text-end w100px">이월잔액</th>                 
                 <th class="text-center w150px">적요</th>
                 <th class="text-center w100px">거래처코드</th>                 
            </thead>
         <tbody>                			
    <?php  
try {	
    $start_num = 1;                
    foreach ($previousBalances as $row) {
        $num = $row['num'];
        $secondordnum = $row['secondordnum'];
        $secondord = $row['secondord'];
        $balance = $row['balance'];        
        $memo = isset($row['memo']) ? $row['memo'] : ''; // 기본값을 제공
        
?>                        
<tr data-toggle="modal" data-target="#monthlyBalanceModal" onclick="fetchMonthlyBalanceData(<?= $num ?>)">
    <td class="text-center"><?= $start_num ?></td>                
    <td class="text-center"><?= $lastMonthDate ?></td>
    <td class="text-start text-primary"><?= $secondord ?></td>    
    <td class="text-end"><?= $balance != 0 ? number_format($balance) : '' ?></td>    
    <td class="text-center"><?= $memo ?> </td>
    <td class="text-center text-info"><?= $secondordnum ?></td>    
</tr>
<?php
    $start_num++;
    } 
} catch (PDOException $Exception) {
    print "오류: ".$Exception->getMessage();
}
?>         
       
      </tbody>
     </table>
    </div>
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
	loader.style.display = 'none';
// 모달창 닫기
$(document).on('click', '.close', function(e) {
	$("#monthlyBalanceModal").modal("hide");
});  
  
});

var dataTable; // DataTables 인스턴스 전역 변수
var bookpageNumber; // 현재 페이지 번호 저장을 위한 전역 변수

function inputNumberFormat(input) {
    var value = input.value.replace(/,/g, ''); // 콤마 제거
    if (!isNaN(value) && value !== '') {
        input.value = parseFloat(value).toLocaleString();
    }
}

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
        "order": [[2, 'desc']], // 공급가액 기준 내림차순 정렬
		"dom": 't<"bottom"ip>' // search 창과 lengthMenu 숨기기
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

function submitForm() {
	$('#board_form').submit();
}

function fetchMonthlyBalanceData(num) {
    $.ajax({
        type: 'POST',
        url: 'fetch_closure.php',
        data: { num: num },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            if (!Array.isArray(response)) {
                console.error("Response is not an array:", response);
                return;
            }
            var tableBody = $('#monthlyBalanceModalBody');
            tableBody.empty();

            response.forEach(function(item) {
                var formattedBalance = parseFloat(item.balance).toLocaleString();
                var row = '<tr>' +
                    '<td class="text-center"><input type="date" class="form-control text-center" value="' + item.closure_date + '"></td>' +
                    '<td class="text-center"><input type="text" class="form-control secondord" value="' + (item.secondord !== null ? item.secondord : '') + '"></td>'  +
                    '<td class="text-end"><input type="text" class="form-control text-end w120px balance" onkeyup="inputNumberFormat(this)" value="' + formattedBalance + '"></td>' +                    
                    '<td class="text-center"><input type="text" class="form-control memo" value="' + (item.memo !== null ? item.memo : '') + '"></td>' +
                    '<td style="display:none;" ><input type="hidden" class="num" value="' + item.num  + '"></td>' +
                    '</tr>';
                tableBody.append(row);
            });
			$('#monthlyBalanceModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error("AJAX error: ", status, error);
        }
    });
}
function saveMonthlyBalanceData() {
    var data = [];
    $('#monthlyBalanceModalBody tr').each(function() {
        var num = $(this).find('.num').val();
        var closure_date = $(this).find('input[type="date"]').val();
        var secondord = $(this).find('.secondord').val();
        var balance = $(this).find('.balance').val().replace(/,/g, ''); // 콤마 제거        
        var memo = $(this).find('.memo').val();

        data.push({
            num: num,
            closure_date: closure_date,
            secondord: secondord,
            balance: balance,            
            memo: memo
        });
    });

    // 버튼 비활성화
    var saveButton = $('button[onclick="saveMonthlyBalanceData()"]');
    saveButton.prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: 'insert_monthly.php',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
            toastAlert('파일저장');
            // 1.5초 후에 모달창 닫고 화면 새로고침
            setTimeout(function(){
                $('#monthlyBalanceModal').modal('hide');
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

// 모달창 닫힐 때 버튼 다시 활성화
$('#monthlyBalanceModal').on('hidden.bs.modal', function () {
    var saveButton = $('button[onclick="saveMonthlyBalanceData()"]');
    saveButton.prop('disabled', false);
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
</script>

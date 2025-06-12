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
$title_message = '금전 출납부';
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
    $fromdate = date("Y-m-01");
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

$sql_conditions[] = "is_deleted = 0";

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
    SUM(CASE WHEN inoutsep = '수입' THEN amount ELSE 0 END) -
    SUM(CASE WHEN inoutsep = '지출' THEN amount ELSE 0 END) AS balance
    FROM $tablename WHERE is_deleted = '0' AND registDate < :fromdate";
$initialBalanceStmh = $pdo->prepare($initialBalanceSql);
$initialBalanceStmh->bindParam(':fromdate', $fromdate);
$initialBalanceStmh->execute();
$initialBalance = $initialBalanceStmh->fetch(PDO::FETCH_ASSOC)['balance'];

$totalIncomeSql = "SELECT SUM(amount) AS totalIncome FROM $tablename WHERE is_deleted = '0' AND inoutsep = '수입' AND registDate BETWEEN :fromdate AND :todate";
$totalIncomeStmh = $pdo->prepare($totalIncomeSql);
$totalIncomeStmh->bindParam(':fromdate', $fromdate);
$totalIncomeStmh->bindParam(':todate', $todate);
$totalIncomeStmh->execute();
$totalIncome = $totalIncomeStmh->fetch(PDO::FETCH_ASSOC)['totalIncome'];

$totalExpenseSql = "SELECT SUM(amount) AS totalExpense FROM $tablename WHERE is_deleted = '0' AND inoutsep = '지출' AND registDate BETWEEN :fromdate AND :todate";
$totalExpenseStmh = $pdo->prepare($totalExpenseSql);
$totalExpenseStmh->bindParam(':fromdate', $fromdate);
$totalExpenseStmh->bindParam(':todate', $todate);
$totalExpenseStmh->execute();
$totalExpense = $totalExpenseStmh->fetch(PDO::FETCH_ASSOC)['totalExpense'];

$finalBalance = $initialBalance + $totalIncome - $totalExpense;

// Bankbook options
$bankbookOptions = [];
$bankbookFilePath = $_SERVER['DOCUMENT_ROOT'] . "/account/bankbook.txt";
if (file_exists($bankbookFilePath)) {
    $bankbookOptions = file($bankbookFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

?>

<form id="board_form" name="board_form" method="post" enctype="multipart/form-data">
    <input type="hidden" id="mode" name="mode" value="<?=$mode?>">
    <input type="hidden" id="num" name="num">
    <input type="hidden" id="tablename" name="tablename" value="<?=$tablename?>">    

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
                <span class="text-center fs-5">  <?=$title_message?>   </span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center align-items-center mt-2">
                    <span>
                        ▷ <?= $total_row ?> &nbsp;
                    </span>
                                
                    <!-- 기간부터 검색까지 연결 묶음 start -->                    
                    <span id="showdate" class="btn btn-dark btn-sm">기간</span>   &nbsp; 
                                
                    <div id="showframe" class="card"> 
                        <div class="card-header" style="padding:2px;">
                            <div class="d-flex justify-content-center align-items-center">  
                                기간 설정
                            </div>
                        </div> 
                        <div class="card-body">                                        
                            <div class="d-flex justify-content-center align-items-center">      
                                <button type="button" class="btn btn-outline-success btn-sm me-1 change_dateRange" onclick='alldatesearch()'>전체</button>  
                                <button type="button" class="btn btn-outline-primary btn-sm me-1 change_dateRange" onclick='pre_year()'>전년도</button>  
                                <button type="button" class="btn btn-dark btn-sm me-1 change_dateRange" onclick='pre_month()'>전월</button>
                                <button type="button" class="btn btn-dark btn-sm me-1 change_dateRange" onclick='dayBeforeYesterday()'>전전일</button>    
                                <button type="button" class="btn btn-dark btn-sm me-1 change_dateRange" onclick='yesterday()'>전일</button>                         
                                <button type="button" class="btn btn-outline-danger btn-sm me-1 change_dateRange" onclick='this_today()'>오늘</button>
                                <button type="button" class="btn btn-dark btn-sm me-1 change_dateRange" onclick='this_month()'>당월</button>
                                <button type="button" class="btn btn-dark btn-sm me-1 change_dateRange" onclick='this_year()'>당해년도</button> 
                            </div>
                        </div>
                    </div>      

                    <input type="date" id="fromdate" name="fromdate" class="form-control" style="width:110px;" value="<?=$fromdate?>">  &nbsp;   ~ &nbsp;  
                    <input type="date" id="todate" name="todate" class="form-control me-1" style="width:110px;" value="<?=$todate?>">  &nbsp;

                    <!-- 첫 번째 select 문: 수입/지출 구분 -->
                    <select id="inoutsep_select" name="inoutsep_select" class="form-control me-1" style="width:50px;">
                        <option value="">전체</option>
                        <option value="수입" <?= $inoutsep_select === '수입' ? 'selected' : '' ?>>수입</option>
                        <option value="지출" <?= $inoutsep_select === '지출' ? 'selected' : '' ?>>지출</option>
                    </select>

                    <!-- 두 번째 select 문: 항목 선택 -->
                    <select id="content_select" name="content_select" class="form-control me-1" style="width:100px;">
                        <option value="">전체</option>
                        <?php
                        $incomeOptions = [
                            '거래처 수금' => '거래처에서 입금한 금액',
                            '최초 현금 입력' => '금전출납부 시작'
                        ];

                        $expenseOptions = [
                            '급여(인건비)' => '직원 급여',
                            '접대비' => '경조사비용',
                            '통신비' => '전화요금, 인터넷요금',
                            '세금과공과금' => '등록면허세, 취득세, 재산세등 각종세금',
                            '차량유지비' => '유류대, 통행료',
                            '보험료' => '차량보험료, 화재보험료등',
                            '운반비' => '택배운반비외 각종운반비',
                            '소모품비' => '각종 소모품 비용',
                            '수수료비용' => '이체수수료, 등기수수료등',
                            '복리후생비' => '직원 식대외 직원 작업복등',
                            '개발비' => '프로그램 개발비용',
                            '이자비용' => '이자비용',
                            '카드대금' => '카드대금',
                            '통관비' => '통관비',
                            '자재비' => '자재비'
                        ];						
                        $options = array_merge(array_keys($incomeOptions), array_keys($expenseOptions));
                        foreach ($options as $option) {
                            $selected = ($content_select === $option) ? 'selected' : '';
                            echo "<option value=\"$option\" $selected>$option</option>";
                        }
                        ?>
                    </select>
                
                <div class="inputWrap30">
                    <input type="text" id="search" class="form-control" style="width:150px;" name="search" value="<?=$search?>" onKeyPress="if (event.keyCode==13){ enter(); }">
                    <button class="btnClear"></button>
                </div>
                &nbsp;&nbsp;
                <button class="btn btn-outline-dark btn-sm" type="button" id="searchBtn"> <i class="bi bi-search"></i> </button> &nbsp;&nbsp;&nbsp;&nbsp;
                <button id="newBtn" type="button" class="btn btn-dark btn-sm me-2"> <i class="bi bi-pencil-square"></i> 신규 </button>
				<button  type="button" class="btn btn-secondary btn-sm me-1" onclick='location.reload();'> <i class="bi bi-arrow-clockwise"></i> 새로고침 </button>	 
				<button type="button" class="btn btn-dark btn-sm me-2" onclick="generateExcel();" > <i class="bi bi-file-earmark-spreadsheet"></i> 엑셀다운로드 </button>
            </div>
            </div>
			<div class="row w400px m-1 mt-2">
				<table class="table table-bordered">
						<thead class="table-secondary">
							<tr>
						<?php
						$tmp = '  ' . $bankbookOptions[0] . ' (계좌 잔액)  :  ';
						if (isset($finalBalance)) {
							$tmp_balance = number_format($finalBalance);
						}
						?>						
								<th class="text-center" style="width:200px;"> <?=$tmp?> </th>
								<th class="text-end text-primary fw-bold" style="width:100px;"> <?=$tmp_balance?> </th>
							</tr>
						</thead>
				</table>
			</div>					
            <div class="table-responsive">
                <table class="table table-hover" id="myTable">
                    <thead class="table-info">
                        <tr>
                            <th class="text-center" style="width:60px;">번호</th>
                            <th class="text-center" style="width:100px;">등록일자</th>
                            <th class="text-center" style="width:100px;">항목</th>
                            <th class="text-center" style="width:150px;">상세내용</th>
                            <th class="text-center" style="width:100px;">수입</th>
                            <th class="text-center" style="width:100px;">지출</th>
                            <th class="text-center" style="width:100px;">잔액</th>                            
                            <th class="text-center" style="width:200px;">적요</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $start_num = $total_row;
						$counter = 1;
                        $balance = $initialBalance; // 초기 잔액 설정
                        while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
                            include '_row.php';
                            if ($inoutsep === '수입') {
                                $balance += $amount;
                            } else {
                                $balance -= $amount;
                            }
                        ?>
                        <tr onclick="loadForm('update', '<?=$num?>');">
                            <td class="text-center"><?= $counter ?></td>
                            <td class="text-center"><?= $registDate ?></td>                            
							<td class="text-center fw-bold "> <?= $content ?> </td>
                            
                            <td class="text-start"><?= $content_detail ?></td>
                            <?php if ($inoutsep === '수입') : ?>
                                <td class="text-end fw-bold text-primary">
                                    <?= is_numeric($amount) ? number_format($amount) : htmlspecialchars($amount) ?>
                                </td>
                                <td class="text-end"></td>
                            <?php else : ?>
                                <td class="text-end"></td>
                                <td class="text-end fw-bold text-danger">
                                    <?= is_numeric($amount) ? number_format($amount) : htmlspecialchars($amount) ?>
                                </td>
                            <?php endif; ?>
                            <td class="text-end fw-bold"><?= number_format($balance) ?></td>                            
                            <td class="text-start"><?= $memo ?></td>
                        </tr>
                        <?php
                            $start_num--;
                            $counter++;
                        }
                        } catch (PDOException $Exception) {
                            print "오류: ".$Exception->getMessage();
                        }
                        ?>
                    </tbody>
					<tfoot class="table-secondary">
						<tr>
							<th class="text-end" colspan="4"> 합계 &nbsp; </th>
							<th class="text-end" id="totalIncomeAmount"></th>
							<th class="text-end" id="totalExpenseAmount"></th>
							<th class="text-end" id="totalBalanceAmount"></th>
							<th class="text-end"></th>
							
						</tr>
					</tfoot>					
                           
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
});


function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

document.addEventListener('DOMContentLoaded', function() {
    const initialBalance = <?= json_encode($initialBalance) ?>;
    const finalBalance = <?= json_encode($finalBalance) ?>;
    
    $(document).ready(function() {
        dataTable = $('#myTable').DataTable({
            "paging": true,
            "ordering": true,
            "searching": true,
            "pageLength": 2000,
            "lengthMenu": [2000],
            "language": {
                "lengthMenu": "Show _MENU_ entries",
                "search": "Live Search:"
            },
            // "order": [[0, 'desc']],
            "dom": 't<"bottom"ip>',
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(), data;

                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                totalIncomeAmount = api.column(4, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);
                totalExpenseAmount = api.column(5, { page: 'current' }).data().reduce(function (a, b) { return intVal(a) + intVal(b); }, 0);

                $(api.column(4).footer()).html(numberWithCommas(totalIncomeAmount));
                $(api.column(5).footer()).html(numberWithCommas(totalExpenseAmount));
                $(api.column(6).footer()).html(numberWithCommas(finalBalance));
            }
        });
    });
});
</script>

<script>

let isSaving = false;
var ajaxRequest = null;

document.addEventListener('DOMContentLoaded', function() {
    
    $("#newBtn").on("click", function() {
        loadForm('insert');
    });

    $("#searchBtn").on("click", function() {
        $("#board_form").submit();
    });


});



function loadForm(mode, num = null) {
    if (num == null) {
        $("#mode").val('insert');
    } else {
        $("#mode").val('update');
        $("#num").val(num);
    }

    if (ajaxRequest !== null) {
        ajaxRequest.abort();
    }
    ajaxRequest = $.ajax({
        type: "POST",
        url: "fetch_modal.php",
        data: { mode: mode, num: num },
        dataType: "html",
        success: function(response) {
            document.querySelector(".modal-body .custom-card").innerHTML = response;			

            $("#myModal").show();
                        
			const expenseOptions = {
				'급여(인건비)': '직원 급여',
				'접대비': '경조사비용',
				'통신비': '전화요금, 인터넷요금',
				'세금과공과금': '등록면허세, 취득세, 재산세등 각종세금',
				'차량유지비': '유류대, 통행료',
				'보험료': '차량보험료, 화재보험료등',
				'운반비': '택배운반비외 각종운반비',
				'소모품비': '각종 소모품 비용',
				'수수료비용': '이체수수료, 등기수수료등',
				'복리후생비': '직원 식대외 직원 작업복등',
				'개발비': '프로그램 개발비용',
				'이자비용': '이자비용',
				'카드대금': '카드대금',
				'통관비': '통관비',
				'자재비': '자재비',
				'기계장치' : '기계구입'
			};

			const incomeOptions = {
				'거래처 수금': '거래처에서 입금한 금액',
				'최초 현금 입력': '금전출납부 시작'
			};
			
			function updateDescription() {
				const contentSelect = document.getElementById('content');
				const descriptionDiv = document.getElementById('content_description');
				if (contentSelect && descriptionDiv) {
					const selectedValue = contentSelect.value;
					const descriptions = document.querySelector('input[name="inoutsep"]:checked').value === '수입' ? incomeOptions : expenseOptions;
					descriptionDiv.innerText = descriptions[selectedValue] || '';
				}
			}

			function updateContentOptions() {
				const contentSelect = document.getElementById('content');
				if (contentSelect) {
					contentSelect.innerHTML = '';

					const options = document.querySelector('input[name="inoutsep"]:checked').value === '수입' ? incomeOptions : expenseOptions;

					for (const [value, text] of Object.entries(options)) {
						const option = document.createElement('option');
						option.value = value;
						option.text = value;
						contentSelect.appendChild(option);
					}

					updateDescription();
				}
			}
			
            // 추가된 스크립트를 실행하도록 보장
            // updateContentOptions();					
            
            $(document).on("click", "#closeBtn", function() {
                $("#myModal").hide();
            });


    $(document).on("click", "#saveBtn", function() {
        // if (isSaving) return;
        // isSaving = true;

            // AJAX 요청을 보냄
        if (ajaxRequest !== null) {
            ajaxRequest.abort();
        }
        ajaxRequest = $.ajax({
                url: "/account/insert.php",
                type: "post",
                data: {
                    mode: $("#mode").val(),
                    num: $("#num").val(),
                    update_log: $("#update_log").val(),
                    registDate: $("#registDate").val(),
                    inoutsep: $("input[name='inoutsep']:checked").val(),
                    content: $("#content").val(),
                    amount: $("#amount").val(),
                    memo: $("#memo").val(),
                    first_writer: $("#first_writer").val(),
                    content_detail: $("#content_detail").val(),
                    bankbook: $("#bankbook").val()
                },
                dataType: "json",
                success: function(response) {
                    Toastify({
                        text: "저장 완료",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "center",
                        backgroundColor: "#4fbe87",
                    }).showToast();
                                    
                    setTimeout(function() {
                        $("#myModal").hide();
                        location.reload();
                    }, 1500); // 1.5초 후 실행
                    
                },
                error: function(jqxhr, status, error) {
                    console.log("AJAX Error: ", status, error);
                    // isSaving = false;
                }
            });
        });

        $(document).on("click", "#deleteBtn", function() {    
            var level = '<?= $_SESSION["level"] ?>';

            if (level !== '1') {
                Swal.fire({
                    title: '삭제불가',
                    text: "관리자만 삭제 가능합니다.",
                    icon: 'error',
                    confirmButtonText: '확인'
                });
                return;
            }

            Swal.fire({
                title: '자료 삭제',
                text: "삭제는 신중! 정말 삭제하시겠습니까?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '삭제',
                cancelButtonText: '취소'
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#mode").val('delete');
                    var formData = $("#board_form").serialize();

                    $.ajax({
                        url: "/account/insert.php",
                        type: "post",
                        data: formData,
                        success: function(response) {
                            Toastify({
                                text: "파일 삭제완료",
                                duration: 2000,
                                close: true,
                                gravity: "top",
                                position: "center",
                                backgroundColor: "#4fbe87",
                            }).showToast();

                            $("#myModal").hide();
                            location.reload();
                        },
                        error: function(jqxhr, status, error) {
                            console.log(jqxhr, status, error);
                        }
                    });
                }
            });
        });

        
        $(".close").on("click", function() {
            $("#myModal").hide();
        });

        // 항목 선택 변경 시 설명 업데이트
        $(document).on("change", "#content", updateDescription);

        $(document).on("change", "input[name='inoutsep']", updateContentOptions);

        },
        error: function(jqxhr, status, error) {
            console.log("AJAX error in loadForm:", status, error);
        }
    });
}

// 페이지 로딩
$(document).ready(function(){    
    var loader = document.getElementById('loadingOverlay');
    loader.style.display = 'none';
});
</script>

<script>
function generateExcel() {
    var table = document.getElementById('myTable');
    var rows = table.getElementsByTagName('tr');
    var data = [];

    // 각 행을 반복하여 데이터 수집
    for (var i = 1; i < rows.length; i++) { // 헤더 행을 건너뜀
        var cells = rows[i].getElementsByTagName('td');
        var rowData = {};
        rowData['number'] = cells[0]?.innerText || '';
        rowData['registDate'] = cells[1]?.innerText || '';
        rowData['content'] = cells[2]?.innerText || '';
        rowData['contentDetail'] = cells[3]?.innerText || '';
        rowData['income'] = cells[4]?.innerText || '';
        rowData['expense'] = cells[5]?.innerText || '';
        rowData['balance'] = cells[6]?.innerText || '';
        rowData['memo'] = cells[7]?.innerText || '';
        
        data.push(rowData);
    }

    // saveExcel.php에 데이터 전송
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "order_saveExcel.php", true);
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            if (xhr.status == 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        console.log('Excel file generated successfully.');
                        // 다운로드 스크립트로 리디렉션
                        window.location.href = 'downloadExcel.php?filename=' + encodeURIComponent(response.filename.split('/').pop());
                    } else {
                        console.log('Failed to generate Excel file: ' + response.message);
                    }
                } catch (e) {
                    console.log('Error parsing response: ' + e.message + '\nResponse text: ' + xhr.responseText);
                }
            } else {
                console.log('Failed to generate Excel file: Server returned status ' + xhr.status);
            }
        }
    };
    xhr.send(JSON.stringify(data));
}
</script>


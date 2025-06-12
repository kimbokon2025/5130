<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

// 첫 화면 표시 문구
$title_message = '거래처 원장'; 
$tablename = 'motor'; 

// 견적서, 거래명세서, 총거래원장등 설정 $item으로 설정하면 됨.
$item_title ='거래처 원장';

include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php';   
?>
<title> <?= $title_message ?> </title>
<style> 
table, th, td {
	border: 1px solid black !important; /* Bold border */
	font-size: 12px !important;
	white-space: nowrap;
}

/* Add background color for date rows */
.date-row {
	background-color: #f0f0f0!important; /* Light gray background */        
}   
.date-row-date {
	background-color: #f0f0f0!important; /* Light gray background */        
	color: blue !important;
}    

@media print {
	body {
		width: 210mm; /* A4 width */
		height: 297mm; /* A4 height */
		margin: 0; /* Remove default margin */
		font-size: 10pt; /* Font size for printing */
	}
	.table {
		width: 100%; /* Full width tables */
		table-layout: fixed; /* Uniform column sizing */
		border-collapse: collapse; /* Ensure borders collapse */
	}
	.table th, .table td {
		padding: 1px; /* Reduce padding */
		border: 1px solid #ddd; /* Ensure borders are visible */
	}
	.text-center {
		text-align: center; /* Maintain center alignment */
	}

	/* Prevent table row splitting */
	.table tr {
		page-break-inside: avoid; /* Prevent breaking inside rows */
		page-break-after: avoid; /* Allow breaking after rows */
	}
	.table thead {
		display: table-header-group; /* Ensure table headers are repeated */
	}
	.table tbody {
		display: table-row-group; /* Ensure table rows are grouped */
	}
	.table tfoot {
		display: table-footer-group; /* Ensure table footers are repeated */
	}

	/* Add top and bottom margins to each page */
	.table tbody:before,
	.table tbody:after {
		content: "";
		display: table-row;
		height: 5mm; /* Adjust as needed for top and bottom margins */
	}

	/* Remove border from the before and after elements */
	.table tbody:before td,
	.table tbody:after td {
		border: none; /* Remove borders */
	}

	/* Adjust the border of the last row on the page */
	.table tbody tr:last-child td {
		border-bottom: none; /* Remove the bottom border */
		border-left: none; /* Remove the bottom border */
		border-right: none; /* Remove the bottom border */
		border-top: none; /* Remove the bottom border */
	}

	/* Prevent border at the connection of two pages */
	.table tbody tr:last-child td:first-child {
		border-bottom: none; /* Remove the bottom border */
		border-left: none; /* Remove the bottom border */
		border-right: none; /* Remove the bottom border */
		border-top: none; /* Remove the bottom border */
	}
	.table tbody tr:last-child td:last-child {
		border-bottom: none; /* Remove the bottom border */
		border-left: none; /* Remove the bottom border */
		border-right: none; /* Remove the bottom border */
		border-top: none; /* Remove the bottom border */
	}
}
</style>
</head>
<body>
<?php

$num = isset($_REQUEST['num']) ? $_REQUEST['num'] : '';  
$fromdate = isset($_REQUEST['fromdate']) ? $_REQUEST['fromdate'] : '';  
$todate = isset($_REQUEST['todate']) ? $_REQUEST['todate'] : '';  
$secondordnum = $num;

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

try {
    // Fetch customer details from phonebook
    $sql = "SELECT * FROM " . $DB . ".phonebook WHERE secondordnum = ?";
    $stmh = $pdo->prepare($sql);
    $stmh->bindValue(1, $num, PDO::PARAM_STR);
    $stmh->execute();
    $customer = $stmh->fetch(PDO::FETCH_ASSOC);
    
    if (!$customer) {
        throw new Exception("거래처 정보를 찾을 수 없습니다.");
    }

    // Fetch sales details from output + output_extra
    $sql = "SELECT o.*, e.ET_total, e.estimateList, e.estimateSlatList, e.etcList, e.screen_unapprovedList, e.slat_unapprovedList, e.motorList, e.bendList, e.controllerList, e.accountList, e.ET_unapproved
            FROM " . $DB . ".output o
            LEFT JOIN " . $DB . ".output_extra e ON o.num = e.parent_num
            WHERE o.secondordnum = ? AND o.outdate BETWEEN ? AND ? AND (o.is_deleted IS NULL OR o.is_deleted = 0)
            ORDER BY o.outdate ASC";
    $stmh = $pdo->prepare($sql);
    $stmh->bindValue(1, $num, PDO::PARAM_STR);
    $stmh->bindValue(2, $fromdate, PDO::PARAM_STR);
    $stmh->bindValue(3, $todate, PDO::PARAM_STR);
    $stmh->execute();
    $rows = $stmh->fetchAll(PDO::FETCH_ASSOC);

    // Fetch payments from account table
    $sql = "SELECT registDate, amount FROM " . $DB . ".account 
        WHERE secondordnum = ? 
        AND registDate BETWEEN ? AND ? 
        AND (is_deleted IS NULL OR is_deleted = 0)
        AND content = '거래처 수금' 
        ORDER BY registDate ASC";
	
    $stmh = $pdo->prepare($sql);
    $stmh->bindValue(1, $num, PDO::PARAM_STR);
    $stmh->bindValue(2, $fromdate, PDO::PARAM_STR);
    $stmh->bindValue(3, $todate, PDO::PARAM_STR);
    $stmh->execute();
    $payments = $stmh->fetchAll(PDO::FETCH_ASSOC);
	    
    // 다른 거래처는 기본적으로 마감 기준일 -1일
    $lastMonthEnd = date("Y-m-t", strtotime($fromdate . " -1 days"));
       
		
// echo '<pre>';
// print_r($lastMonthEnd);
// echo '</pre>';

    // 이월잔액 계산
    $salesBeforeSql = "
        SELECT SUM(COALESCE(e.ET_total, 0)) AS total_sales 
        FROM " . $DB . ".output o
        LEFT JOIN " . $DB . ".output_extra e ON o.num = e.parent_num
        WHERE o.secondordnum = :secondordnum AND o.outdate <= :lastMonthEnd AND (o.is_deleted IS NULL OR o.is_deleted = 0)
    ";
    $paymentBeforeSql = "
        SELECT SUM(CAST(REPLACE(amount, ',', '') AS SIGNED)) AS total_payment 
        FROM " . $DB . ".account
        WHERE secondordnum = :secondordnum AND registDate <= :lastMonthEnd AND (is_deleted IS NULL OR is_deleted = 0) AND content = '거래처 수금'
    ";

    $salesBeforeStmt = $pdo->prepare($salesBeforeSql);
    $salesBeforeStmt->execute([':secondordnum' => $secondordnum, ':lastMonthEnd' => $lastMonthEnd]);
    $salesBeforeData = $salesBeforeStmt->fetch(PDO::FETCH_ASSOC);

    $paymentBeforeStmt = $pdo->prepare($paymentBeforeSql);
    $paymentBeforeStmt->execute([':secondordnum' => $secondordnum, ':lastMonthEnd' => $lastMonthEnd]);
    $paymentBeforeData = $paymentBeforeStmt->fetch(PDO::FETCH_ASSOC);

    $initialSales = isset($salesBeforeData['total_sales']) ? (float)$salesBeforeData['total_sales'] : 0;
    $initialPayments = isset($paymentBeforeData['total_payment']) ? (float)$paymentBeforeData['total_payment'] : 0;

    // Calculate the initial balance 
    $initialBalance = intval(round($initialSales, 2) - round($initialPayments));
	
	// 마지막 자릿수가 1인지 확인
	if (floatval($initialBalance) % 10 === 1) {
		// 마지막 자릿수를 제거 (정수로 처리)
		$initialBalance = floor($initialBalance / 10);
	}	

} catch (Exception $e) {
    echo "오류: " . $e->getMessage();
}

?>

<div class="container mt-2">
    <div class="d-flex align-items-center justify-content-end mt-1 m-2">        
		<i class="bi bi-info-circle-fill"></i> <?=$secondordnum?> &nbsp;
        <button type="button" class="btn btn-dark btn-sm me-1" onclick="location.reload();"> <i class="bi bi-arrow-clockwise"></i> </button> 
        <button class="btn btn-dark btn-sm me-1" onclick="generatePDF()"> PDF 저장 </button>
        <button class="btn btn-dark btn-sm me-1" onclick="sendmail();"> <i class="bi bi-envelope-arrow-up"></i> 전송 </button>
        <button class="btn btn-secondary btn-sm" onclick="self.close();"> <i class="bi bi-x-lg"></i> 닫기 </button>&nbsp;
    </div>
</div>
 <div id="content-to-print">        
    <div class="container" >        
            <div class="d-flex align-items-center justify-content-center mb-3">
                <h2><?= $customer['vendor_name'] ?> 관리대장</h2> 
                <h5>(거래명세서별)</h5>
            </div>
            <div class="row align-items-center justify-content-center mb-1 mt-2">
                <div class="col-sm-6 text-start"> 
                    회사명 : (주) 경동기업 / 담당 : 정미영 차장
                </div>
                <div class="col-sm-6 text-end"> 
                    <?= $fromdate ?> ~ <?= $todate ?>
                </div>
            </div>

        <div class="d-flex align-items-center justify-content-center ">
                <table class="table" style="border-collapse: collapse;">
                    <thead>
                        <tr>             
                            <th class="text-start fw-bold" style="width:20%;">사업자등록번호</th>
                            <th class="text-start fw-bold text-primary" style="width:30%;"><?= $customer['vendor_code'] ?></th>            
                            <th class="text-start fw-bold">대표자</th>
                            <th class="text-start"><?= $customer['representative_name'] ?></th>
                        </tr>
                        <tr>
                            <th class="text-start fw-bold">여신한도</th>
                            <th class="text-start">0</th>
                            <th class="text-start fw-bold">전화</th>
                            <th class="text-start"><?= $customer['phone'] ?> (모바일: <?= $customer['mobile'] ?>)</th>                
                        </tr>
                        <tr>
                            <th class="text-start fw-bold">Email</th>
                            <th class="text-start"><?= $customer['email'] ?></th>
                            <th class="text-start fw-bold">Fax</th>
                            <th class="text-start"><?= $customer['fax'] ?></th>
                        </tr>
                        <tr>                
                            <th class="text-start fw-bold">주소</th>
                            <th colspan="3" class="text-start"><?= $customer['address'] ?></th>                
                        </tr>
                        <tr>
                            <th class="text-start fw-bold">적요</th>
                            <th colspan="3" class="text-start"></th>                
                        </tr>
                    </thead>
                </table>
        </div>

        <div class="d-flex align-items-center justify-content-center ">
            <table class="table" style="border-collapse: collapse;">
                <thead>
                    <tr>
                        <th colspan="5" class="text-center">판매/수금내역</th>
                    </tr>
                    <tr>
                        <th class="text-center">일자</th>
                        <th class="text-center">적요</th>
                        <th class="text-center">판매</th>
                        <th class="text-center">수금</th>
                        <th class="text-center">잔액</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2" class="text-center  fw-bold text-primary">이월잔액</td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-end fw-bold text-primary"><?= number_format($initialBalance) ?></td>
                    </tr>
                    <?php
                    $total_balance = $initialBalance; // 초기 잔액 설정
                    $monthly_sales = [];
                    $current_month = '';
                    $grand_total = 0;
                    $sale_count = 0;
                    $total_payment_sum = 0; // 수금 합계 초기화

                    // Merge sales and payments into one array
                    $events = [];

                    foreach ($rows as $row) {
                        $events[] = [
                            'date' => $row['outdate'],
                            'type' => 'sale',
                            'data' => $row
                        ];
                    }
					// 수금부분
					foreach ($payments as $payment) {
						$events[] = [
							'date' => $payment['registDate'],  // 올바른 키 이름 사용
							'type' => 'payment',
							'data' => [
								'amount' => $payment['amount']  // 올바른 키 이름 사용
							]
						];
					}

                    // Sort events by date
                    usort($events, function($a, $b) {
                        return strcmp($a['date'], $b['date']);
                    }); 

                // 출고예정일 첫번째 요소 저장변수 
                $Last_deadline = '';                
                 foreach ($events as $event) {
                        $event_date = $event['date'];
                        $event_type = $event['type'];
                        $month = date('Y/m', strtotime($event_date));                        

					if ($event_type == 'payment') {
						$payment = $event['data'];
						$payment_date = $event_date; // 날짜는 이벤트의 날짜를 사용
						$payment_amount = (float)str_replace(',', '', $payment['amount']); // payment 대신 amount 키 사용
						$total_balance -= $payment_amount;
						$total_payment_sum += $payment_amount; // 수금 합계에 추가

						if (!isset($monthly_sales[$month])) {
							$monthly_sales[$month] = 0;
						}

                            echo "<tr class='date-row'>
                                <td class='date-row-date text-start'>{$payment_date}</td>
                                <td class='date-row text-start'>입금</td>
                                <td class='date-row text-end'></td>
                                <td class='date-row text-end'>" . number_format($payment_amount) . "</td>
                                <td class='date-row text-end'>" . number_format($total_balance) . "</td>
                            </tr>";
                        }

                        if ($current_month !== $month) {
                            if ($current_month !== '') {
                                echo "<tr>
                                    <td colspan='2' class='text-center fw-bold'>{$current_month} 계</td>
                                    <td class='text-end'>" . number_format($monthly_sales[$current_month]) . "</td>
                                    <td class='text-start'></td>
                                    <td class='text-start'></td>
                                </tr>";
                            }
                            $current_month = $month;
                        }

                    if ($event_type == 'sale') {
                        $row = $event['data'];
                        $outdate = $row['outdate'];
                        $workplacename = isset($row['workplacename']) ? $row['workplacename'] : '';
                        $amount = (float)$row['ET_total'];
                        $total_balance += round($amount);
                        $sale_count++;
                        if (!isset($monthly_sales[$month])) {
                            $monthly_sales[$month] = 0;
                        }
                        $monthly_sales[$month] += $amount;
                        $grand_total += $amount;
                        echo "<tr class='date-row' onclick=\"redirectToView('{$row['num']}', '{$tablename}')\">
                            <td class='date-row-date text-start'>{$outdate}</td>
                            <td class='date-row text-start'>{$workplacename}</td>
                            <td class='date-row text-end'>" . number_format($amount) . "</td>
                            <td class='date-row text-start'></td>
                            <td class='date-row text-end'>" . number_format($total_balance) . "</td>
                        </tr>";

                        // 상세내역: estimateList, estimateSlatList, etcList, screen_unapprovedList, slat_unapprovedList, motorList, bendList, controllerList, accountList
                        $detailColumns = [
                            'estimateList', 'estimateSlatList', 'etcList', 'screen_unapprovedList', 'slat_unapprovedList', 'motorList', 'bendList', 'controllerList', 'accountList'
                        ];
                        foreach ($detailColumns as $col) {
                            $list = isset($row[$col]) ? json_decode($row[$col], true) : [];
                            if (is_array($list)) {
                                foreach ($list as $item) {
                                    // 품목명, 수량, 단가, 금액 등 컬럼명은 데이터 구조에 따라 다를 수 있음
                                    $itemName = isset($item['col1']) ? $item['col1'] : '';
                                    $spec = isset($item['col2']) ? $item['col2'] : '';
                                    $qty = isset($item['col4']) ? $item['col4'] : (isset($item['col2']) ? $item['col2'] : 1);
                                    $unit = isset($item['col5']) ? str_replace(',', '', $item['col5']) : 0;
                                    $amountDetail = floatval($qty) * floatval($unit);
                                    if ($itemName && $amountDetail > 0) {
                                        echo "<tr>
                                            <td class='text-start'></td>
                                            <td class='text-start'>{$itemName} {$spec}</td>
                                            <td class='text-end'>" . number_format($amountDetail) . "</td>
                                            <td class='text-start'></td>
                                            <td class='text-start'></td>
                                        </tr>";
                                    }
                                }
                            }
                        }
                        // 비인정 금액(ET_unapproved) 표시
                        if (isset($row['ET_unapproved']) && floatval($row['ET_unapproved']) > 0) {
                            echo "<tr>
                                <td class='text-start'></td>
                                <td class='text-start text-danger'>비인정 금액</td>
                                <td class='text-end text-danger'>-" . number_format($row['ET_unapproved']) . "</td>
                                <td class='text-start'></td>
                                <td class='text-start'></td>
                            </tr>";
                        }
                    }
                }

                    if ($current_month !== '') {
						
						// 마지막 1원 정리
						if (round($monthly_sales[$current_month]) % 10 === 1) {
							$monthly_sales[$current_month] -= 1;  // 1을 빼서 마지막 자리를 0으로 만듭니다.
						}	
							
                        echo "<tr>
                            <td colspan='2' class='text-center fw-bold date-row'>{$current_month} 계 <span style='font-size:9px;'> (VAT 포함) </span></td>
                            <td class='text-end date-row  fw-bold'>" . number_format($monthly_sales[$current_month]) . "</td>
                            <td class='text-start date-row'></td>
                            <td class='text-start date-row'></td>
                        </tr>";
                    }
					
								
					if (round($grand_total) % 10 === 1) {
						$grand_total -= 1;  // 1을 빼서 마지막 자리를 0으로 만듭니다.
					}

					if (round($total_balance) % 10 === 1) {
						$total_balance -= 1;  // 1을 빼서 마지막 자리를 0으로 만듭니다.
					}					
					
                    ?>
                    <tr class="date-row" >
                        <td colspan="2" class="text-center fw-bold date-row" >총 <?= $sale_count ?>건 누계 <span style='font-size:11px;'> (VAT 포함) </span></td>
                        <td class="text-end date-row fw-bold"><?= number_format($grand_total) ?></td>
                        <td class="text-end date-row fw-bold"><?= number_format($total_payment_sum) ?></td> <!-- 수금 합계 표시 -->
                        <td class="text-end date-row fw-bold"><?= number_format($total_balance) ?></td> <!-- 잔액 합계 표시 -->
                    </tr>
                </tbody>
            </table>
          </div>
    </div>
</div>


<!-- 페이지로딩 -->
<script>
$(document).ready(function(){    
    var loader = document.getElementById('loadingOverlay');
    loader.style.display = 'none';
});

function generatePDF() {
    var workplace = '<?= $customer['vendor_name'] ?>';
    var deadline = '<?php echo $Last_deadline; ?>';
    var deadlineDate = new Date(deadline);
    var formattedDate = "(" + String(deadlineDate.getFullYear()).slice(-2) + "." + ("0" + (deadlineDate.getMonth() + 1)).slice(-2) + "." + ("0" + deadlineDate.getDate()).slice(-2) + ")";
    var result = '경동기업_거래원장(' + workplace + ')' + formattedDate + '.pdf';    
    
    var element = document.getElementById('content-to-print');
    var opt = {
        margin: [15, 8, 17, 8], // Top, right, bottom, left margins
        filename: result,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: { mode: [''] }
    };
    html2pdf().from(element).set(opt).save();
}


function redirectToView(num, tablename) {
    var url = "/output/write_form.php?mode=view&num=" + num + "&tablename=" + tablename;
    customPopup(url, '수주내역', 1850, 900);
}


ajaxRequest = null;


function generatePDF_server(callback) {
    var workplace = '<?= $customer['vendor_name'] ?>';
    var item = '<?php echo $item_title; ?>';    
    var deadline = '<?php echo $Last_deadline; ?>';
    var deadlineDate = new Date(deadline);
    var formattedDate = "(" + String(deadlineDate.getFullYear()).slice(-2) + "." + ("0" + (deadlineDate.getMonth() + 1)).slice(-2) + "." + ("0" + deadlineDate.getDate()).slice(-2) + ")";
    var result = 'DH ' + item +'(' + workplace + ')' + formattedDate + '.pdf';        

    var element = document.getElementById('content-to-print');
    var opt = {
        margin: [15, 8, 17, 8], // Top, right, bottom, left margins
        filename: result,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: { mode: [''] }
    };

    html2pdf().from(element).set(opt).output('datauristring').then(function (pdfDataUri) {
        var pdfBase64 = pdfDataUri.split(',')[1]; // Base64 인코딩된 PDF 데이터 추출
        var formData = new FormData();
        formData.append('pdf', pdfBase64);
        formData.append('filename', result);

        $.ajax({
            type: 'POST',
            url: 'save_pdf.php', // PDF 파일을 저장하는 PHP 파일
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var res = JSON.parse(response);
                if (callback) {
                    callback(res.filename); // 서버에 저장된 파일 경로를 콜백으로 전달
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('Error', 'PDF 저장에 실패했습니다.', 'error');
            }
        });
    });
}

function sendmail() {
    var secondordnum = '<?php echo $secondordnum; ?>'; // 서버에서 가져온 값
    var item = '<?php echo $item_title; ?>'; 
    console.log('secondordnum', secondordnum);
    
    if (typeof ajaxRequest !== 'undefined' && ajaxRequest !== null) {
        ajaxRequest.abort();
    }
    
    ajaxRequest = $.ajax({
        type: 'POST',
        url: 'get_companyCode.php', // 파일 이름 수정
        data: { secondordnum: secondordnum },
        dataType: 'json',
        success: function(response) {
            console.log('response : ', response);
            if (response.error) {
                Swal.fire('Error', response.error, 'error');
            } else {
                var email = response.email;
                var vendorName = response.vendor_name;

                Swal.fire({
                    title: '이메일 보내기',
                    text: '거래처(' + vendorName + ') Email : (' + email + ') 이메일 전송 하시겠습니까?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '보내기',
                    cancelButtonText: '취소',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        generatePDF_server(function(filename) {
                            sendEmail(email, vendorName, item, filename);
                        });
                    }
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', '전송중 오류가 발생했습니다.', 'error');
        }
    });
}

function sendEmail(recipientEmail,vendorName, item, filename) {
    // 이메일 전송 코드 작성 (예: PHP를 호출하여 이메일 전송)
    if (typeof ajaxRequest !== 'undefined' && ajaxRequest !== null) {
        ajaxRequest.abort();
    }
    var deadline = '<?php echo $Last_deadline; ?>';
    var deadlineDate = new Date(deadline);
    var formattedDate = "(" + String(deadlineDate.getFullYear()).slice(-2) + "." + ("0" + (deadlineDate.getMonth() + 1)).slice(-2) + "." + ("0" + deadlineDate.getDate()).slice(-2) + ")";
    
    ajaxRequest = $.ajax({
        type: 'POST',
        url: 'send_email.php', // 이메일 전송을 처리하는 PHP 파일
        data: { email: recipientEmail, vendorName : vendorName, filename: filename, item : item, formattedDate :formattedDate },
        success: function(response) {
            console.log(response);
            Swal.fire('Success', '정상적으로 전송되었습니다.', 'success'); 
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', '전송에 실패했습니다. 확인바랍니다.', 'error'); 
        }
    });
}
</script> 

</body>
</html>
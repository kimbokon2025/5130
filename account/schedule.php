<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");    

if(!isset($_SESSION["level"]) ||intval($_SESSION["level"]) > 7) {
          /*   alert("관리자 승인이 필요합니다."); */
		 sleep(1);
	     header("Location:" . $WebSite . "login/login_form.php"); 
         exit;
}  

$today = date("Y-m-d");
require_once($_SERVER['DOCUMENT_ROOT'] . "/load_header.php");
$titlemessage = '회계 일정관리';

$version = '1';

?>
   
<script src="https://dh2024.co.kr/js/todolist_account.js?v=<?=$version?>"></script> 
<style>
    .editable-item {
        cursor: pointer;
    }
</style>

<title> <?=$titlemessage?>  </title>   
<!-- Favicon-->	
<link rel="icon" type="image/x-icon" href="favicon.ico">   <!-- 33 x 33 -->
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">    <!-- 144 x 144 -->
<link rel="apple-touch-icon" type="image/x-icon" href="favicon.ico">
 
</head>
 
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/myheader.php'); ?>

<form id="board_form" name="board_form" method="post" enctype="multipart/form-data" >	

<input type="hidden" id="num" name="num" value="<?= isset($num) ? $num : '' ?>" >
<input type="hidden" id="mode" name="mode" value="<?= isset($mode) ? $mode : '' ?>" >

<!-- todo모달 컨테이너 -->
<div class="container-fluid">
	<!-- Modal -->
	<div id="todoModal" class="modal">
		<div class="modal-content"  style="width:800px;">
			<div class="modal-header">
				<span class="modal-title">회계일정</span>
				<span class="todo-close">&times;</span>
			</div>
			<div class="modal-body">
				<div class="custom-card"></div>
			</div>
		</div>
	</div>
</div>
<!-- 매월 -->
<div class="container-fluid">
	<!-- Modal -->
	<div id="todoModalMonthly" class="modal">
		<div class="modal-content"  style="width:800px;">
			<div class="modal-header">
				<span class="modal-title">월별 고정 회계일정</span>
				<span class="todo-close">&times;</span>
			</div>
			<div class="modal-body">
				<div class="custom-card"></div>
			</div>
		</div>
	</div>
</div>

<?php // include $_SERVER['DOCUMENT_ROOT'] . '/mymodal.php'; ?>  

<div class="container-fluid">     

<!-- todo Calendar -->
<?php if($chkMobile==false) { ?>
    <div class="container">     
<?php } else { ?>
    <div class="container-fluid">      
<?php } ?>  

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

try {
    $sql = "SELECT num, specialday, yearlyspecialday, title FROM todos_monthly WHERE is_deleted IS NULL ORDER BY specialday ASC";
    $stmh = $pdo->prepare($sql);
    $stmh->execute();
    $data = $stmh->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $Exception) {
    echo "오류: " . $Exception->getMessage();
    exit;
}
?>

<div class="card mt-1">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th colspan="4">연간 및 월간 일정 (수정시 클릭하세요) </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 날짜별 데이터 그룹화 (초기화 필수)
                    $groupedData = ['yearly' => [], 'monthly' => []];

                    foreach ($data as $row) {
                        $title = htmlspecialchars($row['title']);
                        $num = isset($row['num']) ? htmlspecialchars($row['num']) : '';

                        // 연간 일정 그룹화
                        if (!empty($row['yearlyspecialday'])) {
                            $yearlyspecialday = htmlspecialchars($row['yearlyspecialday']);
                            $groupedData['yearly'][$yearlyspecialday][] = [
                                'title' => $title,
                                'num' => $num
                            ];
                        }

                        // 월간 일정 그룹화
                        if (!empty($row['specialday'])) {
                            $specialday = htmlspecialchars($row['specialday']);
                            $groupedData['monthly'][$specialday][] = [
                                'title' => $title,
                                'num' => $num
                            ];
                        }
                    }

                    // 테이블 출력
                    $counter = 0;
                    echo '<tr>'; // 첫 번째 행 시작

                    // 연간 일정 출력 (isset 체크)
                    if (isset($groupedData['yearly']) && count($groupedData['yearly']) > 0) {
                        foreach ($groupedData['yearly'] as $date => $items) {
                            list($month, $day) = explode('/', $date);
                            echo '<td>';
                            echo '<span class="badge bg-primary">매년 ' . $month . '월 ' . $day . '일</span><br>';
                            foreach ($items as $item) {
                                echo '<span class="editable-item" data-num="' . $item['num'] . '" style="cursor: pointer;">' . $item['title'] . '</span><br>';
                            }
                            echo '</td>';
                            $counter++;

                            if ($counter % 4 == 0) {
                                echo '</tr><tr>'; // 4번째 항목 후 새 행 시작
                            }
                        }
                    }

                    // 월간 일정 출력 (isset 체크)
                    if (isset($groupedData['monthly']) && count($groupedData['monthly']) > 0) {
                        foreach ($groupedData['monthly'] as $day => $items) {
                            echo '<td>';
                            echo '<span class="text-primary fw-bold">매월 ' . $day . '일</span><br>';
                            foreach ($items as $item) {
                                echo '<span class="editable-item" data-num="' . $item['num'] . '" style="cursor: pointer;">' . $item['title'] . '</span><br>';
                            }
                            echo '</td>';
                            $counter++;

                            if ($counter % 4 == 0) {
                                echo '</tr><tr>'; // 4번째 항목 후 새 행 시작
                            }
                        }
                    }

                    // 마지막 줄을 채우기 위해 빈 칸 추가
                    if ($counter % 4 !== 0) {
                        for ($i = 0; $i < (4 - $counter % 4); $i++) {
                            echo '<td></td>';
                        }
                        echo '</tr>'; // 마지막 행 닫기
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="card mt-1">
<div class="card-body">
    <div class="row d-flex ">
        <!-- Calendar Controls -->
        <div class="col-sm-3">
		  <div class="d-flex justify-content-start align-items-center ">            
			<h6> <회계부분 상세일정> </h6> &nbsp; &nbsp; (매년/매월)&nbsp;
			<button type='button' class='btn btn-danger btn-sm add-row me-2' data-table='acountTable' style='margin-right: 5px;'>+</button>
		  </div>
        </div>
        <div class="col-sm-6">
            <div class="d-flex justify-content-center align-items-center mb-2">
                <button type="button" id="todo-prev-month_account" class="btn btn-danger  btn-sm me-2"> <i class="bi bi-arrow-left"></i>  </button>
                 <span id="todo-current-period" class="text-dark fs-6 me-2"></span>
                <button  type="button" id="todo-next-month_account" class="btn btn-danger btn-sm me-2">  <i class="bi bi-arrow-right"></i> </button>
                <button  type="button" id="todo-current-month_account" class="btn btn-outline-danger fw-bold btn-sm me-5"> <?php echo date("m",time()); ?> 월</button>                
				<button type="button" class="btn btn-dark btn-sm me-1" onclick='location.reload()'>  <i class="bi bi-arrow-clockwise"></i> </button>      
            </div>        
        </div>       
        <div class="col-sm-3"> </div>
        </div>        
        <div id="todo-calendar-container_account" class="d-flex p-1 justify-content-center"></div>
    </div>
</div>
</div>

<div class="container-fluid">     
<?php include $_SERVER['DOCUMENT_ROOT'] .'/footer.php'; ?>
</div> 
</div>
</div> <!-- container-fulid end -->
</form> 

<script>
// 페이지 로딩
$(document).ready(function(){	
    var loader = document.getElementById('loadingOverlay');
    loader.style.display = 'none';
});

alreadyShown = getCookie("notificationShown");   

var intervalId; // 인터벌 식별자를 저장할 변수
	
function closeMsg(){
	var dialog = document.getElementById("myMsgDialog");
	dialog.close();
}
  	
function restorePageNumber(){
    window.location.reload();
}
 
// 모달 열기 및 데이터 가져오기
document.querySelector('.add-row').addEventListener('click', function() {
    var modal = document.getElementById('todoModalMonthly');
    modal.style.display = 'block';

    fetch('/account/fetch_todoMonthly.php')
        .then(response => response.text())
        .then(data => {
            document.querySelector('#todoModalMonthly .custom-card').innerHTML = data;

            // 닫기 버튼
            $(".todo-close").on("click", function() {
				$("#todoModalMonthly").hide();
			});
			
			// 저장 버튼 통합
			setupSaveButton();
        })
        .catch(error => console.error('Error fetching the monthly schedule:', error));
});

$(document).on('click', '.editable-item', function() {
    var num = $(this).data('num');
    console.log('num', num);

    $.ajax({
        url: '/account/fetch_todoMonthly.php',
        type: 'post',
        data: { num: num, mode: 'modify' },
        success: function(response) {
            $('#todoModalMonthly .custom-card').html(response);

            var modal = document.getElementById('todoModalMonthly');
            modal.style.display = 'block';

            $(".todo-close").on("click", function() {
				$("#todoModalMonthly").hide();
			});

			// 저장 버튼 통합
			setupSaveButton('modify');
        },
        error: function(jqxhr, status, error) {
            console.log(jqxhr, status, error);
        }
    });
});

// 월별일정 삭제 버튼
$(document).on('click', '#deleteBtn_month', function() {
	var user_name = $("#user_name").val();
	var first_writer = $("#first_writer").val();

	if (user_name !== first_writer) {
		Swal.fire({
			title: '삭제불가',
			text: "작성자만 삭제 가능합니다.",
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
				url: "/todo_account/process_month.php",
				type: "post",
				data: formData,
				success: function(response) {
					Toastify({
						text: "일정 삭제완료",
						duration: 2000,
						close: true,
						gravity: "top",
						position: "center",
						style: {
							background: "linear-gradient(to right, #00b09b, #96c93d)"
						},
					}).showToast();

					$("#todoModalMonthly").hide();
					  location.reload();
				},
				error: function(jqxhr, status, error) {
					console.log(jqxhr, status, error);
				}
			});
		}
	});
});

// 라디오 버튼에 따라 해당 필드를 숨기거나 보여주기
function toggleDateFields() {
    const yearlyRow = document.getElementById('yearlyRow');
    const monthlyRow = document.getElementById('monthlyRow');
    const yearlyRadio = document.getElementById('yearly').checked;

    if (yearlyRadio) {
        yearlyRow.style.display = '';
        monthlyRow.style.display = 'none';
    } else {
        yearlyRow.style.display = 'none';
        monthlyRow.style.display = '';
    }
}

// 저장 버튼에 대한 중복 처리 통합
function setupSaveButton(mode) {
	$("#saveBtn_month").off("click").on("click", function() {
		var periodType = $("input[name='period']:checked").val();

		// 매년/매월에 따른 값 처리
		var yearlyspecialday = '';
		var specialday = '';

		if (periodType === 'yearly') {
			var month = $("#month").val();
			var day = $("#day").val();
			yearlyspecialday = month + "/" + day;
			specialday = '';  // 매년 선택 시 매월은 공백으로 설정
		} else {
			specialday = $("#specialday").val();
			yearlyspecialday = '';  // 매월 선택 시 매년은 공백으로 설정
		}
		
		
        $("#mode").val(mode); // insert or modify
		// Append yearlyspecialday and specialday to the form data
		var formData = $("#board_form").serialize() + "&yearlyspecialday=" + yearlyspecialday + "&specialday=" + specialday;

		$.ajax({
			url: "/todo_account/process_month.php",
			type: "post",
			data: formData,
			success: function(response) {
				console.log(response);
				Toastify({
					text: "저장완료",
					duration: 3000,
					close: true,
					gravity: "top",
					position: "center",
					backgroundColor: "#4fbe87",
				}).showToast();
				$("#todoModalMonthly").hide();
				location.reload();
			},
			error: function(jqxhr, status, error) {
				console.log(jqxhr, status, error);
			}
		});
	});
}

$(document).on("click", "#closeBtn_month", function() {
	$("#todoModalMonthly").hide();
});

$(document).ready(function(){
	saveLogData('회계 일정관리 달력'); 
});

 </script> 
 </body>
</html>
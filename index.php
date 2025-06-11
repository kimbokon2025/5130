<?php
 require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  
  
 if(!isset($_SESSION["level"]) ||intval($_SESSION["level"]) > 7) {
          /*   alert("관리자 승인이 필요합니다."); */
		 sleep(1);
	     header("Location:" . $WebSite . "login/login_form.php"); 
         exit;
   }  

// 세션의 만료 시간을 확인합니다.
$expiryTime = ini_get('session.gc_maxlifetime');
$remainingTime = 0;

// 세션의 만료 시간과 현재 시간을 비교하여 남은 시간을 계산합니다.
if (isset($_SESSION['LAST_ACTIVITY'])) {
  $lastActivity = $_SESSION['LAST_ACTIVITY'];
  $currentTime = time();
  $elapsedTime = $currentTime - $lastActivity;
  
  if ($elapsedTime < $expiryTime) {
    $remainingTime = $expiryTime - $elapsedTime;
  }
}

// 세션의 남은 시간을 반환합니다.
// echo $expiryTime;

$today = date("Y-m-d");

$_SESSION["company"] = '경동기업';

require_once($_SERVER['DOCUMENT_ROOT'] . "/load_header.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/common.php");

// (접수/출고 등) 가져오기
include "load_info.php";

?>
 
<title> (주)경동기업 </title> 
  
<!--head 태그 내 추가-->
<!-- Favicon-->	
<link rel="icon" type="image/x-icon" href="favicon.ico">   <!-- 33 x 33 -->
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">    <!-- 144 x 144 -->
<link rel="apple-touch-icon" type="image/x-icon" href="favicon.ico">
<style>

 #todo-list td {
	vertical-align: top!important;
}

.shop-header {
    background-image: linear-gradient(to right, #0090f7, #ba62fc, #f2416b);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}
 </style>
</head>
 
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/myheader.php'); ?>
	
 <?php if($chkMobile==false) { ?>
	<div class="container">     
 <?php } else { ?>
 	<div class="container-fluid">     
	<?php } ?>	 

<?php
    $tabs = array(
		"알림" => 0,
		"작성" => 1,
		"상신" => 2,
		"미결" => 3,
		"진행" => 4,
		"결재" => 5
    );

?>

<div class="sideBanner">
    <span class="text-center text-dark">&nbsp; 전자결재 </span>
     
	<?php	
		// print $eworks_lv  ;		
		foreach ($tabs as $label => $tabId) {
			$badgeId = "badge" . $tabId;	
			
    ?>
	<div class="mb-1 mt-1">
		 <?php if ($label !== "알림") 
			{				
				if($eworks_lv && ($tabId>=3) )
				{
				  print '<button type="button" class="btn btn-dark rounded-pill" onclick="seltab(' . $tabId . '); "> ';
				  echo $label; 
				  print '<span class="badge badge-pill badge-dark" id="' . $badgeId . '"></span>';				  
				} 
				else if (!$eworks_lv)  // 일반결재 상신하는 그룹
				{				
				  print '<button type="button" class="btn btn-dark rounded-pill" onclick="seltab(' . $tabId . '); "> ';
				  echo $label; 
				  print '<span class="badge badge-pill badge-dark" id="' . $badgeId . '"></span>';				  
				} 				
			}
			else 
			{		
				print '<div id="bellIcon"> 🔔결재 </div>';
			}			
		  ?>
		</button>
	</div>
    <?php  }  ?>
</div>

</div>

<form id="board_form" name="board_form" method="post" enctype="multipart/form-data" >	

<input type="hidden" id="num" name="num" value="<?= isset($num) ? $num : '' ?>" >
<input type="hidden" id="mode" name="mode" value="<?= isset($mode) ? $mode : '' ?>" >

<!-- todo모달 컨테이너 -->
<div class="container-fluid">
	<!-- Modal -->
	<div id="todoModal" class="modal">
		<div class="modal-content"  style="width:800px;">
			<div class="modal-header">
				<span class="modal-title">할일</span>
				<span class="todo-close">&times;</span>
			</div>
			<div class="modal-body">
				<div class="custom-card"></div>
			</div>
		</div>
	</div>
</div>

<!-- 달력 일자에 대한 모달 -->
<div class="modal fade" id="dayModal" tabindex="-1" aria-labelledby="dayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dayModalLabel">날짜별 상세보기</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <!-- 데이터가 동적으로 삽입됩니다 -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
            </div>
        </div>
    </div>
</div>


<?php include 'mymodal.php'; ?>  

 <?php if($chkMobile==false) { ?>
	<div class="container">     
 <?php } else { ?>
 	<div class="container-fluid">     
	<?php } ?>

<div class="card mt-2 mb-1 " style="background-color:#f0f8ff;" >	
<div class="row d-flex mb-1 mt-1">		
	<div class="col-sm-2">
		<button  type="button" id="board_view" class="btn btn-primary btn-sm me-2 fw-bold"> <i class="bi bi-chevron-down"></i> </button>            
	</div>		
	<div class="col-sm-8">
		<div class="d-flex justify-content-center align-items-center"> 	
			<span class="fw-bold shop-header fs-5" > 2025년 모두 건강하세요! </span> 	
		</div>
	</div>		
	<div class="col-sm-2">
	  <div class="d-flex justify-content-end" > 
		(주)경동기업 &nbsp;
	  </div>
	</div>
</div>
<div class="row d-flex board_list"  >			
	<!-- 전일 경영 Report -->
	<div class="col-sm-3 board_list" >
		<!-- 출고 통계 -->
		<div class="card justify-content-center  my-card-padding" >
			<div class="card-header text-center  my-card-padding">
				<a href="/output/statistics.php?header=header"> 스크린+스라트 출고 통계 </a>
			</div>
			<div class="card-body  my-card-padding">	
				 <?php  include 'load_statistics.php'; ?>
			</div> 
		</div>	
		<!-- 출고 통계 -->
		<div class="card justify-content-center  my-card-padding" >
			<div class="card-header text-center  my-card-padding">
				<a href="/output/statistics.php?header=header"> 스크린 출고 통계 </a>
			</div>
			<div class="card-body  my-card-padding">	
				 <?php  include 'load_stat_screen.php'; ?>
			</div> 
		</div>	
		<!-- 출고 통계 -->
		<div class="card justify-content-center  my-card-padding" >
			<div class="card-header text-center  my-card-padding">
				<a href="/output/statistics.php?header=header"> 스라트 출고 통계 </a>
			</div>
			<div class="card-body  my-card-padding">	
				 <?php  include 'load_stat_slat.php'; ?>
			</div> 
		</div>	


     <!-- 금일 연차 -->		
	<div class="card justify-content-center">		
		<div class="card-header  text-center  my-card-padding">
			<a href="./annualleave/index.php"> 금일 연차 </a>
		</div>
		<div class="card-body  my-card-padding">
			<?php   
			// 금일 연차인 사람 나타내기
			require_once("./lib/mydb.php"); 
			$pdo = db_connect();   
			$now = date("Y-m-d",time()) ;  

			$sql = "SELECT * FROM chandj.eworks WHERE (al_askdatefrom <= CURDATE() AND al_askdateto >= CURDATE()) AND al_company ='경동'  AND is_deleted IS NULL ";
			$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
			$total_row=$stmh->rowCount();
			if($total_row>0 ) 
			{
			?>  
						
			
			<div class="card justify-content-center">							
				<div class="card-body  my-card-padding">                         
				<?php   				
					  include "./load_aldisplay.php";   				  				  
				?>  					
				</div>							
			</div>   
			<?php   				
			}
			?>  
			
		   </div>
	   </div>  <!-- 금일 연차 -->	
		
	</div>  <!-- end of col-sm-4 -->	
	<div class="col-sm-3  board_list">
		 <!-- 금일 출고 -->	
		 <div class="card justify-content-center  my-card-padding">
			<div class="card-header text-center  my-card-padding ">
					<a href="./output/list.php">금일 총괄 현황 </a>					
				</div>
				<div class="card-body  my-card-padding">	
				<table class="table table-bordered table-hover table-sm">									
					<thead class="align-middle">	
						<tr>									
					<th class="text-center w-25 "> 접수 </th>									
					<th class="text-center w-25 "> 출고예정 </th>									
					<th class="text-center w-25 "> 출고완료 </th>													
						</tr>
					</thead>
					
					<tbody class="align-middle">					 
							<tr onclick="window.location.href='./output/list.php'" style="cursor:pointer;">
								<td class="text-center">                                                                                   
								<span class="text-muted "> <span class="text-center badge bg-secondary" id="indateCount" >  </span>  </span>                                            
								</td>
								<td class="text-center">                                                                                    
								<span class="text-muted "> <span class="badge bg-dark" id="outdateCount" >  </span>  </span>                                            
								</td>
								<td class="text-center">                                                                                   
								<span class="text-muted "> <span class="badge bg-danger" id="doneCount" >  </span>  </span>                                            
								</td>
							</tr>
					</tbody>
				</table>												   
			</div> 
		</div>	
		
		<!-- 수입검사 구매 통계 -->		
		<?php if(intval($level) == 1) : ?>
		<div class="card justify-content-center">		
			<div class="card-header  text-center  my-card-padding">
				<a href="./instock/statistics.php?header=header"> <?=date("Y") . '년 ' . date("m") . '월' ?> 수입검사 구매 </a>				
			</div>
			<div class="card-body  my-card-padding">	
				<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/instock/func_statistics.php');  // 함수로 가져오는 이유는 다른곳에서도 코드를 재활용하기 위함이다. ?>
				<?php  include $_SERVER['DOCUMENT_ROOT'] . '/instock/instock_table.php'; ?>
			</div>   	
		</div>   	
		
		<!-- 배차 차량 -->		
		<div class="card justify-content-center">		
			<div class="card-header  text-center  my-card-padding">
				<a href="./output/list_deliveryfee.php?header=header"> 배차 차량 </a>				
			</div>
			<?php			
			$now = date("Y-m-d", time());
			
			// $oneWeekAgo = date("Y-m-d", strtotime("-1 week", strtotime($now)));			// 1주전 정보		
			$twoweeksAgo = date("Y-m-d", strtotime("-1 months", strtotime($now)));  // 1개월전
			$endOfDay = date("Y-m-d");
			$a = " WHERE outdate BETWEEN '$twoweeksAgo' AND '$endOfDay' and  is_deleted = '0' AND deliveryfeeList IS NOT NULL AND deliveryfeeList != ''  AND deliveryfeeList != '[]'  ORDER BY outdate DESC limit 7";

			$sql = "SELECT * FROM chandj.output" . $a;

			$stmh = $pdo->query($sql);
			$total_row = $stmh->rowCount();	
			
			// print $sql;

			// 현재 날짜를 DateTime 객체로 가져옵니다.
			$currentDate = new DateTime();								
			?>			
			<table class="table table-bordered table-hover table-sm">
				<tbody>				     
			<?php   				
			// 현재 날짜를 DateTime 객체로 가져옵니다.
			$currentDate = new DateTime();					
			if($total_row > 0) {		
				print "<thead class='table-secondary'> <tr>";
				print "<th class='text-center' > 출고일 </th>";								
				print "<th class='text-center' > 현장명 </th>";
				print "<th class='text-center' > 업체 </th>";
				print "<th class='text-center' > 금액 </th>";
				print "</tr> </thead> ";						

		$start_num = 1; // 페이지당 표시되는 첫 번째 글 순번
	
		while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {						          
			$outdate      = $row['outdate']      ?? '';
			$outworkplace = $row['outworkplace'] ?? '';
			$item_num = $row["num"];

			// deliveryfeeList를 JSON -> 배열 디코딩 (여러 개 행이 있을 수 있음)
			$deliveryfeeListData = json_decode($row['deliveryfeeList'], true);
			// 만약 데이터가 없거나 파싱 실패하면, 1개짜리 빈 배열로 처리 (빈 화면 대신)
			if (!is_array($deliveryfeeListData) || count($deliveryfeeListData) === 0) {
				$deliveryfeeListData = [[]];
			}

			// deliveryfeeList의 각 행마다 <tr> 생성
			foreach ($deliveryfeeListData as $index => $feeRow) {            
				$tdNumber  = $start_num ;			
			
				// col1 ~ col11
				$col1  = $feeRow['col1']  ?? '';
				$col2  = $feeRow['col2']  ?? '';
				$col3  = $feeRow['col3']  ?? '';
				$col4  = $feeRow['col4']  ?? '';
				$col5  = $feeRow['col5']  ?? '';
				$col6  = $feeRow['col6']  ?? '';
				$col7  = $feeRow['col7']  ?? '';
				$col8  = $feeRow['col8']  ?? '';
				$col9  = $feeRow['col9']  ?? '';
				$col10 = $feeRow['col10'] ?? '';
				$col11 = $feeRow['col11'] ?? '';

				$formattedDate = explode('-', $outdate, 2)[1]; // '-'로 분리하고 두 번째 부분을 사용 년도를 제거하고 월-일만 나오게 하기					
							
				echo '<tr onclick="viewBoard(\'output\', ' .  $item_num  . ');return false;"\">';
				
				print '<td class="text-center"> ' . $formattedDate . '</td>';												
				$text = mb_substr($outworkplace, 0, 10);
				print '<td class="text-center" > '.  $text . '</td>';							
				print '<td class="text-center" style="cursor:pointer;">' . $col1 . '</td>';
				print '<td class="text-end" style="cursor:pointer;">' . $col5 . '</td>';
				print '</tr>';							
						
						} 
				}
					?>  
					</tbody>
				</table>							
				<?php } ?>
		</div>   			<!-- end of 배차 차량 -->	

		<?php endif;  // 수입검사 구매통계 ?>					
					
		<!-- 수입검사 -->		
		<div class="card justify-content-center">		
			<div class="card-header  text-center  my-card-padding">
				<a href="instock/list.php?header=header"> 수입검사 </a>
				<!-- <span class="cursor-pointer" onclick="popupCenter('instock/list.php' , '수입검사', 1800, 800);">    수입검사 (최근 5건) </span>			  -->
			</div>
			<?php
			//도장관련 글이 일주일에 해당되면
			$now = date("Y-m-d", time());
			
			// $oneWeekAgo = date("Y-m-d", strtotime("-1 week", strtotime($now)));			// 1주전 정보		
			$twoweeksAgo = date("Y-m-d", strtotime("-5 months", strtotime($now)));  // 5개월전
			$endOfDay = date("Y-m-d 23:59:59", time());
			$a = " WHERE inspection_date BETWEEN '$twoweeksAgo' AND '$endOfDay' and is_deleted IS NULL ORDER BY inspection_date DESC limit 7";

			$sql = "SELECT * FROM chandj.instock" . $a;

			$stmh = $pdo->query($sql);
			$total_row = $stmh->rowCount();	

			// 현재 날짜를 DateTime 객체로 가져옵니다.
			$currentDate = new DateTime();					
			if($total_row > 0) {
			?>			
			<table class="table table-bordered table-hover table-sm">
				<tbody>				     
				<?php   				
						// 현재 날짜를 DateTime 객체로 가져옵니다.
						$currentDate = new DateTime();					
						if($total_row > 0) {		
							print "<thead class='table-secondary'> <tr>";
							print "<th class='text-center' > 검사일 </th>";								
							print "<th class='text-center' > 판정 </th>";
							print "<th class='text-center' > 품명 </th>";
							print "<th class='text-center' > 공급사 </th>";
							print "</tr> </thead> ";						
						$innerCount = 0;		
						while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
							
							$specification_clean = preg_replace('/\s+/', ' ', $row['specification']); 
							$remarks_clean = preg_replace('/\s+/', ' ', $row['remarks']);
							$iListArray = json_decode($row['iList'], true);
							$item_num = $row["num"];								
							$item_name = $row["item_name"];		

							// resultJudgement 값을 찾기 위한 루프
							$resultJudgement = null;
							
							if(!empty($iListArray )) {
								foreach ($iListArray as $item) {
									// 배열에서 inputItems를 가진 항목이 있는지 확인
									if (isset($item['inputItems']['resultJudgement'])) {
										$resultJudgement = $item['inputItems']['resultJudgement'];
										break; // 값을 찾으면 루프 종료
									}
								}
								$innerCount ++;
							}
							
							$formattedDate = explode('-',$row["inspection_date"], 2)[1]; // '-'로 분리하고 두 번째 부분을 사용 년도를 제거하고 월-일만 나오게 하기								
							
							echo "<tr onclick=\"viewBoardInstock('$item_num', '$item_name', '$specification_clean', '$remarks_clean');\">";
							
							print '<td class="text-center"> ';
							print $formattedDate;
							print '</td>';					
							$class = ($resultJudgement === '합격') ? 'text-success' : 'text-danger';				
							print '<td class="text-center fw-bold ' . $class . '" > '.  $resultJudgement . '</td>';
							$text = mb_substr($row["item_name"], 0, 20);
							$text = str_replace(',', '', $text);
							print '<td class="text-start" style="cursor:pointer;"> &nbsp; ' . $text . '</td>';
							//공급사
							$text = mb_substr($row["supplier"], 0, 12);
							$text = str_replace(',', '', $text);
							print '<td class="text-start" style="cursor:pointer;"> &nbsp; ' . $text . '</td>';
							// $text = mb_substr($row["weight_kg"], 0, 10);
							// $text = str_replace(',', '', $text);

							// // 결과값이 숫자 형태인지 확인하고, 숫자일 경우 number_format 적용
							// if (is_numeric($text) && $text != '' && $text != '0') {
								// $text = number_format((float)$text);
							// }

							// print '<td class="text-end" style="cursor:pointer;"> &nbsp; ' . $text . '</td>';							

							print '</tr>';
							
							if($innerCount >= 5)
								break;
							}
						} 
					?>  
					</tbody>
				</table>							
				<?php } ?>
		</div>   	
			
		<!-- 인정검사 -->		
		<div class="card justify-content-center">		
			<div class="card-header  text-center  my-card-padding">
				<a href="output/list_ACI.php?header=header"> 인정검사 </a>			
				<!-- <span class="cursor-pointer" onclick="popupCenter('output/list_ACI.php' , '수입검사', 1800, 800);">    인정검사 </span>			  -->
			</div>
			<?php
			//도장관련 글이 일주일에 해당되면
			$now = date("Y-m-d", time());
			
			// $oneWeekAgo = date("Y-m-d", strtotime("-1 week", strtotime($now)));			// 1주전 정보		
			$twoweeksAgo = date("Y-m-d", strtotime("-5 months", strtotime($now)));  // 5개월전
			$endOfDay = date("Y-m-d 23:59:59", time());
			$a = " WHERE ACIregDate BETWEEN '$twoweeksAgo' AND '$endOfDay' and (is_deleted IS NULL or is_deleted ='0') ORDER BY ACIdoneDate DESC limit 7";  // 검사완료일자 기준

			$sql = "SELECT * FROM chandj.output" . $a;

			$stmh = $pdo->query($sql);
			$total_row = $stmh->rowCount();	

			// 현재 날짜를 DateTime 객체로 가져옵니다.
			$currentDate = new DateTime();					
			if($total_row > 0) {
			?>			
			<table class="table table-bordered table-hover table-sm">
				<tbody>				     
				<?php   				
						// 현재 날짜를 DateTime 객체로 가져옵니다.
						$currentDate = new DateTime();					
						if($total_row > 0) {		
							print "<thead class='table-secondary'> <tr>";
							print "<th class='text-center' > 요청일 </th>";								
							print "<th class='text-center' > 완료일 </th>";
							print "<th class='text-center' > 제품명 </th>";
							print "<th class='text-center' > 현장명 </th>";
							print "</tr> </thead> ";													
							while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
								
								$num = $row['num']; 
								$prodCode = $row['prodCode']; 
								$ACIaskDate = preg_replace('/\s+/', ' ', $row['ACIaskDate']); 
								$ACIdoneDate = preg_replace('/\s+/', ' ', $row['ACIdoneDate']);
								$ACIaskDate = explode('-', $ACIaskDate, 2)[1]; // '-'로 분리하고 두 번째 부분을 사용 년도를 제거하고 월-일만 나오게 하기
								$ACIdoneDate = explode('-', $ACIdoneDate, 2)[1];
																
								$outworkplace = $row['outworkplace'] ;							
																			

								$text = mb_substr($outworkplace, 0, 8);
								echo "<tr onclick=\"viewACI('$num','$prodCode');\">";								
									print '<td class="text-center"> ';
									print $ACIaskDate;
									print '</td>';					
									print '<td class="text-center"> ';
									print $ACIdoneDate;
									print '</td>';		
									print '<td class="text-center"> ';
									print $prodCode;
									print '</td>';					
									print '<td class="text-center"> ';
									print $text;
									print '</td>';											
									print '</tr>';								
								}
						} 
					?>  
				   </tbody>
				</table>							
				<?php } ?>
		</div>   	
	</div>	<!-- end of col-sm-3 -->
<div class="col-sm-3 board_list">	
	<?php
	$title_message = '품의서';
	$tablename     = 'eworks';

	// 오늘 날짜, 3개월 전
	$now           = date("Y-m-d");
	$threeMonthsAgo= date("Y-m-d", strtotime("-3 months", strtotime($now)));
	$endOfDay      = $now;

	// 최근 3개월·결재취소되지 않고, eworks_item='품의서'인 데이터 7건
	$where = " WHERE indate BETWEEN '$threeMonthsAgo' AND '$endOfDay'
			AND (is_deleted IS NULL OR is_deleted='0')
			AND eworks_item='품의서'
			ORDER BY indate DESC
			LIMIT 7";

	$sql       = "SELECT * FROM {$DB}.{$tablename}" . $where;
	$stmh      = $pdo->query($sql);
	$total_row = $stmh->rowCount();
	?>
	<!-- 품의서 -->
	<div class="card justify-content-center">
	<div class="card-header text-center my-card-padding">
		<a href="./askitem/list.php?header=header"><?=$title_message?></a>
	</div>

		<table class="table table-bordered table-hover table-sm">
			<tbody>
			<?php if ($total_row > 0): ?>
			<thead class="table-secondary">
				<tr>
				<th class="text-center">작성일</th>
				<th class="text-center">제목</th>
				<th class="text-center">금액</th>
				<th class="text-center">결재완료</th>
				</tr>
			</thead>
			<?php while ($row = $stmh->fetch(PDO::FETCH_ASSOC)): 
				// 원본 컬럼에서 필요한 값 추출
				$indate      = $row['indate']          ?? '';
				$titleFull   = $row['outworkplace']    ?? '';
				$amount      = $row['suppliercost']    ?? '';
				$status      = $row['status']          ?? '';
				$e_confirm   = $row['e_confirm']       ?? '';

				// 년도 제거하고 "MM-DD" 형태로
				$formattedDate = explode('-', $indate, 2)[1] ?? $indate;
				// 제목은 최대 10글자
				$titleShort    = mb_substr($titleFull, 0, 10);
				// 결재 완료 여부
				$approvedMark  = ($status === 'end' && !empty($e_confirm)) ? '✅' : '';
			?>
			<tr onclick="viewBoard('품의서', <?=$row['num']?>); return false;" style="cursor:pointer;">
				<td class="text-center"><?=$formattedDate?></td>
				<td class="text-center"><?=$titleShort?></td>
				<td class="text-end"><?=$amount?></td>
				<td class="text-center"><?=$approvedMark?></td>
			</tr>
			<?php endwhile; ?>
			<?php endif; ?>
			</tbody>
		</table>
	</div>		<!-- end of 품의서 -->

	<!-- 지출결의서 -->
	<?php
		$title_message = '지출결의서';
		$tablename     = 'eworks';

		// 오늘 날짜, 3개월 전
		$now            = date("Y-m-d");
		$threeMonthsAgo = date("Y-m-d", strtotime("-3 months", strtotime($now)));
		$endOfDay       = $now;

		// 최근 3개월·삭제되지 않고 eworks_item='지출결의서'인 데이터 7건
		$where = " WHERE indate BETWEEN '$threeMonthsAgo' AND '$endOfDay'
				AND (is_deleted IS NULL OR is_deleted='0')
				AND eworks_item='지출결의서'
				ORDER BY indate DESC
				LIMIT 7";

		$sql       = "SELECT * FROM {$DB}.{$tablename}" . $where;
		$stmh      = $pdo->query($sql);
		$total_row = $stmh->rowCount();
		?>
		<!-- 지출결의서 -->
		<div class="card justify-content-center">
		<div class="card-header text-center my-card-padding">
			<a href="./askitem_ER/list.php?header=header"><?=$title_message?></a>
		</div>

		<table class="table table-bordered table-hover table-sm">
			<tbody>
			<?php if ($total_row > 0): ?>
			<thead class="table-secondary">
				<tr>
				<th class="text-center">작성일</th>
				<th class="text-center">제목</th>
				<th class="text-center">금액</th>
				<th class="text-center">결재완료</th>
				</tr>
			</thead>
			<?php while ($row = $stmh->fetch(PDO::FETCH_ASSOC)):
				// 작성일
				$indate = $row['indate'] ?? '';
				$formattedDate = explode('-', $indate, 2)[1] ?? $indate;

				// expense_data JSON 파싱
				$expenseData = json_decode($row['expense_data'] ?? '[]', true);
				if (!is_array($expenseData)) $expenseData = [];

				// 제목(첫 항목 + 외 N건)
				$items = [];
				$totalAmount = 0;
				foreach ($expenseData as $exp) {
				if (!empty($exp['expense_item'])) {
					$items[] = $exp['expense_item'];
				}
				if (!empty($exp['expense_amount'])) {
					$totalAmount += intval(str_replace(',', '', $exp['expense_amount']));
				}
				}
				if (count($items) > 1) {
				$titleShort = $items[0] . ' 외 ' . (count($items) - 1) . '건';
				} elseif (count($items) === 1) {
				$titleShort = $items[0];
				} else {
				$titleShort = '';
				}

				// 결재 완료 표시
				$approvedMark = ($row['status'] === 'end' && !empty($row['e_confirm'])) ? '✅' : '';
			?>
			<tr onclick="viewBoard('지출결의서', <?=$row['num']?>); return false;" style="cursor:pointer;">
				<td class="text-center"><?=$formattedDate?></td>
				<td class="text-center"><?=htmlspecialchars(mb_substr($titleShort, 0, 10))?></td>
				<td class="text-end"><?=number_format($totalAmount)?></td>
				<td class="text-center"><?=$approvedMark?></td>
			</tr>
			<?php endwhile; ?>
			<?php endif; ?>
			</tbody>
		</table>
		</div> 		<!-- end of 지출결의서 -->

	<!-- 차량관리 -->
    <div class="card justify-content-center">
        <div class="card-header text-center my-card-padding">
            <a href="car/list.php?header=header"> 차량관리 </a>
        </div>
        <?php
        // 오늘 날짜
        $now = date("Y-m-d");

        // 삭제되지 않은 차량 데이터를 purchase_date 역순으로 조회
        $sql   = "SELECT * FROM chandj.car
                  WHERE (is_deleted IS NULL OR is_deleted = '0')
                  ORDER BY purchase_date DESC";
        $stmh  = $pdo->query($sql);
        $count = $stmh->rowCount();

        if ($count > 0):
        ?>
        <table class="table table-bordered table-hover table-sm">
            <thead class="table-secondary">
                <tr>
                    <th class="text-center">차종</th>
                    <th class="text-center">내역</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $stmh->fetch(PDO::FETCH_ASSOC)):
                $num              = $row['num'];
                $vehicle_type     = $row['vehicle_type'];
                $engine_oil_data  = json_decode($row['engine_oil_change_data'], true)  ?? [];
                $maintenance_data = json_decode($row['maintenance_data'], true)       ?? [];

				// 1) 엔진오일 교체 날짜 내림차순 정렬
				usort($engine_oil_data, function($a, $b) {
					if (empty($a['engine_oil_change_date'])) return 1;
					if (empty($b['engine_oil_change_date'])) return -1;
					return strtotime($b['engine_oil_change_date']) <=> strtotime($a['engine_oil_change_date']);
				});

				// 2) 정비 내역 날짜 내림차순 정렬
				usort($maintenance_data, function($a, $b) {
					if (empty($a['maintenance_date'])) return 1;
					if (empty($b['maintenance_date'])) return -1;
					return strtotime($b['maintenance_date']) <=> strtotime($a['maintenance_date']);
				});


                // 3) 팝오버용 전체 HTML과 표시용 날짜 문자열 준비
                $all_records   = '';
                $display_dates = [];

                if (!empty($engine_oil_data)) {
                    $all_records .= '<div class="mb-2"><strong>엔진오일 교체</strong><br>';
                    foreach ($engine_oil_data as $oil) {
                        $date    = $oil['engine_oil_change_date'] ?? '';
                        $mileage = $oil['mileage'] ?? '';
                        if ($date || $mileage) {
                            $all_records   .= htmlspecialchars($date).' - 주행거리: '.htmlspecialchars($mileage).' km<br>';
                            $display_dates[] = '오일' . $date;
                        }
                    }
                    $all_records .= '</div>';
                }

                if (!empty($maintenance_data)) {
                    $all_records .= '<div><strong>정비내역</strong><br>';
                    foreach ($maintenance_data as $mnt) {
                        $date   = $mnt['maintenance_date'] ?? '';
                        $record = $mnt['maintenance_record'] ?? '';
                        if ($date && $record) {
                            $all_records   .= htmlspecialchars($date).' - '.htmlspecialchars($record).'<br>';
                            $display_dates[] = '정비' . $date;
                        }
                    }
                    $all_records .= '</div>';
                }

            ?>
                <tr onclick="viewCar('<?= $num ?>');">
                    <td class="text-center"><?= htmlspecialchars($vehicle_type) ?></td>
                    <td class="text-center">
                        <?php if ($all_records): 
                            // 날짜들 오름차순 정렬 후 한 줄 30자까지 잘라서 '...' 추가
                            sort($display_dates);
                            $display_text = implode(' ', $display_dates);
                            $display_text = substr($display_text, 0, 50) . (strlen($display_text) > 50 ? '...' : '');
                        ?>
                            <span
                                class="text-primary d-inline-block text-truncate"
                                style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                                data-bs-toggle="popover"
                                data-bs-html="true"
                                data-bs-content="<?= htmlspecialchars($all_records, ENT_QUOTES) ?>"
                                data-bs-placement="left"
                                onclick="event.stopPropagation();"
                            ><?= htmlspecialchars($display_text) ?></span>
                        <?php else: ?>
                            -
                        <?php endif ?>
                    </td>
                </tr>
            <?php endwhile ?>
            </tbody>
        </table>
        <?php endif ?>
    </div>		

	<!-- 지게차관리 -->
	<div class="card justify-content-center">
		<div class="card-header text-center my-card-padding">
			<a href="lift/list.php?header=header"> 지게차 </a>
		</div>
		<?php
		// 오늘 날짜
		$now   = date("Y-m-d");
		// 삭제되지 않은 지게차 데이터를 purchase_date 역순으로 조회
		$sql   = "SELECT * FROM chandj.lift
				WHERE (is_deleted IS NULL OR is_deleted = '0')
				ORDER BY purchase_date DESC";
		$stmh  = $pdo->query($sql);
		$count = $stmh->rowCount();

		if ($count > 0):
		?>
		<table class="table table-bordered table-hover table-sm">
			<thead class="table-secondary">
				<tr>
					<th class="text-center">차종</th>
					<th class="text-center">내역</th>
				</tr>
			</thead>
			<tbody>
			<?php while ($row = $stmh->fetch(PDO::FETCH_ASSOC)):
				$num              = $row['num'];
				$vehicle_type     = $row['vehicle_type'];
				$engine_oil_data  = json_decode($row['engine_oil_change_data'], true)  ?? [];
				$maintenance_data = json_decode($row['maintenance_data'],       true)  ?? [];

					// 1) 엔진오일 교체 날짜 내림차순 정렬
					usort($engine_oil_data, function($a, $b) {
						if (empty($a['engine_oil_change_date'])) return 1;
						if (empty($b['engine_oil_change_date'])) return -1;
						return strtotime($b['engine_oil_change_date']) <=> strtotime($a['engine_oil_change_date']);
					});
	
					// 2) 정비 내역 날짜 내림차순 정렬
					usort($maintenance_data, function($a, $b) {
						if (empty($a['maintenance_date'])) return 1;
						if (empty($b['maintenance_date'])) return -1;
						return strtotime($b['maintenance_date']) <=> strtotime($a['maintenance_date']);
					});
	
	
					// 3) 팝오버용 전체 HTML과 표시용 날짜 문자열 준비
					$all_records   = '';
					$display_dates = [];
	
					if (!empty($engine_oil_data)) {
						$all_records .= '<div class="mb-2"><strong>엔진오일 교체</strong><br>';
						foreach ($engine_oil_data as $oil) {
							$date    = $oil['engine_oil_change_date'] ?? '';
							$mileage = $oil['mileage'] ?? '';
							if ($date || $mileage) {
								$all_records   .= htmlspecialchars($date).' - 주행거리: '.htmlspecialchars($mileage).' km<br>';
								$display_dates[] = '오일' . $date;
							}
						}
						$all_records .= '</div>';
					}
	
					if (!empty($maintenance_data)) {
						$all_records .= '<div><strong>정비내역</strong><br>';
						foreach ($maintenance_data as $mnt) {
							$date   = $mnt['maintenance_date'] ?? '';
							$record = $mnt['maintenance_record'] ?? '';
							if ($date && $record) {
								$all_records   .= htmlspecialchars($date).' - '.htmlspecialchars($record).'<br>';
								$display_dates[] = '정비' . $date;
							}
						}
						$all_records .= '</div>';
					}
	
				?>
					<tr onclick="viewLift('<?= $num ?>');">
						<td class="text-center"><?= htmlspecialchars($vehicle_type) ?></td>
						<td class="text-center">
							<?php if ($all_records): 
								// 날짜들 오름차순 정렬 후 한 줄 30자까지 잘라서 '...' 추가
								sort($display_dates);
								$display_text = implode(' ', $display_dates);
								$display_text = substr($display_text, 0, 25) . (strlen($display_text) > 25 ? '...' : '');
							?>
								<span
									class="text-primary d-inline-block text-truncate"
									style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
									data-bs-toggle="popover"
									data-bs-html="true"
									data-bs-content="<?= htmlspecialchars($all_records, ENT_QUOTES) ?>"
									data-bs-placement="left"
									onclick="event.stopPropagation();"
								><?= htmlspecialchars($display_text) ?></span>
							<?php else: ?>
								-
							<?php endif ?>
						</td>
					</tr>
				<?php endwhile ?>
				</tbody>
			</table>
			<?php endif ?>
		</div>	
	  	
	</div>	<!-- end of col-sm-3 -->	
	<div class="col-sm-3 board_list">			
	<!-- 전체 공지 -->	
	<div class="card justify-content-center">		
		<div class="card-header  text-center  my-card-padding">
			<a href="./notice/list.php"> 전체 공지 </a>
		</div>
			<div class="card-body  my-card-padding" >					
			<?php   
			//전체 공지사항
			$now = date("Y-m-d",time()) ;				  
			$a="   where noticecheck='y' order by num desc  limit 5";  				  
			$sql="select * from $DB.notice " . $a; 		
			$stmh = $pdo->query($sql);
			$total_row = $stmh->rowCount();
			
			// 현재 날짜를 DateTime 객체로 가져옵니다.
			$currentDate = new DateTime();
			
			if($total_row > 0) {
				echo '<table class="table table-hover">';
				
				while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
					// 데이터의 등록 날짜를 DateTime 객체로 가져옵니다.
					$dataDate = new DateTime($row["regist_day"]);
					
					// 날짜 차이를 계산합니다.
					$interval = $currentDate->diff($dataDate)->days;

					// 이미지 태그 초기화
					$newImage = '';

					// 7일 이내면 이미지를 추가합니다.
					if($interval < 7) {
						$newImage = '<img src="./img/new-gif.gif" style="width:10%;" alt="New" /> &nbsp;';
					}
					 
					  $item_num = $row["num"]; 
					  $sqlsub="select * from $DB.notice_ripple where parent=$item_num";
					  $stmh1 = $pdo->query($sqlsub); 
					  $num_ripple=$stmh1->rowCount(); 

					// 데이터-속성 추가하여 공지의 ID 또는 필요한 정보를 저장
					print '<td class="text-start" style="cursor:pointer;" onclick="viewBoard(\'notice\', ' .  $item_num  . ');return false;"> &nbsp;  ' . $newImage . $row["subject"] ;

					   if($num_ripple>0)
							echo ' &nbsp; <span class="badge bg-dark "> ' . $num_ripple . ' </span> </td> ';						
						  else
							  echo  '</td> ';

						echo '</tr>'; // 테이블 행 종료
					}

					echo '</table>';
				} else {
					echo '<span> &nbsp; </span>';
				}
				?>  				
	</div>   
</div> 

		<!-- 새소식 -->	
		<div class="card justify-content-center">		
			<div class="card-header  text-center  my-card-padding">
				<a href="./qna/list.php"> 자료실 </a>
			</div>
		<div class="card-body  my-card-padding">	
		<table class="table table-bordered table-hover ">
			<tbody>				     
			<?php   

			//자료실
			$now = date("Y-m-d", time());

			// // 1주일 전 날짜 계산
			$oneWeekAgo = date("Y-m-d", strtotime("-30 week", strtotime($now)));			// 30주전 정보		
			$endOfDay = date("Y-m-d 23:59:59", time());
			$a = " WHERE regist_day BETWEEN '$oneWeekAgo' AND '$endOfDay' ORDER BY num DESC limit 5";

			$sql = "SELECT * FROM $DB.qna" . $a;

			$stmh = $pdo->query($sql);
			$total_row = $stmh->rowCount();


			// 현재 날짜를 DateTime 객체로 가져옵니다.
			$currentDate = new DateTime();					
			if($total_row > 0) {						
			print '<tr>';				
			print '<td class="align-middle no-hover" rowspan="' . ($total_row) . '" style="width:20%;"  onmouseover="this.style.backgroundColor=\'initial\';" onmouseout="this.style.backgroundColor=\'initial\';"> 자료실 </td> ';					
			while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
				// 데이터의 등록 날짜를 DateTime 객체로 가져옵니다.
				$dataDate = new DateTime($row["regist_day"]);
				
				// 날짜 차이를 계산합니다.
				$interval = $currentDate->diff($dataDate)->days;

				// 이미지 태그 초기화
				$newImage = '';

				// 7일 이내면 이미지를 추가합니다.
				if($interval < 7) {
					$newImage = '<img src="./img/new-gif.gif" style="width:10%;" alt="New" /> &nbsp;';
				}
				// 데이터-속성 추가하여 공지의 ID 또는 필요한 정보를 저장
				print '<td class="text-start" ';
				print ' onclick="viewBoard(\'qna\', ' . $row["num"] . ');">' . $newImage . $row["subject"] . '</td>';
				print '</tr>';
			}
			} 
		?>  
			</tbody>
			</table>
		</div>   
	</div>
	
		<!-- 개발일지 -->	
		<div class="card justify-content-center">		
			<div class="card-header  text-center  my-card-padding">
				<a href="./rnd/list.php"> 개발일지 </a>
			</div>
		<div class="card-body  my-card-padding">	
		<table class="table table-bordered table-hover ">
			<tbody>				     
			<?php   
			// 개발일지
			$now = date("Y-m-d", time());

			// 1주일 전 날짜 계산
			$oneWeekAgo = date("Y-m-d", strtotime("-30 week", strtotime($now)));			// 30주전 정보		
			$endOfDay = date("Y-m-d 23:59:59", time());
			$a = " WHERE regist_day BETWEEN '$oneWeekAgo' AND '$endOfDay' ORDER BY num DESC  limit 5";

			$sql = "SELECT * FROM $DB.rnd" . $a;

			$stmh = $pdo->query($sql);
			$total_row = $stmh->rowCount();


			// 현재 날짜를 DateTime 객체로 가져옵니다.
			$currentDate = new DateTime();					
			if($total_row > 0) {								
				while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
					// 데이터의 등록 날짜를 DateTime 객체로 가져옵니다.
					$dataDate = new DateTime($row["regist_day"]);
					
					// 날짜 차이를 계산합니다.
					$interval = $currentDate->diff($dataDate)->days;

					// 이미지 태그 초기화
					$newImage = '';
					print '<tr>';						

					// 7일 이내면 이미지를 추가합니다.
					if($interval < 7) {
						$newImage = '<img src="./img/new-gif.gif" style="width:7%;" alt="New" /> &nbsp;';
					}
					// 데이터-속성 추가하여 공지의 ID 또는 필요한 정보를 저장
					print '<td class="text-start" ';
					print ' onclick="viewBoard(\'rnd\', ' . $row["num"] . ');">' . $newImage . $row["subject"] . '</td>';
					print '</tr>';
				}
			} 
			?>  
				</tbody>
				</table>
			</div>   
		</div>    				
	</div>    <!-- end of col-sm-4 -->	
</div>    <!-- end of row -->           	    
</div>               	    
</div>               
</div>
</div>


<!-- todo Calendar -->
<?php if($chkMobile==false) { ?>
    <div class="container">     
<?php } else { ?>
    <div class="container-xxl">     
<?php } ?>     
<div class="card mt-1">
<div class="card-body">
    <div class="row">	
        <!-- Calendar Controls -->
        <div class="col-sm-4">
		  <div class="d-flex justify-content-start align-items-center ">
            <button  type="button" id="todo_view" class="btn btn-primary btn-sm me-2 fw-bold"> <i class="bi bi-chevron-down"></i> </button>  
               <!-- <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_160x56dp.png">	-->
			<h6> <월간상세일정> </h6> <span class="text-secondary mx-1" > <i class="bi bi-tree-fill"></i> 연차 </span>
		  </div>
        </div>
        <div class="col-sm-4">
            <div class="d-flex justify-content-center align-items-center mb-2">
                <button type="button" id="todo-prev-month" class="btn btn-primary  btn-sm me-2"><i class="bi bi-arrow-left"></i>   </button>
                 <span id="todo-current-period" class="text-dark fs-6 me-2"></span>
                <button  type="button" id="todo-next-month" class="btn btn-primary btn-sm me-2"><i class="bi bi-arrow-right"></i> </button>
                <button  type="button" id="todo-current-month" class="btn btn-outline-primary fw-bold btn-sm me-5"> <?php echo date("m",time()); ?> 월</button>                
            </div>        
        </div>       
		<div class="col-sm-4">			
			<div class="d-flex justify-content-end align-items-center mb-1">
				<div class="inputWrap me-1 d-flex align-items-center">
					<input type="text" name="searchTodo" id="searchTodo" class="form-control me-1" autocomplete="off" style="width:200px; font-size:12px; height:30px;" />
					<button type="button" class="btnClear d-flex align-items-center justify-content-center" ></button>
				</div> 				
				<button type="button" id="searchTodoBtn" class="btn btn-dark btn-sm me-2 d-flex align-items-center justify-content-center"> 
					<i class="bi bi-search"></i> 
				</button>								
			</div>  
		</div>
        </div>   	
		<div id="todo-board">
		<div class="row d-flex ">
			<div class="col-sm-5">		  
			</div>
			<div class="col-sm-7">            
				<!-- 라디오 버튼 추가 -->
				<div class="d-flex justify-content-end align-items-center mb-2">
					<label class="radio-label d-flex align-items-center me-3">
						<input type="radio" name="filter" id="filter_all" class="filter-radio me-2" checked>
						<span class="badge bg-dark">전체</span>
					</label>				
					<label class="radio-label d-flex align-items-center me-3">
						<input type="radio" name="filter" id="filter_al" class="filter-radio me-2">
						<span class="text-dark fw-bold"> 연차 </span>
					</label>
					<label class="radio-label d-flex align-items-center me-3">
						<input type="radio" name="filter" id="filter_registration" class="filter-radio me-2">
						<span class="badge bg-secondary">접수</span>
					</label>
					<label class="radio-label d-flex align-items-center me-3">
						<input type="radio" name="filter" id="filter_shutter" class="filter-radio me-2">
						<span class="badge bg-success">출고</span>
					</label>					
					<label class="radio-label d-flex align-items-center">
						<input type="radio" name="filter" id="filter_etc" class="filter-radio me-2">
						<span class="text-dark fw-bold"> 기타 </span>
					</label>					
				</div>		
			</div>
              		
			</div>    
			</div>    
		<div id="todosMain-list">
		</div>	
			
			<div class="row">		
				<div id="todo-calendar-container"></div>	
			</div>	
		</div>			
		<div class="row">
			<div class="col-sm-12">		
				<div id="todo-calendar-container" class="p-1"></div>
			</div>
		</div>
    </div>
</div>
</div>
 
 <?php if($chkMobile==false) { ?>
	<div class="container">     
 <?php } else { ?>
 	<div class="container-fluid">     
	<?php } ?>
    
<?php     

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();	

   $a = " where outdate='$now' and is_deleted ='0'  and  (devMode <> '1' OR devMode IS NULL) order by num desc ";    
   $sql="select * from $DB.output " . $a; 					
   $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
   $total_row=$stmh->rowCount();
   if($total_row>0) 
        include "./load_outputlist.php";
	else
		include "./load_null.php";		   
 ?> 
</div> 

<?php if($chkMobile==false) { ?>
<div class="container">     
<?php } else { ?>
<div class="container-fluid">     
<?php } ?>
	<div class="card">		
		<div class="card-header">       	      
			<div class="d-flex mb-2 mt-2 justify-content-center">    
				<H4> <span id="advice"> </span> </H4>
			</div>  
		</div>  
		<div class="card-body">     
		<?
		// 난수를 발생해서 이미지 불러오기 (명언 관련 이미지)

			$rndimg = rand(1,36);
			$maxwidth = 400;
			$maxheight = 400;
			
			print '<br> <div class="d-flex justify-content-center"> 		 ';
			$imgpath = './img/goodwordgif/' . $rndimg . '.gif' ;
			$imgsize = getimagesize($imgpath);

			print '<img	src="' . $imgpath . '">  </div>';
		?>  
		</div>
	</div>
</div>


 <?php if($chkMobile==false) { ?>
	<div class="container">     
 <?php } else { ?>
 	<div class="container-fluid">     
	<?php } ?>

<?php
	// if($_SESSION["division"] == '경동')		
		// include 'footer.php'; 
	// else		
		include 'footer.php'; 
?>

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
</script>

<script>  	
$(document).ready(function() {

    var indateCount = '<?php echo isset($indateCount) ? $indateCount : ''; ?>';
    var outdateCount = '<?php echo isset($outdateCount) ? $outdateCount : ''; ?>';
    var doneCount = '<?php echo isset($doneCount) ? $doneCount : ''; ?>';

    if ($('#indateCount').length && indateCount) {
        $('#indateCount').text(indateCount); 
    }
    if ($('#outdateCount').length && outdateCount) {
        $('#outdateCount').text(outdateCount); 
    }
    if ($('#doneCount').length && doneCount) {
        $('#doneCount').text(doneCount); 
    }
});

function viewBoard(sel, num) {
	if(sel==='notice')
		popupCenter("./notice/view.php?num=" + num + "&tablename=notice" , '공지사항', 1300, 850);	  
	if(sel==='qna')
		popupCenter("./qna/view.php?num=" + num + "&menu=no&page=1&tablename=qna" , '자료실', 1500, 900);	  
	if(sel==='rnd')
		popupCenter("./rnd/view.php?num=" + num + "&menu=no&tablename=rnd" , '개발일지', 1300, 900);	 
 	if(sel==='vote')
		popupCenter("./vote/view.php?num=" + num + "&menu=no&page=1&tablename=vote" , '투표', 1500, 900);	  
 	if(sel==='daylaborer')
		popupCenter("./daylaborer/write_form_ask.php?num=" + num + "&menu=no&page=1&tablename=daylaborer" , '일용직관리', 500, 550);	     
 	if(sel==='output')
		popupCenter("./output/write_form.php?mode=view&num=" + num + "&menu=no&tablename=output" , '수주', 1900, 920); 	     	
 	if(sel==='품의서')
		popupCenter("./askitem/write_form.php?mode=view&num=" + num + "&menu=no&tablename=eworks" , '품의서', 800, 850); 	     	
 	if(sel==='지출결의서')
		popupCenter("./askitem_ER/write_form.php?mode=view&num=" + num + "&menu=no&tablename=eworks" , '지출결의서', 800, 850); 	     	
}

alreadyShown = getCookie("notificationShown");   

var intervalId; // 인터벌 식별자를 저장할 변수
	
$(document).ready(function() {

    // DH모터 금일 정보 가져오기

    // if ($('#motor_registedate').length && motor_registedate) {
        // $('#motor_registedate').text(motor_registedate); 
    // }
    // if ($('#motor_duedate').length && motor_duedate) {
        // $('#motor_duedate').text(motor_duedate); 
    // }
    // if ($('#motor_outputdonedate').length && motor_outputdonedate) {
        // $('#motor_outputdonedate').text(motor_outputdonedate); 
    // }
	
});


function closeMsg(){
	var dialog = document.getElementById("myMsgDialog");
	dialog.close();
}
function closeDialog(){
	var dialog = document.getElementById("closeDialog");
	dialog.close();
}
		
function sendMsg(){
	var dialog = document.getElementById("myMsgDialog");
	dialog.close();
}
  	
function restorePageNumber(){
    window.location.reload();
}

document.addEventListener('DOMContentLoaded', () => {
    const parts = document.querySelectorAll('.part');
    const descParts = document.querySelectorAll('.desc-part');
    let index = 0;

    function showNextPart() {
        if (index < parts.length) {
            parts[index].classList.add('show');
            index++;
            setTimeout(showNextPart, 500); // Adjust the delay for each part to show up
        } else if (index - parts.length < descParts.length) {
            descParts[index - parts.length].classList.add('show');
            index++;
            setTimeout(showNextPart, 500); // Adjust the delay for each desc part to show up
        }
    }

    showNextPart();
});

$(document).ready(function(){
	// 결재창 계속 5초 간격 확인하기		  
	var timer = setInterval(function() 
	{
		// PHP에서 세션 변수 'level' 읽기
		var level = "<?php echo isset($_SESSION['level']) ? $_SESSION['level'] : ''; ?>";

		// level 변수가 비어있지 않으면 함수 실행
		if (level !== '') {
			 alert_eworkslist();	
		}
	
	}, 5000); 	// 5초 간격	  
});

$(document).ready(function() {
	
	function inputEnter(inputID, buttonID) {
		document.getElementById(inputID).addEventListener('keydown', function(event) {
			if (event.key === 'Enter') {
				document.getElementById(buttonID).click();
				event.preventDefault(); // 기본 동작 차단
			}
		});
	}
				
    // searchTodo 입력 필드에서 Enter 키를 누르면 searchTodoBtn 버튼 클릭
    inputEnter('searchTodo', 'searchTodoBtn');    
			
    // board_view
    $("#board_view").on("click", function() {
		var showBoardView = getCookie("showBoardView");		
		var board_list = $(".board_list");
		if (showBoardView === "show") {
			board_list.css("display", "none");
			setCookie("showBoardView",  "hide"  , 10);
		} else {
			board_list.css("display", "block");
			setCookie("showBoardView",  "show"  , 10);
		}		
    });	

	// 최초 실행될때 쿠키값을 기억하고 행하는 구문임.	
	var showBoardView = getCookie("showBoardView");		
	var board_list = $(".board_list");
	if (showBoardView === "show") {		
		board_list.css("display", "block");		
	} else {
		board_list.css("display", "none");	
	}	
	
    // todo_view
    $("#todo_view").on("click", function() { 
		var showTodoView = getCookie("showTodoView");
		var todoCalendarContainer = $("#todo-list");
		if (showTodoView === "show") {
			todoCalendarContainer.css("display", "none");
			setCookie("showTodoView",  "hide"  , 10);
		} else {
			todoCalendarContainer.css("display", "block");
			setCookie("showTodoView",  "show"  , 10);
		}
    });	

	// 페이지 로드 시, 쿠키 값에 따라 todo-list의 표시 상태를 결정
	var showTodoView = getCookie("showTodoView");
	var todoCalendarContainer = $("#todo-list");
	if (showTodoView === "show") {
		todoCalendarContainer.css("display", "block");
	} else {
		todoCalendarContainer.css("display", "none");
	}	
	
// 인생의 조언 60가지 가져와서 보여주기
fetch('advice.json')
  .then(response => response.json())
  .then(data => {
    let randomIndex = Math.floor(Math.random() * data.length);
    let advice = data[randomIndex].advice;
    document.getElementById('advice').innerHTML = "오늘의 격언 : " + "'" + advice + "'";
  });	
		

});

// 차량관리 창 호출
function viewCar(num) {
	var title = '차량관리';
	var tablename = 'car';
	popupCenter('/car/write_form.php?mode=modify&num=' + num + '&tablename=' + tablename,  title , 1100, 900);   
}

// 지게차 관리 창 호출
function viewLift(num) {
	var title = '지게차관리';
	var tablename = 'lift';
	popupCenter('/lift/write_form.php?mode=modify&num=' + num + '&tablename=' + tablename,  title , 1100, 900);   
}

// 인정검사 호출
function viewACI(num, prodcode) {
	let url; 
	const tablename = 'output';

    // prodcode에 따라 URL 설정
    if (prodcode.startsWith('KS') || prodcode.startsWith('KW')) {
        url = "/output/write_ACI.php?num=" + num + "&tablename=" + tablename;
    } else {
        url = "/output/write_ACI_slat.php?num=" + num + "&tablename=" + tablename;
    }

    customPopup(url, '인정검사', 800, 900);
}

// Initialize all tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            html: true,
            placement: 'top'
        });
    });
});

// Bootstrap 5 Popover 초기화 (페이지 어디서든 한 번만 호출)
document.addEventListener('DOMContentLoaded', function () {
    var popoverTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="popover"]')
    );
    popoverTriggerList.forEach(function (el) {
        new bootstrap.Popover(el);
    });
});

 </script> 
  
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/instock/common/viewJS.php'; ?> <!--공통 JS -->

</body>
</html>

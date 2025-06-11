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

$_SESSION["company"] = '주일기업';  // 세션으로 기록

$today = date("Y-m-d");

require_once($_SERVER['DOCUMENT_ROOT'] . "/load_header.php");

// (접수/출고 등) 가져오기
include "load_info.php";
?>
 
<title> (주)주일기업 </title> 
  
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

/* 모바일 레이아웃 스타일 */
@media (max-width: 65px) {
    .mobile-layout .btn {
        font-size: 5rem;
        padding: 0.75rem;
    }
    .mobile-layout .radio-label .badge {
        font-size: 4.5rem;
        padding: 1rem;
    }
}
    /* 라디오 버튼 크기를 10배로 키움 */
    .filter-radio {
        width: 15px; /* 기본 크기 지정 */
        height: 15px; /* 기본 크기 지정 */
        transform: scale(1.2); /* 크기를 10배로 확대 */
        transform-origin: 0 0; /* 좌측 상단을 기준으로 확대 */
        margin-right: 6px; /* 확대된 크기에 맞게 여백 조정 */
    }

    /* 라디오 버튼이 너무 커지면 상하좌우 여백이 부족해지므로 조정 */
    .radio-label {
        display: flex;
        align-items: center;
        margin-bottom: 10px; /* 각 라디오 버튼 사이에 적당한 여백 추가 */
    }
	
</style>  
</head> 
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/myheader1.php'); // 주일기업 ?>
	
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
		// print $eworks_level  ;		
		foreach ($tabs as $label => $tabId) {
			$badgeId = "badge" . $tabId;	
			
    ?>
        <div class="mb-1 mt-1">
		     <?php if ($label !== "알림") 
				{					
					if($eworks_level && ($tabId>=3) )
					{
					  print '<button type="button" class="btn btn-dark rounded-pill" onclick="seltab(' . $tabId . '); "> ';
					  echo $label; 
					  print '<span class="badge badge-pill badge-dark" id="' . $badgeId . '"></span>';				  
					} 
					else if (!$eworks_level)  // 일반결재 상신하는 그룹
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

<?php include 'mymodal.php'; ?>  

 <?php if($chkMobile==false) { ?>
	<div class="container">     
 <?php } else { ?>
 	<div class="container-fluid">     
	<?php } ?>

<div class="card mt-2 mb-1 " style="background-color:#f0f8ff;" >	
<div class="row d-flex mt-1 mb-1">		
	<div class="col-sm-2">
		<button  type="button" id="board_view" class="btn btn-primary btn-sm me-2 fw-bold"> <i class="bi bi-chevron-down"></i> </button>            
	</div>		
	<div class="col-sm-8">
		<div class="d-flex justify-content-center align-items-center"> 	
			<span class="fw-bold shop-header fs-5" > 2025년 건강하고 보람차게 ~ </span> 	
		</div>
	</div>			
	<div class="col-sm-2">
	  <div class="d-flex justify-content-end" > 
		(주)주일기업 &nbsp;
	  </div>
	</div>
</div>
<div class="row d-flex board_list"  >			
	<!-- 전일 경영 Report -->
	<div class="col-sm-3 board_list" >
		 <!-- 공사진행 -->	
		 <div class="card justify-content-center  my-card-padding">
			<div class="card-header text-center  my-card-padding ">
					공사진행
				</div>
				<div class="card-body  my-card-padding">	
				<table class="table table-bordered table-hover table-sm">									
					<thead class="align-middle">	
						<tr>																
							<th class="text-center"> 장비투입 </th>													
							<th class="text-center"> 시공중 </th>									
							<th class="text-center"> 결선중 </th>													
							<th class="text-center"> 착공전 </th>									
							<th class="text-center"> <span  data-bs-toggle="tooltip" data-bs-placement="right" title="착공 1개월 이내" > 착공(M-1) </span>   </th>									
						</tr>
					</thead>
					
					<tbody class="align-middle">					 
							<tr onclick="window.location.href='./work/list.php'" style="cursor:pointer;">								
								<td class="text-center">                                                                                   
								<span class="text-muted ">
								<a href="../load_request_equipment.php?header=header">
									<span class="badge bg-secondary" id="total_equipment" >  </span> 
								</a>
								</span>                                            
								</td>
								<td class="text-center">                                                                                    
								<span class="text-muted "> 
								<a href="../load_work.php?header=header">
									<span class="badge bg-primary" id="total_work_su_main" >  </span> 
								</a>
								</span>                                            
								</td>
								<td class="text-center">                                                                                   
								<span class="text-muted "> 
								<a href="../load_work_wire.php?header=header">
									<span class="badge bg-success" id="total_work_wire_su_main" >  </span>  
								</a>
								</span>
								</td>
								<td class="text-center">                                                                                   
								<a href="../load_work_before.php?header=header">	
									<span class="text-muted "> <span class="text-center badge bg-dark" id="contractCount" >  </span>  
								</a>
								</span>                                            
								</td>
								<td class="text-center">                      
								<a href="../load_work_new.php?header=header">								
									<span class="text-muted "> <span class="text-center badge bg-dark" id="WorkboforeCount" >  </span> 
								</a>
								</span>                                            
								</td>
							</tr>
							</tbody>
						</table>												   
				</div> 
			</div>	
		 <!-- 주요 요청사항 -->	
		 <div class="card justify-content-center  my-card-padding">
			<div class="card-header text-center  my-card-padding f6-5">
					요청사항
				</div>
				<div class="card-body  my-card-padding">	
				<table class="table table-bordered table-hover table-sm">									
						<thead class="align-middle">	
							<tr>																
								<th class="text-center "> 방문 </th>									
								<th class="text-center "> 실측 </th>									
								<th class="text-center "> 발주 </th>													
								<th class="text-center "> 결선 </th>													
								<th class="text-center "> 인정라벨부착 </th>													
								<th class="text-center "> AS </th>													
							</tr>
						</thead>						
						<tbody class="align-middle">					 
							<tr style="cursor:pointer;">								
								<td class="text-center">                                                                                   
								<span class="text-muted"> 
								<a href="../load_request_visit.php?header=header">	
									<span class="badge bg-secondary" id="display_visit" >  </span>  
								</a>
								</span>
								</td>
								<td class="text-center">                                                                                   
								<span class="text-muted  "> 
								<a href="../load_request_measure.php?header=header">	
									<span class="badge bg-primary" id="display_measure" >  </span>  
								</a>
								</span>
								</td>
								<td class="text-center">                                                                                   
								<span class="text-muted  "> 
								<a href="../load_request_order.php?header=header">	
									<span class="badge bg-danger" id="display_order" >  </span>  
								</a>
								</span>
								</td>
								<td class="text-center">                                                                                   
								<span class="text-muted  ">
								<a href="../load_request_cablework.php?header=header">	
									<span class="badge bg-success" id="display_cablework" >  </span>  
								</a>
								</span>
								</td>
								<td class="text-center">                                                                                   
								<span class="text-muted  ">
								<a href="../load_request_label.php?header=header">	
									<span class="badge bg-warning" id="display_label" >  </span>  
								</a>
								</span>
								</td>
								<td class="text-center">                                                                                   
								<span class="text-muted  ">
								<a href="../load_request_as.php?header=header">	
									<span class="badge bg-info" id="display_as" >  </span> 
								</a>
								</span>
								</td>
							</tr>
							</tbody>
						</table>												   
				</div> 
			</div>	  <!-- 주요 요청사항 -->	
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

				$sql = "SELECT * FROM chandj.eworks WHERE (al_askdatefrom <= CURDATE() AND al_askdateto >= CURDATE()) AND al_company ='주일' AND is_deleted IS NULL ";
				$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
				$total_row=$stmh->rowCount();
				if($total_row>0 ) 
				{
				?>  					
				<div class="card justify-content-center">							
					<div class="card-body  my-card-padding">                         
					<?php   				
						  include "./load_aldisplay_juil.php";   				  				  
					?>  					
					</div>							
				</div>   
				<?php   				
				}
				?>  
		    </div>
	   </div>  <!-- 금일 연차 -->			
	</div>  <!-- end of col-sm-3 -->
	
	<div class="col-sm-3  board_list">   
		             	          						
	</div>	<!-- end of col-sm-3 -->	
	<div class="col-sm-3  board_list">   
		<!-- 차량관리 -->		
		<div class="card justify-content-center">		
			<div class="card-header  text-center  my-card-padding">
				<a href="juilcar/list.php?header=header"> 차량관리 </a>	
				<!-- <span class="cursor-pointer" onclick="popupCenter('juilcar/list.php' , '차량관리', 1800, 800);"> 차량관리 </span>	-->
			</div>
			<?php
			//도장관련 글이 일주일에 해당되면
			$now = date("Y-m-d", time()); 
			
			$a = " WHERE (is_deleted IS NULL or is_deleted ='0') ORDER BY purchase_date ASC  ";  

			$sql = "SELECT * FROM chandj.juilcar" . $a;

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
							print "<th class='text-center' > 차종 </th>";								
							print "<th class='text-center' > 담당 </th>";								
							print "<th class='text-center' > E오일 </th>";
							print "<th class='text-center' > 정비 </th>";
							print "</tr> </thead> ";													
							while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
								
								$num = $row['num']; 
								$vehicle_type = $row['vehicle_type']; 
								$responsible_person = $row['responsible_person']; 
								$engine_oil_data = json_decode($row['engine_oil_change_data'], true);
								$maintenance_data = json_decode($row['maintenance_data'], true);	
			
								echo "<tr onclick=\"viewCar('$num');\">";								
									print '<td class="text-center"> ';
									print $vehicle_type;
									print '</td>';								
									print '<td class="text-center"> ';
									print $responsible_person;
									print '</td>';					
									print '<td class="text-center"> ';
										if (!empty($engine_oil_data) && is_array($engine_oil_data)) {
											echo '<ul>';
											foreach ($engine_oil_data as $oil) {
												if (!empty($oil['engine_oil_change_date']) || !empty($oil['mileage'])) {
													echo '<li>';
													echo '' . htmlspecialchars($oil['engine_oil_change_date'], ENT_QUOTES, 'UTF-8') ;
													echo '<br> 주행거리 : ' . htmlspecialchars($oil['mileage'], ENT_QUOTES, 'UTF-8') . ' km';
													echo '</li>';
												}
											}
											echo '</ul>';
										} else {
											echo '-';
										}									
									print '</td>';		
									print '<td class="text-center"> ';
										if (!empty($maintenance_data) && is_array($maintenance_data)) {
											echo '<ul>';
											foreach ($maintenance_data as $maintenance) {
												if (!empty($maintenance['maintenance_date']) && !empty($maintenance['maintenance_record'])) {
													echo '<li>' . htmlspecialchars($maintenance['maintenance_date'], ENT_QUOTES, 'UTF-8') . ': ' 
														. htmlspecialchars($maintenance['maintenance_record'], ENT_QUOTES, 'UTF-8') . '</li>';
												}
											}
											echo '</ul>';
										} else {
											echo '-';
										}									
									print '</td>';											
									print '</tr>';								
								}
						} 
					?>  
					</tbody>
				</table>							
				<?php } ?>
		</div>  
		<!-- 차량운행일지 -->		
		<div class="card justify-content-center">		
			<div class="card-header  text-center  my-card-padding">
				<a href="juilcarlog/list.php?header=header"> 차량운행일지 </a>	
				<!-- <span class="cursor-pointer" onclick="popupCenter('juilcar/list.php' , '차량운행일지', 1800, 800);"> 차량운행일지 </span>	-->
			</div>
			<?php
			//도장관련 글이 일주일에 해당되면
			$now = date("Y-m-d", time()); 
			
			$a = " WHERE (is_deleted IS NULL or is_deleted ='0') ORDER BY use_date DESC  ";  

			$sql = "SELECT * FROM chandj.juilcarlog" . $a;

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
							print "<th class='text-center' > 일자 </th>";								
							print "<th class='text-center' > 차량번호 </th>";
							print "<th class='text-center' > 출발 </th>";
							print "<th class='text-center' > 도착 </th>";
							print "<th class='text-center' > 운행거리 </th>";
							print "</tr> </thead> ";													
							while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
								
								$num = $row['num']; 
								$use_date = $row['use_date']; 
								$month_day = date("m/d", strtotime($use_date));
								$car_number = $row['car_number']; 
								$departure = $row['departure']; 
								$destination = $row['destination']; 
								$driving_distance = $row['driving_distance']; 
			
								echo "<tr onclick=\"viewCar('$num');\">";								
									print '<td class="text-center"> ';
									print $month_day;
									print '</td>';					
									print '<td class="text-start"> ';								
									print $car_number;
									print '</td>';											
									print '<td class="text-start"> ';								
									print $departure;
									print '</td>';					
									print '<td class="text-start"> ';								
									print $destination;
									print '</td>';					
									print '<td class="text-end"> ';								
									print $driving_distance;
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
<!-- 전체 공지 -->	
		<div class="card justify-content-center">		
			<div class="card-header  text-center  my-card-padding">
				<a href="./notice1/list.php"> 전체 공지 </a>
			</div>
				<div class="card-body  my-card-padding" >					
				<?php   
				//전체 공지사항
				$now = date("Y-m-d",time()) ;				  
				$a="   where noticecheck='y' order by num desc ";  				  
				$sql="select * from $DB.notice1 " . $a; 		
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
						  $sqlsub="select * from $DB.notice1_ripple where parent=$item_num";
						  $stmh1 = $pdo->query($sqlsub); 
						  $num_ripple=$stmh1->rowCount(); 

						// 데이터-속성 추가하여 공지의 ID 또는 필요한 정보를 저장
						print '<td class="text-start" style="cursor:pointer;" onclick="viewBoard(\'notice1\', ' .  $item_num  . ');return false;"> &nbsp;  ' . $newImage . $row["subject"] ;

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
					<a href="./qna1/list.php"> 자료실 </a>
				</div>
			<div class="card-body  my-card-padding">	
			<table class="table table-bordered table-hover ">
				<tbody>				     
				<?php   

				//자료실
				$now = date("Y-m-d", time());

				// // 1주일 전 날짜 계산
				$oneWeekAgo = date("Y-m-d", strtotime("-3 week", strtotime($now)));			// 3주전 정보		
				$endOfDay = date("Y-m-d 23:59:59", time());
				$a = " WHERE regist_day BETWEEN '$oneWeekAgo' AND '$endOfDay' ORDER BY num DESC";

				$sql = "SELECT * FROM $DB.qna1" . $a;

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
					print ' onclick="viewBoard(\'qna1\', ' . $row["num"] . ');">' . $newImage . $row["subject"] . '</td>';
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
				<a href="./rnd1/list.php"> 개발일지 </a>
			</div>
		<div class="card-body  my-card-padding">	
		<table class="table table-bordered table-hover ">
			<tbody>				     
			<?php   
			// 개발일지
			$now = date("Y-m-d", time());


			// // 1주일 전 날짜 계산
			$oneWeekAgo = date("Y-m-d", strtotime("-1 week", strtotime($now)));			// 1주전 정보		
			$endOfDay = date("Y-m-d 23:59:59", time());
			$a = " WHERE regist_day BETWEEN '$oneWeekAgo' AND '$endOfDay' ORDER BY num DESC";

			$sql = "SELECT * FROM $DB.rnd1" . $a;

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
					print ' onclick="viewBoard(\'rnd1\', ' . $row["num"] . ');">' . $newImage . $row["subject"] . '</td>';
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
    <div class="container-fluid">     
<?php } ?>     
<div class="card mt-1">
<div class="card-body">

<?php if($chkMobile==false) { ?>    
<div class="row">
    <div class="col-sm-4">
        <div class="d-flex justify-content-start align-items-center">
            <button type="button" id="todo_view" class="btn btn-primary btn-sm me-2 fw-bold"><i class="bi bi-chevron-down"></i></button>
            <h5><월간상세일정></h5>
        </div>
    </div>		
    <div class="col-sm-3">
        <div class="d-flex justify-content-center align-items-center mb-2">
            <button type="button" id="todo-prev-month" class="btn btn-primary btn-sm me-2"><i class="bi bi-arrow-left"></i></button>
            <span id="todo-current-period" class="text-dark fs-6 me-2"></span>
            <button type="button" id="todo-next-month" class="btn btn-primary btn-sm me-2"><i class="bi bi-arrow-right"></i></button>
            <button type="button" id="todo-current-month" class="btn btn-outline-primary fw-bold btn-sm me-5"><?php echo date("m", time()); ?> 월</button>
        </div>
    </div>
    <div class="col-sm-5">
        <!-- 라디오 버튼 추가 -->
        <div class="d-flex justify-content-end align-items-center">
            <label class="radio-label">
                <input type="radio" name="filter" id="filter_all" class="filter-radio" checked>
                <span class="checkmark"></span> <span class="badge bg-dark" > 전체 </span>
            </label>
            <label class="radio-label">				
					<input type="radio" name="filter" id="filter_al" class="filter-radio">
					<span class="checkmark"></span> <span class="text-dark"> 연차 </span>				
            </label>			
            <label class="radio-label">
                <input type="radio" name="filter" id="filter_hyeonseol" class="filter-radio">
                <span class="checkmark"></span> <span class="badge bg-success" > 현설 </span>
            </label>
            <label class="radio-label">
                <input type="radio" name="filter" id="filter_ipchal" class="filter-radio">
               <span class="checkmark"></span> <span class="badge bg-warning" > 입찰 </span>
            </label>
            <label class="radio-label">
                <input type="radio" name="filter" id="filter_etc" class="filter-radio">
                <span class="checkmark"></span> <span class="badge bg-secondary" > 기타 </span>
            </label>
        </div>
    </div>
</div>    

<?php } else { ?>

<div class="row <?php echo $chkMobile ? 'mobile-layout' : ''; ?>  mb-3">
    <div class="d-flex mb-2 fs-1 justify-content-center mb-3 mt-3">
        <div class="d-flex justify-content-center align-items-center flex-column flex-sm-row">
            <button type="button" id="todo_view" class="btn btn-primary btn-lg mb-3 mt-3  fw-bold fs-1 me-5 ms-2"><i class="bi bi-chevron-down"></i></button>
            <h5 class="text-center fs-1 text-sm-start"><월간상세일정></h5>
        </div>
    </div>		
    <div class="d-flex mb-2 fs-1 justify-content-center mb-3 mt-3">
        <div class="d-flex justify-content-center align-items-center mb-2 flex-column flex-sm-row">
            <button type="button" id="todo-prev-month" class="btn btn-primary btn-lg mb-2 fs-1 me-4"><i class="bi bi-arrow-left"></i> </button>
            <span id="todo-current-period" class="text-dark fs-1 text-center me-2"></span>
            <button type="button" id="todo-next-month" class="btn btn-primary btn-lg fs-1 me-2 ms-2"><i class="bi bi-arrow-right"></i> </button>
            <button type="button" id="todo-current-month" class="btn btn-outline-primary fs-1 fw-bold btn-lg "><?php echo date("m", time()); ?> 월</button>
        </div>
    </div>	
	<div class="d-flex mb-2 fs-1 justify-content-center  align-items-center mb-5 mt-2">            
            <label class="radio-label fs-2 mb-5 mt-5">
                <input type="radio" name="filter" id="filter_all" class="filter-radio fs-1"  checked>
                <span class="checkmark"></span> <span class="badge bg-dark fs-1"> 전체 </span>
            </label>
            <label class="radio-label fs-2 mb-5 mt-5">
                <input type="radio" name="filter" id="filter_al" class="filter-radio">
                <span class="checkmark"></span> <span class="text-dark fs-1"> 연차 </span>
            </label>
            <label class="radio-label fs-2 mb-5 mt-5">
                <input type="radio" name="filter" id="filter_hyeonseol" class="filter-radio">
                <span class="checkmark"></span> <span class="badge bg-success fs-1"> 현설 </span>
            </label>
            <label class="radio-label fs-2 mb-5 mt-5">
                <input type="radio" name="filter" id="filter_ipchal" class="filter-radio">
                <span class="checkmark"></span> <span class="badge bg-warning fs-1"> 입찰 </span>
            </label>
            <label class="radio-label fs-2 mb-5 mt-5">
                <input type="radio" name="filter" id="filter_etc" class="filter-radio">
                <span class="checkmark"></span> <span class="badge bg-secondary fs-1"> 기타 </span>
            </label>
    </div>
</div>  

<?php }  ?>    
<div class="row">
    <div class="col-sm-12">
        <div id="todo-calendar-container"></div>
    </div>
</div>

    </div>
</div>
</div>

<!-- 추가된 CSS -->
<style>
.radio-label {
    position: relative;
    padding-left: 30px;
    margin-right: 20px;
    font-size: 1.2em; /* 글씨 크기 조정 */
    cursor: pointer;
    user-select: none;
}

.radio-label input[type="radio"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

.radio-label .checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: #fff;
    border-radius: 50%;
    border: 2px solid #2196F3;
}

.radio-label input[type="radio"]:checked + .checkmark {
    background-color: #2196F3;
}

.radio-label .checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.radio-label input[type="radio"]:checked + .checkmark:after {
    display: block;
}

.radio-label .checkmark:after {
    top: 6px;
    left: 6px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
}
</style>
 
 <?php if($chkMobile==false) { ?>
	<div class="container">     
 <?php } else { ?>
 	<div class="container-fluid">     
	<?php } ?>
    
<?php     
require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();	

// 장비사용중 현장
$a = " ";    
$sql="select * from {$DB}.work where (is_deleted IS NULL or  is_deleted = 0)  " . $a; 					   
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$counter = 0;
while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
	$equipmentList = json_decode($row['equipmentList'], true);
		
		
	if (!$equipmentList || !is_array($equipmentList)) {
		$equipmentList = []; // 비어있거나 JSON 파싱 실패시 빈 배열 처리
	}
	foreach ($equipmentList as $equipment) {
		 if(!empty($equipment['col2']) && empty($equipment['col3']) )
		 {
			$counter++;		 			
		 }		
	}
}
if($counter>0) 
	include "./load_request_equipment.php";
	
// 시공중 현장
$a = " where  (is_deleted IS NULL or  is_deleted = 0)  and workStatus='시공중' ";    
$sql="select * from {$DB}.work " . $a; 					
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$total_row=$stmh->rowCount();
if($total_row>0) 
	include "./load_work.php";

// 결선중 현장	
$a = " where  (is_deleted IS NULL or  is_deleted = 0)  and cableworkStatus='결선중' ";    
$sql="select * from {$DB}.work " . $a; 					
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$total_row=$stmh->rowCount();
if($total_row>0) 
	include "./load_work_wire.php";			

// 착공전 
$sql="select * from {$DB}.work where (is_deleted IS NULL or is_deleted = 0) and workStatus='착공전' order by workday desc"; 	
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$total_row = $stmh->rowCount();
if($total_row > 0) 
	include "./load_work_before.php";

// 착공(1개월 이내) 등록된 현장을 필터링
$a = " WHERE regist_day >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) and (is_deleted IS NULL or  is_deleted = 0)  ";    
$sql = "SELECT * FROM {$DB}.work " . $a; 					
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$total_row = $stmh->rowCount();
if($total_row > 0) 
	include "./load_work_new.php";

// 방문요청
$a = " where  (is_deleted IS NULL or  is_deleted = 0)  and checkstep like '%방문요청%' ";      
$sql = "SELECT * FROM {$DB}.work " . $a; 					
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$total_row = $stmh->rowCount();
if($total_row > 0) 
    include "./load_request_visit.php";
	
// 실측요청
$a = " where  (is_deleted IS NULL or  is_deleted = 0)  and checkstep like '%실측요청%' ";      
$sql = "SELECT * FROM {$DB}.work " . $a; 					
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$total_row = $stmh->rowCount();
if($total_row > 0) 
    include "./load_request_measure.php";

// 발주요청
$a = " where  (is_deleted IS NULL or  is_deleted = 0)  and checkstep like '%발주요청%' ";      
$sql = "SELECT * FROM {$DB}.work " . $a; 					
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$total_row = $stmh->rowCount();
if($total_row > 0) 
    include "./load_request_order.php";
	
// 결선요청
$a = " where (is_deleted IS NULL or  is_deleted = 0) and checkstep like '%결선요청%' ";      
$sql = "SELECT * FROM {$DB}.work " . $a; 					
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$total_row = $stmh->rowCount();
if($total_row > 0) 
    include "./load_request_cablework.php";
	
// 인정라벨부착요청
$a = " where (is_deleted IS NULL or  is_deleted = 0)  and checkstep like '%인정라벨부착요청%' ";      
$sql = "SELECT * FROM {$DB}.work " . $a; 					
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$total_row = $stmh->rowCount();
if($total_row > 0) 
    include "./load_request_label.php";

// AS요청
$a = " where  (is_deleted IS NULL or  is_deleted = 0)  and checkstep like '%AS요청%' ";      
$sql = "SELECT * FROM {$DB}.work " . $a; 					
$stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
$total_row = $stmh->rowCount();
if($total_row > 0) 
    include "./load_request_as.php";	
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
		include 'footer1.php'; 
?>

</div> 
</div>
</div> <!-- container-fulid end -->
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
</script>

<script>  
	
$(document).ready(function() {    
    var contractCount = '<?php echo isset($contractCount) ? $contractCount : ''; ?>';
    var total_work_su_main = '<?php echo isset($total_work_su_main) ? $total_work_su_main : ''; ?>';
    var total_work_wire_su_main = '<?php echo isset($total_work_wire_su_main) ? $total_work_wire_su_main : ''; ?>';
    var total_equipment = '<?php echo isset($display_equipment) ? $display_equipment : ''; ?>';
    var display_visit = '<?php echo isset($display_visit) ? $display_visit : ''; ?>';
    var display_measure = '<?php echo isset($display_measure) ? $display_measure : ''; ?>';
    var display_order = '<?php echo isset($display_order) ? $display_order : ''; ?>';
    var display_cablework = '<?php echo isset($display_cablework) ? $display_cablework : ''; ?>';
    var display_label = '<?php echo isset($display_label) ? $display_label : ''; ?>';
    var display_as = '<?php echo isset($display_as) ? $display_as : ''; ?>';
    var display_equipment = '<?php echo isset($display_equipment) ? $display_equipment : ''; ?>';
    var WorkboforeCount = '<?php echo isset($WorkboforeCount) ? $WorkboforeCount : ''; ?>'; // 착공전 건수
    

    if ($('#WorkboforeCount').length && WorkboforeCount) {
        $('#WorkboforeCount').text(WorkboforeCount); 
    }
    if ($('#contractCount').length && contractCount) {
        $('#contractCount').text(contractCount); 
    }
    if ($('#total_work_su_main').length && total_work_su_main) {
        $('#total_work_su_main').text(total_work_su_main); 
    }
    if ($('#total_work_wire_su_main').length && total_work_wire_su_main) {
        $('#total_work_wire_su_main').text(total_work_wire_su_main); 
    }
    if ($('#display_visit').length && display_visit) {
        $('#display_visit').text(display_visit); 
	}
    if ($('#display_measure').length && display_measure) {
        $('#display_measure').text(display_measure); 
    }
    if ($('#display_order').length && display_order) {
        $('#display_order').text(display_order); 
    }
    if ($('#display_cablework').length && display_cablework) {
        $('#display_cablework').text(display_cablework); 
    }
    if ($('#display_label').length && display_label) {
        $('#display_label').text(display_label); 
    }
    if ($('#display_as').length && display_as) {
        $('#display_as').text(display_as); 
    }
    if ($('#display_equipment').length && display_equipment) {
        $('#display_equipment').text(display_equipment); 
    }
    if ($('#total_equipment').length && total_equipment) {
        $('#total_equipment').text(total_equipment); 
    }
    
});

function viewBoard(sel, num) {
	if(sel==='notice1')
		popupCenter("./notice1/view.php?num=" + num + "&tablename=notice1" , '공지사항', 1300, 850);	  
	if(sel==='qna1')
		popupCenter("./qna1/view.php?num=" + num + "&menu=no&page=1&tablename=qna1" , '자료실', 1500, 900);	  
	if(sel==='rnd1')
		popupCenter("./rnd1/view.php?num=" + num + "&menu=no&tablename=rnd1" , '개발일지', 1300, 900);	 
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
  	
function restoreFirstPage(){
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

});
</script> 
   
<script>
document.addEventListener('DOMContentLoaded', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });  
  	// $("#order_form_write").modal("show");	  
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
	var tablename = 'juilcar';
	popupCenter('/juilcar/write_form.php?mode=modify&num=' + num + '&tablename=' + tablename,  title , 1100, 900);   
}

</script>
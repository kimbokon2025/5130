<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

$header = $_REQUEST['header'] ?? '';

$title_message = '결선중 ';   

if($header == 'header') 
{
	include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php'; 
	require_once($_SERVER['DOCUMENT_ROOT'] . '/myheader1.php');	
	$tablename = 'work'; 
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
	$pdo = db_connect();  

   echo '<title>  ' . $title_message . ' </title> ';
	
}
 
$now = date("Y-m-d",time());
  
 // 기간을 정하는 구간
$fromdate=date("Y-m-d");  
 
$sql="select * from chandj.work where is_deleted IS NULL and cableworkStatus='결선중' order by endworkday desc"; 					
	                         
$nowday=date("Y-m-d");   // 현재일자 변수지정          					 

$start_num=1;					
	   
 try{   
    $stmh = $pdo->query($sql);             
    $total_row = $stmh->rowCount();  
  } catch (PDOException $Exception) {
	print "오류: ".$Exception->getMessage();
  }   
  
// print $sql;  

$total_work_wire_su_main = $total_row;
  
?>

<style>
.rounded-card {
        border-radius: 15px !important;  /* 조절하고 싶은 라운드 크기로 설정하세요. */
    }
th {		
   text-align : center;	
}  
.table-hover tbody tr:hover {
	cursor: pointer;
}	
</style>
	
<div class="card rounded-card  mb-2 mt-3">
<div class="card-header  text-center ">            
		<div class="row"> 		
			<div class="col-sm-12"> 		
				<div class="d-flex  justify-content-center align-items-center "> 				   
					<span  class="text-dark fs-6 me-3"> 
					<a href="../load_work_wire.php?header=header">
						<span class="badge bg-success me-1 fs-6"> <?=$title_message?> </span> 				
					 </a>  </span> 			
				  <h6> <span id="total_work_wire_su" class="badge bg-success"></span>  
				  </h6>
				</div>			 
			</div>				 
		</div>			
	</div>
<div class="card-body p-2 m-1 mb-3  d-flex justify-content-center">	
		
<table class="table table-bordered table-hover table-sm">
	<thead class="table-success">
		<tr>
			<th class="text-center" style="width:60px;">번호</th>
			<th class="text-center" style="width:90px;">시공완료일</th>
			<th class="text-center" style="width:250px;">현장명</th>
			<th class="text-center" style="width:150px;">발주처</th>
			<th class="text-center" style="width:150px;">담당자</th>
			<th class="text-center" style="width:120px;">연락처</th>
			<th class="text-center" style="width:100px;">공사담당</th>
			<th class="text-center" style="width:100px;">진행현황</th>                        
			<th class="text-center">시공내역</th>
		</tr>
	</thead>
<tbody>	 
	<?php
	$start_num = $total_row;
	while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
		include $_SERVER['DOCUMENT_ROOT'] . '/work/_row.php'; 

		$checkstep_str = "";
		$font1 = "black";
		switch ($checkstep) {
			case "상담요청" : $checkstep_str = "상담요청"; $font1 = "black"; break;
			case "방문요청" : $checkstep_str = "방문요청"; $font1 = "blue"; break;
			case "실측요청" : $checkstep_str = "실측요청"; $font1 = "red"; break;
			case "발주요청" : $checkstep_str = "발주요청"; $font1 = "grey"; break;
			case "결선요청" : $checkstep_str = "결선요청"; $font1 = "green"; break;
			case "견적요청" : $checkstep_str = "견적요청"; $font1 = "brown"; break;
			default: $font1 = "grey"; $checkstep_str = "";
		}
	?>
		<tr onclick="redirectToView('<?= $num ?>', '<?= $tablename ?>')">
			<td class="text-center"><?= $start_num ?></td>
			<td class="text-center"><?= $endworkday == '0000-00-00' ? '' : $endworkday ?></td>
			<td class="text-start  fw-bold text-success"><?= $workplacename ?></td>
			<td class="text-center"><?= $secondord ?></td>
			<td class="text-center"><?= $secondordman ?></td>
			<td class="text-center"><?= $secondordmantel?></td>
			<td class="text-center"><?= $chargedperson ?></td>
			<td class="text-center">
				<?php
				switch ($cableworkStatus) {									
					case "결선중" : $badge_class = "badge bg-success"; break;
					case "결선완료" : $badge_class = "badge bg-danger"; break;  // 빨간색
					default: $badge_class = "badge bg-secondary"; break;  // 회색
				}
				?>
				<span class="<?= $badge_class ?>"><?= $cableworkStatus ?></span>
			</td>	                       							
			
			<td class="text-start"><?= $worklist ?></td>
		</tr>
	<?php
		$start_num--;
	}
	?>
</tbody>
</table>
</div>
</div> <!--container-->
<script>
// 페이지 로딩
$(document).ready(function(){	
    var loader = document.getElementById('loadingOverlay');
    if(loader) 
		loader.style.display = 'none';
});
</script>

<script> 
function redirectToView(num) {
	popupCenter("./work/write_form.php?mode=view&tablename=work&num=" + num , "공사수주내역", 1850, 900); 	      
}

$(document).ready(function(){	    	
	document.getElementById('total_work_wire_su').textContent = ' <?= $total_row ?>';
}); 
</script> 
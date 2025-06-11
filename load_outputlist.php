<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

$sum_motor = array();
 
$now = date("Y-m-d",time());
  
 // 기간을 정하는 구간
$fromdate=date("Y-m-d");  
 
$sql="select * from chandj.output where outdate  between date('$fromdate') and date('$fromdate') and is_deleted ='0'  and (devMode <> '1' OR devMode IS NULL)  order by num desc"; 	// 개발자 모드 안나오게 수정				
	                         
$nowday=date("Y-m-d");   // 현재일자 변수지정          					 

$start_num=1;					
	   
 try{   
    $stmh = $pdo->query($sql);             
    $total_row = $stmh->rowCount();  
  } catch (PDOException $Exception) {
	print "오류: ".$Exception->getMessage();
  }   
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
			<div class="col-sm-7"> 		
			<div class="d-flex  justify-content-end align-items-center "> 				   
				<span id="dis_text2" class="text-dark fs-6 me-5"> <a href="output/list.php"> 금일(<?=$today?>) 출고 예정   </a>  </span> 
			</div>	
		  </div>	
		<div class="col-sm-5"> 		
		<div class="d-flex  justify-content-end align-items-center "> 				   
			  <h6> <span id="total_screen" class="text-primary me-2"></span> 
				   <span id="total_screen_m2" class="badge bg-primary me-5"></span> 
				  <span id="total_egi" class="text-secondary me-2"></span> 
				  <span id="total_egi_m2" class="badge bg-secondary me-2"></span> 
			  </h6>
		</div>			 
			</div>				 
		</div>			
		
			<!--
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="popupCenter('motor/register.php','일일접수',1500,900);">  <i class="bi bi-r-square-fill"></i> </ion-icon> 일일접수 </button>    							 
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="popupCenter('motor/plan_making.php','출고예정',1500,900);">  <ion-icon name="calendar-outline"></ion-icon> 출고예정 </button>    							 
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="popupCenter('motor/plan_making_kd.php','경동입고',1500,900);">  <i class="bi bi-truck-flatbed"></i> 경동입고 </button>    							 
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="popupCenter('motor/delivery.php','화물택배',1500,900);">  <i class="bi bi-truck"></i> 화물택배 </button>    							 
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="popupCenter('motor/print_group.php','출고증 묵음',1500,900);">  <i class="bi bi-printer"></i> 출고증 묶음 </button>    							 
			-->
	</div>
<div class="card-body p-2 m-1 mb-3  d-flex justify-content-center">	
		
<table class="table table-bordered table-hover table-sm">
    <thead class="table-primary">	
		<tr>
			<th style="width:40px;"> 번호</th>			
			<th style="width:50px;"> 진행</th>
			<th class="text-center" style="width:40px;">절곡</th>
			<th class="text-center" style="width:30px;">모터</th>			
			<th style="width:100px;"> 발주처</th>
			<th style="width:180px;"> 현장명</th>
			<th style="width:90px;"> 운송방법</th>
			<th style="width:80px;"> 수신처</th>
			<th style="width:200px;"> 수신 주소</th>
			<th style="width:30px;"> 틀수</th>
			<th style="width:30px;"> (㎡)</th>
			<th style="width:250px;"> 비고</th>
		</tr>
	</thead>
<tbody>	 

<?php  

try{  
	$stmh = $pdo->query($sql);             
	$total_row = $stmh->rowCount(); 

	$start_num=$total_row;    // 페이지당 표시되는 첫번째 글순번
	
	$total_m2 = 0 ;
	$total_m2_formatted = 0 ;
	$total_egi_m2_formatted = 0 ;
	
	$total_screen_sum = 0;		
	$total_screen_m2 = 0;		
	
	$total_egi_sum = 0;		
	$total_egi_m2 = 0;		
	$total_sum = 0;		
	$total_egi = 0;		

	while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {

        include $_SERVER['DOCUMENT_ROOT'] . "/output/_row.php";		
		
		// 콤마를 제거하고 숫자로 변환
		$screen_su_cleaned = floatval(str_replace(',', '', $screen_su));
		$screen_m2_cleaned = floatval(str_replace(',', '', $screen_m2));

		$slat_su_cleaned = floatval(str_replace(',', '', $slat_su));
		$slat_m2_cleaned = floatval(str_replace(',', '', $slat_m2));

		$screenlists = json_decode($screenlist, true);
		// var_dump($screenlists["screen_m2"]);
	  					
        if($steel!="1")
			$bend_state="";			
		
        if($motor=="1")
        $motor="모터";			

        if($root=="주일") 
        $root_font="text-dark";
        else			
        $root_font="text-primary";						

        $date_font="text-dark";  // 현재일자 Red 색상으로 표기
        if($today==$outdate) {
        $date_font="text-danger";
        }	
					  
		if($outdate!="") {
		$week = array("(일)" , "(월)"  , "(화)" , "(수)" , "(목)" , "(금)" ,"(토)") ;
		$outdate = $outdate . $week[ date('w',  strtotime($outdate)  ) ] ;
		}  
					  
			 ?>
			<tr onclick="redirectToView_motor('<?=$num?>')">	
			    <td >	<?=$start_num ?></td>						    
				<td class="text-center" > 
					<?php			
						  switch ($regist_state) {
							case   "등록"     :  
								$regist_word="등록"; 
								echo '<span class="badge bg-danger">' .$regist_word . '</span>';
								break;
							case   "수정"     :  
								 $regist_word="수정"; 
								 echo '<span class="badge bg-warning blink">' .$regist_word . '</span>';
								 break;	
							case   "접수"     :  
								 $regist_word="접수"; 
								 echo '<span class="badge bg-success">' .$regist_word . '</span>';
								 break;	
							case   "완료"     :  
								 $regist_word="완료"; 
								 echo '<span class="badge bg-dark">' .$regist_word . '</span>';
								break;					
						}						
					?>							
				</td>
			<td class="text-center" style="width:40px;"><?= $bend_state ?></td>
			<td class="text-center " style="width:40px;"><?= $motor ?></td>
			<td class="text-center " ><?= $secondord ?></td>
				<td class="text-start" > <?=$outworkplace?> </td>
				<td class="text-center" >
				<?php			
						if (strpos($delivery, '경동') !== false) {
							echo '<span class="text-primary">' . $delivery . '</span>';
						} else if (strpos($delivery, '대신') !== false) {
							echo '<span class="text-success">' . $delivery . '</span>';
						} else {
							switch ($delivery) {
								case "직접배차":
									echo '<span class="badge bg-secondary">' . $delivery . '</span>';
									break;
								case "직접수령":
									echo '<span class="badge bg-dark">' . $delivery . '</span>';
									break;
								case "상차(선불)":									
									echo '<span class="badge bg-info">' . $delivery . '</span>';
									break;
								case "상차(착불)":																	
									echo '<span class="badge bg-warning">' . $delivery . '</span>';
									break;
								default:
									echo '<span class="text-dark">' . $delivery . '</span>';
									break;
							}
						}
						
					?>			
				</td>				
				<td ><?=$receiver ?></td>
				<td class="text-start" ><?=$outputplace ?></td>
	         <?php		
			$row_sum = $screen_su_cleaned + $slat_su_cleaned ;
			$row_m2 = $screen_m2_cleaned + $slat_m2_cleaned ;	

			$row_sum_formatted = number_format($row_sum);			
			$row_m2_formatted = number_format($row_m2, 1);			
						  
			if ($row_sum_formatted > 0) 
				print '<td class="text-end" style=>' . $row_sum_formatted . '</td>';
			else
				print '<td class="text-end" style=>&nbsp;</td>'; 

			if ($row_m2_formatted > 0) 
				print '<td class="text-end" style=>' . $row_m2_formatted . '</td>';
			else
				print '<td class="text-end" style=>&nbsp;</td>';
				
			$total_sum += $screen_su_cleaned;
			$total_m2 += $screen_m2_cleaned;
			$total_egi += $slat_su_cleaned;
			$total_egi_m2 += $slat_m2_cleaned;
		
		// // 소수점 첫째자리까지만 포맷팅
		$total_m2_formatted = number_format($total_m2, 1);
		$total_egi_m2_formatted = number_format($total_egi_m2, 1);
		?>		
			<td class="text-start">
				<?= $comment ?>
				<?php if (!empty($updatecomment) && $regist_state !== '완료'): ?>
					<span class="text-danger fw-bold blink"><?= $updatecomment ?></span>
				<?php else: ?>
					<span class="text-danger fw-bold"><?= $updatecomment ?></span>
				<?php endif; ?>

			</td>
		</tr>
      <?php
        $start_num--;  
      } 
      } catch (PDOException $Exception) {
      print "오류: ".$Exception->getMessage();
      }           
      ?>
            </tbody>
    </table>
</div>
</div>

<script> 

function redirectToView_motor(num) {
	popupCenter("./output/write_form.php?mode=view&tablename=output&num=" + num , "수주내역", 1900, 950); 	  
    
}

$(document).ready(function(){
	
	// $("#batchBtn").click(function(){
		// popupCenter('./output/batchDB_invoice.php','묶음출고증',1800,780);  
	// });
	// $("#mobileBtn").click(function(){
		// popupCenter('./mmotor/list.php','모바일 관리화면',1920,1000);  
	// });

});

function restorePageNumber() {
    var savedPageNumber = getCookie('motorpageNumber');
    // if (savedPageNumber) {
        // dataTable.page(parseInt(savedPageNumber) - 1).draw('page');
    // }
	location.reload(true);
}

$(document).ready(function(){	    
	document.getElementById('total_screen').textContent = '스크린 : <?= $total_sum ?>';
	document.getElementById('total_screen_m2').textContent = '면적 : <?= $total_m2_formatted ?> ㎡';
	document.getElementById('total_egi').textContent = '철재(스라트) : <?= $total_egi ?>';
	document.getElementById('total_egi_m2').textContent = '면적 : <?= $total_egi_m2_formatted ?> ㎡';
}); 
 

</script> 



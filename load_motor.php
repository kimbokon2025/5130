<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

$sum_motor = array();
 
$now = date("Y-m-d",time()) ;
  
// 출고완료 수량 체크  
$a="   where outputdate='$now'  and is_deleted IS NULL ";    
$sql="select * from " . $DB . ".motor " . $a; 					
	   
 try{   
    $stmh = $pdo->query($sql);             
    $total_row = $stmh->rowCount();  
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  } 
  
$motor_outputdonedate = $total_row ; 	    
  
	$a="   where deadline='$now'  and is_deleted IS NULL  order by num desc ";  
	$sql="select * from " . $DB . ".motor " . $a; 					

	  
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
 <!--		 <button class="btn btn-primary btn-sm " type="button" id="batchBtn" > <ion-icon name="print-outline"></ion-icon></button> &nbsp;&nbsp;		 		 
		 <button class="btn btn-secondary btn-sm " type="button" id="lasermotorplanBtn" > <ion-icon name="calendar-outline"></ion-icon>  </button> &nbsp;&nbsp;		  

		 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			
		 		 -->
		 
            <span id="dis_text2" class="text-dark fs-6"> <a href="motor/list.php"> DH모터 금일(<?=$today?>) 출고 예정   </a> </span>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="popupCenter('motor/register.php','일일접수',1500,900);">  <i class="bi bi-r-square-fill"></i> </ion-icon> 일일접수 </button>    							 
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="popupCenter('motor/plan_making.php','출고예정',1500,900);">  <ion-icon name="calendar-outline"></ion-icon> 출고예정 </button>    							 
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="popupCenter('motor/plan_making_kd.php','경동입고',1500,900);">  <i class="bi bi-truck-flatbed"></i> 경동입고 </button>    							 
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="popupCenter('motor/delivery.php','화물택배',1500,900);">  <i class="bi bi-truck"></i> 화물택배 </button>    							 
			<button type="button" class="btn btn-dark btn-sm me-1" onclick="popupCenter('motor/print_group.php','출고증 묵음',1500,900);">  <i class="bi bi-printer"></i> 출고증 묶음 </button>    							 
        </div>
        <div class="card-body p-2 m-1 mb-3  d-flex justify-content-center">	
		
<table class="table table-bordered table-hover table-sm">
    <thead class="table-primary">	
            <tr >
				<th  style="width:80px; " >진행상황</th>
				<th class="text-center" style="width:60px;"> 출하 <ion-icon name="image-outline"></ion-icon> </th>	
				<th  style="width:140px; ">발주처</th>                
                <th  style="width:20%; "  > 현장명 </th>
				<th class="text-center"  style="width:60px;"> 배송방법 </th>                                												                
				<th class="text-center"  style="width:150px;"> 상차지 </th>                                
				<th class="text-center"  style="width:15%;"> 배송주소 </th>                             
				<th class="text-center"  style="width:25%;"> 내역 </th>                                												
				
            </tr> 
        </thead>
        <tbody>	 

<?php  

	
try{  
	  $stmh = $pdo->query($sql);             
	  $total_row = $stmh->rowCount(); 

	$start_num=$total_row;    // 페이지당 표시되는 첫번째 글순번

	while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
		include 'motor/_row.php';  
	    
	  // 사진등록 찾기
	  $registpicture = '' ;
	  $sqltmp=" select * from ".$DB.".picuploads where parentnum ='$num'";		  
		 try{  
		// 레코드 전체 sql 설정
		   $stmhtmp = $pdo->query($sqltmp);    
		   
		   while($rowtmp = $stmhtmp->fetch(PDO::FETCH_ASSOC)) {
				$registpicture = "등록" ;
				}		 
		   } catch (PDOException $Exception) {
			print "오류: ".$Exception->getMessage();
		  			
		   }		
	// 주자재 합계 문자열 생성
			$contentslist = '';
			$firstItemAdded = false;

			$items = [
				'realscreensu' => '스크린M',
				'realsteelsu' => '철재M',
				'realprotectsu' => '방범M',
				'realsmokesu' => '제연M',
				'realexplosionsu' => '방폭M'
			];

			foreach ($items as $key => $value) {
				if (!empty($row[$key])) {
					if (!$firstItemAdded) {
						$contentslist .= '<span class="badge bg-primary"> 모,브 </span> ';
						$firstItemAdded = true;
					}
					$contentslist .= $value . ' ' . $row[$key] . 'EA, ';
				}
			}

		// 마지막 쉼표 제거
		$contentslist = rtrim($contentslist, ', ');

		// 연동제어기
		$conses = json_decode($controllerlist, true);
		$controllerlist = '';
		$firstAccessory = true;

		foreach ($conses as $cons) {
			if ($firstAccessory) {
				$controllerlist .= '<span class="badge bg-success"> 연동 </span>  ';
				$firstAccessory = false;
			}
			$controllerlist .= $cons['col2'] . ':' . $cons['col3'] . 'EA, ';
		}

		// 마지막 쉼표 제거
		$controllerlist = rtrim($controllerlist, ', ');

		// 부속자재 합계 문자열 생성
		$accessories = json_decode($accessorieslist, true);
		$accessorieslist = '';
		$firstAccessory = true;

		foreach ($accessories as $accessory) {
			if ($firstAccessory) {
				$accessorieslist .= '<span class="badge bg-secondary"> 부속 </span>  ';
				$firstAccessory = false;
			}
			$accessorieslist .= $accessory['col1'] . ':' . $accessory['col2'] . 'EA, ';
		}

		// 마지막 쉼표 제거
		$accessorieslist = rtrim($accessorieslist, ', ');

		// 각 리스트를 합치고 <br> 추가
		$finalList = '';
		if (!empty($contentslist)) {
			$finalList .= $contentslist . '<br>';
		}
		if (!empty($controllerlist)) {
			$finalList .= $controllerlist . '<br>';
		}
		if (!empty($accessorieslist)) {
			$finalList .= $accessorieslist . '<br>';
		}

		// 마지막 <br> 제거
		$finalList = rtrim($finalList, '<br>');

		$contentslist =  $finalList;	
		
       if($deliverymethod == '대신화물' || $deliverymethod == '경동화물'  )
		{
			// 상차지에 화물지점표기함
		  $address = $delbranch . (!empty($delbranchaddress) ? ' (' . $delbranchaddress . ')' : '');
		  $loadplace = '(주)대한 본사';
		}
       if($deliverymethod == '택배'  )
		{
			// 상차지에 화물지점표기함		  
		  $loadplace = '(주)대한 본사';
		}
       if($deliverymethod == '직배송' )
		{
			// 상차지에 화물지점표기함		  
		  $loadplace = '(주)대한 본사';
		}

?>			
		 
		<tr onclick="redirectToView_motor('<?=$num?>')">			
			<td class="text-center align-middle">
				<?php
				if ($status == '접수대기') {
					echo '<span class="badge bg-warning">' . $status . '</span>';
				} else if ($status == '접수확인') {
					echo '<span class="badge bg-success">' . $status . '</span>';
				} else if ($status == '준비중') {
					echo '<span class="badge bg-info">' . $status . '</span>';
				}else if ($status == '출고대기') {
					echo '<span class="badge bg-secondary">' . $status . '</span>';
				}else if ($status == '출고완료') {
					echo '<span class="badge bg-danger">' . $status . '</span>';
				}
				?>
			</td>
			<td class="text-center align-middle fw-bold"> <?=$registpicture?>  </td>	
			<td class="text-center align-middle"> <?=$secondord?> </td>
			<td class="align-middle"> <?=$workplacename?> </td>
			<td class="text-center align-middle"><?= $deliverymethod ?></td>
			<td class="text-center align-middle"><?= $loadplace?> </td>
			<td class="text-start align-middle"><?= $address?> </td>
			<td class="text-start align-middle"> <?=$contentslist?> </td>
			
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
	popupCenter("./motor/write_form.php?mode=view&num=" + num, "DH모터 수주내역", 1850, 900); 	  
    
}

$(document).ready(function(){
	
	$("#batchBtn").click(function(){
		popupCenter('./motor/batchDB_invoice.php','묶음출고증',1800,780);  
	});
	$("#mobileBtn").click(function(){
		popupCenter('./mmotor/list.php','모바일 관리화면',1920,1000);  
	});

});

function restorePageNumber() {
    var savedPageNumber = getCookie('motorpageNumber');
    // if (savedPageNumber) {
        // dataTable.page(parseInt(savedPageNumber) - 1).draw('page');
    // }
	location.reload(true);
}


</script> 
  

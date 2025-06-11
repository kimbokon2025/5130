<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

$tablename = "eworks";
 
  require_once("./lib/mydb.php");
  $pdo = db_connect();	
  
  include "./annualleave/load_DB.php";
 
  $page=1;	 
  
  $scale = 20;       // 한 페이지에 보여질 게시글 수
  $page_scale = 20;   // 한 페이지당 표시될 페이지 수  10페이지
  $first_num = ($page-1) * $scale;  // 리스트에 표시되는 게시글의 첫 순번.
	 
  $now = date("Y-m-d",time()) ;
  
  $a= " where status<>'end' and is_deleted IS NULL  and al_askdatefrom IS NOT NULL  order by num desc ";  
  
  if($user_name == '이경묵')	  
		$a = " where status<>'end' and al_part='제조파트' and is_deleted IS NULL  and al_askdatefrom IS NOT NULL order by num desc ";  
   if($user_name== '최장중')	  
		$a = " where status<>'end' and al_part='지원파트' and is_deleted IS NULL  and al_askdatefrom IS NOT NULL order by num desc ";  	
	   
   $sql="select * from " . $DB . "." . $tablename . "  " . $a; 		

   // print $sql;   
	   
 try{  
	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;	
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  }  	  
if($total_row > 0 ) 
{		   
	   
 try{  
	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;	
	 
?>			 

    <div class="card rounded-card  mb-2 mt-3">
        <div class="card-header  text-center ">   
				<h6>  <span class="text-center" > (연차 신청) 결재 진행   </span> </h6>
		</div>
            
	<div class="card-body p-2 m-1 mb-3  d-flex justify-content-center">	
	<div class="table-reponsive">
		<table class="table table-bordered table-hover">
		<thead class="table-primary">
			<tr>
			  <th class="text-center" scope="col" >번호</th>
			  <th class="text-center" scope="col">신청인 성명</th>
			  <th class="text-center" scope="col">구분</th>
			  <th class="text-center" scope="col">연차 시작일</th>
			  <th class="text-center" scope="col">연차 종료일</th>
			  <th class="text-center" scope="col">잔여 일수</th>
			  <th class="text-center" scope="col">신청 일수</th>
			  <th class="text-center" scope="col">신청 사유</th>
			  <th class="text-center" scope="col">결재진행 상태</th>
			</tr>
		  </thead>
		  <tbody>
        <?php
	    
        if ($page <= 1)
          $start_num = $total_row;
        else
          $start_num = $total_row - ($page - 1) * $scale;

        while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
          include "./annualleave/rowDBask.php";
          // 연차 잔여일수 산출
          $totalusedday = 0;
          $totalremainday = 0;
          for ($i = 0; $i < count($totalname_arr); $i++) {
            if ($author == $totalname_arr[$i]) {
              $availableday = $availableday_arr[$i];
            }
          }

          // 연차 사용일수 계산
          for ($i = 0; $i < count($totalname_arr); $i++) {
            if ($author == $totalname_arr[$i]) {
              $totalusedday = $totalused_arr[$i];
              $totalremainday = $availableday - $totalusedday;
            }
          }
		  
			   switch($status) {
				   
				   case 'send':
				      $statusstr = '결재상신';
					  break;
				   case 'ing':
				      $statusstr = '결재중';
					  break;
				   case 'end':
				      $statusstr = '결재완료';
					  break;
				   default:
					  $statusstr = '';
					  break;
			   }		  
		  
		?>
		<tr onclick="popupCenter('./annualleave/process.php?num=<?=$num?>', '연차 결재진행', 420, 750);">
		  <td class="text-center"><?=$start_num?></td>
		  <td class="text-center"><?=$author?></td>
		  <td class="text-center"><?=$al_item?></td>
		  <td class="text-center"><?=$al_askdatefrom?></td>
		  <td class="text-center"><?=$al_askdateto?></td>
		  <td class="text-center"><?=$totalremainday?></td>
		  <td class="text-center"><?=$al_usedday?></td>
		  <td class="text-center"><?=$al_content?></td>
		  <td class="text-center"><?=$statusstr?></td>
		</tr>     

			<?php
			
			if($status=='1차결재')
				array_push($approvalarr,'mirae');
			if($al_part=='지원파트' && $status=='send')
				array_push($approvalarr,'cjj');
			if($al_part=='제조파트' && $status=='send')
				array_push($approvalarr,'7473');
			
			
			$start_num--;
			 } 
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  }  
   // 페이지 구분 블럭의 첫 페이지 수 계산 ($start_page)
      $start_page = ($current_page - 1) * $page_scale + 1;
   // 페이지 구분 블럭의 마지막 페이지 수 계산 ($end_page)
      $end_page = $start_page + $page_scale - 1;  
 ?>
 
       </tbody>
    </table>
  </div>
     
</div>
</div>

 <?php } ?>

  
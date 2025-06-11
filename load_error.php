 <?php
 
  require_once("./lib/mydb.php");
  $pdo = db_connect();	
  
  $page=1;	 
  
  $scale = 20;       // 한 페이지에 보여질 게시글 수
  $page_scale = 20;   // 한 페이지당 표시될 페이지 수  10페이지
  $first_num = ($page-1) * $scale;  // 리스트에 표시되는 게시글의 첫 순번.
	 
  $now = date("Y-m-d",time()) ;
  
  $a= " where approve<>'처리완료' order by num desc ";  
  
  // if($user_name== '이경묵')	  
		// $a=" where approve='결재상신' and part='제조파트' order by num desc ";  
   // if($user_name== '김선영')	  
		// $a=" where approve='결재상신' and approve<>'1차결재' and part='지원파트' order by num desc ";  	
	   
   $sql="select * from " . $DB . ".error " . $a; 		

// print $sql;   
	   
	 try{  

	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;			 
?>			 
		 
<style>
    .rounded-card {
        border-radius: 15px !important;  /* 조절하고 싶은 라운드 크기로 설정하세요. */
    }

	
</style>			
			
<div class="card rounded-card mt-2 mb-1">
<div class="card-header  text-center ">   
  <h6>  <span class="text-center" > 부적합(불량) 결재 요청  </span> </h6>
</div>
<div class="card-body p-1 justify-content-center">	

  <div class="d-flex justify-content-center">
    <table class="table table-bordered table-hover">
    <thead class="table-danger">
      <tr>          
        <th class="text-center" scope="col" style="width:8%;" >보고자</th>
        <th class="text-center" scope="col" style="width:8%;" >유형</th>
        <th class="text-center" scope="col" style="width:10%;" >발생일</th>
        <th class="text-center" scope="col" style="width:22%;"  >현장명</th>
        <th class="text-center" scope="col" style="width:22%;">원인</th>
        <th class="text-center" scope="col" style="width:22%;">개선대책</th>
        <th class="text-center" scope="col" style="width:10%;" >진행</th>
      </tr>
    </thead>
    <tbody>		 		 
	
		<?php  
			  if ($page<=1)  
				$start_num=$total_row;    // 페이지당 표시되는 첫번째 글순번
			  else 
				$start_num=$total_row-($page-1) * $scale;
	    
	       while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {	
	       include "./error/rowDB.php";	   		   
	    ?>
				   
		<tr>			
			<td class="text-center">
				<a href='./error/process.php?num=<?=$num?>'  onclick="popupCenter(this.href, '부적합 보고서 조회/결재', 1000, 960);return false;">
					<?=$reporter?>
				</a>
			</td>
			<td class="text-center">
				<a href='./error/process.php?num=<?=$num?>'  onclick="popupCenter(this.href, '부적합 보고서 조회/결재', 1000, 960);return false;">
					<?=iconv_substr($errortype,0,7,"utf-8")?>
				</a>
			</td>
			<td class="text-center">
				<a href='./error/process.php?num=<?=$num?>'  onclick="popupCenter(this.href, '부적합 보고서 조회/결재', 1000, 960);return false;">
					<?=$occur?>
				</a>
			</td >
			<td  class="text-start">
				<a href='./error/process.php?num=<?=$num?>'  onclick="popupCenter(this.href, '부적합 보고서 조회/결재', 1000, 960);return false;">
					<?=$place?>
				</a>
			</td>
			<td  class="text-start">
				<a href='./error/process.php?num=<?=$num?>'  onclick="popupCenter(this.href, '부적합 보고서 조회/결재', 1000, 960);return false;">
					<?=$content?>
				</a>
			</td>
			<td  class="text-start">
				<a href='./error/process.php?num=<?=$num?>'  onclick="popupCenter(this.href, '부적합 보고서 조회/결재', 1000, 960);return false;">
					<?=$method?>
				</a>
			</td>
			<td class="text-center">
				<a href='./error/process.php?num=<?=$num?>'  onclick="popupCenter(this.href, '부적합 보고서 조회/결재', 1000, 960);return false;">
					<?=$approve?>
				</a>
			</td>
		</tr>

			<?php
			
			if($approve=='1차결재')
				array_push($approvalarr,'신동조');
			if($part=='지원파트' && $approve=='결재상신')
				array_push($approvalarr,'김선영');
			if($part=='제조파트' && $approve=='결재상신')
				array_push($approvalarr,'이경묵');
			
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
  
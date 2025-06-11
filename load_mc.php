<?php
if(!isset($_SESSION))      
		session_start(); 
if(isset($_SESSION["DB"]))
		$DB = $_SESSION["DB"] ;	
 $level= $_SESSION["level"];
 $user_name= $_SESSION["name"];
 $user_id= $_SESSION["userid"];	
 
  require_once("./lib/mydb.php");
  $pdo = db_connect();	  
 
  $page=1;	 
  
  $scale = 20;       // 한 페이지에 보여질 게시글 수
  $page_scale = 20;   // 한 페이지당 표시될 페이지 수  10페이지
  $first_num = ($page-1) * $scale;  // 리스트에 표시되는 게시글의 첫 순번.
	 
  $now = date("Y-m-d",time()) ;
  
  $a="   where done is null order by num desc ";  	
 
  $sql="select * from ".$DB.".mymclist " . $a; 					
	  
  $NocheckDeviceNum = 0;  // 장비 미점검 숫자	  
	 try{  

	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;			 
	  
	  if ($page<=1)  
			$start_num=$total_row;    // 페이지당 표시되는 첫번째 글순번
		  else 
			$start_num=$total_row-($page-1) * $scale;
	  
?>				 
<style>
.rounded-card {
        border-radius: 15px !important;  /* 조절하고 싶은 라운드 크기로 설정하세요. */
    }

.custom-thead {
  background-color: #FFDAB9; /* 원하는 배경색 */
  /* 기타 원하는 스타일 속성 */
}

.my-gradient {
   background: linear-gradient(to right, #f2f2f2, #dddddd);
}
	
</style>			
		
   <div class="card rounded-card mb-2 mt-1">
		<div class="card-header  text-center ">   
		  <div id="toggleMCBtn"  ><h6>  <span style="cursor:pointer;" > 장비 미점검 <span class="badge bg-primary"><?=$total_row?></span> 건 </span> </h6> </div>
		</div>        
		
     <div class="card-body p-2 m-1 mb-3 justify-content-center" >
     <div id="MCtable" style="display:none;"  >
        
	 <table class="table table-bordered table-hover table-sm">
	   <thead class="table-primary text-center">	
	   
      <th scope="col">번호</th>
      <th scope="col">점검예정일</th>
      <th scope="col">장비명</th>
      <th scope="col">점검구분</th>
      <th scope="col">담당(정)</th>
      <th scope="col">담당(부)</th>          
    </thead>
    <tbody>		
	 
		    	      
	<?php  
	    
	       while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {			   
	       include "./qc/rowDB.php";	   			    
   ?>
				<tr onclick="popupCenter('../qc/view.php?num=<?=$num?>', '장비 점검', 1200, 800); return false;" style="cursor:pointer;">
				  <td class="text-center"><?=$start_num?></td>
				  <td class="text-center"><?=$checkdate?></td>
				  <td class="text-center"><?=$item?></td>
				  <td class="text-center"><?=$term?></td>
				  <td class="text-center"><?=$writer?></td>
				  <td class="text-center"><?=$writer2?></td>
				</tr>
			<?php
			$start_num--;
			$NocheckDeviceNum ++ ;
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
  
  
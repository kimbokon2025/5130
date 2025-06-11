<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

isset($_REQUEST["num"])  ? $num=$_REQUEST["num"] :   $num=''; 
isset($_REQUEST["workitem"])  ? $workitem=$_REQUEST["workitem"] :   $workitem=''; 
 	  
require_once("./lib/mydb.php");
$pdo = db_connect();	
// $find="unitper";	    //검색할때 고정시킬 부분 저장 ex) 전체/공사담당/건설사 등


 try{
      $sql = "select * from ".$DB."." . $workitem . "  where num = ? ";   // workitem 전달 받아서 
      $stmh = $pdo->prepare($sql); 
      $stmh->bindValue(1,$num,PDO::PARAM_STR); 
      $stmh->execute();
      $count = $stmh->rowCount();              
    if($count<1){  
         // 변수 초기화
		$update_log = "";
				
     }   else    {      
		$row = $stmh->fetch(PDO::FETCH_ASSOC);
		$update_log = $row["update_log"];
		// $update_log = str_replace("  ", "<br>", $update_log);
		$update_log_arr = preg_replace('/(\d{4})/', "<br>$1", $update_log);		
		// $update_log_arr = explode("&#10", $update_log );		
		// var_dump($update_log_arr);
		}      									  		      										

     }catch (PDOException $Exception) {
       print "오류: ".$Exception->getMessage();
 }
 


	?>	

 <?php include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php' ?> 

</head>

<body>

<form id="board_form" name="board_form" method="post" enctype="multipart/form-data"   >			 

<title> 로그 기록 </title>

<div class="container" style="width:450px;">

<!--Extra Full Modal -->	
	<div class="card justify-content-center mt-3 mb-3 p-2 m-2">
		<div class="card-header  justify-content-center">
			<h5 class="modal-title text-center fs-5" id="myModalLabel16"> 로그 기록  </h5>								
		</div>
		<div class="card-body" >											
			<div class="card">
				<table class="table table-striped">
				   <thead class="modal-title justify-content-center"> 
					  <tr>
					  <th class="justify-content-center text-center fs-6" > 기록 시간 / 사용자 </th>
					  </tr>
					</thead>
				   <tbody> 				   				   
				   
					  <?php
					  						  
						$update_log_arr = str_replace("&#10;", "<br>", $update_log_arr);					  
						  echo " <tr> <td class='text-center fs-6 '> ";
						  // echo $update_log_arr[$i];
						  echo $update_log_arr;
						  echo "</td> </tr>";
					  
					 // }
					  ?>					  
					</tbody>
				</table>
			</div>
		</div>
			<div class="modal-footer justify-content-start mt-2 mb-2">
				<button type="button"  id="closeBtn" class="btn btn-secondary btn-sm">		
					<i class="bi bi-x-lg"></i> 창닫기
				</button>
			</div>
		</div>
		
</div>
</form>
</body>
</html>

<!-- 페이지로딩 -->
<script>
// 페이지 로딩
$(document).ready(function(){	
    var loader = document.getElementById('loadingOverlay');
    loader.style.display = 'none';
});
</script>
	 
<script>

ajaxRequest = null;

/* ESC 키 누를시 팝업 닫기 */
$(document).keydown(function(e){
		//keyCode 구 브라우저, which 현재 브라우저
		var code = e.keyCode || e.which;
		
		if (code == 27) { // 27은 ESC 키번호
			self.close();
		}
});


$(document).ready(function(){	  
	 
	// 창닫기 버튼
	$("#closeBtn").on("click", function() {
		self.close();
	});	

   	
});	  // end of ready		
	
</script>
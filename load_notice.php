 <?php
  require_once("./lib/mydb.php");
  $pdo = db_connect();	
 
  $page=1;	 
  
  $scale = 10;       // 한 페이지에 보여질 게시글 수
  $page_scale = 10;   // 한 페이지당 표시될 페이지 수  10페이지
  $first_num = ($page-1) * $scale;  // 리스트에 표시되는 게시글의 첫 순번.
	 
  $now = date("Y-m-d",time()) ;
  
		$a="   where noticecheck='y' order by num desc ";  
  
	   $sql="select * from mirae8440.notice " . $a; 					
	   
	 try{  

	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;			 
?>		
		 
	  <span class="text-secondary"  >
          	 전체공지  </span> &nbsp;&nbsp;
     
    	      
			<?php  
			$color_arr = array();
			array_push($color_arr,"blue");
			array_push($color_arr,"brown");
			array_push($color_arr,"purple");
			array_push($color_arr,"black");
	        $counter = 0;
	       while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
			   
			  $num=$row["num"];

			  $subject=$row["subject"];
			  $content=$row["content"];
			 ?>
			     <a href="./notice/view.php?DB=notice&num=<?=$num?>&page=1" style="font-size:16px;color:<?=$color_arr[$counter]?>;font-weight:bold;">
			 	 
					<span class="blink_me" style="width:300px;" > <?=$subject?> </span>		
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;					
				
				  </a>
			<?php
            $counter++;
			 } 
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  }  
?>
    
<?php   
// 금일 연차인 사람 나타내기
   require_once("./lib/mydb.php");
   $pdo = db_connect();   
   $now = date("Y-m-d",time()) ;  
   
   $sql="select * from mirae8440.al where (askdatefrom between date('$now') and date('$now')) and (askdateto between date('$now') and date('$now'))" ;
   $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
   $total_row=$stmh->rowCount();
   if($total_row>0 ) 
      include "./load_aldisplay.php";   
  
  // between date('$yesterday') and date('$yesterday') 
  
?>  		  
      
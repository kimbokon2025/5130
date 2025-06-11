<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

require_once("./lib/mydb.php");
$pdo = db_connect();	

$now = date("Y-m-d",time()) ;
  
// 접수일자로 접수수량 체크  
$a="   where indate='$now' and is_deleted = '0'  order by num desc ";    
$sql="select * from " . $DB . ".output " . $a; 					
	   
	 try{  

	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;	  
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  }  	  
	  
$indateCount = $total_row;	  
  
// 납기예정 수량 체크  
$a="   where outdate='$now' and is_deleted = '0'   order by num desc ";    
$sql="select * from " . $DB . ".output " . $a; 					
	   
	 try{  

	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;	  
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  }  	  
	  
$outdateCount = $total_row;	  

// 출고완료 수량 체크  
$a="   where outdate='$now' and regist_state='완료' and is_deleted = '0'   order by num desc ";    
$sql="select * from " . $DB . ".output " . $a; 					
	   
  try{  
	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;	  
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  }  	  
	  
$doneCount = $total_row;	  


	  
?>
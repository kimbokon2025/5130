<?php


$dancre_registedate = 0;
$dancre_duedate  = 0;
$dancre_outputdonedate = 0;
$daontech_registedate = 0;
$daontech_duedate = 0;
$daontech_outputdonedate = 0;

$now = date("Y-m-d",time()) ;

// 접수일 기준
  
$a="   where orderday='$now' order by num desc ";    
$sql="select * from " . $DB . ".outorder " . $a; 						   
	 try{  

	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;			  				   
	  
	       while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {							   
				$firstord = $row["firstord"];
				if (strpos($firstord, '덴크리') !== false) {					
					$dancre_registedate ++ ;
				}
				if (strpos($firstord, '다온텍') !== false) {					
					$daontech_registedate ++ ;
				}

			 } 
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  }  
  
// 출하일 기준 (입고완료)
  
$a="   where deadline='$now' order by num desc ";    
$sql="select * from " . $DB . ".outorder " . $a; 						   
	 try{  

	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;			  				   
	  
	       while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {							   
				$firstord = $row["firstord"];
				if (strpos($firstord, '덴크리') !== false) {					
					$dancre_duedate ++ ;
				}
				if (strpos($firstord, '다온텍') !== false) {					
					$daontech_duedate ++ ;
				}

			 } 
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  }  
  
  
  
// 출하일 기준 (입고완료)
  
$a="   where workday='$now' order by num desc ";    
$sql="select * from " . $DB . ".outorder " . $a; 						   
	 try{  

	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();    
      $total_row=$temp1;			  				   
	  
	       while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {							   
				$firstord = $row["firstord"];
				if (strpos($firstord, '덴크리') !== false) {
					// '서한'이 포함되어 있지 않을 때 실행할 코드
					$dancre_outputdonedate ++ ;
				}
				if (strpos($firstord, '다온텍') !== false) {
					// '서한'이 포함되어 있지 않을 때 실행할 코드
					$daontech_outputdonedate ++ ;
				}

			 } 
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  }  
  
  
  
  
  
 ?>
 
	
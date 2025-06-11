<?php
for($i=0;$i<=1000;$i++)
{
  require_once("./lib/mydb.php");
  $pdo1 = db_connect();
  $num=$i;

 try{
     $sql = "select * from chandj.work where num=?";
     $stmh = $pdo1->prepare($sql);  
     $stmh->bindValue(1, $num, PDO::PARAM_STR);      
     $stmh->execute();            
      
     $row = $stmh->fetch(PDO::FETCH_ASSOC);
 	
     $item_num     = $row["num"];
	 $num=$row["num"];
    }		
  catch (PDOException $Exception) {
       print "오류: ".$Exception->getMessage();
}

/* 	print "접속완료"	  ; */
if($item_num!=0)
{	
   try{
     $pdo->beginTransaction();
  	 
     $sql = "insert into chandj.work(num)";  
	   
     $stmh = $pdo->prepare($sql); 
	 $numupdate= $item_num+1000;
     $stmh->bindValue(1, $numupdate, PDO::PARAM_STR);  

     $stmh->execute();
     $pdo->commit(); 
     } catch (PDOException $Exception) {
          $pdo->rollBack();
       print "오류: ".$Exception->getMessage();
     }   
   }
}

?>
  
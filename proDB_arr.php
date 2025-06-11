<?php

if(!isset($_SESSION))      
		session_start(); 
if(isset($_SESSION["DB"]))
		$DB = $_SESSION["DB"] ;	

header("Content-Type: application/json");  //json을 사용하기 위해 필요한 구문

isset($_REQUEST["table"])  ? $table=$_REQUEST["table"] :   $table=''; 
isset($_REQUEST["command"])  ? $command=$_REQUEST["command"] :   $command=''; 
isset($_REQUEST["field"])  ? $field=$_REQUEST["field"] :   $field=''; 
isset($_REQUEST["strtmp"])  ? $strtmp=$_REQUEST["strtmp"] :   $strtmp=''; 
isset($_REQUEST["recnum"])  ? $recnum=$_REQUEST["recnum"] :   $recnum=''; 
isset($_REQUEST["datanum"])  ? $datanum=$_REQUEST["datanum"] :   $datanum=''; 
isset($_REQUEST["arr"])  ? $arr=$_REQUEST["arr"] :   $arr=''; 
isset($_REQUEST["fieldarr"])  ? $fieldarr=$_REQUEST["fieldarr"] :   $fieldarr=''; 


$strarr = explode(",",$arr[0]);
$fieldstrarr = explode(",",$fieldarr[0]);


// 명령어 전달형식은 table이름 해당 필드명 field 배열은 arr1 command는 삽입/수정/삭제 수행

// 데이터 수정하는 구간			
	  
// Grid는 update인 경우 삭제하고 추가하는 방식으로 해야 함. 기존의 데이터의 개수가 다를 수 있으니 이렇게 처리함.
   require_once("./lib/mydb.php");
   $pdo = db_connect();
     
// update인 경우 
if($command=='update')
{	
	 try{
		$pdo->beginTransaction();   
		$sql = "update " . $DB . "." . $table . " set " . $field . "=? ";
		$sql .= " where num=?  LIMIT 1";		
		   
		 $stmh = $pdo->prepare($sql); 
		 $stmh->bindValue(1, $strtmp, PDO::PARAM_STR);  
		 $stmh->bindValue(2, $recnum, PDO::PARAM_STR);  
		 
		 $stmh->execute();
		 $pdo->commit(); 
			} catch (PDOException $Exception) {
			   $pdo->rollBack();
			   print "오류: ".$Exception->getMessage();
		   }    
}   
 
// delete인 경우 
if($command=='delete')
{	
   try{
     $pdo->beginTransaction();
     $sql = "delete from " . $DB . "." . $table . "  where num = ? " ;  
     $stmh = $pdo->prepare($sql);
	 $stmh->bindValue(1, $recnum, PDO::PARAM_STR);      
     $stmh->execute();   
     $pdo->commit(); 
		} catch (PDOException $Exception) {
		   $pdo->rollBack();
		   print "오류: ".$Exception->getMessage();
	   }    
}  

// insert인 경우 
if($command=='insert')
{	
     $sql = "insert into " . $DB . "." . $table . "  ( " ;     	

	for($j=0;$j<count($fieldstrarr);$j++)  //  필드 배열수 만큼 반복
	 {
		 if($j!=0)
			  $sql .= ' , ';
	   $sql .= $fieldstrarr[$j] ;
	 }
	  $sql .= ' )  values( ';

	for($j=0;$j<count($fieldstrarr);$j++)  //  필드 배열수 만큼 반복
	 {
		 if($j!=0 ) 
			  $sql .= ' , ';
	   $sql .= '?';
	 }	      
	$sql .= ' ) ';
	
   try{
     $pdo->beginTransaction();
     $stmh = $pdo->prepare($sql);
	 for($i=0;$i<count($strarr);$i++)
       $stmh->bindValue($i+1,$strarr[$i],PDO::PARAM_STR);      	 
	 
     $stmh->execute();   
     $pdo->commit(); 
		} catch (PDOException $Exception) {
		   $pdo->rollBack();
		   print "오류: ".$Exception->getMessage();
	   }    
}
   
//각각의 정보를 하나의 배열 변수에 넣어준다.
$data = array(
		"command" =>  $command,
		"recnum" =>  $recnum,
		"field" =>  $field,
		"table" =>  $table,
		"strtmp" =>  $strtmp,
		"strarr" =>  $strarr,
		"fieldstrarr" =>  $fieldstrarr,
		"sql" =>  $sql,
);

//json 출력
echo(json_encode($data, JSON_UNESCAPED_UNICODE));

?>


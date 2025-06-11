 <?php include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php' ?>
 
 <?php
  
  $file_dir = './imgmotor/'; 
  
 $num=$_REQUEST["num"];
 $search=$_REQUEST["search"];  //검색어

 if(isset($_REQUEST["check"])) 
	 $check=$_REQUEST["check"]; 
   else
     $check=$_POST["check"];  
 	 
require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();
 
 try{
     $sql = "select * from " . $DB . ".motor where num=?";
     $stmh = $pdo->prepare($sql);  
     $stmh->bindValue(1, $num, PDO::PARAM_STR);      
     $stmh->execute();            
      
    $row = $stmh->fetch(PDO::FETCH_ASSOC); 	 
  
	$workplacename=$row["workplacename"];
	$secondord=$row["secondord"];
	$deadline=$row["deadline"];

	$type=$row["type"];			  
	$inseung=$row["inseung"];			  	
					
     }catch (PDOException $Exception) {
       print "오류: ".$Exception->getMessage();
     }
  
// 포장 이미지에 대한 부분
 require_once("./lib/mydb.php");
 $pdo = db_connect();
 
 // 실측서 이미지 이미 있는 것 불러오기 
$picData=array(); 

$sql=" select * from " . $DB . ".ceilpicfile where parentnum ='$num' ";	

 try{  
   $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh   
   while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
			array_push($picData, $row["picname"]);			
        }		 
   } catch (PDOException $Exception) {
    print "오류: ".$Exception->getMessage();
  }  
  
$updir = './imgmotor/';

   
 ?>
 
 
 <title> (주) 대한 출하 사진 </title>
 </head>
  <body>
 <style>
    .rotated {
  transform: rotate(90deg);
  -ms-transform: rotate(90deg); /* IE 9 */
  -moz-transform: rotate(90deg); /* Firefox */
  -webkit-transform: rotate(90deg); /* Safari and Chrome */
  -o-transform: rotate(90deg); /* Opera */
}
</style> 

 <div  class="container">
    <div class="card">
     <div class="card-title justify-content-center ">
	 	<div class="d-flex  mb-1 justify-content-center fs-3"> 
			현장명 :       <?=$workplacename?>	
		</div>
	 </div>
       <div class="card-body ">	
	   
				<div class="table-reponsive mb-2  fs-4">
				<table class="table table-bordered">
				   <tbody>				   
					 <tr>
					   <td class="text-center fw-bold" > 발주처 </td>
							<td class="text-center" >	
								  <?=$secondord?>  
							</td>
					  <td class="text-center fw-bold" > 인승 </td>
					  <td class="text-center" >								
							<?=$inseung?> 					  
					  </td>
					  <td class="text-center fw-bold" > 타입 </td>
					     <td class="text-center" >	
							<?=$type?>                                                
						  </td>
						<td class="text-center fw-bold" > 납품일자 </td>
						<td class="text-center" >	
							  <?=$deadline?>                                               
						</td>
					  </tr>
					  
						
				</tbody>
				</table>
				</div>		   
	   
	   	   		   
		     <div class="d-flex  mt-2 mb-1 justify-content-center fs-3"> 
					 출하 사진 
			  </div>
	    <div class="d-flex  mt-2 mb-1 justify-content-center fs-3"> 	      
		  <div class='imagediv' >
	<?php
	  if(count($picData)>0)
	  {
	     for($i=0;$i<count($picData);$i++)	
		 {	
	 
		    print ' <div class="d-flex  mb-1 justify-content-center fs-3"> ';
		    print '<img class="before_work" src="' . $updir . $picData[$i] . '" > <br><br>';
		    print '</div>';
		 }
		
	  }
	   ?>
	   </div>		   
	   </div>
	   </div> 
 </div>
 </div>
  </body>
</html>    
 
 
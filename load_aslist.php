<?php

  require_once("./lib/mydb.php");
  $pdo = db_connect();	
 
 // 기간을 정하는 구간
$fromdate=date("Y-m-d");  
// $fromdate="2019-08-22";  
 

$sql="select * from chandj.work where asproday  between date('$fromdate') and date('$fromdate') order by num desc"; 					
$sqlcon="select * from chandj.work where asproday  between date('$fromdate') and date('$fromdate') order by num desc"; 					
	                         
$nowday=date("Y-m-d");   // 현재일자 변수지정          					 

$start_num=1;	
			?>
	
      <div id="firstpage_col_as">  
      <div class="clear"></div>		 
      <div id="aslist_top_title">
      <ul>
         <li id="aslist_title1"><img src="../img/list_title1.png"></li>
         <li id="aslist_title2"><img src="../img/list_title2.png"></li>     <!-- 현장명-->
         <li id="aslist_title3"><img src="../img/list_title3.png"></li>     <!-- 발주처-->
         <li id="aslist_title44"><img src="../img/aslist_title10.png"></li>     <!-- 요청사항-->		 
         <li id="aslist_title5"> <img src="../img/aslist_title5.png"></a></li>      <!-- 처리예정일-->
         <li id="aslist_title6"><img src="../img/aslist_title6.png"></li>  <!-- AS담당-->
         <li id="aslist_title7"><img src="../img/aslist_title7.png"></li>  <!-- 공사단계-->
         <li id="aslist_title99"><img src="../img/aslist_title9.png"></li>     <!-- 무/유상 금액 -->
      </ul>
      </div> <!-- end of list_top_title -->
      <div id="list_content_copy">
			<?php     
	 try{  
	  $allstmh = $pdo->query($sql);         // 검색 조건에 맞는 쿼리 전체 개수
      $temp2=$allstmh->rowCount();  
	  $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
      $temp1=$stmh->rowCount();
	      
	  $total_row = $temp2;     // 전체 글수	 
 		
	    
			$start_num=1;
	    
	       while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
			  $item_num=$row["num"];
			  $item_id=$row["id"];
			  $item_name=$row["chargedperson"];
			  $item_nick=$row["nick"];
			  $item_hit=$row["hit"];
			  $item_man=$row["chargedperson"];
			  $item_date=$row["regist_day"];
			  $item_date=substr($item_date, 0, 10);
			  $item_orderco=$row["secondord"];
			  $item_orderman=$row["secondordman"];
			  $asday=$row["asday"];
			  $asproday=$row["asproday"];
			  $asman=$row["asman"];
			  $asfee=$row["asfee"];
			  $asfee_estimate=$row["asfee_estimate"];
			  $aswriter=$row["aswriter"];
			  $setdate=$row["setdate"];
			  $as_step=$row["as_step"];
			  $as_checkboxvalue1=$row["as_checkboxvalue1"];
			  $as_checkboxvalue2=$row["as_checkboxvalue2"];
			  $as_checkboxvalue3=$row["as_checkboxvalue3"];
			  $as_checkboxvalue4=$row["as_checkboxvalue4"];				  
			  
			  if($asfee=="1")
				     if($asfee_estimate>0)
				        $as_amount=$asfee_estimate;
				     else 
			            $as_amount="유상(금액미정)";
				  else
					  $as_amount="";

			 // $item_subject=str_replace(" ", "&nbsp;", $row["workplacename"]);
			  $item_subject=$row["workplacename"];
              if(strlen($item_subject)>=60)
							$item_subject=substr($item_subject,0,58) . "..";   // 글자수가 초과하면 ...으로 표기됨			  
			  
			  
			  $state_work=0;
			  if(substr($row["condate"],0,2)=="20") $state_work=1;
			  if(substr($row["workday"],0,2)=="20") $state_work=2;
			  if(substr($row["endworkday"],0,2)=="20") $state_work=3;
			  if(substr($row["cableday"],0,2)=="20") $state_work=4;
			  if(substr($row["endcableday"],0,2)=="20") $state_work=5;
			  			  
			  $font="black";
			  switch ($state_work) {
                            case 1: $state_str="착공전"; $font="black";break;				  
							case 2: $state_str="시공중"; $font="blue"; break;
							case 3: $state_str="결선대기"; $font="brown"; break;
							case 4: $state_str="결선중"; $font="purple"; break;
							case 5: $state_str="결선완료"; $font="red";break;							
							default: $font="grey"; $state_str="계약전"; 
						}

              $state_as=0;    // AS 색상 등 표현하는 계산 
			  if(substr($row["asday"],0,2)=="20") $state_as=1;
			  if(substr($row["asproday"],0,2)=="20") $state_as=2;
			  if(substr($row["setdate"],0,2)=="20") $state_as=3;
			  if(substr($row["asendday"],0,2)=="20") $state_as=4;	

			  $font_as_step="black";
			  switch ($as_step) {
                            case "AS" : $font_as_step="blue";break;				  
							case "결선" : $font_as_step="brown"; break;
							case "입결선" : $font_as_step="purple"; break;
							case "셔터시공": $font_as_step="red"; break;	
						    default: $font_as_step="grey";
						}			  
			  if($as_step=="없음") $as_step="";	
			  
              if($asday=='0000-00-00') $asday=""; 			  
              if($asproday=='0000-00-00') $asproday=""; 			  
              if($setdate=='0000-00-00') $setdate=""; 			  
			  
			  $font_as="black";
			  switch ($state_as) {
							case 1: $state_astext="접수완료"; $font_as="blue"; break;
							case 2: $state_astext="처리예약"; $font_as="grey"; break;
							case 3: $state_astext="세팅예약"; $font_as="green"; break;
							case 4: $state_astext="처리완료"; $font_as="red"; break;							
							default: $state_astext="미접수"; 
						}							
							  
 if($outdate!="") {
    $week = array("(일)" , "(월)"  , "(화)" , "(수)" , "(목)" , "(금)" ,"(토)") ;
    $outdate = $outdate . $week[ date('w',  strtotime($outdate)  ) ] ;
}  
		    $as_tmp="";			  
			if($as_checkboxvalue1==null && $as_checkboxvalue2==null && $as_checkboxvalue3==null && $as_checkboxvalue4==null)
				  $as_tmp=$as_step;
			   else {
				if($as_checkboxvalue1=='Y')
					 $as_tmp .= "AS  ";
				if($as_checkboxvalue2=='Y')
					 $as_tmp .= "결선  ";
				if($as_checkboxvalue3=='Y')
					 $as_tmp .= "입결선  ";
				if($as_checkboxvalue4=='Y')
					 $as_tmp .= "셔터시공";
			   }

			  
			 ?>
	<div id="index_aslist" >
			  <div id="index_aslist1"><?= $start_num ?></div>
				<div id="index_aslist2"> <a href="./as/view.php?num=<?=$item_num?>&page=1&find=<?=$find?>&search=<?=$search?>&asprocess=전체&year=전체" style="font-size:12px" ><?=iconv_substr($item_subject,0,60,"utf-8") ?>&nbsp;</a>
		
				</div>				
				<div id="index_aslist3"><?=iconv_substr($item_orderco,0,40,"utf-8")?>&nbsp;</div>
				<div id="index_aslist4" style="color:blue;"> <?= $as_tmp?>&nbsp;</div>
				<div id="index_aslist5"><?=iconv_substr($asproday,0,40,"utf-8")?>&nbsp;</div>
				<div id="index_aslist6"><?=iconv_substr($asman,0,40,"utf-8")?>&nbsp;</div>
				<div id="index_aslist7" style="color:<?=$font?>;"><?= $state_str?>&nbsp;</div>						
				<div id="index_aslist9" style="color:red"><?= $as_amount?>&nbsp;</div>
			  </div> 
			<?php
			$start_num++;
			 } 
  } catch (PDOException $Exception) {
  print "오류: ".$Exception->getMessage();
  }  

 ?>

       </div>
       </div>


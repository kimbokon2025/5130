<meta charset="utf-8">
 
 <?php
 session_start(); 
 $file_dir = './uploads/'; 
  
 $num=$_REQUEST["num"];
 $search=$_REQUEST["search"];  //검색어

 if(isset($_REQUEST["check"])) 
	 $check=$_REQUEST["check"]; 
   else
     $check=$_POST["check"]; 
 
 if(isset($_REQUEST["page"]))
 {
    $page=$_REQUEST["page"]; 
 }
  else
  {
    $page=1;	 
  }
	 
 require_once("./lib/mydb.php");
 $pdo = db_connect();
 
 try{
     $sql = "select * from mirae8440.work where num=?";
     $stmh = $pdo->prepare($sql);  
     $stmh->bindValue(1, $num, PDO::PARAM_STR);      
     $stmh->execute();            
      
     $row = $stmh->fetch(PDO::FETCH_ASSOC); 	
 
  $checkstep=$row["checkstep"];
  $workplacename=$row["workplacename"];
  $address=$row["address"];
  $firstord=$row["firstord"];
  $firstordman=$row["firstordman"];
  $firstordmantel=$row["firstordmantel"];
  $secondord=$row["secondord"];
  $secondordman=$row["secondordman"];
  $secondordmantel=$row["secondordmantel"];
  $chargedman=$row["chargedman"];
  $chargedmantel=$row["chargedmantel"];
  $orderday=$row["orderday"];
  $measureday=$row["measureday"];
  $drawday=$row["drawday"];
  $deadline=$row["deadline"];
  $workday=$row["workday"];
  $worker=$row["worker"];
  $endworkday=$row["endworkday"];
  $doneday=$row["doneday"];  // 시공완료일  
  $attachment=$row["attachment"];   
  
  $material1=$row["material1"];
  $material2=$row["material2"];
  $material3=$row["material3"];
  $material4=$row["material4"];
  $material5=$row["material5"];
  $material6=$row["material6"];
  $widejamb=$row["widejamb"];
  $normaljamb=$row["normaljamb"];
  $smalljamb=$row["smalljamb"];
  $memo=$row["memo"];
  $regist_day=$row["regist_day"];
  $update_day=$row["update_day"];
  $update_log=$row["update_log"];
  
  $delivery=$row["delivery"];
  $delicar=$row["delicar"];
  $delicompany=$row["delicompany"];
  $delipay=$row["delipay"];
  $delimethod=$row["delimethod"];
  $demand=$row["demand"];
  $startday=$row["startday"];
  $testday=$row["testday"];
  $hpi=$row["hpi"];  
  $filename1=$row["filename1"];
  $filename2=$row["filename2"];
  $imgurl1="../imgwork/" . $filename1;
  $imgurl2="../imgwork/" . $filename2;
    $designer=$row["designer"];  
                $draw_done="";			  
			  if(substr($row["drawday"],0,2)=="20") 
			  {
			      $draw_done = "OK";	
					if($designer!='')
						 $draw_done = $designer ;
			  }
  

		      if($orderday!="0000-00-00" and $orderday!="1970-01-01"  and $orderday!="") $orderday = date("Y-m-d", strtotime( $orderday) );
					else $orderday="";
		      if($measureday!="0000-00-00" and $measureday!="1970-01-01" and $measureday!="")   $measureday = date("Y-m-d", strtotime( $measureday) );
					else $measureday="";
		      if($drawday!="0000-00-00" and $drawday!="1970-01-01" and $drawday!="")  $drawday = date("Y-m-d", strtotime( $drawday) );
					else $drawday="";
		      if($deadline!="0000-00-00" and $deadline!="1970-01-01" and $deadline!="")  $deadline = date("Y-m-d", strtotime( $deadline) );
					else $deadline="";
		      if($workday!="0000-00-00" and $workday!="1970-01-01"  and $workday!="")  $workday = date("Y-m-d", strtotime( $workday) );
					else $workday="";					
		      if($endworkday!="0000-00-00" and $endworkday!="1970-01-01" and $endworkday!="")  $endworkday = date("Y-m-d", strtotime( $endworkday) );
					else $endworkday="";		      
		      if($demand!="0000-00-00" and $demand!="1970-01-01" and $demand!="")  $demand = date("Y-m-d", strtotime( $demand) );
					else $demand="";		
		      if($startday!="0000-00-00" and $startday!="1970-01-01" and $startday!="")  $startday = date("Y-m-d", strtotime( $startday) );
					else $startday="";	
		      if($testday!="0000-00-00" and $testday!="1970-01-01" and $testday!="")  $testday = date("Y-m-d", strtotime( $testday) );
					else $testday="";			
		      if($doneday!="0000-00-00" and $doneday!="1970-01-01" and $doneday!="")  $doneday = date("Y-m-d", strtotime( $doneday) );
					else $doneday="";									
					
     }catch (PDOException $Exception) {
       print "오류: ".$Exception->getMessage();
     }
  
   
$todate=date("Y-m-d")  // 현재일 저장   
   
 ?>
 
 <!DOCTYPE HTML>
 <html> 
 <head>
 <meta charset="UTF-8">
<script src="https://bossanova.uk/jexcel/v3/jexcel.js"></script>
<script src="https://bossanova.uk/jsuites/v2/jsuites.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jexcel/v3/jexcel.css" type="text/css" />
<link rel="stylesheet" href="https://bossanova.uk/jsuites/v2/jsuites.css" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" >
<link rel="stylesheet" href="../css/partner.css" type="text/css" />

 <title> 시공 전, 시공 후 사진 </title>
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

			<div class="row">
		         <h1 class="display-4 font-center text-left"> 		   
       현장명 :       <?=$workplacename?>
	   </H1>  </div>
<br> <br>		   
<br>
     <h1 class="display-5 font-center text-left"> 	  시공완료일 :       <?=$doneday?> , 작업소장 :       <?=$worker?>  </H1>

		   <div class="clear"> </div> <br> <br> <div class="row"> </div>
		      <div class="row"> 	 <H1  class="display-4 font-center text-center" style="color:blue;margin-top:30px;" > 시공 전 사진 </H1>  </div>
	          <div class="clear"> </div> 
		  <div class='imagediv' >
	<?php
	     if($filename1!="") 
		    print '<img class="before_work" src="' . $imgurl1 . '" >';
	   ?>
	   </div>
	          <div class="clear"> </div> <br> <br> <div class="row"> </div>
   <div class="row"> 	 <H1  class="display-4 font-center text-center"  style="color:red;margin-top:30px;" > 시공 후 사진 </H1>  </div>
	          <div class="clear"> </div> 
    <div class='imagediv' >
		<?php
	     if($filename2!="") 
		  print '<img class="after_work" src="' . $imgurl2 . '" >';
	   ?>
	          <div class="clear"> </div> 
			  <br><br><br>       
      <span style="color:red;font-size:30px;margin-top:30px;"> &nbsp; </span>			  
	   </div>  
	          <div class="clear"> </div> 
		   
	   </div>	    		
   
	   </div> 

 </div>
  </body>
</html>    
 
 
 <script language="javascript">
/* function new(){
 window.open("viewimg.php","첨부이미지 보기", "width=300, height=200, left=30, top=30, scrollbars=no,titlebar=no,status=no,resizable=no,fullscreen=no");
} */
var imgObj = new Image();
function showImgWin(imgName) {
imgObj.src = imgName;
setTimeout("createImgWin(imgObj)", 100);
}
function createImgWin(imgObj) {
if (! imgObj.complete) {
setTimeout("createImgWin(imgObj)", 100);
return;
}
imageWin = window.open("", "imageWin",
"width=" + imgObj.width + ",height=" + imgObj.height);
}

   function inputNumberFormat(obj) { 
    obj.value = comma(uncomma(obj.value)); 
} 
function comma(str) { 
    str = String(str); 
    return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,'); 
} 
function uncomma(str) { 
    str = String(str); 
    return str.replace(/[^\d]+/g, ''); 
}


function date_mask(formd, textid) {

/*
input onkeyup에서
formd == this.form.name
textid == this.name
*/

var form = eval("document."+formd);
var text = eval("form."+textid);

var textlength = text.value.length;

if (textlength == 4) {
text.value = text.value + "-";
} else if (textlength == 7) {
text.value = text.value + "-";
} else if (textlength > 9) {
//날짜 수동 입력 Validation 체크
var chk_date = checkdate(text);

if (chk_date == false) {
return;
}
}
}

function checkdate(input) {
   var validformat = /^\d{4}\-\d{2}\-\d{2}$/; //Basic check for format validity 
   var returnval = false;

   if (!validformat.test(input.value)) {
    alert("날짜 형식이 올바르지 않습니다. YYYY-MM-DD");
   } else { //Detailed check for valid date ranges 
    var yearfield = input.value.split("-")[0];
    var monthfield = input.value.split("-")[1];
    var dayfield = input.value.split("-")[2];
    var dayobj = new Date(yearfield, monthfield - 1, dayfield);
   }

   if ((dayobj.getMonth() + 1 != monthfield)
     || (dayobj.getDate() != dayfield)
     || (dayobj.getFullYear() != yearfield)) {
    alert("날짜 형식이 올바르지 않습니다. YYYY-MM-DD");
   } else {
    //alert ('Correct date'); 
    returnval = true;
   }
   if (returnval == false) {
    input.select();
   }
   return returnval;
  }
  
function input_Text(){
    document.getElementById("test").value = comma(Math.floor(uncomma(document.getElementById("test").value)*1.1));   // 콤마를 계산해 주고 다시 붙여주고
}  

function copy_below(){	

var park = document.getElementsByName("asfee");

document.getElementById("ashistory").value  = document.getElementById("ashistory").value + document.getElementById("asday").value + " " + document.getElementById("aswriter").value+ " " + document.getElementById("asorderman").value + " ";
document.getElementById("ashistory").value  = document.getElementById("ashistory").value  + document.getElementById("asordermantel").value + " " ;
     if(park[1].checked) {
        document.getElementById("ashistory").value  = document.getElementById("ashistory").value +" 유상 " + document.getElementById("asfee").value + " ";		
	 }		 
	   else
	   {
	    document.getElementById("ashistory").value  = document.getElementById("ashistory").value +" 무상 "+ document.getElementById("asfee").value + " ";				   
	   }
	   
document.getElementById("ashistory").value  += document.getElementById("asfee_estimate").value + " " + document.getElementById("aslist").value+ " " + document.getElementById("as_refer").value + " ";	
document.getElementById("ashistory").value  += document.getElementById("asproday").value + " " + document.getElementById("setdate").value+ " " + document.getElementById("asman").value + " ";	
document.getElementById("ashistory").value  += document.getElementById("asendday").value + " " + document.getElementById("asresult").value+ "        ";
//    = text1.concat(" ", text2," ", text3, " ",  text4);
// document.getElementById("asday").value . document.getElementById("aswriter").value;
	//+ document.getElementById("aswriter").value ;   // 콤마를 계산해 주고 다시 붙여주고붙여주고
   // document.getElementById("test").value = comma(Math.floor(uncomma(document.getElementById("test").value)*1.1));   // 콤마를 계산해 주고 다시 붙여주고붙여주고
   
}  

function input_measureday_btn(href)
     {
     if(confirm("현재일로 실측일을 전송합니다.\n\n 정말 본사 전산에 입력 하시겠습니까?")) {		 
         document.location.href = href ;		 
    }
}

function input_doneday_btn(href)
     {
     if(confirm("현재일로 시공완료일을 전송합니다.\n\n 전산에 기록 하시겠습니까?")) {
         document.location.href = href ;	 
    }
}
function input_measureday(href)
     {
     if(confirm("수정된 실측일을 전송합니다.\n\n 정말 본사 전산에 입력 하시겠습니까?")) {
		 var measureday = $("#measureday").val() ;
         document.location.href = href + "&measureday=" + measureday;		 
    }
}

function input_doneday(href)
     {
     if(confirm("수정된 시공완료일을 전송합니다.\n\n 전산에 기록 하시겠습니까?")) {
		 var doneday = $("#doneday").val() ;		 
         document.location.href = href + "&doneday=" + doneday;	 
    }
}

     function del(href) 
     {
		 var level=Number($('#session_level').val());
		 if(level>2)
		     alert("삭제하려면 관리자에게 문의해 주세요");
		 else {
         if(confirm("한번 삭제한 자료는 복구할 방법이 없습니다.\n\n정말 삭제하시겠습니까?")) {
           document.location.href = href;
          } 
		 }

     }
	 
function input_message(href)
{
     document.location.href = href;		 
}

function move_url(href)
{
     document.location.href = href;		 
}

// 사진 회전하기
function rotate_image()
{	
 var box = $('.imagediv');
 var imgObj = new Image();
 var imgObj2 = new Image();
 imgObj.src = "<? echo $imgurl1; ?>" ; 
 imgObj2.src = "<? echo $imgurl2; ?>" ; 
 box.css('width','800px');
 box.css('height','1200px');
 box.css('margin-top','250px');
 
 if( imgObj.width > imgObj.height  ||  imgObj2.width > imgObj2.height)
   {
		$('.before_work').addClass('rotated');
		$('.after_work').addClass('rotated');		
   }

}

setTimeout(function() {
 // console.log('Works!');
 rotate_image();
}, 1000);
</script>

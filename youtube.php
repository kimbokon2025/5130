<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  
   
 ?>   
      
 <?php include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php' ?>
 
 
<meta property="og:type" content="(주)대한 유튜브">
<meta property="og:title" content="(주)대한">
<meta property="og:url" content="dh2024.co.kr">
<meta property="og:description" content="(주)대한 유튜브">
<meta property="og:image" content="https:dh2024.co.kr/img/dh2024thumbnail.jpg"> 

<!-- viewport
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />	
 -->
 

 <title> (주)대한 DH모터 유튜브 </title>
 </head>
<body>

<? include $_SERVER['DOCUMENT_ROOT'] . '/myheader.php'; ?>   


<!-- background: -webkit-linear-gradient(left, #33156d 0%,#f282bc 100%); /* Chrome10-25,Safari5.1-6 */  -->
<style>
	.progress-bar {
	background: -webkit-linear-gradient(left, #dcdcdc 0%,#3c3c3c 100%); /* Chrome10-25,Safari5.1-6 */
	}
	.progress-bar2 {
	background: -webkit-linear-gradient(left, #CCCCFF 0%,#aaaaaa 100%); /* Chrome10-25,Safari5.1-6 */
	}

	@charset "utf-8";

	.typing-txt{display: none;}
	.typeing-txt ul{list-style:none;}
	.typing {  
	  display: inline-block; 
	  animation-name: cursor; 
	  animation-duration: 0.3s; 
	  animation-iteration-count: infinite; 
	} 
	@keyframes cursor{ 
	  0%{border-right: 1px solid #fff} 
	  50%{border-right: 1px solid #000} 
	  100%{border-right: 1px solid #fff}   
	  }    
</style>

<div class="container" >  
	<input type="hidden" id="voc_alert" name="voc_alert" value="<?=$voc_alert?>" size="5" > 	
	<input type="hidden" id="ma_alert" name="ma_alert" value="<?=$ma_alert?>" size="5" > 	
	<input type="hidden" id="order_alert" name="order_alert" value="<?=$order_alert?>" size="5" > 					

 <!-- 타이틀 -->
  	  <div class="d-flex p-2 mb-2 mt-5 justify-content-center"> 		 
		    <h1 class="text-secondary text-center"> 				
				<i class="bi bi-youtube"></i>
		    </h1>	
	  </div>	
 <!-- 1개의 영상마다 구분됨 -->
  	  <div class="d-flex p-2 mb-2 mt-3 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 
				회수예정 회수완료 처리방법
		    </h4>	
	  </div>					
	  <div class="d-flex p-2 mb-2 justify-content-center"> 
		    <h4 class="text-secondary text-center"> 
				<iframe width="560" height="315" src="https://www.youtube.com/embed/zcXPtF21fG8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		    </h4>	
	  </div>
 <!-- 1개의 영상마다 구분됨 -->
  	  <div class="d-flex p-2 mb-2 mt-3 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 
				반품처리 수주등록 처리방법
		    </h4>	
	  </div>					
	  <div class="d-flex p-2 mb-2 justify-content-center"> 
		    <h4 class="text-secondary text-center"> 		
				<iframe width="560" height="315" src="https://www.youtube.com/embed/9Jd7TNiVwNk?si=Lq7OI-gwhSEHdMTK" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		    </h4>	
	  </div>	
 <!-- 1개의 영상마다 구분됨 -->
  	  <div class="d-flex p-2 mb-2 mt-3 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 
				연차신청부터 결재처리까지
		    </h4>	
	  </div>					
	  <div class="d-flex p-2 mb-2 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 		
				<iframe width="560" height="315" src="https://www.youtube.com/embed/b8DDXEKt__8?si=zsvUqCnBqITdVXn6" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		    </h4>	
	  </div>
 <!-- 1개의 영상마다 구분됨 -->
  	  <div class="d-flex p-2 mb-2 mt-3 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 
				배송지 즐겨찾기 기능 구현
		    </h4>	
	  </div>					
	  <div class="d-flex p-2 mb-2 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 		
				<iframe width="560" height="315" src="https://www.youtube.com/embed/FxPQrEbcm_s?si=VXTadsKOtp_a1int" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		    </h4>	
	  </div>	
 <!-- 1개의 영상마다 구분됨 -->
  	  <div class="d-flex p-2 mb-2 mt-3 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 
				화물회사 배송사진 등록 설명
		    </h4>	
	  </div>					
	  <div class="d-flex p-2 mb-2 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 		
				<iframe width="560" height="315" src="https://www.youtube.com/embed/xcUHcg0dz4Y?si=HghFb0eeD6j75Fh8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		    </h4>	
	  </div>	
 <!-- 1개의 영상마다 구분됨 -->
  	  <div class="d-flex p-2 mb-2 mt-3 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 
				수주내역 입력창 개선사항 구현
		    </h4>	
	  </div>					
	  <div class="d-flex p-2 mb-2 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 		
				<iframe width="560" height="315" src="https://www.youtube.com/embed/SzetVIYHKrw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		    </h4>	
	  </div>
 <!-- 1개의 영상마다 구분됨 -->
  	  <div class="d-flex p-2 mb-2 mt-3 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 
				부속자재 개별 단가 조회 및 변경기능 구현
		    </h4>	
	  </div>					
	  <div class="d-flex p-2 mb-2 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 		
				<iframe width="560" height="315" src="https://www.youtube.com/embed/-r0NNPnleWc" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		    </h4>	
	  </div>
 <!-- 1개의 영상마다 구분됨 -->
  	  <div class="d-flex p-2 mb-2 mt-3 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 
				부속자재 검색 실시간 적용 알고리즘 구현
		    </h4>	
	  </div>					
	  <div class="d-flex p-2 mb-2 justify-content-center"> 		 
		    <h4 class="text-secondary text-center"> 		
				<iframe width="560" height="315" src="https://www.youtube.com/embed/-IzW58COg-w?si=SbAn-EZH3OPooQp-" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		    </h4>	
	  </div>		 
		
<br/><br/>

<? include 'footer.php'; ?>   

 </div> <!-- container-fulid end -->

  
</body>


</html>

<script>
// 페이지 로딩
$(document).ready(function(){	
    var loader = document.getElementById('loadingOverlay');
    loader.style.display = 'none';
});
</script>
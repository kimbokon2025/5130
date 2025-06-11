<?php
$WebSite = "https://dh2024.co.kr/";	
?>

<!doctype html>
<html class="h-100" lang="ko">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">  

<meta property="og:type" content="DH모터의 모든것 (주)대한">
<meta property="og:title" content="DH모터의 모든것 (주)대한">
<meta property="og:url" content="www.dh2024.co.kr">
<meta property="og:description" content="DH모터의 모든것 (주)대한">
<meta property="og:image" content="https://dh2024.co.kr/img/dh.jpg"/>

<!--head 태그 내 추가-->
<!-- Favicon-->	
<link rel="icon" type="image/x-icon" href="favicon.ico">   <!-- 33 x 33 -->
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">    <!-- 144 x 144 -->
<link rel="apple-touch-icon" type="image/x-icon" href="favicon.ico">

<title> DH모터 주)대한 </title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" >  
<link rel="stylesheet" href="css/theme.css">  
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-zoom/1.6.1/jquery.zoom.min.js" integrity="sha512-xhvWWTTHpLC+d+TEOSX2N0V4Se1989D03qp9ByRsiQsYcdKmQhQ8fsSTX3KLlzs0jF4dPmq0nIzvEc3jdYqKkw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>	

<style>

.dropdown:hover .dropdown-menu {
    display: block;
    margin-top: 0;
}
/* 마우스 오버하면 드롭다운하기 */

/* 파일선택 CSS */
.box-file-input label{
  display:inline-block;
  background:#23a3a7;
  color:#fff;
  padding:0px 15px;
  line-height:35px;
  cursor:pointer;
}

.box-file-input label:after{
  content:"파일등록";
}

.box-file-input .file-input{
  display:none;
}

.box-file-input .filename{
  display:inline-block;
  padding-left:10px;
}


/* inter-300 - latin */
@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 300;
  font-display: swap;
  src: local(''),
       url('./fonts/inter-v12-latin-300.woff2') format('woff2'), /* Chrome 26+, Opera 23+, Firefox 39+ */
       url('./fonts/inter-v12-latin-300.woff') format('woff'); /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
}

@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: local(''),
       url('./fonts/inter-v12-latin-500.woff2') format('woff2'), /* Chrome 26+, Opera 23+, Firefox 39+ */
       url('./fonts/inter-v12-latin-500.woff') format('woff'); /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
}
@font-face {
  font-family: 'Inter';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: local(''),
       url('./fonts/inter-v12-latin-700.woff2') format('woff2'), /* Chrome 26+, Opera 23+, Firefox 39+ */
       url('./fonts/inter-v12-latin-700.woff') format('woff'); /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
}

.bigPicture {
	position: absolute;
	display:flex;
	justify-content: center;
	align-items: center;
}

.bigPicture img {
	height:100%; /*새로기준으로 꽉차게 보이기 */
}

 /* 우측배너 제작 */
.sideBanner {
  position: absolute;
  width: 120px;
  height: 200px;
  top: calc(100vh - 300px);
  left: calc(100vw - 150px);  
}


@import url("https://cdn.jsdelivr.net/gh/orioncactus/pretendard/dist/web/static/pretendard.css");

* {
  margin: 0;
  padding: 0;
  list-style: none;
  box-sizing: border-box;  
  font-family: Pretendard;
}

.flip { 
  width:  calc(100vw - 77vw);
  height: calc(100vh - 70vh);
  position: relative; 
  perspective: 1100px;
  margin: 2rem;
}

.card {
  width: 100%; 
  height: 100%; 
  position: relative;
  transition: .4s;
  transform-style: preserve-3d;
} 

.front, .back {
  position: absolute;
  width: 100%; 
  height: 100%;
  backface-visibility: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
}

.front {
  color: #000000;
  background: #dcdcdc; 
}

.back { 
  color: #FFFF;
  background: royalblue; 
  transform: rotateY(180deg);
}

.flip:hover .card {
  transform: rotateY(180deg);
}

/*  모바일에서 보이도록 설정하기 */
@media screen and (max-width: 1280px) {			
	.flip { 
	  width:  calc(100vw - 30vw);
	  height: calc(100vh - 60vh);
	  position: relative; 
	  perspective: 1100px;
	  margin: 2rem;
	}

	.card {
	  width: 100%; 
	  height: 100%; 
	  position: relative;
	  transition: .4s;
	  transform-style: preserve-3d;
	} 

	.front, .back {
	  position: absolute;
	  width: 100%; 
	  height: 100%;
	  backface-visibility: hidden;
	  display: flex;
	  justify-content: center;
	  align-items: center;
	}

	.front {
	  color: #000000;
	  background: #dcdcdc; 
	}

	.back { 
	  color: #FFFF;
	  background: royalblue; 
	  transform: rotateY(180deg);
	}

	.flip:hover .card {
	  transform: rotateY(180deg);
	}
}


#myMsgDialog {
	width:40%; 
	background-color: #BEEFFF; 
	border:1px solid black; 
	border-radius: 7px;
}		

#closeDialog {
	width:25%; 
	background-color: #BEEFFF; 
	border:1px solid black; 
	border-radius: 7px;
}		

#mButton, #closeButton {
	padding: 7px 30px;
	background-color: #66ccff;
	color: white;
	font-size: 15px;
	border: 0;
	outline: 0;
}

#cButton {
	padding: 7px 30px;
	background-color: #2828CD;
	color: white;
	font-size: 15px;
	border: 0;
	outline: 0;
}

@media screen and (max-width: 1280px) {		
		#myMsgDialog {
			width:100%; 
			background-color: #BEEFFF; 
			border:1px solid black; 
			border-radius: 7px;
		}		

		#closeDialog {
			width:25%; 
			background-color: #BEEFFF; 
			border:1px solid black; 
			border-radius: 7px;
		}		

		#mButton, #closeButton {
			padding: 7px 30px;
			background-color: #66ccff;
			color: white;
			font-size: 15px;
			border: 0;
			outline: 0;
		}
		
		#cButton {
			padding: 7px 30px;
			background-color: #2828CD;
			color: white;
			font-size: 15px;
			border: 0;
			outline: 0;
		}

}	

.modal-dialog.modal-80size {
  width: 80%;
  height: 50%;
  margin: 0;
  padding: 0;
  z-index: 9999;
}

.modal-content.modal-80size {
  height: auto;
  min-height: 40%;
  z-index: 9999;
}

.modal.modal-center {
  text-align: center;
  z-index: 9999;
}

@media screen and (min-width: 768px) {
  .modal.modal-center:before {
    display: inline-block;
    vertical-align: middle;
    content: " ";
    height: 80%;
	z-index: 9999;
  }
}

.modal-dialog.modal-center {
  display: inline-block;
  text-align: left;
  vertical-align: middle;
   z-index: 9999;
}

.login-button {
	display: inline-block;
	padding: 10px 20px;
	font-size: 16px;
	text-align: center;
	cursor: pointer;
	background-color: #16D5FF;
	color: white;
	border: none;
	border-radius: 5px;
	transition: background-color 0.3s;
}

.login-button:hover {
	background-color: #16D500;
}

</style>
</head>

<body data-bs-spy="scroll" data-bs-target="#navScroll">
<nav id="navScroll" class="navbar navbar-expand-lg navbar-light fixed-top mb-5" tabindex="0">
  <div class="container">
    <a class="navbar-brand " href="/" style="width:40%;">
		<img src="<?$root_dir?>/img/dhlogo.png" style="width:50%;"> 
	</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

	  <li class="nav-item">
		<a class="nav-link" href="#aboutus" >
		  회사소개
		</a>
	  </li>
	  
	<li class="nav-item dropdown">
		<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">제품</a>
		<div class="dropdown-menu shadow-sm m-0">
			<a href="#ceiling"  class="dropdown-item">셔터(방화,방범)모터</a>
			<a href="#jambcladding" class="dropdown-item">방화스크린</a>
			<a href="#sillcover" class="dropdown-item"> 샤프트 강관 </a>
		</div>
	</li> 	  	  
	
	<li class="nav-item dropdown">
		<a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">자료실</a>
		<div class="dropdown-menu shadow-sm m-0">
			<a href="board1/list.php"  class="dropdown-item"> 법규 자료실 </a>
			<a href="board2/list.php" class="dropdown-item"> 자주 묻는 질문 </a>
			<a href="board3/list.php" class="dropdown-item"> 게시판 </a>
		</div>
	</li> 	  
	
  <li class="nav-item">
    <a href="#" class="nav-link" onclick="showMsg()" >
	 견적/제품문의 
      
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#testimonials">
       우리들의 이야기
    </a>
  </li>

    </ul> 
	
		</div>
	</div>
</nav>


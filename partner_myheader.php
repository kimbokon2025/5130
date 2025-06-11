
<?php

$root_dir = $_SERVER['DOCUMENT_ROOT'] ;


// 모바일 사용여부 확인하는 루틴
$mAgent = array("iPhone","iPod","Android","Blackberry", 
    "Opera Mini", "Windows ce", "Nokia", "sony" );
$chkMobile = false;
for($i=0; $i<sizeof($mAgent); $i++){
    if(stripos( $_SERVER['HTTP_USER_AGENT'], $mAgent[$i] )){
        $chkMobile = true;
		if($user_name=='권영철')
		     $submenu = 1 ;
		// print '권영철';
        break;
    }
}

?>

<style>
.dropdown:hover .dropdown-menu {
    display: block;
    margin-top: 0;
}

.navbar-custom {
    background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 35%, rgba(0,212,255,1) 100%);
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2);
}

.navbar-custom .navbar-brand,
.navbar-custom .navbar-nav .nav-link {
    color: #ffffff;
}



@media (max-width: 720px) {
    /* 모바일 화면에 대한 스타일 설정 */
    .container-fluid {
        /* 스타일 변경 */
		width : 940px;
		
    }
    .container {
        /* 스타일 변경 */
		width : 900px;
		
    }
}


</style>

 <?php if($chkMobile==false) { ?>
	<div class="container">     
 <?php } else { ?>
 	<div class="container-fluid">     
	<?php } ?>
 
    <div class="d-flex justify-content-center">        
            <a href="<?$root_dir?>/outorder/list.php">
			  <span class="badge bg-primary fs-3"> 미래기업 통합정보시스템(IIS) 외주관리 </span>
            </a>        
            &nbsp;&nbsp;

            <div class="nav-item dropdown ">			 
					<!-- 드롭다운 메뉴-->
					 <a class="nav-link  dropdown-toggle" href="#"  data-toggle="dropdown" >  <?=$_SESSION["name"]?>님  </a>                
					<div class="dropdown-menu">                   
						<a class="dropdown-item fs-6 " href="<?$root_dir?>/login/logout.php"> 로그아웃 </a>                    
						<a class="dropdown-item fs-6 " href="<?$root_dir?>/member/updateForm.php?id=<?=$_SESSION["userid"]?>"> 정보수정 </a>                    
					<?php
					if($_SESSION["userid"]=='a' || $_SESSION["userid"]=='mirae')				        
							{
								?>
									<a class="dropdown-item fs-6 " href="<?$root_dir?>/automan/list.php"> 전산실장 정산 </a> 
							<? }  ?>                    
					</div>

                </div>
	</div>
</div>


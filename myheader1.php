<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  
?> 
<form id="eworks_board_form" name="eworks_board_form" method="post" enctype="multipart/form-data" >	    

<input type="hidden" id="eworksPage" name="eworksPage" value="<?= isset($eworksPage) ? $eworksPage : '' ?>" > 
<input type="hidden" id="e_viewexcept_id" name="e_viewexcept_id" value="<?= isset($e_viewexcept_id) ? $e_viewexcept_id : '' ?>" >   <!-- 전자결재 보기 제한 -->    
<input type="hidden" id="e_num" name="e_num" value="<?= isset($e_num) ? $e_num : '' ?>" > 
<input type="hidden" id="ripple_num" name="ripple_num" value="<?= isset($ripple_num) ? $ripple_num : '' ?>" > 
<input type="hidden" id="SelectWork" name="SelectWork" value="<?= isset($SelectWork) ? $SelectWork : '' ?>" > 
<input type="hidden" id="eworksel" name="eworksel" value="<?= isset($eworksel) ? $eworksel : '' ?>" >    <!-- 전자결재 진행상태  draft send -->    
<input type="hidden" id="choice" name="choice" value="<?= isset($choice) ? $choice : '' ?>" >    <!-- 전자결재 진행상태  draft send -->        
<input type="hidden" id="approval_right" name="approval_right" value="<?= isset($approval_right) ? $approval_right : '' ?>" >   
<input type="hidden" id="done" name="done" value="<?= isset($done) ? $done : '' ?>" >    <!-- 전자결재 진행상태  done -->        
<input type="hidden" id="author_id" name="author_id" value="<?= isset($author_id) ? $author_id : '' ?>" > 
	
<!-- 전자결재 관련 배열 -->	
<input id="numid_arr" name="numid_arr[]" type="hidden" >
<input id="registdate_arr" name="registdate_arr[]" type="hidden" >
<input id="eworks_item_arr" name="eworks_item_arr[]" type="hidden" >
<input id="author_arr" name="author_arr[]" type="hidden" >
<input id="author_id_arr" name="author_id_arr[]" type="hidden" >
<input id="e_title_arr" name="e_title_arr[]" type="hidden" >
<input id="e_line_id_arr" name="e_line_id_arr[]" type="hidden" >
<input id="e_line_arr" name="e_line_arr[]" type="hidden" >
<input id="r_line_arr" name="r_line_arr[]" type="hidden" >		   
<input id="r_line_id_arr" name="r_line_id_arr[]" type="hidden" >		   
<input id="e_confirm" name="e_confirm" type="hidden" >		   
<input id="e_confirm_arr" name="e_confirm_arr[]" type="hidden" >		   
<input id="e_confirm_id" name="e_confirm_id" type="hidden" >		   
<input id="e_confirm_id_arr" name="e_confirm_id_arr[]" type="hidden" >		   
	
 <?php if($chkMobile==false) { ?>
	<div class="container">     
 <?php } else { ?>
 	<div class="container-fluid">     
	<?php } ?>	
  
<div class="row d-flex">        
    <div class="col-sm-2 justify-content-center">        	
		<div class="d-flex justify-content-center align-items-center fs-6 ">	
			<a href="<?=$WebSite?>index1.php">
			 <img src="<?=$WebSite?>img/companylogo1.png" alt="(주)주일기업" style="width:100%;" >					 
			 </a>	
		 </div>		 
	</div>
<div class="col-sm-10 justify-content-center">     
	<nav class="navbar navbar-expand navbar-custom ">
	<div class="navbar-nav ">   
            <div class="nav-item me-1" id="home-menu">
				<div class="d-flex justify-content-center align-items-center align-items-center fs-5">					
					<button type='button' class="nav-link" onclick='location.href="<?$root_dir?>/index.php?home=1"' title="경동기업 이동"  > <i class="bi bi-arrow-left-right"></i>  </button>			
				</div>						
            </div>						

            <div class="nav-item dropdown flex-fill me-3">			 			 
                <!-- 드롭다운 메뉴-->
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" >
                    공사수주
                </a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/work/list_all.php">
						<i class="bi bi-border-all"></i> 전체
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/work/list.php">
						<i class="bi bi-list-task"></i> 진행중
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/work/list_done.php">
						<i class="bi bi-pc-display-horizontal"></i> 공사완료
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/pb_juil/list.php?header=header">
						<i class="bi bi-journal-text"></i> 발주처 주소록
					</a>
					<hr style="margin:7px!important;">
					<a class="dropdown-item" href="<?=$root_dir?>/load_request_equipment.php?header=header">
						<i class="bi bi-truck-front-fill"></i> 장비투입
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/load_work.php?header=header">
						<i class="bi bi-hammer"></i> 시공중
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/load_work_wire.php?header=header">
						<i class="bi bi-plug-fill"></i> 결선중
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/load_work_before.php?header=header">
						<i class="bi bi-calendar2-week"></i> 착공전
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/load_work_new.php?header=header">
						<i class="bi bi-calendar2-check"></i> 착공(M-1)
					</a>
					<hr style="margin:7px!important;">
					<a class="dropdown-item" href="<?=$root_dir?>/load_request_visit.php?header=header">
						<i class="bi bi-person-walking"></i> 방문 요청
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/load_request_measure.php?header=header">
						<i class="bi bi-rulers"></i> 실측 요청
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/load_request_order.php?header=header">
						<i class="bi bi-cart-check"></i> 발주 요청
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/load_request_label.php?header=header">
						<i class="bi bi-tag-fill"></i> 인정라벨부착 요청
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/load_request_as.php?header=header">
						<i class="bi bi-tools"></i> AS 요청
					</a>
				</div>
            </div>	          
         <?php if($user_name=='개발자' || $user_name=='이경호'  || $user_name=='김영민'  || $user_name=='우지영'  || $user_name=='손기준' || $user_name=='함신옥' ) { ?>
			<div class="nav-item dropdown flex-fill me-3">			 			 
                <!-- 드롭다운 메뉴-->
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" >
                    영업일지
                </a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/bid/list.php">
						<i class="bi bi-folder-symlink"></i> 현설
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/bid/statistics.php">
						<i class="bi bi-graph-up-arrow"></i> 통계
					</a>
				</div>
            </div>	
		 <?php } ?>                 
			<div class="nav-item dropdown flex-fill me-3">			 			 
                <!-- 드롭다운 메뉴-->
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" >
                    차량운행
                </a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/juilcar/list.php">
						<i class="bi bi-truck-front"></i> 차량관리
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/juilcarlog/list.php">
						<i class="bi bi-journal-bookmark"></i> 차량일지
					</a>
				</div>

            </div>			 
		<?php if ($mycompany == '주일' && $mypart == '경리' || $user_name == '이경호' || $user_name == '개발자') { ?>		
            <div class="nav-item dropdown flex-fill me-3">			 			 
                <!-- 드롭다운 메뉴-->
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" >
                    회계
                </a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/work/accountlist.php">
						<i class="bi bi-coin"></i> 전체 미수금
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/work/accountlist_good.php">
						<i class="bi bi-coin"></i> 미수금(일반)
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/work/accountlist_bad.php">
						<i class="bi bi-exclamation-octagon-fill text-danger"></i> 악성 미수금
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/work/accountlist_request.php">
						<i class="bi bi-person-bounding-box"></i> 채권추심
					</a>
				</div>

            </div>	
		<?php } ?>
            <div class="nav-item dropdown flex-fill me-3">			 
                <!-- 드롭다운 메뉴-->
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    공유
                </a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/annualleave/index.php">
						<i class="bi bi-calendar-heart"></i> 연차
					</a>
					<!-- <a class="dropdown-item" href="/absent/index.php"> <i class="bi bi-fingerprint"></i> 근태 </a> -->
					<a class="dropdown-item" href="<?=$root_dir?>/qrcode/index.php">
						<i class="bi bi-qr-code"></i> QR코드 생성
					</a>
				</div>
            </div>	
            <div class="nav-item dropdown flex-fill me-3">			 
                <!-- 드롭다운 메뉴-->
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    게시
                </a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/notice1/list.php">
						<i class="bi bi-megaphone-fill"></i> 공지사항
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/qna1/list.php">
						<i class="bi bi-folder2-open"></i> 자료실
					</a>
					<hr style="margin:7px!important;">
					<a class="dropdown-item" href="<?=$root_dir?>/rnd1/list.php">
						<i class="bi bi-journal-code"></i> 개발일지
					</a>
				</div>

            </div>              		 
            <div class="nav-item dropdown flex-fill me-3">				 
                <!-- 드롭다운 메뉴-->
                 <a class="nav-link  dropdown-toggle" href="#"  data-toggle="dropdown" >  <?=$_SESSION["name"]?>님(Lv<?=$_SESSION["level"]?>)  </a>                
					<div class="dropdown-menu">                   
						<a class="dropdown-item" href="<?=$root_dir?>/login/logout.php">
							<i class="bi bi-box-arrow-right"></i> 로그아웃
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/member/updateForm.php?id=<?=$_SESSION["userid"]?>">
							<i class="bi bi-person-gear"></i> 정보수정
						</a>
						<hr style="margin:7px!important;">
						<?php if($_SESSION["level"] == '1') { ?>
							<a class="dropdown-item" href="<?=$root_dir?>/member/list.php">
								<i class="bi bi-people-fill"></i> 회원관리
							</a>
							<a class="dropdown-item" href="<?=$root_dir?>/logdata.php">
								<i class="bi bi-door-open-fill"></i> 로그인기록
							</a>
							<a class="dropdown-item" href="<?=$root_dir?>/logdata_menu.php">
								<i class="bi bi-menu-button-wide-fill"></i> 메뉴접속기록
							</a>
						<?php } ?>
					</div>
            </div>				
			<div class="nav-item flex-fill me-6">			 
				<!-- 전자결재 관련 알람 -->
				<a class="nav-link dropdown-toggle" href="#" onclick="seltab(3);"> 				 
					<span id="alert_eworks_bell" style="display:none; font-size:15px;">🔔결재</span>
					<i class="bi bi-folder-check"></i>
					<span id="alert_eworks"></span>
					전자결재
				</a>                     
			</div>
		  </div>		
		</nav>      
  </div>  			
</div>  	
  
<?php 
	// 전자결재 관련 모달
	require_once($_SERVER['DOCUMENT_ROOT'] . "/eworks/list_form.php");   
	require_once($_SERVER['DOCUMENT_ROOT'] . "/eworks/write_form.php");   
?>

<div class="sideEworksBanner" style="display:none;">
    <span class="text-center text-dark">
		<img src="<?=$WebSite?>img/eworks_reach.png" > 
	</span>     
</div>
</div>
</form>
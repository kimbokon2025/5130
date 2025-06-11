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
	<input id="numid_arr" name="numid_arr[]" type="hidden">
	<input id="registdate_arr" name="registdate_arr[]" type="hidden">
	<input id="eworks_item_arr" name="eworks_item_arr[]" type="hidden">
	<input id="author_arr" name="author_arr[]" type="hidden">
	<input id="author_id_arr" name="author_id_arr[]" type="hidden">
	<input id="e_title_arr" name="e_title_arr[]" type="hidden">
	<input id="e_line_id_arr" name="e_line_id_arr[]" type="hidden">
	<input id="e_line_arr" name="e_line_arr[]" type="hidden">
	<input id="r_line_arr" name="r_line_arr[]" type="hidden">		   
	<input id="r_line_id_arr" name="r_line_id_arr[]" type="hidden">
	<input id="e_confirm" name="e_confirm" type="hidden">		   
	<input id="e_confirm_arr" name="e_confirm_arr[]" type="hidden">
	<input id="e_confirm_id" name="e_confirm_id" type="hidden">
	<input id="e_confirm_id_arr" name="e_confirm_id_arr[]" type="hidden">
	
 <?php if($chkMobile==false) { ?>
	<div class="container">     
 <?php } else { ?>
 	<div class="container-fluid">     
	<?php } ?>	
  
<div class="row d-flex">        
    <div class="col-sm-2 justify-content-center">        	
		<div class="d-flex justify-content-center align-items-center fs-6 ">	
			<a href="<?=$WebSite?>index.php">
			 <img src="<?=$WebSite?>img/companylogo0.png" alt="(주)경동기업" style="width:100%;" >					 
			 </a>	
		 </div>		 
	</div>
<div class="col-sm-10 justify-content-center align-items-center">     
	<nav class="navbar navbar-expand navbar-custom ">
	<div class="navbar-nav ">   
            <div class="nav-item me-1" id="home-menu">
				<div class="d-flex justify-content-center align-items-center align-items-center fs-5">					
					<button type='button' class="nav-link" onclick='location.href="<?$root_dir?>/index1.php?home=1"' title="주일기업 이동"  > <i class="bi bi-arrow-left-right"></i>  </button>			
				</div>						
            </div>						
			<div class="nav-item dropdown flex-fill me-3">			 
                <!-- 드롭다운 메뉴-->				
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    견적
                </a>								
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/estimate/list.php">
						<i class="bi bi-card-checklist"></i> 견적 List
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/estimate/list_sim.php">
						<i class="bi bi-sliders2"></i> 견적 시뮬레이션
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/estimate/statistics.php">
						<i class="bi bi-bar-chart-line"></i> 견적통계
					</a>
					<hr style="margin:7px!important;">
					<a class="dropdown-item" href="<?=$root_dir?>/models/list.php">
						<i class="bi bi-kanban"></i> 모델 및 품목 관리
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/price_etc/list.php?header=header">
						<i class="bi bi-clipboard2-data"></i> 소모품 단가표
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/estimate/list_unit.php">
						<i class="bi bi-table"></i> 단가표
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/bendingfee/list.php">
						<i class="bi bi-calculator"></i> 절곡BOM단가
					</a>
					<hr style="margin:7px!important;">
					<?php if($level =='1' || $user_name =='함신옥' || $user_name =='이세희') { ?>
						<a class="dropdown-item" href="<?=$root_dir?>/price_raw_materials/list.php">
							<i class="bi bi-box-seam"></i> 주자재
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/price_motor/list.php">
							<i class="bi bi-cpu"></i> 모터
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/price_bend/list.php">
							<i class="bi bi-border-style"></i> 절곡판
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/price_shaft/list.php">
							<i class="bi bi-align-start"></i> 부자재(샤프트)
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/price_pole/list.php">
							<i class="bi bi-dash-circle"></i> 부자재(환봉)
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/price_angle/list.php">
							<i class="bi bi-signpost"></i> 부자재(앵글)
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/price_pipe/list.php">
							<i class="bi bi-diagram-3"></i> 부자재(각파이프)
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/price_screenplate/list.php">
							<i class="bi bi-window-stack"></i> 부자재(스크린평철)
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/price_smokeban/list.php">
							<i class="bi bi-wind"></i> 부자재(연기차단재)
						</a>
					<?php } ?>
				</div>
            </div>
			<div class="nav-item dropdown flex-fill me-3">			 
                <!-- 드롭다운 메뉴-->				
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    수주
                </a>								
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/output/list.php">
						<i class="bi bi-journals"></i> 수주 List
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/output/list_account.php">
						<i class="bi bi-journals"></i> 판매 List
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/output/list.php?option=미출고">
						<i class="bi bi-clock-history"></i> 미출고 수주List
					</a>
					<hr style="margin:7px!important;">
					<a class="dropdown-item" href="<?=$root_dir?>/acigroup/list.php?header=header">
						<i class="bi bi-diagram-3"></i> 품질관리서 그룹관리
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/output/list_requestACI.php">
						<i class="bi bi-clipboard-check"></i> 인정제품 제품검사요청서
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/output/list_QCdoc.php">
						<i class="bi bi-file-earmark-text"></i> 자동방화셔터 품질관리서
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/output/list_QCsales.php">
						<i class="bi bi-file-earmark-bar-graph"></i> 품질인정자재등의 판매실적
					</a>
					<hr style="margin:7px!important;">
					<a class="dropdown-item" href="<?=$root_dir?>/output/list_order.php">
						<i class="bi bi-file-earmark-plus"></i> 발주서
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/output/list_output.php">
						<i class="bi bi-box-arrow-up"></i> 출고증
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/output/list_POD.php">
						<i class="bi bi-file-check"></i> 납품확인서
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/output/month_schedule.php">
						<i class="bi bi-calendar-range"></i> 출고일정 월달력
					</a>
					<hr style="margin:7px!important;">
					<a class="dropdown-item" href="<?=$root_dir?>/output/list_deliveryfee.php">
						<i class="bi bi-truck-front-fill"></i> 배차 차량 일지
					</a>
					<hr style="margin:7px!important;">
					<a class="dropdown-item" href="<?=$root_dir?>/output/statistics.php">
						<i class="bi bi-graph-up-arrow"></i> 제조통계
					</a>
				</div>
            </div>
			<div class="nav-item dropdown flex-fill me-3">			 
                <!-- 드롭다운 메뉴-->				
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    공정
                </a>								
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/make/list.php">
						<i class="bi bi-grid-1x2"></i> 스크린
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/egimake/list.php">
						<i class="bi bi-hammer"></i> 철재(스라트)
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/motororder/list.php">
						<i class="bi bi-cpu-fill"></i> 모터
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/steelcut/list.php">
						<i class="bi bi-scissors"></i> 절곡
					</a>
				</div>

            </div>
			<div class="nav-item dropdown flex-fill me-3">			 
                <!-- 드롭다운 메뉴-->				
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    LOT&수입검사
                </a>								
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/prodcode/list.php?header=header">
						<i class="bi bi-upc-scan"></i> 품목코드
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/lot_sales/list.php?header=header">
						<i class="bi bi-box-arrow-in-down"></i> 판매-LOT
					</a>
					<hr style="margin:7px!important;">
					<a class="dropdown-item" href="<?=$root_dir?>/instock/list.php?header=header">
						<i class="bi bi-journal-bookmark-fill"></i> 수입검사대장
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/instock/list_sheet.php?header=header">
						<i class="bi bi-file-earmark-bar-graph"></i> 수입검사 성적서
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/instock/statistics.php?header=header">
						<i class="bi bi-graph-up"></i> 수입검사품목 통계
					</a>
				</div>

            </div>			
			<div class="nav-item dropdown flex-fill me-3">			 
                <!-- 드롭다운 메뉴-->				
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    품질관리
                </a>								
					<div class="dropdown-menu">
						<a class="dropdown-item" href="<?=$root_dir?>/output/list_document.php">
							<i class="bi bi-journal-check"></i> 실적신고
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/output/list_document_except.php">
							<i class="bi bi-journal-x"></i> 실적신고(제외)
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/output/list_ACI.php">
							<i class="bi bi-clipboard-data"></i> 인정검사 성적서
						</a>
						<hr style="margin:7px!important;">
						<a class="dropdown-item" href="<?=$root_dir?>/output/list_screen.php">
							<i class="bi bi-display"></i> 스크린 작업일지
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/output/list_screen_mid.php">
							<i class="bi bi-file-earmark-text"></i> 스크린 중간 검사성적서
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/output/list_slat.php">
							<i class="bi bi-building"></i> 철재(스라트) 작업일지
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/output/list_slat_mid.php">
							<i class="bi bi-clipboard-check"></i> 철재(스라트) 중간 검사성적서
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/output/list_jointbar.php">
							<i class="bi bi-align-end"></i> 조인트바 중간 검사성적서
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/output/list_bending.php">
							<i class="bi bi-scissors"></i> 절곡 작업일지
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/output/list_bending_mid.php">
							<i class="bi bi-clipboard-pulse"></i> 절곡 중간 검사성적서
						</a>
						<hr style="margin:7px!important;">
						<a class="dropdown-item" href="<?=$root_dir?>/phonebook_CE/list.php?header=header">
							<i class="bi bi-telephone-forward-fill"></i> 시공업체 주소록
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/phonebook_SU/list.php?header=header">
							<i class="bi bi-people-fill"></i> 감리업체 주소록
						</a>
					</div>
            </div>		

			<div class="nav-item dropdown flex-fill me-3">			 
				<!-- 드롭다운 메뉴-->				
				<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
					절곡품
				</a>								
					<div class="dropdown-menu">
						<a class="dropdown-item" href="<?=$root_dir?>/bending/list.php?header=header">
							<i class="bi bi-journal-richtext"></i> 절곡 바라시 기초자료
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/lot/list.php?header=header">
							<i class="bi bi-layers"></i> 절곡품 재고생산로트
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/lot/list_sheet.php?header=header">
							<i class="bi bi-clipboard-pulse"></i> 재고생산 작업일지/중간검사성적서
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/bendingmap/list.php?header=header">
							<i class="bi bi-diagram-3"></i> 절곡품 매핑(그룹)
						</a>
						<hr style="margin:7px!important;">
						<a class="dropdown-item" href="<?=$root_dir?>/guiderail/list.php?header=header">
							<i class="bi bi-signpost-2"></i> 가이드레일
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/shutterbox/list.php?header=header">
							<i class="bi bi-box2"></i> 케이스
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/bottombar/list.php?header=header">
							<i class="bi bi-align-bottom"></i> 하장바세트
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/etcbending/list.php?header=header">
							<i class="bi bi-puzzle"></i> 기타 절곡품
						</a>
					</div>

			</div>	
            <div class="nav-item dropdown flex-fill me-3">			 
                <!-- 드롭다운 메뉴-->
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    차량/지게차
                </a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/car/list.php">
						<i class="bi bi-truck-front"></i> 차량 관리
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/carrecord/list.php">
						<i class="bi bi-calendar-range"></i> 차량일지/월간사진 기록
					</a> 
					<a class="dropdown-item" href="<?=$root_dir?>/lift/list.php">
						<i class="bi bi-box-arrow-in-up"></i> 지게차 관리
					</a>
				</div>

            </div>	
		<?php if ($mycompany == '경동' && $mypart == '경리' || $user_name == '이경호' || $user_name == '개발자') { ?>		
			<div class="nav-item dropdown flex-fill me-3">			 
				<!-- 드롭다운 메뉴-->				
				<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
					회계
				</a>								
				<div class="dropdown-menu">		
					<a class="dropdown-item" href="<?=$root_dir?>/account/PS_summarytable.php">
						<i class="bi bi-clipboard-data"></i> 매입 매출 집계표
					</a>					
					<a class="dropdown-item" href="<?=$root_dir?>/output/statis_output.php?header=header">
						<i class="bi bi-bar-chart-fill"></i> 매출 통계
					</a>					
					<a class="dropdown-item" href="<?=$root_dir?>/account/list.php">
						<i class="bi bi-journal-text"></i> 금전출납부
					</a>	
					<a class="dropdown-item" href="<?=$root_dir?>/account/list_daily.php">
							<i class="bi bi-journal-bookmark-fill"></i> 일일 일보
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/account/receivable.php">
						<i class="bi bi-exclamation-circle-fill"></i> 미수금현황
					</a>

					<a class="dropdown-item" href="<?=$root_dir?>/account/S_transaction.php?header=header">
						<i class="bi bi-person-vcard-fill"></i> 거래처 원장
					</a>

					<a class="dropdown-item" href="<?=$root_dir?>/getmoney/list.php?header=header">
						<i class="bi bi-coin"></i> 수금현황
					</a>

					<a class="dropdown-item" href="<?=$root_dir?>/account/month_sales.php?header=header">
						<i class="bi bi-bar-chart-steps"></i> 당월판매회계반영
					</a>

					<a class="dropdown-item" href="<?=$root_dir?>/account_plan/list.php">
						<i class="bi bi-clipboard-data"></i> 월별 수입/지출 예상내역서
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/phonebook/baddebt.php?header=header">
						<i class="bi bi-exclamation-octagon-fill text-danger"></i> 악성 채권추심
					</a>
					<hr style="margin:7px!important;">
					<a class="dropdown-item" href="#" onclick="event.preventDefault(); customPopup('../account/settings.php', '계정 관리', 600, 850);">
							<i class="bi bi-gear-fill"></i> 수입/지출 계정
					</a>	
					<a class="dropdown-item" href="<?=$root_dir?>/KDunitprice/list.php?header=header">
						<i class="bi bi-currency-dollar"></i>  단가설정
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/account/cardlist.php?header=header">
						<i class="bi bi-credit-card"></i>  법인카드
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/account/accoutlist.php?header=header">
						<i class="bi bi-cash-coin"></i>  법인계좌
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/phonebook/list.php?header=header">
						<i class="bi bi-person-lines-fill"></i> 발주처
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/phonebook_buy/list.php?header=header">
						<i class="bi bi-building-fill"></i> 매입처
					</a>
				</div>

			</div>  
		<?php } ?>   			
            <div class="nav-item dropdown flex-fill me-3">			 
                <!-- 드롭다운 메뉴-->
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    게시/설정
                </a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/notice/list.php">
						<i class="bi bi-megaphone-fill"></i> 공지사항
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/qna/list.php">
						<i class="bi bi-folder2-open"></i> 자료실
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/holiday/list.php?header=header">
						<i class="bi bi-calendar-check"></i> 일정표 휴일설정
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/qrcode/index.php">
						<i class="bi bi-qr-code-scan"></i> QR코드 생성
					</a>					
					<a class="dropdown-item" href="<?=$root_dir?>/rnd/list.php">
						<i class="bi bi-journal-code"></i> 개발일지
					</a>
				</div>
            </div>			
         <?php if(!empty($allpass)) { ?>
            <div class="nav-item dropdown flex-fill me-3">
                <!-- 드롭다운 메뉴-->
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                    공유
                </a>
				<div class="dropdown-menu">
					<a class="dropdown-item" href="<?=$root_dir?>/youtube.php">
						<i class="bi bi-youtube"></i> (주)경동기업
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/annualleave/index.php">
						<i class="bi bi-person-bounding-box"></i> 연차
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/absent/index.php">
						<i class="bi bi-fingerprint"></i> 근태
					</a>
					<a class="dropdown-item" href="<?=$root_dir?>/roadview.php">
						<i class="bi bi-people-fill"></i> 직원 주소록
					</a>
				</div>

            </div>          
		 <?php } ?>                 		 
				<div class="nav-item dropdown flex-fill me-3">				 
					<!-- 드롭다운 메뉴-->
					<a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
						<?=$_SESSION["name"]?>님(Lv<?=$_SESSION["level"]?>)
					</a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="<?=$root_dir?>/login/logout.php">
							<i class="bi bi-box-arrow-right"></i> 로그아웃
						</a>
						<a class="dropdown-item" href="<?=$root_dir?>/member/updateForm.php?id=<?=$_SESSION["userid"]?>">
							<i class="bi bi-person-gear"></i> 정보수정
						</a>
						<hr style="margin:7px!important;">
						<?php if ($_SESSION["level"] == '1') { ?>
							<a class="dropdown-item" href="<?=$root_dir?>/member/list.php">
								<i class="bi bi-people"></i> 회원관리
							</a>
							<a class="dropdown-item" href="<?=$root_dir?>/logdata.php">
								<i class="bi bi-door-open"></i> 로그인기록
							</a>
							<a class="dropdown-item" href="<?=$root_dir?>/logdata_menu.php">
								<i class="bi bi-list-task"></i> 메뉴접속기록
							</a>
						<?php } ?>
					</div>
				</div>

				<div class="nav-item flex-fill me-6">			 
					<!-- 전자결재 관련 알람 -->
					<a class="nav-link dropdown-toggle" href="#" onclick="seltab(3);">
						<span id="alert_eworks_bell" style="display:none; font-size:15px;">🔔결재</span>
						<i class="bi bi-file-earmark-text"></i>
						<span id="alert_eworks"></span>
						전자결재
					</a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="<?=$root_dir?>/annualleave/index.php">
							<i class="bi bi-person-bounding-box"></i> 연차
						</a>
						<hr style="margin:7px!important;">
						<a class="dropdown-item" href="<?=$root_dir?>/askitem/list.php">
							<i class="bi bi-person-bounding-box"></i> 품의서
						</a>
						<!-- Expenditure resolution 지출결의서 -->
						<a class="dropdown-item" href="<?=$root_dir?>/askitem_ER/list.php">
							<i class="bi bi-person-bounding-box"></i> 지출결의서
						</a>
					</div>
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
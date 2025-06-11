<div class="container-fluid justify-content-center align-items-center">  	
  <!-- Modal --> 
  <div  id="myModal"  class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg modal-center" >
    
      <!-- Modal content-->
      <div class="modal-content modal-lg">
        <div class="modal-header">          
          <h2 class="modal-title">알림</h2>
        </div>
        <div class="modal-body">
		<div class="d-flex justify-content-center text-dark fs-3 mb-2"> 
		    <span id="alertmsg"> </span>
			<!-- <img id=popupwindow src="./img/popupmall.jpg"  style="width:60%; height:60%;"> 	-->
			<!-- <img id=popupwindow src="./img/steelname2.jpg"  style="width:100%; height:100%;"> 	-->			
		</div>
		</div>			
        <div class="modal-footer">		
          <button type="button" class="btn btn-default" id="closemodalBtn" data-dismiss="modal">닫기</button>
        </div>
		</div>
      </div>
	</div>
</div>

<div class="container-fluid justify-content-center align-items-center">  
  <!-- Modal -->
  <div id="updatepriceModal" class="modal">
    <div class="modal-content" style="width:800px;">
      <div class="modal-header">          
       <h2 class="modal-title">정보 수정</h2>
		<button type="button" class="closemodalBtn" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
		</button>
      </div>
      <div class="modal-body">
        <table class="table table-hover">
          <thead class="table-primary">
            <tr>
              <th class="text-center">번호</th>
              <th class="text-center" style="width:300px;">품목</th>
              <th class="text-center" style="width:100px;">할인여부</th>
              <th class="text-center" style="width:100px;">원가</th>
              <th class="text-center" style="width:100px;">단가</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-center">1</td>
              <td><input type="text" class="form-control" id="modalItem"></td>
              <td><input type="text" class="form-control" id="modalIsDc"></td>
              <td><input type="text" class="form-control" id="modalOriginalCost" oninput="formatNumber(this)"></td>
              <td><input type="text" class="form-control" id="modalPrice" oninput="formatNumber(this)"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">		
        <button type="button" class="btn btn-dark" id="saveChangesBtn">수정</button>
        <button type="button" class="btn btn-outline-dark closemodalBtn" data-dismiss="modal"><i class="bi bi-x-lg"></i> 닫기</button>
      </div>
    </div>
  </div>
</div>

 <!-- Modal HTML -->
<div class="container-fluid justify-content-center align-items-center">   
    <div id="timeModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">서버 이관작업 안내</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2> 금일 작업한 도면을 Nas2dual 회사 서버에 올려주세요. </h2>
					<br>
					<br>
					<h2> 오늘도 수고 많으셨습니다.</h2>
                </div>
                <div class="modal-footer">
                    <button id="timeModalcloseBtn" type="button" class="btn btn-secondary fs-3"  onclick="stopInterval()" data-dismiss="modal">닫기</button>
                </div>
            </div>
        </div>
    </div>
</div>
	
<!-- Modal --> 
<!-- Vertically centered modal -->    
<div class="container-fluid justify-content-center align-items-center">  
<div class="modal fade" id="Approval Modal" role="dialog">
	<div class="modal-dialog modal-dialog-centered">

		<!-- Modal content-->
		<div class="modal-content modal-lg">
			<div class="modal-header">          
			<h4 class="modal-title">결재 알림</h4>
			</div>
				<div class="modal-body">
				<div class="d-flex justify-content-center mb-2 fs-5"> 
				결재 내용이 있습니다. 확인바랍니다.
				</div>
				</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="closemodalApprovalBtn" data-dismiss="modal">닫기</button>
			</div>
		</div>
    </div>
</div>
</div>

<!-- 모터, 브라켓 Modal -->
<div class="container-fluid justify-content-center align-items-center">  
<div id="lotModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content ">
            <div class="modal-header">          
                <h2 class="modal-title">품목코드/로트번호 매칭</h2>
                <button type="button" class="btn btn-outline-dark lotModalclose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead class="table-primary">
                        <tr>                            
                            <th class="text-center" style="width:35%;">품목코드</th>                            
                            <th class="text-center" style="width:35%;">로트번호</th>
                            <th class="text-center" style="width:15%;">재고</th>
                            <th class="text-center" style="width:15%;">발주수량</th>
                        </tr>
                    </thead>
                    <tbody id="lotModalBody">
                    </tbody>
                </table>
            </div>
		  <div class="modal-footer">		
             발주수량(참고) :  <input type="number" id="request_qty" name="request_qty" class="form-control text-center me-2" style="width:100px;" />   		  
			<button type="button" class="btn btn-dark btn-sm adaptBtn me-2" > 발주적용</button>
			<button type="button" class="btn btn-outline-dark btn-sm lotModalclose" data-dismiss="modal"><i class="bi bi-x-lg"></i> 닫기</button>
		  </div>			
        </div>
    </div>
</div>
</div>

<!-- 연동제어기 모달 Modal -->
<div class="container-fluid justify-content-center align-items-center">  
<div id="controllerlotModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content ">
            <div class="modal-header">          
                <h2 class="modal-title">품목코드/로트번호 매칭</h2>
                <button type="button" class="btn btn-outline-dark controllerlotModalclose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead class="table-primary">
                        <tr>                            
                            <th class="text-center" style="width:35%;">품목코드</th>                            
                            <th class="text-center" style="width:35%;">로트번호</th>
                            <th class="text-center" style="width:15%;">재고</th>
                            <th class="text-center" style="width:15%;">발주수량</th>
                        </tr>
                    </thead>
                    <tbody id="controllerlotModalBody">
                    </tbody>
                </table>
            </div>
		  <div class="modal-footer">		
             발주수량(참고) :  <input type="number" id="controllerrequest_qty" name="controllerrequest_qty" class="form-control text-center me-2" style="width:100px;" />   		  
			<button type="button" class="btn btn-dark btn-sm controlleradaptBtn me-2" > 발주적용</button>
			<button type="button" class="btn btn-outline-dark btn-sm controllerlotModalclose" data-dismiss="modal"><i class="bi bi-x-lg"></i> 닫기</button>
		  </div>			
        </div>
    </div>
</div>
</div>

<!-- 부속자재 모달 Modal -->
<div class="container-fluid justify-content-center align-items-center">  
<div id="sublotModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document" >
        <div class="modal-content ">
            <div class="modal-header">          
                <h2 class="modal-title">품목코드/로트번호 매칭</h2>
                <button type="button" class="btn btn-outline-dark sublotModalclose">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead class="table-primary">
                        <tr>                            
                            <th class="text-center" style="width:35%;">품목코드</th>                            
                            <th class="text-center" style="width:35%;">로트번호</th>
                            <th class="text-center" style="width:15%;">재고</th>
                            <th class="text-center" style="width:15%;">발주수량</th>
                        </tr>
                    </thead>
                    <tbody id="sublotModalBody">
                    </tbody>
                </table>
            </div>
		  <div class="modal-footer">		
             발주수량(참고) :  <input type="number" id="subrequest_qty" name="subrequest_qty" class="form-control text-center me-2" style="width:100px;" />   		  
			<button type="button" class="btn btn-dark btn-sm subadaptBtn me-2" > 발주적용</button>
			<button type="button" class="btn btn-outline-dark btn-sm sublotModalclose" data-dismiss="modal"><i class="bi bi-x-lg"></i> 닫기</button>
		  </div>			
        </div>
    </div>
</div>
</div>

<!-- 수신처 모달 Modal -->
<div class="container-fluid justify-content-center align-items-center">  
	<div id="telModal" class="modal fade" tabindex="-1">
		<div class="modal-dialog modal-full" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title">수신처 조회</h2>
					<button type="button" class="btn btn-outline-dark Modalclose" data-dismiss="modal">
						<i class="bi bi-x-lg"></i>
					</button>
				</div>
				<div class="modal-body">
					<table class="table table-hover">
						<thead class="table-primary">
							<tr>
								<th class="text-center" style="width:10%;">번호</th>
								<th class="text-center" style="width:15%;">수신처(반장) 이름</th>
								<th class="text-center" style="width:15%;">연락처</th>
								<th class="text-center" style="width:60%;">수신처</th>
							</tr>                                          
						</thead>
						<tbody id="ModalBody">
						</tbody>
					</table>
				</div>
				<div class="modal-footer">					
					<button type="button" class="btn btn-outline-dark btn-sm Modalclose" data-dismiss="modal">
						<i class="bi bi-x-lg"></i> 닫기
					</button>
				</div>			
			</div>
		</div>
	</div>
</div>

<!-- 현장명 검색 Modal -->
<div class="container-fluid justify-content-center align-items-center">  
	<div id="outworkplaceModal" class="modal fade" tabindex="-1">
		<div class="modal-dialog modal-full" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title"> 현장명 조회 </h2>
					<button type="button" class="btn btn-outline-dark Modalclose" data-dismiss="modal">
						<i class="bi bi-x-lg"></i>
					</button>
				</div>
				<div class="modal-body">
					<table class="table table-hover">
						<thead class="table-primary">
							<tr>
								<th class="text-center" style="width:5%;">번호</th>
								<th class="text-center" style="width:40%;">현장명 </th>								
								<th class="text-center" style="width:15%;">수신처</th>
								<th class="text-center" style="width:30%;">수신주소</th>
								<th class="text-center" style="width:10%;"> <i class="bi bi-telephone-outbound-fill"></i> </th>
							</tr>                                          
						</thead>
						<tbody class="ModalBody">
						</tbody>
					</table>
				</div>
				<div class="modal-footer">					
					<button type="button" class="btn btn-outline-dark btn-sm Modalclose" data-dismiss="modal">
						<i class="bi bi-x-lg"></i> 닫기
					</button>
				</div>			
			</div>
		</div>
	</div>
</div>

<!-- 스크린 견적 검색 Modal -->
<div class="container-fluid justify-content-center align-items-center">  
   <div id="loadEstimateModal" class="modal fade" tabindex="-1" aria-hidden="false" >
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title"> 스크린 견적 조회 </h2>
                    <button type="button" class="btn btn-outline-dark Modalclose" data-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- 검색어 입력 필드 추가 -->
                    <div class="d-flex justify-content-center align-items-center mb-2">
						<div class="inputWrap">
								<input type="text" id="searchEstimate" name="searchEstimate" class="form-control " style="width:200px;" onkeydown="JavaScript:EstimateSearchEnter(event);" autocomplete='off' placeholder="검색어 입력" >
								<button class="btnClear"></button>
						</div>									
						<button class="btn btn-outline-secondary" id="searchEstimateBtn" type="button"><i class="bi bi-search"></i>  </button>
                    </div>

                    <!-- 검색 결과 테이블 -->
                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center" style="width:5%;">번호</th>
                                <th class="text-center" style="width:8%;">접수일</th>
                                <th class="text-center" style="width:10%;">견적번호</th>
                                <th class="text-center" style="width:6%;">대분류</th>
                                <th class="text-center" style="width:6%;">제품모델</th>
                                <th class="text-center" style="width:10%;">발주처</th>								
                                <th class="text-center" style="width:15%;">현장명</th>
                                <th class="text-center" style="width:10%;">금액</th>
                                <th class="text-center" style="width:5%;">담당자</th>       
                            </tr>                                          
                        </thead>
                        <tbody class="ModalBody">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">					
                    <button type="button" class="btn btn-outline-dark btn-sm Modalclose" data-dismiss="modal">
                        <i class="bi bi-x-lg"></i> 닫기
                    </button>
                </div>			
            </div>
        </div>
    </div>
</div>

<!-- 철재스라트 견적 검색 Modal -->
<div class="container-fluid justify-content-center align-items-center">  
   <div id="loadEstimateModal_slat" class="modal fade" tabindex="-1" >
        <div class="modal-dialog modal-full" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title"> 철재스라트 견적 조회 </h2>
                    <button type="button" class="btn btn-outline-dark Modalclose" data-dismiss="modal">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- 검색어 입력 필드 추가 -->
                    <div class="d-flex justify-content-center align-items-center mb-2">
						<div class="inputWrap">
								<input type="text" id="searchEstimate_slat" name="searchEstimate_slat" class="form-control " style="width:200px;" onkeydown="JavaScript:EstimateSearchEnter_slat(event);" autocomplete='off' placeholder="검색어 입력" >
								<button class="btnClear"></button>
						</div>									
						<button class="btn btn-outline-secondary" id="searchEstimateBtn_slat" type="button"><i class="bi bi-search"></i>  </button>
                    </div>

                    <!-- 검색 결과 테이블 -->
                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center" style="width:5%;">번호</th>
                                <th class="text-center" style="width:8%;">접수일</th>
                                <th class="text-center" style="width:10%;">견적번호</th>
                                <th class="text-center" style="width:6%;">대분류</th>
                                <th class="text-center" style="width:6%;">제품모델</th>
                                <th class="text-center" style="width:10%;">발주처</th>								
                                <th class="text-center" style="width:15%;">현장명</th>
                                <th class="text-center" style="width:10%;">금액</th>
                                <th class="text-center" style="width:5%;">담당자</th>      
                            </tr>                                          
                        </thead>
                        <tbody class="ModalBody">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">					
                    <button type="button" class="btn btn-outline-dark btn-sm Modalclose" data-dismiss="modal">
                        <i class="bi bi-x-lg"></i> 닫기
                    </button>
                </div>			
            </div>
        </div>
    </div>
</div>

<!-- 모달창 구조 -->
<div class="modal fade" id="selectEstimateModal" tabindex="-999" >
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">견적 항목 선택</h5>
        <button type="button" class="close closeModal_Estimate Modalclose" >
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
                <th>
                    <input type="checkbox" class="selectAllEstimates" checked> 
                </th>
				<th>층</th>
				<th>부호</th>
				<th>제품명</th>
				<th>종류</th>				
				<th>가로(폭) 제작</th>
				<th>세로(높이) 제작</th>				
				<th>셔터 수량</th>
            </tr>
          </thead>
          <tbody id="estimateListBody">
            <!-- 견적 리스트가 이곳에 삽입될 예정 -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="applySelectedEstimates">선택 적용</button>
        <button type="button" class="btn btn-secondary closeModal_Estimate" data-dismiss="modal">닫기</button>
      </div>
    </div>
  </div>
</div>
<?php
// 품의서 작성/수정/삭제
require_once $_SERVER['DOCUMENT_ROOT'] . '/load_GoogleDrive.php'; // 세션 등 여러가지 포함됨 파일 포함	
$title_message = '품의서';    
?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/common.php' ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php'; ?>  
<title> <?=$titlemsg?> </title>
</head>
<style>
.show {display:block} /*보여주기*/
.hide {display:none} /*숨기기*/
  input[type="text"] {
    text-align: left !important ; 
  }  
  input[type="number"] {
    text-align: left !important ;
  }
 td, th, tr, span, input {
    vertical-align: middle;
  }
</style>	
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . "/common/modal.php"; ?>   
<?php   
$tablename = 'eworks';  
  
$mode=  $_REQUEST["mode"] ?? '' ;
$num=  $_REQUEST["num"] ?? '' ;
$author=  $user_name ?? '' ;
// ---------------------------------------------
// timekey: 임시 저장용 key 생성
// ---------------------------------------------
if (empty($_REQUEST['num'])) {
  // 32자리 랜덤 문자열 생성 (PHP 7 이상)
  $timekey = bin2hex(random_bytes(16));
} else {
  // 이미 생성된 key가 넘어오면 재사용
  $timekey = $_REQUEST['num'];
}

$indate=date("Y-m-d") ?? '' ;
 
  if ($mode=="modify" or $mode=="view"){
    try{
      $sql = "select * from {$DB}.eworks where num = ? ";
      $stmh = $pdo->prepare($sql); 

      $stmh->bindValue(1,$num,PDO::PARAM_STR); 
      $stmh->execute();
      $count = $stmh->rowCount();            
	  $row = $stmh->fetch(PDO::FETCH_ASSOC);  // $row 배열로 DB 정보를 불러온다.
    if($count<1){  
      print "결과가 없습니다.<br>";
     }else{		 
 		include  $_SERVER['DOCUMENT_ROOT'] . '/eworks/_row.php';	
		// 전자결재의 정보를 다시 변환해 준다.		
		$mytitle = $outworkplace ?? '';
		$content = $al_content ?? '';
		$content_reason = $request_comment ?? '';	

		$titlemsg = $mode === 'modify' ? '품의서(수정)' : '품의서(조회)'; 
		
      }
     }catch (PDOException $Exception) {
       print "오류: ".$Exception->getMessage();
     } 
  }
  else{
    // 신규 작성일경우 초기화
    include $_SERVER['DOCUMENT_ROOT'] .'/eworks/_request.php';
    $titlemsg = '품의서 작성';
    $mytitle = $outworkplace ?? '';
		$content = $al_content ?? '';
		$content_reason = $request_comment ?? '';	
  }  
  
      
  if ($mode!="modify" and $mode!="view" and $mode!="copy"){    // 수정모드가 아닐때 신규 자료일때는 변수 초기화 한다.
          
			  $indate=date("Y-m-d");
			  $author = $user_name;
			  $titlemsg	= '품의서 작성';
  } 
  
  
  if ($mode=="copy"){
    try{
      $sql = "select * from {$DB}.eworks where num = ? ";
      $stmh = $pdo->prepare($sql); 

      $stmh->bindValue(1,$num,PDO::PARAM_STR); 
      $stmh->execute();
      $count = $stmh->rowCount();            
	  $row = $stmh->fetch(PDO::FETCH_ASSOC);  // $row 배열로 DB 정보를 불러온다.
    if($count<1){  
      print "결과가 없습니다.<br>";
     }else{
		 include $_SERVER['DOCUMENT_ROOT'] .'/eworks/_row.php';		
		// 전자결재의 정보를 다시 변환해 준다.		
		$mytitle = $outworkplace ?? '';
		$content = $al_content ?? '';
		$content_reason = $request_comment ?? '';	
		$indate=date("Y-m-d");
      }
     }catch (PDOException $Exception) {
       print "오류: ".$Exception->getMessage();
     }
	 
     $titlemsg	= '(데이터 복사) 품의서';	
	 $num='';	 
	 $id = $num;  
	 $parentid = $num;    
	 $author = $user_name;
	 $update_log='';
  }  
  
// 초기 프로그램은 $num사용 이후 $id로 수정중임  
$id=$num;    
require_once $_SERVER['DOCUMENT_ROOT'] . '/load_GoogleDriveSecond.php'; // attached, image에 대한 정보 불러오기  
?>

<form id="board_form" name="board_form" method="post"  onkeydown="return captureReturnKey(event)"  >	
    
	<!-- 전달함수 설정 input hidden -->
	<input type="hidden" id="id" name="id" value="<?=$id?>" >			  								
	<input type="hidden" id="num" name="num" value="<?=$num?>" >			  								
	<input type="hidden" id="parentid" name="parentid" value="<?=$parentid?>" >			  									
	<input type="hidden" id="item" name="item" value="<?=$item?>" >			  									
	<input type="hidden" id="tablename" name="tablename" value="<?=$tablename?>" >			  								
	<input type="hidden" id="savetitle" name="savetitle" value="<?=$savetitle?>" >			  								
	<input type="hidden" id="pInput" name="pInput" value="<?=$pInput?>" >			  								
	<input type="hidden" id="mode" name="mode" value="<?=$mode?>" >		
	<input type="hidden" id="timekey" name="timekey" value="<?=$timekey?>" >  <!-- 신규데이터 작성시 parentid key값으로 사용 -->		
	<input type="hidden" id="update_log" name="update_log" value="<?=$update_log?>"  >		
	<input type="hidden" id="first_writer" name="first_writer" value="<?=$first_writer?>"  >		
	
<div class="container-fluid" >
<div class="card">
<div class="card-body">			   

<div class="row">
	<div class="col-sm-7">
		<div class="d-flex mb-5 mt-5 justify-content-center align-items-center ">
			<h4> <?=$titlemsg?> 
<span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-html="true" 
      title="
<b>제목:</b> 품의할 사항에 대한 요약을 제목으로 기재합니다. <br>
<b>품의내역:</b> 품의할 사항에 대한 구체적인 내역을 표시합니다. <br>
<b>품의사유:</b> 근거나 사유에 대해서 제시합니다. 
">
  <i class="bi bi-info-circle-fill"></i>  
</span>

					</h4> 
				</div>
			</div>
			
	   <div class="col-sm-5">		
	<?php
		//var_dump($al_part);			

		// $al_part=='지원파트';
	  //  if($e_confirm ==='' || $e_confirm === null) 
	  //  {
		// 	$formattedDate = date("m/d", strtotime($registdate)); // 월/일 형식으로 변환
		// 	// echo $formattedDate; // 출력
			
		// 	if($al_part=='제조파트')
		// 	{
		// 		$approvals = array(
		// 			array("name" => "공장장 이경묵", "date" =>  $formattedDate),
		// 			array("name" => "대표 소현철", "date" =>  $formattedDate),
		// 			// 더 많은 결재권자가 있을 수 있음...
		// 		);	
		// 	}
		// 	if($al_part=='지원파트')
		// 	{
		// 		$approvals = array(
		// 			array("name" => "이사 최장중", "date" =>  $formattedDate),
		// 			array("name" => "대표 소현철", "date" =>  $formattedDate),
		// 			// 더 많은 결재권자가 있을 수 있음...
		// 		);	
		// 	}
	  //  }
	  //  else
	  //  {			
			$approver_ids = explode('!', $e_confirm_id);
			$approver_details = explode('!', $e_confirm);

			$approvals = array();

			foreach($approver_ids as $index => $id) {
				if (isset($approver_details[$index])) {
					// Use regex to match the pattern (name title date time)
					// The pattern looks for any character until it hits a series of digits that resemble a date followed by a time
					preg_match("/^(.+ \d{4}-\d{2}-\d{2}) (\d{2}:\d{2}:\d{2})$/", $approver_details[$index], $matches);

					// Ensure that the full pattern and the two capturing groups are present
					if (count($matches) === 3) {
						$nameWithTitle = $matches[1]; // This is the name and title
						$time = $matches[2]; // This is the time
						$date = substr($nameWithTitle, -10); // Extract date from the end of the 'nameWithTitle' string
						$nameWithTitle = trim(str_replace($date, '', $nameWithTitle)); // Remove the date from the 'nameWithTitle' to get just the name and title
						$formattedDate = date("m/d H:i:s", strtotime("$date $time")); // Combining date and time

						$approvals[] = array("name" => $nameWithTitle, "date" => $formattedDate);
					}
				}
			}

        // // Now $approvals contains the necessary details
        // foreach ($approvals as $approval) {
          // echo "Approver: " . $approval['name'] . ", Date: " . $approval['date'] . "<br>";
        // }
	   // }					
		
		if($status === 'end' and ($e_confirm !=='' && $e_confirm !== null) )
		  {
		?>				
		
			<div class="container mb-2">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th colspan="<?php echo count($approvals); ?>" class="text-center fs-6">결재</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<?php foreach ($approvals as $approval) { ?>
								<td class="text-center fs-6" style="height: 60px;"><?php echo $approval["name"]; ?></td>
							<?php } ?>
						</tr>
						<tr>
							<?php foreach ($approvals as $approval) { ?>
								<td class="text-center"><?php echo $approval["date"]; ?></td>
							<?php } ?>
						</tr>
					</tbody>
				</table>
			</div>			  
		  
		  <?  } 
				 else
				 {
		   ?>
					<div class="container mb-2">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th colspan="<?php echo count($approvals); ?>" class="text-center fs-6">결재 진행 전</th>
						</tr>
					</thead>
					<tbody>
						<tr>								
						</tr>
					</tbody>
				</table>
			</div>	
	  <?  }   ?>
		
				
 </div> 			
</div> 
 	
	
<?php if($mode!='view') { ?>		
	
	<div class="row">
		<div class="col-sm-9">		   
			<div class="d-flex  mb-1 justify-content-start  align-items-center"> 		   
				<button id="saveBtn" type="button" class="btn btn-dark  btn-sm me-2"  > <i class="bi bi-floppy"></i> 저장(결재상신)  </button> 
			</div> 			
		</div> 	
		<div class="col-sm-3">	
				<div class="d-flex  mb-1 justify-content-end"> 	
				   <button class="btn btn-secondary btn-sm" onclick="self.close();"  > <i class="bi bi-x-lg"></i> 창닫기 </button>&nbsp;					
				</div> 			
		</div> 			
	</div> 
<?php } else {  ?>		
       <div class="row">
		<?php if($chkMobile) { ?>	
		  <div class="col-sm-12">
		<?php } if(!$chkMobile) { ?>	
		  <div class="col-sm-7">
		<?php  } ?>			 		   
      <div class="d-flex  justify-content-start"> 							
				<?php if($chkMobile==true)	{ ?>
					<button class="btn btn-dark btn-sm" onclick="location.href='list.php'" > <i class="bi bi-card-list"></i> 목록 </button>&nbsp;	
				<?php } ?>						
					<button type="button"   class="btn btn-dark btn-sm mx-1" onclick="location.href='write_form.php?mode=modify&num=<?=$num?>'" > <i class="bi bi-pencil-square"></i>  수정 </button> &nbsp;
				 <?php if($user_id === $author_id || $admin) { ?>
					<button type="button" class="btn btn-danger btn-sm mx-1"
							onclick="deleteFn()">
					  <i class="bi bi-trash"></i> 삭제
					</button>
				 <?php } ?>
					<button type="button"   class="btn btn-dark btn-sm mx-1" onclick="location.href='write_form.php'" > <i class="bi bi-pencil"></i>  신규 </button>		&nbsp;										
					<button type="button"   class="btn btn-primary btn-sm mx-1" onclick="location.href='write_form.php?mode=copy&num=<?=$num?>'" > <i class="bi bi-copy"></i> 복사 </button>	&nbsp;							
			 </div> 			
		 </div> 			
		<?php if($chkMobile) { ?>	
		  <div class="col-sm-12">
		<?php } if(!$chkMobile) { ?>	
		  <div class="col-sm-5 text-end">
		<?php  } ?>	
				<div class="d-flex  mb-1 justify-content-end"> 	
					<button class="btn btn-secondary btn-sm" type="button" onclick="self.close();" >  &times; 창닫기 </button>&nbsp;									
				</div> 					 
			</div> 
	 </div> <!-- end of row -->	
<?php } // end of elseif  ?>	

  <div class="row mt-2">  
      <table class="table table-bordered">
		<tr>
		  <td class=" text-center w-25 fw-bold">
			<label for="indate">작성일</label>
		  </td>          			
		 <td >				
			<input type="date" class="form-control w120px viewNoBtn" id="indate" name="indate" value="<?=$indate?>" >				
		  </td>	
		   <td class=" text-center w-25 fw-bold">
			<label for="author">기안자</label>
		  </td>          			
		 <td>				
			<input type="text" class="form-control text-center w80px viewNoBtn" id="author" name="author" value="<?=$author?>" >				
		  </td>					 
		</tr>
		<tr>
		  <td class=" text-center w-25 fw-bold">
			<label for="mytitle">품의서 제목</label>
		  </td>
		  <td colspan="3">
				<input type="text" class="form-control viewNoBtn" id="mytitle" name="mytitle" value="<?=$mytitle?>"  placeholder ="제목 : 품의할 사항에 대한 요약" >
		  </td>
		</tr>		
      </table>
    </div>
	<div class="row mt-2">  	
	  <table class="table table-bordered">        
		<tr>
		  <td class="text-center w-25 fw-bold">
			<label for="store">구매처</label>
		  </td>
		  <td>
			<input type="text" class="form-control viewNoBtn" id="store" name="store" value="<?=$store?>"  placeholder ="구매처(업체명)" >			
		  </td>
		</tr>      
		<tr>
		  <td class="text-center w-25 fw-bold">
			<label for="content">품의 내역</label>
		  </td>
		  <td>
			<textarea class="form-control auto-expand viewNoBtn" id="content" name="content" autocomplete="off"  placeholder ="품의할 사항에 대한 구체적인 내역을 표시합니다."  required rows="5" style="resize: none;"><?=$content?></textarea>
		  </td>
		</tr>
		<tr>
		  <td class="text-center w-25 fw-bold">
			<label for="content_reason">품의 사유</label>
		  </td>
		  <td>
			<textarea class="form-control auto-expand viewNoBtn" id="content_reason" name="content_reason" placeholder="근거나 사유에 대해서 제시합니다 " autocomplete="off" rows="5" style="resize: none;"><?=$content_reason?></textarea>
		  </td>
		</tr>
		<tr>
		  <td class="text-center w-25 fw-bold">
			<label for="suppliercost">예상 비용</label>
		  </td>
		  <td>
			<div class="d-flex  justify-content-start align-items-center "> 
				<input type="text" class="form-control w110px viewNoBtn text-end me-1" id="suppliercost" name="suppliercost"  placeholder="예상 총 비용"  value="<?=$suppliercost?>" style="text-align: right !important;" oninput="formatInput(this)"> 원

			</div>
		  </td>
		</tr>
	  </table>	  	  
	</div>
	</div> 
   
	  <div class="d-flex mt-3 mb-1 justify-content-center">  	 			
		    <label  for="upfileimage" class="btn btn-outline-dark btn-sm ">  사진 첨부 </label>	
				 <input id="upfileimage"  name="upfileimage[]" type="file" onchange="this.value" multiple accept=".gif, .jpg, .png" style="display:none">		
       </div>
		<div class="d-flex  mb-1 justify-content-center"> 
		   <div class="card justify-content-center ">	
				   <div class="card-body justify-content-center ">	
					   <div class="d-flex  mb-1 justify-content-center fs-6"> 
							<div id ="displayImage" class="row d-flex mt-3 mb-1 justify-content-center" style="display:none;">  	 		 					 
							</div>		
						  </div>		
					</div>   
			</div>   
		</div>   
	
	 </div>	  
		</div>	  
		</div>	  
		</div>	  
 </div>	  
</form>	

<script>
var ajaxRequest = null;

// 페이지 로딩
$(document).ready(function(){	
    var loader = document.getElementById('loadingOverlay');
	if(loader)
		loader.style.display = 'none';
});
$(document).ready(function(){
		
	 $("#saveBtn").click(function(){ 
		// 조건 확인
		if($("#mytitle").val() === '' || $("#content").val() === '' || $("#content_reason").val()  === '' ) {
			showWarningModal();
		} else {
		   showMsgModal(2); // 파일저장중
			Toastify({
				text: "변경사항 저장중...",
				duration: 2000,
				close:true,
				gravity:"top",
				position: "center",
				style: {
					background: "linear-gradient(to right, #00b09b, #96c93d)"
				},
			}).showToast();	
			setTimeout(function(){
					 saveData();
			}, 1000);
		  
		}
	});

	function showWarningModal() {
		Swal.fire({                                    
			title: '등록 오류 알림',
			text: '제목, 내용, 사유는 필수입력 요소입니다.',
			icon: 'warning',
			// ... 기타 설정 ...
		}).then(result => {
			if (result.isConfirmed) { 
				return; // 사용자가 확인 버튼을 누르면 아무것도 하지 않고 종료
			}         
		});
	}

	function saveData() {		
		var num = $("#num").val();  		
		// 결재상신이 아닌경우 수정안됨     
		if(Number(num) < 1) 				
				$("#mode").val('insert');     			  						
		//  console.log($("#mode").val());    
		// 폼데이터 전송시 사용함 Get form         
		var form = $('#board_form')[0];  	    	
		var datasource = new FormData(form); 

		// console.log(data);
		if (ajaxRequest !== null) {
			ajaxRequest.abort();
		}		 
		ajaxRequest = $.ajax({
			enctype: 'multipart/form-data',    // file을 서버에 전송하려면 이렇게 해야 함 주의
			processData: false,    
			contentType: false,      
			cache: false,           
			timeout: 600000, 			
			url: "insert.php",
			type: "post",		 
			data: datasource,			
			dataType: "json", 
			success : function(data){
				  console.log('data :' , data);
				  Swal.fire(
					  '자료등록 완료',
					  '데이터가 성공적으로 등록되었습니다.',
					  'success'
					);
				setTimeout(function(){									
					if (window.opener && !window.opener.closed) {
						// 부모 창에 restorePageNumber 함수가 있는지 확인
						if (typeof window.opener.restorePageNumber === 'function') {
							window.opener.restorePageNumber(); // 함수가 있으면 실행
						}								
					}
				setTimeout(function(){		
					hideMsgModal();	
					// location.href = "view.php?num=" + data["num"];
					self.close();
				}, 1000);	
							
				}, 1000);						
			},
			error : function( jqxhr , status , error ){
				console.log( jqxhr , status , error );
						} 			      		
		   });					
	}	
});

// 기존 deleteFn(...) 함수를 아래로 교체
function deleteFn() {
  Swal.fire({
    title: '자료 삭제',
    text: "삭제는 신중! 정말 삭제하시겠습니까?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: '삭제',
    cancelButtonText: '취소'
  }).then((result) => {
    if (result.isConfirmed) {
      // soft-delete 모드 세팅
      $("#mode").val('delete');

      $.ajax({
        url: 'insert.php',    // ← delete.php 대신 insert.php
        type: 'POST',
        data: $("#board_form").serialize(),
        dataType: 'json',
      }).done(function(data) {
        Toastify({
          text: "자료 삭제 처리 완료",
          duration: 2000,
          close: true,
          gravity: "top",
          position: "center",
          style: {
            background: "linear-gradient(to right, #00b09b, #96c93d)"
          },
        }).showToast();

        setTimeout(function() {
          if (window.opener && !window.opener.closed) {
            window.opener.restorePageNumber();
            window.opener.location.reload();
          }
          setTimeout(() => window.close(), 500);
        }, 1000);
      }).fail(function(jqxhr, status, error) {
        console.error("삭제 오류:", status, error);
        Swal.fire('오류', '삭제 처리 중 문제가 발생했습니다.', 'error');
      });
    }
  });
}

	 
function captureReturnKey(e) {
    if(e.keyCode==13 && e.srcElement.type != 'textarea')
    return false;
}
</script> 

<script>
$(document).ready(function () {
	displayFileLoad();	// 기존파일 업로드 보이기			 
	displayImageLoad();	// 기존이미지 업로드 보이기			 
	
    // 첨부파일 업로드 처리
    $("#upfile").change(function (e) {
		if (this.files.length === 0) {
			// 파일이 선택되지 않았을 때
			console.warn("파일이 선택되지 않았습니다.");
			return;
		}		
		
        const form = $('#board_form')[0];
        const data = new FormData(form);

        // 추가 데이터 설정
        data.append("tablename", $("#tablename").val() );
        data.append("item", "attached");
        data.append("upfilename", "upfile"); // upfile 파일 name
        data.append("folderPath", "경동기업/uploads");
		data.append("DBtable", "picuploads");        

		showMsgModal(2); // 파일저장중

        // AJAX 요청 (Google Drive API)
        $.ajax({
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            url: "/filedrive/fileprocess.php",
            type: "POST",
            data: data,
            success: function (response) {
                 console.log("응답 데이터:", response);

                let successCount = 0;
                let errorCount = 0;
                let errorMessages = [];

                response.forEach((item) => {
                    if (item.status === "success") {
                        successCount++;
                    } else if (item.status === "error") {
                        errorCount++;
                        errorMessages.push(`파일: ${item.file}, 메시지: ${item.message}`);
                    }
                });

                if (successCount > 0) {
                    Toastify({
                        text: `${successCount}개의 파일이 성공적으로 업로드되었습니다.`,
                        duration: 2000,
                        close: true,
                        gravity: "top",
                        position: "center",
                        backgroundColor: "#4fbe87",
                    }).showToast();
                }

                if (errorCount > 0) {
                    Toastify({
                        text: `오류 발생: ${errorCount}개의 파일 업로드 실패\n상세 오류: ${errorMessages.join("\n")}`,
                        duration: 5000,
                        close: true,
                        gravity: "top",
                        position: "center",
                        backgroundColor: "#f44336",
                    }).showToast();
                }

                setTimeout(function () {                    
					displayFile();
					hideMsgModal();	
                }, 1000);
                
            },
            error: function (jqxhr, status, error) {
                console.error("업로드 실패:", jqxhr, status, error);
            },
        });
    });
	
    // 첨부 이미지 업로드 처리
    $("#upfileimage").change(function (e) {
		if (this.files.length === 0) {
			// 파일이 선택되지 않았을 때
			console.warn("파일이 선택되지 않았습니다.");
			return;
		}	
		
        const form = $('#board_form')[0];
        const data = new FormData(form);
		
        // 추가 데이터 설정
        data.append("tablename", $("#tablename").val() );
        data.append("item", "image");
        data.append("upfilename", "upfileimage"); // upfile 파일 name
        data.append("folderPath", "경동기업/uploads");
        data.append("DBtable", "picuploads");

		showMsgModal(1); // 이미지저장중

        // AJAX 요청 (Google Drive API)
        $.ajax({
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            url: "/filedrive/fileprocess.php",
            type: "POST",
            data: data,
            success: function (response) {
                console.log("응답 데이터:", response);

                let successCount = 0;
                let errorCount = 0;
                let errorMessages = [];

                response.forEach((item) => {
                    if (item.status === "success") {
                        successCount++;
                    } else if (item.status === "error") {
                        errorCount++;
                        errorMessages.push(`파일: ${item.file}, 메시지: ${item.message}`);
                    }
                });

                if (successCount > 0) {
                    Toastify({
                        text: `${successCount}개의 파일이 성공적으로 업로드되었습니다.`,
                        duration: 2000,
                        close: true,
                        gravity: "top",
                        position: "center",
                        backgroundColor: "#4fbe87",
                    }).showToast();
                }

                if (errorCount > 0) {
                    Toastify({
                        text: `오류 발생: ${errorCount}개의 파일 업로드 실패\n상세 오류: ${errorMessages.join("\n")}`,
                        duration: 5000,
                        close: true,
                        gravity: "top",
                        position: "center",
                        backgroundColor: "#f44336",
                    }).showToast();
                }

                setTimeout(function () {
					displayImage();
					hideMsgModal();						
                }, 1000);
                
            },
            error: function (jqxhr, status, error) {
                console.error("업로드 실패:", jqxhr, status, error);
            },
        });
    });


});

// 화면에서 저장한 첨부된 파일 불러오기
function displayFile() {
    $('#displayFile').show();
    const params = $("#timekey").val() ? $("#timekey").val() : $("#num").val();

    if (!params) {
        console.error("ID 값이 없습니다. 파일을 불러올 수 없습니다.");
        alert("ID 값이 유효하지 않습니다. 다시 시도해주세요.");
        return;
    }

    console.log("요청 ID:", params); // 요청 전 ID 확인

    $.ajax({
        url: '/filedrive/fileprocess.php',
        type: 'GET',
        data: {
            num: params,
			tablename: $("#tablename").val(),
            item: 'attached',
            folderPath: '경동기업/uploads',
        },
        dataType: 'json',
    }).done(function (data) {
        console.log("파일 데이터:", data);

        $("#displayFile").html(''); // 기존 내용 초기화

        if (Array.isArray(data) && data.length > 0) {
            data.forEach(function (fileData, index) {
                const realName = fileData.realname || '다운로드 파일';
                const link = fileData.link || '#';
                const fileId = fileData.fileId || null;

                if (!fileId) {
                    console.error("fileId가 누락되었습니다. index: " + index, fileData);
                    $("#displayFile").append(
                        "<div class='text-danger'>파일 ID가 누락되었습니다.</div>"
                    );
                    return;
                }

				$("#displayFile").append(
					"<div class='row mt-1 mb-2'>" +
						"<div class='d-flex align-items-center justify-content-center'>" +
							"<span id='file" + index + "'>" +
								"<a href='#' onclick=\"popupCenter('" + link + "', 'filePopup', 800, 600); return false;\">" + realName + "</a>" +
							"</span> &nbsp;&nbsp;" +
							"<button type='button' class='btn btn-danger btn-sm' id='delFile" + index + "' onclick=\"delFileFn('" + index + "', '" + fileId + "')\">" +
								"<i class='bi bi-trash'></i>" +
							"</button>" +
						"</div>" +
					"</div>"
				);


            });
        } else {
            $("#displayFile").append(
                "<div class='text-center text-muted'>No files</div>"
            );
        }
    }).fail(function (error) {
        console.error("파일 불러오기 오류:", error);
        Swal.fire({
            title: "파일 불러오기 실패",
            text: "파일을 불러오는 중 문제가 발생했습니다.",
            icon: "error",
            confirmButtonText: "확인",
        });
    });
}

// 기존 파일 불러오기 (Google Drive에서 가져오기)
function displayFileLoad() {
    $('#displayFile').show();
    var data = <?php echo json_encode($savefilename_arr); ?>;

    $("#displayFile").html(''); // 기존 내용 초기화

    if (Array.isArray(data) && data.length > 0) {
        data.forEach(function (fileData, i) {
            const realName = fileData.realname || '다운로드 파일';
            const link = fileData.link || '#';
            const fileId = fileData.fileId || null;

            if (!fileId) {
                console.error("fileId가 누락되었습니다. index: " + i, fileData);
                return;
            }

			$("#displayFile").append(
				"<div class='row mb-3'>" +
					"<div class='d-flex mb-3 align-items-center justify-content-center'>" +
						"<span id='file" + i + "'>" +
							"<a href='#' onclick=\"popupCenter('" + link + "', 'filePopup', 800, 600); return false;\">" + realName + "</a>" +
						"</span> &nbsp;&nbsp;" +
						"<button type='button' class='btn btn-danger btn-sm' id='delFile" + i + "' onclick=\"delFileFn('" + i + "', '" + fileId + "')\">" +
							"<i class='bi bi-trash'></i>" +
						"</button>" +
					"</div>" +
				"</div>"
			);

        });
    } else {
        $("#displayFile").append(
            "<div class='text-center text-muted'>No files</div>"
        );
    }
}

// 파일 삭제 처리 함수
function delFileFn(divID, fileId) {
    Swal.fire({
        title: "파일 삭제 확인",
        text: "정말 삭제하시겠습니까?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "삭제",
        cancelButtonText: "취소",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/filedrive/fileprocess.php',
                type: 'DELETE',
                data: JSON.stringify({
                    fileId: fileId,
                    tablename: $("#tablename").val(),
                    item: "attached",
                    folderPath: "경동기업/uploads",
                    DBtable: "picuploads",
                }),
                contentType: "application/json",
                dataType: 'json',
            }).done(function (response) {
                if (response.status === 'success') {
                    console.log("삭제 완료:", response);
                    $("#file" + divID).remove();
                    $("#delFile" + divID).remove();

                    Swal.fire({
                        title: "삭제 완료",
                        text: "파일이 성공적으로 삭제되었습니다.",
                        icon: "success",
                        confirmButtonText: "확인",
                    });
                } else {
                    console.log(response.message);
                }
            }).fail(function (error) {
                console.error("삭제 중 오류:", error);
                Swal.fire({
                    title: "삭제 실패",
                    text: "파일 삭제 중 문제가 발생했습니다.",
                    icon: "error",
                    confirmButtonText: "확인",
                });
            });
        }
    });
}

// 첨부된 이미지 불러오기
function displayImage() {
    $('#displayImage').show();
    const params = $("#timekey").val() ? $("#timekey").val() : $("#num").val();

    if (!params) {
        console.error("ID 값이 없습니다. 파일을 불러올 수 없습니다.");
        alert("ID 값이 유효하지 않습니다. 다시 시도해주세요.");
        return;
    }

    $.ajax({
        url: '/filedrive/fileprocess.php',
        type: 'GET',
        data: {
            num: params,
            tablename: $("#tablename").val(),
            item: 'image',
            folderPath: '경동기업/uploads',
        },
        dataType: 'json',
    }).done(function (data) {
        $("#displayImage").html('');
        if (Array.isArray(data) && data.length > 0) {
            data.forEach(function (fileData, index) {
                const realName = fileData.realname || '다운로드 파일';
                const thumbnail = fileData.thumbnail || '/assets/default-thumbnail.png';
                const link = fileData.link || '#';
                const fileId = fileData.fileId || null;
                const rotation = fileData.rotation || 0;

                if (!fileId) {
                    console.error("fileId가 누락되었습니다. index: " + index, fileData);
                    $("#displayImage").append(
                        "<div class='text-danger'>파일 ID가 누락되었습니다.</div>"
                    );
                    return;
                }

                // 구글 드라이브 이미지 팝업 링크 생성
                $("#displayImage").append(
                    "<div class='row mb-3'>" +
                        "<div class='col d-flex align-items-center justify-content-center'>" +
                            "<div class='position-relative'>" +
                                "<a href='#' onclick=\"openTmpImagePopup('" + link + "', " + rotation + "); return false;\">" +
                                    "<img id='image" + index + "' src='" + thumbnail + "' style='width:150px; height:auto; transform: rotate(" + rotation + "deg);'>" +
                                "</a>" +
                                "<div class='position-absolute top-0 end-0 mt-1 me-1'>" +
                                    "<button type='button' class='btn btn-primary btn-sm rotate-btn' onclick=\"rotateImage('" + fileId + "', 'image" + index + "')\">" +
                                        "<i class='bi bi-arrow-clockwise'></i>" +
                                    "</button>" +
                                "</div>" +
                            "</div>" +
                            "&nbsp;&nbsp;" +
                            "<button type='button' class='btn btn-danger btn-sm' id='delImage" + index + "' onclick=\"delImageFn('" + index + "', '" + fileId + "')\">" +
                                "<i class='bi bi-trash'></i>" +
                            "</button>" +
                        "</div>" +
                    "</div>"
                );
            });
        } else {
            $("#displayImage").append(
                "<div class='text-center text-muted'>No files</div>"
            );
        }
    }).fail(function (error) {
        console.error("파일 불러오기 오류:", error);
        Swal.fire({
            title: "파일 불러오기 실패",
            text: "파일을 불러오는 중 문제가 발생했습니다.",
            icon: "error",
            confirmButtonText: "확인",
        });
    });
}

// 기존 이미지 불러오기 (Google Drive에서 가져오기)
function displayImageLoad() {
    $('#displayImage').show();
    var data = <?php echo json_encode($saveimagename_arr); ?>;
    $("#displayImage").html('');
    if (Array.isArray(data) && data.length > 0) {
        data.forEach(function (fileData, i) {
            const realName = fileData.realname || '다운로드 파일';
            const thumbnail = fileData.thumbnail || '/assets/default-thumbnail.png';
            const link = fileData.link || '#';
            const fileId = fileData.fileId || null;
            const rotation = fileData.rotation || 0;

            if (!fileId) {
                console.error("fileId가 누락되었습니다. index: " + i, fileData);
                return;
            }

            // 구글 드라이브 이미지 팝업 링크 생성
            $("#displayImage").append(
                "<div class='row mb-3'>" +
                    "<div class='col d-flex align-items-center justify-content-center'>" +
                        "<div class='position-relative'>" +
                            "<a href='#' onclick=\"openTmpImagePopup('" + link + "', " + rotation + "); return false;\">" +
                                "<img id='image" + i + "' src='" + thumbnail + "' style='width:150px; height:auto; transform: rotate(" + rotation + "deg);'>" +
                            "</a>" +
                            "<div class='position-absolute top-0 end-0 mt-1 me-1'>" +
                                "<button type='button' class='btn btn-primary btn-sm rotate-btn' onclick=\"rotateImage('" + fileId + "', 'image" + i + "')\">" +
                                    "<i class='bi bi-arrow-clockwise'></i>" +
                                "</button>" +
                            "</div>" +
                        "</div>" +
                        "&nbsp;&nbsp;" +
                        "<button type='button' class='btn btn-danger btn-sm' id='delImage" + i + "' onclick=\"delImageFn('" + i + "', '" + fileId + "')\">" +
                            "<i class='bi bi-trash'></i>" +
                        "</button>" +
                    "</div>" +
                "</div>"
            );
        });
    } else {
        $("#displayImage").append(
            "<div class='text-center text-muted'>No files</div>"
        );
    }
}

// 이미지 삭제 처리 함수
function delImageFn(divID, fileId) {
    Swal.fire({
        title: "이미지 삭제 확인",
        text: "정말 삭제하시겠습니까?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "삭제",
        cancelButtonText: "취소",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/filedrive/fileprocess.php',
                type: 'DELETE',
                data: JSON.stringify({
                    fileId: fileId,
                    tablename: $("#tablename").val(),
                    item: "image",
                    folderPath: "경동기업/uploads",
                    DBtable: "picuploads",
                }),
                contentType: "application/json",
                dataType: 'json',
            }).done(function (response) {
                if (response.status === 'success') {
                    console.log("삭제 완료:", response);
                    $("#image" + divID).remove();
                    $("#delImage" + divID).remove();

                    Swal.fire({
                        title: "삭제 완료",
                        text: "파일이 성공적으로 삭제되었습니다.",
                        icon: "success",
                        confirmButtonText: "확인",
                    });
                } else {
                    console.log(response.message);
                }
            }).fail(function (error) {
                console.error("삭제 중 오류:", error);
                Swal.fire({
                    title: "삭제 실패",
                    text: "파일 삭제 중 문제가 발생했습니다.",
                    icon: "error",
                    confirmButtonText: "확인",
                });
            });
        }
    });
}

// 이미지 회전 함수 추가
function rotateImage(fileId, imageId) {
    const img = document.getElementById(imageId);
    const currentRotation = parseInt(img.style.transform.replace('rotate(', '').replace('deg)', '')) || 0;
    const newRotation = (currentRotation + 90) % 360;
    img.style.transform = `rotate(${newRotation}deg)`;
    
    // 회전 각도를 서버에 저장        
    saveRotationAngle(fileId, newRotation);
}

// 회전 각도를 서버에 저장하는 함수
function saveRotationAngle(fileId, angle) {
    // 로딩 표시
    Toastify({
        text: "회전 상태 저장 중...",
        duration: 2000,
        close: true,
        gravity: "top",
        position: "center",
        style: {
            background: "linear-gradient(to right, #4a90e2, #67b26f)"
        }
    }).showToast();

    // AJAX 요청 전 데이터 확인
    const requestData = {
        action: 'saveRotation',        
        fileId: fileId,
        rotation: angle,
        tablename: $("#tablename").val()
    };

    console.log('전송 데이터:', JSON.stringify(requestData)); // 디버깅용

    $.ajax({
        url: '/filedrive/fileprocess.php',
        type: 'POST',
        data: JSON.stringify(requestData),
        contentType: "application/json",
        dataType: 'json',
        success: function(response) {
            console.log('서버 응답:', response); // 디버깅용
            
            if (response && response.status === 'success') {
                Toastify({
                    text: response.message || "회전 상태가 저장되었습니다",
                    duration: 2000,
                    close: true,
                    gravity: "top",
                    position: "center",
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)"
                    }
                }).showToast();
            } else {
                const errorMessage = response ? response.message : '알 수 없는 오류가 발생했습니다.';
                console.error('회전 각도 저장 실패:', errorMessage);
                Toastify({
                    text: errorMessage,
                    duration: 2000,
                    close: true,
                    gravity: "top",
                    position: "center",
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)"
                    }
                }).showToast();
            }
        },
        error: function(xhr, status, error) {
            console.error('회전 각도 저장 중 오류:', {
                status: status,
                error: error,
                response: xhr.responseText
            });

            let errorMessage = "회전 상태 저장 중 오류가 발생했습니다.";
            try {
                if (xhr.responseText) {
                    const response = JSON.parse(xhr.responseText);
                    if (response && response.message) {
                        errorMessage = response.message;
                    }
                }
            } catch (e) {
                console.error('응답 파싱 오류:', e);
            }
            
            Toastify({
                text: errorMessage,
                duration: 2000,
                close: true,
                gravity: "top",
                position: "center",
                style: {
                    background: "linear-gradient(to right, #ff5f6d, #ffc371)"
                }
            }).showToast();
        }
    });
}

function getGoogleDriveFileId(link) {
    // 구글드라이브 파일ID 추출
    var match = link.match(/\/d\/([a-zA-Z0-9_-]+)/);
    return match ? match[1] : '';
}

function openTmpImagePopup(link, rotation) {
    var fileId = getGoogleDriveFileId(link);
    if (!fileId) {
        alert('구글드라이브 파일ID 추출 실패');
        return;
    }
    $.post('/filedrive/download_and_rotate.php', { fileId: fileId, rotation: rotation }, function(res) {
        if (res.success) {
            // 팝업에 회전값도 전달
            popupCenter('/filedrive/view_tmpimg.php?img=' + encodeURIComponent(res.imgUrl) + '&rotation=' + rotation, 'imagePopup', 800, 600);
        } else {
            alert(res.msg || '이미지 처리 실패');
        }
    }, 'json');
}

</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const textareas = document.querySelectorAll("textarea.auto-expand");

  function adjustHeight(el) {
    el.style.height = "auto";
    el.style.height = el.scrollHeight + "px";
  }

  textareas.forEach(textarea => {
    textarea.addEventListener("input", function() {
      adjustHeight(this);
    });
    adjustHeight(textarea);
  });
});

$(document).ready(function () {
	// 모드가 'view'인 경우 disable 처리 (기존 코드 유지)
	var mode = '<?php echo $mode; ?>';
	if (mode === 'view') {
		disableView(); 
	}

	function disableView() {
			$('input, textarea ').prop('readonly', true); // Disable all input, textarea, and select elements
			$('input[type=hidden]').prop('readonly', false); 

			// checkbox와 radio는 클릭 불가능하게 하고 시각적 강조
			$('input[type="checkbox"], input[type="radio"]').each(function() {
				$(this).addClass('readonly-checkbox readonly-radio');
			});

			// 파일 입력 비활성화 
			$('input[type=file]').prop('disabled', true); 
			$('.fetch_receiverBtn').prop('disabled', true);  // 수신자 버튼 비활성화
			$('.viewNoBtn').prop('disabled', true);  //버튼 비활성화
			$('.searchplace').prop('disabled', true);  // 수신자 버튼 비활성화
			$('.searchsecondord').prop('disabled', true);  // 수신자 버튼 비활성화
			
			// 레이블 텍스트 크게 설정
			$('label').css('font-size', '1.2em');
			
			// select 속성 readonly 효과 내기
			$('select[data-readonly="true"]').on('mousedown', function(event) {
				event.preventDefault();
			});

			// checkbox 속성 readonly 효과 내기
			$('input[type="checkbox"][data-readonly="true"]').on('click', function(event) {
				event.preventDefault();
			});

	}
});


function formatInput(input) {
    let value = input.value;
    value = value.replace(/,/g, ""); // Remove all existing commas
    value = value.replace(/[^\d]/g, ""); // Remove all non-digit characters
    input.value = numberWithCommas(value); // Add commas and update the value
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
</script>
<!-- 부트스트랩 툴팁 -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });  
  	// $("#order_form_write").modal("show");	  
});
</script>

<script>
function popupCenter(url, title, w, h) {
    // URL에 이미 파라미터가 있는지 확인
    const separator = url.includes('?') ? '&' : '?';
    
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}
</script>

</body>
</html>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/session.php';   

header("Content-Type: application/json");  //json을 사용하기 위해 필요한 구문  
  
  // 임시저장된 첨부파일을 확정하기 위해 검사하기  
isset($_REQUEST["timekey"])  ? $timekey=$_REQUEST["timekey"] :  $timekey='';   // 신규데이터에 생성할때 임시저장키  

$mode = $_REQUEST["mode"] ?? 'insert';
$num = $_REQUEST["num"] ?? '';
$indate = $_REQUEST["indate"] ?? '';
$mytitle = $_REQUEST["mytitle"] ?? '';
$content = $_REQUEST["content"] ?? '';
$content_reason = $_REQUEST["content_reason"] ?? '';
$first_writer = $_REQUEST["first_writer"] ?? ''; 
$author = $_REQUEST["author"] ?? '';
$update_log = $_REQUEST["update_log"] ?? '';
$suppliercost = $_REQUEST["suppliercost"] ?? '';
$store = $_REQUEST["store"] ?? '';

// 전자결재의 변수에 매칭 (저장변수가 다른경우 선언)
$outworkplace = $mytitle;    // 제목
$al_content = $content;      // 품의 내역
$request_comment = $content_reason; // 품의 사유

$e_title = $mytitle; // 전자결재 제목
$eworks_item = '품의서';

// 전자 결재에 보여질 내용 data 수정 update       
$data = array(    
	"e_title" => $e_title,
	"indate" => $indate,
	"author" => $author,	
	"outworkplace" => $outworkplace,  // mytitle 매칭
	"al_content" => $al_content, // content 매칭	
	"request_comment" => $request_comment,
	"store" => $store,
	"suppliercost" => $suppliercost  // 비용 총액 저장
);

$contents = json_encode($data, JSON_UNESCAPED_UNICODE);
  
require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

// 전자결재 이름찾아 결재 아이디 찾아내기
try {
    $membersql = "SELECT * FROM {$DB}.member WHERE name = ?";
    $stmh = $pdo->prepare($membersql); 
    $stmh->bindValue(1, trim($author), PDO::PARAM_STR); 
    $stmh->execute(); 
    $rowMember = $stmh->fetch(PDO::FETCH_ASSOC);

    // 조회된 데이터가 있는지 확인 후 설정
    $first_approval_id = trim($rowMember['first_approval_id']) ?? ''; // 값이 없으면 빈 문자열
    $first_approval_name = trim($rowMember['first_approval_name']) ?? '';

} catch (PDOException $Exception) {
    print "오류: " . $Exception->getMessage();
}
     
 if ($mode=="modify"){
    $data=date("Y-m-d H:i:s") . " - "  . $_SESSION["name"] . "  " ;	
	$update_log = $data . $update_log . "&#10";  // 개행문자 Textarea      
    
    try {
        $pdo->beginTransaction();   
        
        // UPDATE 문 (변경된 컬럼명 반영)
        $sql = "UPDATE {$DB}.eworks SET 
                    indate = ?, 
                    outworkplace = ?, 
                    request_comment = ?, 
                    first_writer = ?, 
                    author = ?, 
                    update_log = ?, 
                    contents = ?, 
                    e_title = ?, 
                    eworks_item = ?,
					al_content=?, 
					suppliercost=?,  
					store=?  
                WHERE num = ? 
                LIMIT 1";

        $stmh = $pdo->prepare($sql);      

        // 바인딩된 값
        $stmh->bindValue(1, $indate, PDO::PARAM_STR);  
        $stmh->bindValue(2, $outworkplace, PDO::PARAM_STR);  
        $stmh->bindValue(3, $request_comment, PDO::PARAM_STR);  
        $stmh->bindValue(4, $first_writer, PDO::PARAM_STR);  
        $stmh->bindValue(5, $author, PDO::PARAM_STR);  
        $stmh->bindValue(6, $update_log, PDO::PARAM_STR);  
        $stmh->bindValue(7, $contents, PDO::PARAM_STR);  
        $stmh->bindValue(8, $e_title, PDO::PARAM_STR);  
        $stmh->bindValue(9, $eworks_item, PDO::PARAM_STR);  
        $stmh->bindValue(10, $al_content, PDO::PARAM_STR);  
        $stmh->bindValue(11, $suppliercost, PDO::PARAM_STR);  
        $stmh->bindValue(12, $store, PDO::PARAM_STR);  
        $stmh->bindValue(13, $num, PDO::PARAM_STR);        

        $stmh->execute();
        $pdo->commit(); 
    } catch (PDOException $Exception) {
        $pdo->rollBack();
        print "오류: " . $Exception->getMessage();
    }                         
}

else if ($mode == "copy" || $mode == "insert") {
    // 데이터 신규 등록
    $registdate = date("Y-m-d H:i:s");

    $e_line_id = $first_approval_id;
    $e_line = $first_approval_name ;    

    // if (is_array($approvalLines)) {
    //     foreach ($approvalLines as $line) {
    //         if ($al_part == $line['savedName']) {
    //             foreach ($line['approvalOrder'] as $order) {
    //                 $e_line_id .= $order['user-id'] . '!';
    //                 $e_line .= $order['name'] . '!';
    //             }
    //             break;
    //         }
    //     }
    // }

    // 결재 상태 설정
    $status = 'send';
    $author_id = $user_id;
    $author = $user_name;

    // 최초 등록자 정보
    $first_writer = $_SESSION["name"] . " _" . date("Y-m-d H:i:s");

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO {$DB}.eworks (
                    indate, outworkplace, request_comment, first_writer, 
                    author, update_log, contents, e_title, eworks_item, 
                    registdate, author_id, status, e_line_id, e_line, al_content, suppliercost, store 
					) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmh = $pdo->prepare($sql);

        // 바인딩된 값 설정
        $stmh->bindValue(1, $indate, PDO::PARAM_STR);
        $stmh->bindValue(2, $outworkplace, PDO::PARAM_STR);
        $stmh->bindValue(3, $request_comment, PDO::PARAM_STR);
        $stmh->bindValue(4, $first_writer, PDO::PARAM_STR);
        $stmh->bindValue(5, $author, PDO::PARAM_STR);
        $stmh->bindValue(6, $update_log, PDO::PARAM_STR);
        $stmh->bindValue(7, $contents, PDO::PARAM_STR);
        $stmh->bindValue(8, $e_title, PDO::PARAM_STR);
        $stmh->bindValue(9, $eworks_item, PDO::PARAM_STR);
        $stmh->bindValue(10, $registdate, PDO::PARAM_STR);
        $stmh->bindValue(11, $author_id, PDO::PARAM_STR);
        $stmh->bindValue(12, $status, PDO::PARAM_STR);
        $stmh->bindValue(13, rtrim($e_line_id, '!'), PDO::PARAM_STR);
        $stmh->bindValue(14, rtrim($e_line, '!'), PDO::PARAM_STR);
		$stmh->bindValue(15, $al_content, PDO::PARAM_STR);  
        $stmh->bindValue(16, $suppliercost, PDO::PARAM_STR);  		
        $stmh->bindValue(17, $store, PDO::PARAM_STR);  		

        $stmh->execute();
        $pdo->commit();
    } catch (PDOException $Exception) {
        $pdo->rollBack();
        print "오류: " . $Exception->getMessage();
    }

    // 신규 레코드 번호 가져오기
    try {
        $sql = "SELECT num FROM {$DB}.eworks ORDER BY num DESC LIMIT 1";
        $stmh = $pdo->prepare($sql);
        $stmh->execute();
        $row = $stmh->fetch(PDO::FETCH_ASSOC);
        $num = $row["num"];
    } catch (PDOException $Exception) {
        print "오류: " . $Exception->getMessage();
    }

    // 첨부파일의 임시 키를 정상적인 번호로 업데이트
    try {
        $pdo->beginTransaction();
        $sql = "UPDATE {$DB}.picuploads SET parentnum = ? WHERE parentnum = ?";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, $num, PDO::PARAM_STR);
        $stmh->bindValue(2, $timekey, PDO::PARAM_STR);
        $stmh->execute();
        $pdo->commit();
    } catch (PDOException $Exception) {
        $pdo->rollBack();
        print "오류: " . $Exception->getMessage();
    }
}

else if ($mode == "delete") {	
    try {
        // update_log에 삭제자 기록 추가
        $logEntry = date("Y-m-d H:i:s") . " - " . $_SESSION["name"] . " 삭제됨\n";
        $update_log = $logEntry . ($update_log ?? '');

        $pdo->beginTransaction();
        $sql = "UPDATE {$DB}.eworks
                SET is_deleted = 1,
                    update_log = ?
                WHERE num = ?
                LIMIT 1";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, $update_log, PDO::PARAM_STR);
        $stmh->bindValue(2, $num,        PDO::PARAM_INT);
        $stmh->execute();
        $pdo->commit();

        echo json_encode(["num"=>$num, "mode"=>$mode], JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["error"=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
    }    	
	
	exit;
	
   // 첨부파일 삭제
   // try{									
		 // $pdo->beginTransaction();
		 // $sql1 = "delete from {$DB}.picuploads where parentnum = ? and tablename = ? ";  
		 // $stmh1 = $pdo->prepare($sql1);
		 // $stmh1->bindValue(1,$num, PDO::PARAM_STR);      		 
		 // $stmh1->bindValue(2,'request_etc', PDO::PARAM_STR);      
		 // $stmh1->execute();  

		 // $pdo->commit();
		 
		 // } catch (Exception $ex) {
			// $pdo->rollBack();
			// print "오류: ".$Exception->getMessage();
	   // } 
	
}


$data = array(
	"num" =>  $num,
	"mode" =>  $mode
);

//json 출력
echo(json_encode($data, JSON_UNESCAPED_UNICODE));     
?>

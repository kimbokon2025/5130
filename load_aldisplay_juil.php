<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/session.php'); 

$tablename = "eworks";

require_once("./lib/mydb.php");
$pdo = db_connect();	
// 배열로 기본정보 불러옴
require_once($_SERVER['DOCUMENT_ROOT'] . "/almember/load_DB.php");
 
  $page=1;	 
  $scale = 20;       // 한 페이지에 보여질 게시글 수
  $page_scale = 20;   // 한 페이지당 표시될 페이지 수  10페이지
  $first_num = ($page-1) * $scale;  // 리스트에 표시되는 게시글의 첫 순번.
  $now = date("Y-m-d",time()) ;
  $sql = "SELECT * FROM " . $DB . "." . $tablename . "  WHERE (al_askdatefrom <= CURDATE() AND al_askdateto >= CURDATE())  AND al_company ='주일' AND is_deleted IS NULL ";

   try {  
    $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
    $temp1=$stmh->rowCount();    
    $total_row=$temp1;	

// print $sql;	
?>

<style>
    .rounded-card {
        border-radius: 15px !important;  /* 조절하고 싶은 라운드 크기로 설정하세요. */
    }
	
    table th,
    table td {
        text-align: center;
    }
	
</style>

<table class="table table-bordered table-hover table-sm">
    <thead class="table-secondary">
        <tr>      
            <th scope="col">신청인</th>
            <th scope="col">구분</th>
            <th scope="col">일자</th>
            <th scope="col">기간</th>
            <th scope="col">사유</th>
        </tr>
    </thead>
    <tbody>
        <?php  
        if ($page<=1)  
            $start_num=$total_row;    // 페이지당 표시되는 첫번째 글순번
        else 
            $start_num=$total_row-($page-1) * $scale;
    
        while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {		
            include "./annualleave/rowDBask.php";	   	
            $totalusedday = 0;
            $totalremainday = 0;		
            for($i=0;$i<count($totalname_arr);$i++)	 
                if($author== $totalname_arr[$i])
                {
                    $availableday  = $availableday_arr[$i];
                }	
            for($i=0;$i<count($totalname_arr);$i++)	 
                if($author== $totalname_arr[$i])
                {
                    $totalusedday = $totalused_arr[$i];
                    $totalremainday = $availableday - $totalusedday;	
                }	
					
	// 연도를 제거하고 나오게 하기				
	if ($al_askdatefrom !== $al_askdateto) {
		// 연도 부분 추출
		preg_match('/\d{4}-(\d{2}-\d{2})/', $al_askdatefrom, $matches_from);
		preg_match('/\d{4}-(\d{2}-\d{2})/', $al_askdateto, $matches_to);
		
		// 추출된 연도 부분 사용
		$datestr = $matches_from[1] . ' ~ ' . $matches_to[1];
	} else {
		// 연도 부분 추출
		preg_match('/\d{4}-(\d{2}-\d{2})/', $al_askdatefrom, $matches_from);
		
		// 추출된 연도 부분 사용
		$datestr = $matches_from[1];
	}

				   
				
        ?>	   
         <tr onclick="window.location.href='./annualleave/index.php'" style="cursor:pointer;">             
            <td><?=$author?></td>
            <td><?=$al_item?></td>
            <td><?=$datestr?></td>            
            <td><?=$al_usedday?></td>
            <td><?=$al_content?></td>
        </tr>
        <?php
        $start_num--;
        } 
    } catch (PDOException $Exception) {
        print "오류: ".$Exception->getMessage();
    }  
    
    // 페이지 구분 블럭의 첫 페이지 수 계산 ($start_page)
    $start_page = ($current_page - 1) * $page_scale + 1;
    // 페이지 구분 블럭의 마지막 페이지 수 계산 ($end_page)
    $end_page = $start_page + $page_scale - 1;  
    ?>

    </tbody>
</table>

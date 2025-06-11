<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

$header = $_REQUEST['header'] ?? '';

$title_message = '장비투입 (투입일 기준) ';   

if($header == 'header') 
{
	include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php'; 
	require_once($_SERVER['DOCUMENT_ROOT'] . '/myheader1.php');	
	$tablename = 'work'; 
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
	$pdo = db_connect();  

   echo '<title>  ' . $title_message . ' </title> ';
	
}

$sql = "SELECT * FROM chandj.work WHERE (is_deleted IS NULL OR is_deleted = 0) ORDER BY num DESC";
$nowday = date("Y-m-d");  
$start_num = 1;
$counter = 0;

try {   
    $stmh = $pdo->query($sql);                 
} catch (PDOException $Exception) {
    print "오류: " . $Exception->getMessage();
}

?>

<div class="card rounded-card mb-2 mt-3">
    <div class="card-header text-center">
        <div class="row"> 
            <div class="col-sm-12"> 
                <div class="d-flex justify-content-center align-items-center"> 
                    <span class="text-dark fs-6 me-3">
                        <a href="../load_request_equipment.php?header=header">
                            <span class="badge bg-secondary me-1 fs-6"> <?=$title_message?> </span>
                        </a>
                    </span>
                    <h6>
                        <span id="display_equipment_su" class="badge bg-secondary"></span>
                    </h6>
                </div> 
            </div>
        </div> 
    </div>

    <div class="card-body p-2 m-1 mb-3 d-flex justify-content-center">    
        <table class="table table-bordered table-hover table-sm">
            <thead class="table-secondary">
                <tr>
                    <th class="text-center" style="width:3%;">번호</th>
                    <th class="text-center" style="width:15%;">현장명</th>
                    <th class="text-center" style="width:8%;">공사담당</th>
                    <th class="text-center" style="width:20%;">시공내역</th>
                    <th class="text-center" style="width:5%;">장비명</th>
                    <th class="text-center" style="width:5%;">투입일</th>
                    <th class="text-center" style="width:2%;">수량</th>
                    <th class="text-center" style="width:5%;">장비업체</th>
                    <th class="text-center" style="width:5%;">업체담당</th>
                    <th class="text-center" style="width:5%;">연락처</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $start_num = $total_row;
                while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
                    // equipmentList를 JSON으로 파싱
                    $equipmentList = json_decode($row['equipmentList'], true);
                    
                    if (!$equipmentList || !is_array($equipmentList)) {
                        $equipmentList = []; // 비어있거나 JSON 파싱 실패시 빈 배열 처리
                    }
					                    
                    foreach ($equipmentList as $equipment) {
						 if(!empty($equipment['col2']) && empty($equipment['col3']) )
						 {
								$counter++;		 
								$start_num--;
                        ?>
                        <tr onclick="redirectToView_equipment('<?= $row['num'] ?>')">
                            <td class="text-center"><?= $counter ?></td>
                            <td class="text-start "><?= $row['workplacename'] ?></td>
                            <td class="text-center"><?= $row['chargedperson'] ?></td>
                            <td class="text-start"><?= $row['worklist'] ?></td>
                            <td class="text-center"><?= htmlspecialchars($equipment['col1']) ?></td> <!-- 장비명 -->
                            <td class="text-center text-primary "><?= htmlspecialchars($equipment['col2']) ?></td> <!-- 투입일 -->
                            <td class="text-center"><?= htmlspecialchars($equipment['col4']) ?></td> <!-- 수량 -->
                            <td class="text-center"><?= htmlspecialchars($equipment['col5']) ?></td> <!-- 장비업체 -->
                            <td class="text-center"><?= htmlspecialchars($equipment['col6']) ?></td> <!-- 업체담당 -->
                            <td class="text-center"><?= htmlspecialchars($equipment['col7']) ?></td> <!-- 연락처 -->
                        </tr>
                        <?php					
						 }
                    }
                    
                }
				
				$display_equipment = $counter;
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// 페이지 로딩
$(document).ready(function(){	
    var loader = document.getElementById('loadingOverlay');
    if(loader) 
		loader.style.display = 'none';
});
</script>

<script>
function redirectToView_equipment(num) {
    popupCenter("./work/write_form.php?mode=view&tablename=work&num=" + num, "공사수주내역", 1850, 900);
}

$(document).ready(function() {    	
    document.getElementById('display_equipment_su').textContent = '<?= $counter ?>';
});
</script>

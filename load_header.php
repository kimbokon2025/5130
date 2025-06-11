<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

if (!isset($_SESSION["level"]) || $_SESSION["level"] > 5) {
    sleep(1);
    header("Location:" . $WebSite . "login/login_form.php");
    exit; 
}   

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $root_dir = $_SERVER['DOCUMENT_ROOT'] ;
// $root_dir = $_SERVER['DOCUMENT_ROOT'] ;

// 모바일 사용여부 확인하는 루틴
$mAgent = array("iPhone","iPod","Android","Blackberry", 
    "Opera Mini", "Windows ce", "Nokia", "sony" );
	
$chkMobile = false;
for($i=0; $i<sizeof($mAgent); $i++){
    if(stripos( $_SERVER['HTTP_USER_AGENT'], $mAgent[$i] )){
        $chkMobile = true;		
        break;
    }
}

// 자바스크립트 자동 업데이트를 위한 version 설정
$version = '10';
?>

<style>
#overlay {
    position: fixed; /* 화면에 고정 */
    width: 100%; /* 전체 너비 */
    height: 100%; /* 전체 높이 */
    top: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.5); /* 검정색 반투명 배경 */
    z-index: 1000; /* 상위 레이어에 위치 */
}

#loadingOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    color: white;
    font-size: 1.5em;
}

.spinner {
    border: 8px solid #f3f3f3; /* Light grey */
    border-top: 8px solid #3498db; /* Blue */
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<!-- 로딩 스피너 컨테이너 -->
<div id="loadingOverlay">
	<div class="spinner"></div>
	<p> 페이지 Loading...</p>
</div>

<div id="overlay" style="display: none;"></div>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta property="og:type" content="">
<meta property="og:title" content="">
<meta property="og:url" content="5130.co.kr">
<meta property="og:description" content="통합 업무">
<meta property="og:image" content="<?$root_dir?>/img/thumbnail.jpg"> 
 
<script src="/js/jquery.min.js"></script>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;700&display=swap">
<link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.10/sweetalert2.min.css" rel="stylesheet">
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js" > </script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">   
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> 

<!-- 최초화면에서 보여주는 상단메뉴 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"></script> <!-- 쪽지 우측하단에 나오는 것 구현  bootstrap 앞에 나와야 한다.-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.10/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastify-js/1.12.0/toastify.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.js" integrity="sha512-6HrPqAvK+lZElIZ4mZ64fyxIBTsaX5zAFZg2V/2WT+iKPrFzTzvx6QAsLW2OaLwobhMYBog/+bvmIEEGXi0p1w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js" integrity="sha512-SIMGYRUjwY8+gKg7nn9EItdD8LCADSDfJNutF9TPrvEo86sQmFMh6MyralfIyhADlajSxqc7G0gs7+MwWF/ogQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/3.0.1/chartjs-plugin-annotation.min.js" integrity="sha512-Hn1w6YiiFw6p6S2lXv6yKeqTk0PLVzeCwWY9n32beuPjQ5HLcvz5l2QsP+KilEr1ws37rCTw3bZpvfvVIeTh0Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js" integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="stylesheet" href="https://uicdn.toast.com/tui.pagination/latest/tui-pagination.css" />
<script src="https://uicdn.toast.com/tui.pagination/latest/tui-pagination.js"></script>
<link rel="stylesheet" href="https://uicdn.toast.com/tui-grid/latest/tui-grid.css"/>
 <script src="https://uicdn.toast.com/tui-grid/latest/tui-grid.js"></script>	
 
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.0/font/bootstrap-icons.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<!-- date 날짜 시간 선택하기 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<!--
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/ko.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/luxon/2.3.2/luxon.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-grid.css">
<link rel="stylesheet" href="https://unpkg.com/ag-grid-community/styles/ag-theme-alpine.css">
<script src="https://unpkg.com/ag-grid-community/dist/ag-grid-community.noStyle.js"></script>
-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome 아이콘 나오는 것 구현 -->


<script src="<?$root_dir?>/js/common.js?version=<?=$version?>"></script> 
<script src="<?$root_dir?>/js/date.js?version=<?=$version?>"></script> 
<script src="<?$root_dir?>/js/index1.js?version=<?=$version?>"></script> 

<script src="<?$root_dir?>/js/todolist<?= $_SESSION["company"] == '주일기업' ? '1' : '' ?>.js?version=<?=$version?>"></script>
  
<script src="<?$root_dir?>/order/order.js?version=<?=$version?>"></script> 

<link rel="stylesheet" href="<?$root_dir?>/css/style.css?version=<?=$version?>">
<link rel="stylesheet" href="<?$root_dir?>/css/eworks.css?version=<?=$version?>">

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/common.php");  // 초기파일 로드

$now = date("Y-m-d",time()) ;  
require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

// 필요한 데이터를 담을 배열 초기화
$firstStep = array();
$firstStepID = array(); // 추가: 결재권한 ID를 저장할 배열 초기화

$admin = 0;
$ALadmin = 0; // 연차 레벨(결제권자는 1을 부여)

try {
    $sql = "SELECT * FROM $DB.member WHERE division IS NOT NULL";
    $stmh = $pdo->prepare($sql);
    $stmh->execute();

    while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
        // 필요한 데이터만 추출하여 배열에 저장
        $eworks_lv = (int)$row["eworks_lv"];
        if ($eworks_lv === 1 || $eworks_lv === 2) {
            $firstStep[] = $row["name"] . " " . $row["position"];
            $firstStepID[] = $row["id"]; // 결재권한 ID를 배열에 추가
        }
    }
} catch (PDOException $Exception) {
    print "오류: " . $Exception->getMessage();
}

// 현재 사용자가 결재권자인지 확인
$eworks_lv = in_array($user_id, $firstStepID) ? 1 : 0;

$_SESSION["eworks_lv"] = $eworks_lv;

if ($level == '1') {
    $admin = 1;    
	$ALadmin = 1;
} else {
    for ($i = 0; $i < count($firstStepID); $i++) {
        if ($user_id === $firstStepID[$i]) {
            $admin = 2;
			$ALadmin = 2;
            break; // 일치하는 경우가 발견되면 루프를 종료
        }
    }
}

?>
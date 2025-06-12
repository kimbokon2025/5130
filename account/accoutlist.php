<?php
// accoutlist.php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  
if (!isset($_SESSION["level"]) || $_SESSION["level"] > 5) {
    sleep(1);
    header("Location:" . $WebSite . "login/login_form.php");
    exit;
}
include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php';
$title_message = '법인계좌 목록 관리';
?>
<title><?= $title_message ?></title>
<?php if($chkMobile==true) { ?>
<style>
  @media (max-width: 1000px) {
    body { font-size: 25px; }
    .form-control, .fw-bold, .table td, .table th { font-size: 25px; }
    button { font-size: 30px; }
    .modal-body, .modal-title { font-size: 30px; }
  }
</style>
<?php } ?>
</head>
<body>

<?php
// 메뉴를 표현할지 판단하는 header
$header = $_REQUEST['header'] ?? '';

// if ($header == 'header') {
require_once($_SERVER['DOCUMENT_ROOT'] . '/myheader.php');

$jsonFile = $_SERVER['DOCUMENT_ROOT'] . '/account/accoutlist.json';
$cards = [];

if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $cards = json_decode($jsonContent, true);
    if (!is_array($cards)) {
        $cards = [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $index = isset($_POST['index']) ? intval($_POST['index']) : -1;
    $company = trim($_POST['company'] ?? '');
    $number = trim($_POST['number'] ?? '');
    $memo = trim($_POST['memo'] ?? '');

    if ($action === 'insert' && $company !== '' && $number !== '') {
        $cards[] = [
            "company" => $company,
            "number" => $number,
            "memo"   => $memo
        ];
    } elseif ($action === 'update' && $index >= 0 && $index < count($cards)) {
        $cards[$index]["company"] = $company;
        $cards[$index]["number"] = $number;
        $cards[$index]["memo"] = $memo;
    } elseif ($action === 'delete' && $index >= 0 && $index < count($cards)) {
        array_splice($cards, $index, 1);
    }

    file_put_contents($jsonFile, json_encode($cards, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
?> 
 
<div class="container mt-3">
  <div class="card">
    <div class="card-header d-flex justify-content-center align-items-center text-center">
      <h3 class="mb-0"><?= $title_message ?></h3>            
    </div>
    <div class="card-body">
      <form id="addCardForm" method="post" action="accoutlist.php" class="row g-3 mb-3">
        <input type="hidden" name="action" id="action" value="insert">
        <input type="hidden" name="index" id="index" value="-1">
        <div class="d-flex justify-content-center align-items-center text-center">
          <input type="text" name="company" id="company" class="form-control mx-1" placeholder="은행명" autocomplete="off" style="width:150px;">
          <input type="text" name="number" id="number" class="form-control mx-1" placeholder="계좌 번호" autocomplete="off" style="width:200px;">
          <input type="text" name="memo" id="memo" class="form-control mx-1" placeholder="비고" autocomplete="off" style="width:120px;">
          <button type="submit" class="btn btn-primary btn-sm mx-1" id="submitBtn">등록</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered table-hover text-center">
          <thead class="table-secondary">
            <tr>
              <th>순번</th>
              <th>은행명</th>
              <th>계좌 번호</th>
              <th>비고</th>
              <th>수정/삭제</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($cards)): ?>
              <?php foreach ($cards as $i => $card): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($card["company"], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($card["number"], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($card["memo"], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-outline-primary editBtn" data-index="<?= $i ?>">수정</button>
                  <button type="button" class="btn btn-sm btn-outline-danger deleteBtn" data-index="<?= $i ?>">삭제</button>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5">등록된 계좌가 없습니다.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
    // 로딩 오버레이 제거
    var loader = document.getElementById('loadingOverlay');
    if(loader) loader.style.display = 'none';

    $('.editBtn').on('click', function(){
        var row = $(this).closest('tr');
        var index = $(this).data('index');
        var company = row.find('td:eq(1)').text().trim();
        var number = row.find('td:eq(2)').text().trim();
        var memo = row.find('td:eq(3)').text().trim();
        $('#company').val(company);
        $('#number').val(number);
        $('#memo').val(memo);
        $('#index').val(index);
        $('#action').val('update');
        $('#submitBtn').text('수정');
    });

    $('.deleteBtn').on('click', function(){
        var index = $(this).data('index');
        Swal.fire({
            title: '계좌 삭제',
            text: "정말 삭제하시겠습니까?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '삭제',
            cancelButtonText: '취소'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#index').val(index);
                $('#action').val('delete');
                $('#addCardForm').submit();
            }
        });
    });
});
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/common/modal.php'; ?>
</body>
</html>

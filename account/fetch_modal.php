<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
$num = isset($_POST['num']) ? $_POST['num'] : '';

$tablename = 'account';
require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

if ($mode === 'update' && $num) { 
    try {
        $sql = "SELECT * FROM " . $tablename . " WHERE num=?";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, $num, PDO::PARAM_INT);
        $stmh->execute();
        $row = $stmh->fetch(PDO::FETCH_ASSOC);

        $content = $row['content'];  // 저장된 content 값을 가져옴
        include '_row.php';
		// 콤마 제거 후 숫자로 변환
		$amount = floatval(str_replace(',', '', $row['amount']));
    } catch (PDOException $Exception) {
        echo "오류: " . $Exception->getMessage();
        exit;
    }
} else {
    include '_request.php';
    $mode = 'insert';
    $registDate = date('Y-m-d');
    $inoutsep = '지출';
    $amount = 0;
    $content = '';  // 기본값 설정
}

$title_message = ($mode === 'update') ? '금전출납부 수정' : '금전출납부 신규 등록';

// Bankbook options
$bankbookOptions = [];
$jsonFile = $_SERVER['DOCUMENT_ROOT'] . "/account/accoutlist.json";
$accounts = [];
$selectedAccount = null;

if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $accounts = json_decode($jsonContent, true);
    if (is_array($accounts) && !empty($accounts)) {
        // 선택된 계좌 또는 기본 계좌(첫 번째) 설정
        $selectedAccountIndex = isset($_REQUEST['selected_account']) ? intval($_REQUEST['selected_account']) : 0;
        $selectedAccount = $accounts[$selectedAccountIndex] ?? $accounts[0];
        
        // bankbookOptions 배열에 계좌 정보 추가
        foreach ($accounts as $account) {
            $displayText = $account['company'] . ' ' . $account['number'];
            if (!empty($account['memo'])) {
                $displayText .= ' (' . $account['memo'] . ')';
            }
            $bankbookOptions[] = $displayText;
        }
    }
}

// 수입/지출 계정 정보 가져오기
include 'fetch_options.php';

if($inoutsep == '수입')	
	$options = $incomeOptions;
  else
	  $options = $expenseOptions;
  
// 선택된 항목의 세부항목 가져오기
$selectedKey = $content ?? null; // URL의 'key' 매개변수로 전달
$details = null;

if ($selectedKey) {
	// 수입에서 검색
	if (isset($jsonData['수입'][$selectedKey])) {
		$details = $jsonData['수입'][$selectedKey]['하위계정'];
	}
	// 지출에서 검색
	if (isset($jsonData['지출'][$selectedKey])) {
		$details = $jsonData['지출'][$selectedKey]['하위계정'];
	}
}  

// '개인대출'과 '주일기업' 등 키만 추출
$Suboptions = [];
if ($details) {
    foreach ($details as $detail) {
        foreach ($detail as $key => $value) {
            $Suboptions[] = $key;
        }
    }
}

// 항목의 세부항목 수정시 처리하는 구문
if (isset($_POST['action']) && $_POST['action'] === 'getSubOptions') {
    $selectedKey = $_POST['selectedKey'] ?? null;

    $subOptions = [];
    if ($selectedKey) {
        if (isset($incomeOptions[$selectedKey]) && isset($jsonData['수입'][$selectedKey]['하위계정'])) {
            $subOptions = $jsonData['수입'][$selectedKey]['하위계정'];
        } elseif (isset($expenseOptions[$selectedKey]) && isset($jsonData['지출'][$selectedKey]['하위계정'])) {
            $subOptions = $jsonData['지출'][$selectedKey]['하위계정'];
        }
    }

	// $Suboptions 배열을 키를 기준으로 오름차순 정렬
	ksort($Suboptions); 
	
    echo json_encode(['subOptions' => $subOptions], JSON_UNESCAPED_UNICODE);
    exit;
}

// echo '<pre>';
// print_r($Suboptions);
// echo '</pre>';
// echo '<pre>';
// print_r($contentSub);
// echo '</pre>';

//print_r($details[);
// $options = array_merge($incomeOptions, $expenseOptions);

// $options 배열을 키를 기준으로 오름차순 정렬
ksort($options); 

// $Suboptions 배열을 키를 기준으로 오름차순 정렬
ksort($Suboptions); 

?>

<input type="hidden" id="update_log" name="update_log" value="<?=$update_log?>">
<input type="hidden" id="first_writer" name="first_writer" value="<?=$first_writer?>">

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-center">
        <div class="card justify-content-center">
            <div class="card-header text-center">
                <span class="text-center fs-5"><?=$title_message?></span>
            </div>
            <div class="card-body">
                <div class="row justify-content-center text-center">
                    <div class="d-flex align-items-center justify-content-center m-2">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="text-center fs-6 fw-bold" style="width:150px;">등록일자</td>
                                    <td class="text-center" style="width:200px;">
                                        <input type="date" class="form-control fs-6" id="registDate" name="registDate" style="width:130px;" value="<?=$registDate?>">
                                    </td>
                                    <td class="text-center fs-6 fw-bold" style="width:150px;">구분</td>
                                    <td class="text-center" style="width:200px;">
                                        <div>
                                            <input type="radio" class="form-check-input" id="income" name="inoutsep" value="수입" <?= $inoutsep === '수입' ? 'checked' : '' ?>>
                                            <label for="income" class="form-check-label fs-6">수입</label>
                                            &nbsp;&nbsp;
                                            <input type="radio" class="form-check-input" id="expense" name="inoutsep" value="지출" <?= $inoutsep === '지출' ? 'checked' : '' ?>>
                                            <label for="expense" class="form-check-label fs-6">지출</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center fs-6 fw-bold">계좌</td>
                                    <td class="text-center" colspan="3">
                                        <select class="form-control fs-6" id="bankbook" name="bankbook">
                                            <?php 
                                            $currentBankbook = isset($row['bankbook']) ? $row['bankbook'] : '';
                                            foreach ($bankbookOptions as $option): 
                                                $isSelected = ($currentBankbook === $option);
                                            ?>
                                                <option value="<?= htmlspecialchars($option) ?>" <?= $isSelected ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center fs-6 fw-bold" style="width:150px;">항목</td>
                                    <td class="text-center">
                                        <select class="form-control fs-6" id="content" name="content">
                                            <?php foreach ($options as $key => $value): ?>
                                                <option value="<?= htmlspecialchars($key) ?>" <?= $content === $key ? 'selected' : '' ?>><?= htmlspecialchars($key) ?></option>
                                            <?php endforeach; ?>
                                        </select>
										<span class="text-start" id="content_description">
											<?= $options[$content] ?? '' ?>
										</span>
                                    </td>
								<td class="text-center fs-6 fw-bold" style="width:150px;">세부항목</td>
								<td class="text-center">
									<select class="form-control fs-6" id="contentSub" name="contentSub">
										<?php foreach ($Suboptions as $value): // 키를 사용하지 않고 값만 사용 ?>
											<option value="<?= htmlspecialchars($value) ?>" <?= $contentSub === $value ? 'selected' : '' ?>>
												<?= htmlspecialchars($value) ?>
											</option>
										<?php endforeach; ?>
									</select>
								</td>

                                </tr>
                                <tr>
                                    <td class="text-center fs-6 fw-bold">상세 내역</td>
                                    <td class="text-start" colspan="3">
                                        <input type="text" class="form-control fs-6" id="content_detail" name="content_detail" value="<?=$content_detail?>" autocomplete="off">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center fs-6 fw-bold" style="width:150px;">금액</td>
                                    <td class="text-center">
                                        <input type="text" class="form-control fs-6 text-end" id="amount" name="amount" value="<?= isset($amount) ? number_format($amount) : '' ?>" autocomplete="off" onkeyup="inputNumberFormat(this)">
                                    </td>
                                    <td class="text-center fs-6 fw-bold" style="width:150px;">(거래처코드)수금</td>
                                    <td class="text-center">
                                        <input type="text" class="form-control fs-6 text-end" id="secondordnum" name="secondordnum" value="<?= isset($secondordnum) ? $secondordnum : '' ?>"   autocomplete="off"  >
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center fs-6 fw-bold" style="width:150px;">적요</td>
                                    <td class="text-center" colspan="3">
                                        <input type="text" class="form-control fs-6" id="memo" name="memo" value="<?=$memo?>" autocomplete="off">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" id="saveBtn" class="btn btn-dark btn-sm me-3">
                       <i class="bi bi-floppy-fill"></i> 저장
                    </button>
                    <?php if($mode != 'insert') { ?>
                    <button type="button" id="deleteBtn" class="btn btn-danger btn-sm me-3">
                        <i class="bi bi-trash"></i>  삭제 
                    </button>
                    <?php } ?>
                    <button type="button" id="closeBtn" class="btn btn-outline-dark btn-sm me-2">
                        &times; 닫기
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

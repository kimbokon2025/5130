<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
$num = isset($_POST['num']) ? $_POST['num'] : '';

$tablename = 'todos_monthly';
require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

try {
    if ($mode === 'modify' && $num) {
        $sql = "SELECT * FROM " . $tablename . " WHERE num=?";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, $num, PDO::PARAM_INT);
        $stmh->execute();
        $row = $stmh->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $title = isset($row['title']) ? $row['title'] : '';
            $registdate = isset($row['registdate']) ? $row['registdate'] : '';
            $itemsep = isset($row['itemsep']) ? $row['itemsep'] : '';
            $specialday = isset($row['specialday']) ? $row['specialday'] : '';
            $yearlyspecialday = isset($row['yearlyspecialday']) ? $row['yearlyspecialday'] : '';

            // Determine which period type to show based on saved data
            $isYearly = !empty($yearlyspecialday); // 매년 값이 있는지 판단
            if ($isYearly) {
                // Split yearlyspecialday if available
                list($month, $day) = explode('/', $yearlyspecialday);
            } else {
                $month = 1;
                $day = 1;
            }

            $first_writer = isset($row['first_writer']) ? $row['first_writer'] : '';
            $update_log = isset($row['update_log']) ? $row['update_log'] : '';
            $searchtag = isset($row['searchtag']) ? $row['searchtag'] : '';
        } else {
            echo "Record not found.";
            exit;
        }
    } else {
        $title = '';
        $registdate = date('Y-m-d');
        $itemsep = '';
        $specialday = '';
        $month = 1; // Default month for yearlyspecialday
        $day = 1;   // Default day for yearlyspecialday
        $first_writer = $user_name;
        $update_log = '';
        $searchtag = '';
        $isYearly = false; // Default to monthly if no data
    }
} catch (PDOException $Exception) {
    echo "오류: " . $Exception->getMessage();
    exit;
}
?>

<input type="hidden" id="update_log" name="update_log" value="<?= isset($update_log) ? $update_log : '' ?>">   
<input type="hidden" id="num" name="num" value="<?= isset($num) ? $num : '' ?>">   
<input type="hidden" id="searchtag" name="searchtag" value="<?= isset($searchtag) ? $searchtag : '' ?>">   
<input type="hidden" id="user_name" name="user_name" value="<?= isset($user_name) ? $user_name : '' ?>">   
<input type="hidden" id="first_writer" name="first_writer" value="<?= isset($first_writer) ? $first_writer : '' ?>">   

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-center">
        <div class="card justify-content-center">
            <div class="card-header text-center">
                <span class="text-center fs-5"><?= $mode === 'update' ? '회계 월별 할일 수정' : '회계 월별 할일 신규 등록' ?></span>
            </div>
            <div class="card-body">
                <div class="row justify-content-center text-center">
                    <div class="d-flex align-items-center justify-content-center m-2">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="text-center fs-6 fw-bold" style="width:150px;">등록일자</td>
                                    <td class="text-center" style="width:200px;">
                                        <input type="date" class="form-control fs-6" id="registdate" name="registdate" style="width:130px;" value="<?= htmlspecialchars($registdate) ?>">
                                    </td>                                    
                                    <td class="text-center fs-6 fw-bold" style="width:150px;">작성자</td>
                                    <td class="text-center" colspan="1">
                                        <input type="text" class="form-control fs-6" id="first_writer" name="first_writer" value="<?= htmlspecialchars($first_writer) ?>" readonly>
                                    </td>
                                </tr>
								<tr>
									<td class="text-center fs-6 fw-bold" style="width:150px;">주기</td>
									<td class="text-center" colspan="3">
										<div class="d-flex align-items-center justify-content-center">
											<input type="radio" id="yearly" name="period" value="yearly" style="transform: scale(1.5);" <?= $isYearly ? 'checked' : '' ?> onchange="toggleDateFields()"> &nbsp;<span class="fs-6 ms-2 me-2">매년</span>
											<input type="radio" id="monthly" name="period" value="monthly" style="transform: scale(1.5);" <?= !$isYearly ? 'checked' : '' ?> onchange="toggleDateFields()"> &nbsp;<span class="fs-6  ms-2 me-2">매월</span>
										</div>
									</td>
								</tr>
								<tr id="monthlyRow" style="<?= !$isYearly ? '' : 'display:none;' ?>">
									<td class="text-center fs-6 fw-bold" style="width:150px;">매월</td>
									<td class="text-center" colspan="3">
										<div class="d-flex align-items-center justify-content-center">
											<select class="form-control fs-6" style="width:60px;" id="specialday" name="specialday">
												<?php for ($day = 1; $day <= 31; $day++): ?>
													<option value="<?= $day ?>" <?= $specialday == $day ? 'selected' : '' ?>>
														<?= $day ?>
													</option>
												<?php endfor; ?>
											</select>
											&nbsp;<span class="fs-6">일</span>
										</div>
									</td>
								</tr>
								<tr id="yearlyRow" style="<?= $isYearly ? '' : 'display:none;' ?>">
									<td class="text-center fs-6 fw-bold" style="width:150px;">매년</td>
									<td class="text-center" colspan="3">
										<div class="d-flex align-items-center justify-content-center">
											<select class="form-control fs-6" style="width:60px;" id="month" name="month">
												<?php for ($m = 1; $m <= 12; $m++): ?>
													<option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>><?= $m ?></option>
												<?php endfor; ?>
											</select>
											&nbsp;<span class="fs-6">월</span>
											<select class="form-control fs-6 ms-2" style="width:60px;" id="day" name="day">
												<?php for ($d = 1; $d <= 31; $d++): ?>
													<option value="<?= $d ?>" <?= $d == $day ? 'selected' : '' ?>><?= $d ?></option>
												<?php endfor; ?>
											</select>
											&nbsp;<span class="fs-6">일</span>
										</div>
									</td>
								</tr>

								<tr>
                                    <td class="text-center fs-6 fw-bold" style="width:150px;">내용</td>
                                    <td class="text-center" colspan="3" >
                                        <input type="text" class="form-control fs-6" id="title" name="title" value="<?= htmlspecialchars($title) ?>" autocomplete="off">
                                    </td>
                                </tr>                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" id="saveBtn_month" class="btn btn-dark btn-sm me-3">
                        <i class="bi bi-floppy-fill"></i> 저장
                    </button>
                    <?php if ($mode === 'modify') { ?>
                    <button type="button" id="deleteBtn_month" class="btn btn-danger btn-sm me-3">
                        <i class="bi bi-trash"></i>  삭제 
                    </button>
                    <?php } ?>
                    <button type="button" id="closeBtn_month" class="btn btn-outline-dark btn-sm me-2">
                        &times; 닫기
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDateFields() {
    const yearlyRow = document.getElementById('yearlyRow');
    const monthlyRow = document.getElementById('monthlyRow');
    const yearlyRadio = document.getElementById('yearly').checked;

    if (yearlyRadio) {
        yearlyRow.style.display = '';
        monthlyRow.style.display = 'none';
    } else {
        yearlyRow.style.display = 'none';
        monthlyRow.style.display = '';
    }
}
</script>

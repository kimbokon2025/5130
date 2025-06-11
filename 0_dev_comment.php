(경동기업, 주일기업)

난 한국사람이고, 한국어를 제일 잘한다. 위의 두개의 회사의 업무를 처리하는 프로그램을 개발하고 있다.
이제부터 코드를 만들거나 수정하는 일을 할 것이다.
http://5130.co.kr 웹사이트를 개발하고 관리하고 있다.
mysql, php, javascript로 대부분을 만들었다.

오류에 대한 언급을 하면, 그 해당오류에 대한 코드만 보여주면 좋겠다.
이제부터 코드에 관련된 내용을 물어볼 것이니, 최선을 다해 답변해줘.
모든 script에 대한 선언은 이미 되어 있는 상태다.

예를 들어 부트스트랩, 제이쿼리 등 전체 코드에 필요한 CDN 선언은 load_header.php 파일에 선언되어있다.
발주부터 출하, 재고관리, 인사관리 등등 전산시스템을 구축하고 있다.
회계부분도 매출, 출고통계, 로트번호관리 등 여러가지 종합적인 프로그램을 진행중에 있다.
지난 6개월간의 GPT와 협업해서 많은 코드를 만들었다. 하지만, 추가적인 요구사항들이 있어서 계속 수정할 것이다.
이제부터 이 개발과 관련된 코드에 대해 물어볼 것이다.
난 GPT의 모든 정보를 신뢰한다. 고마워. GPT 선생님! 추가적인 질문을 시작한다.
오류에 대한 언급을 하면, 그 해당오류에 대한 코드만 보여주면 좋겠다.
이제부터 코드에 관련된 내용을 물어볼 것이니, 최선을 다해 답변해줘.

이제부터 내가 질문하는 것에는 전체코드 생성이라는 말이 없으면 부분적으로 내가 요청한 부분만 대답해줘. 알겠지?

08/01 질문내용
위의 파일은 statistics.php이다. 이 코드에는 스크린, 스라트의 제조통계를 화면에 보여주는 것인데,
내가 만들고 싶은 것은 두개의 합산된 것도 유지하면서,
col-sm-4을 활용해서 첫번째 col-sm-6에는 스크린의 차트가 보이고
옆의 col-sm-4에는 스라트
그리고 마지막 col-sm-4에는 두개의 합친 지금의 코드를 보여주는 코드로 수정하고자 한다.
가독성을 높이고, 경영자에게 더 좋은 정보를 제공하기 위해서이다.
아래의 코드로 충분히 이렇게 수정가능한 것 같다. 코드를 만들고 오류가 없지는 체크까지 부탁한다.

chandj의 DB 내용중 output 테이블은 아래의 형태를 갖고 있다.

num: 고유 번호
con_num: 공사 번호
is_deleted: 삭제 여부
outdate: 출고일
indate: 접수일
outworkplace: 출고 작업장
orderman: 발주자
outputplace: 수신처 주소
receiver: 수신자
phone: 연락처
comment: 비고
file_name_0 ~ file_name_4: 파일명
file_copied_0 ~ file_copied_4: 파일 경로
root: 회사 구분
steel: 절곡 발주 여부
motor: 모터 발주 여부
delivery: 배송 방식
regist_state: 등록 상태
bend_state: 절곡 상태
motor_state: 모터 상태
searchtag: 검색 태그
update_log: 업데이트 로그
screen: 스크린 정보
screen_state: 스크린 상태
screen_su: 스크린 수량
screen_m2: 스크린 면적 (m²)
screenlist: 스크린 목록
slatlist: 슬랫 목록
slat: 슬랫 정보
slat_state: 슬랫 상태
slat_su: 슬랫 수량
slat_m2: 슬랫 면적 (m²)
updatecomment: 수정 사항 기록

이 테이블의 구조를 이해할 수 있겠니? 각 컬럼의 역할도 마찬가지로 기억해줘.
이 모든 내용을 output 테이블이라고 칭하고 싶다.

fetch_deadlineDate.php 내용은 아래와 같다.

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

$month = $_POST['month'];
$year = $_POST['year'];

$data_order = array();

// 출고일 outputdate 기준
try {
    $stmh = $pdo->query("SELECT pjname, deadlineDate, secondord, deliverymethod, num, outputDate, status, hallDoorList, carDoorList, carWallList, etcList  
                         FROM " . $DB . ".order 
                         WHERE is_deleted IS NULL 
                         AND MONTH(deadlineDate) = $month 
                         AND YEAR(deadlineDate) = $year");

    while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
        array_push($data_order, $row);
    }

    $data_order = array(
        "data_order" => $data_order,
    );

    echo(json_encode($data_order, JSON_UNESCAPED_UNICODE));

} catch (PDOException $Exception) {
    print "오류: ".$Exception->getMessage();
}
?>

fetch_outputDate.php 내용은 아래와 같다.

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

$month = $_POST['month'];
$year = $_POST['year'];

$data_order = array();

// 출고일 deadlineDate 기준
try {
    $stmh = $pdo->query("SELECT pjname, deadlineDate, secondord, deliverymethod, num, outputDate, status, hallDoorList, carDoorList, carWallList, etcList  
                         FROM " . $DB . ".order 
                         WHERE is_deleted IS NULL 
                         AND MONTH(deadlineDate) = $month 
                         AND YEAR(deadlineDate) = $year");

    while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
        array_push($data_order, $row);
    }

    $data_order = array(
        "data_order" => $data_order,
    );

    echo(json_encode($data_order, JSON_UNESCAPED_UNICODE));

} catch (PDOException $Exception) {
    print "오류: ".$Exception->getMessage();
}
?>

위의 코드에는 공통사항이 많다. 내가 언급한 output 테이블의 요소들로 바꾸고 두개의 파일을 하나의 파일로 만들고 전달하는 인자에 따라 처리하면 될 것 같다.
fetch_date.php 파일로 통합하고 output테이블의 컬럼요소에 맞게 수정해줘.

아래의 파일은 month_schedule.php 파일인데, 한산엘테크 웹사이트에서 가져온 코드이다.
output 테이블에 맞게 모든 것을 수정해야 하는데,
방금 만든 fetch_date는 여기서 두개의 파일호출에 적용하면 된다.

스크린의 면적을 누적하고, 스라트의 면적을 누적하는 코드가 있어야 한다. 이미 정의는 되어있다.

################################## 주일기업
(주일기업)
난 한국사람이고, 한국어를 제일 잘한다. 위의 두개의 회사의 업무를 처리하는 프로그램을 개발하고 있다.
이제부터 코드를 만들거나 수정하는 일을 할 것이다.
http://5130.co.kr 웹사이트를 개발하고 관리하고 있다.
mysql, php, javascript로 대부분을 만들었다.

오류에 대한 언급을 하면, 그 해당오류에 대한 코드만 보여주면 좋겠다.
이제부터 코드에 관련된 내용을 물어볼 것이니, 최선을 다해 답변해줘.
모든 script에 대한 선언은 이미 되어 있는 상태다.

예를 들어 부트스트랩, 제이쿼리 등 전체 코드에 필요한 CDN 선언은 load_header.php 파일에 선언되어있다.
발주부터 출하, 재고관리, 인사관리 등등 전산시스템을 구축하고 있다.
회계부분도 매출, 출고통계, 로트번호관리 등 여러가지 종합적인 프로그램을 진행중에 있다.
지난 6개월간의 GPT와 협업해서 많은 코드를 만들었다. 하지만, 추가적인 요구사항들이 있어서 계속 수정할 것이다.
이제부터 이 개발과 관련된 코드에 대해 물어볼 것이다.
난 GPT의 모든 정보를 신뢰한다. 고마워. GPT 선생님! 추가적인 질문을 시작한다.
오류에 대한 언급을 하면, 그 해당오류에 대한 코드만 보여주면 좋겠다.
이제부터 코드에 관련된 내용을 물어볼 것이니, 최선을 다해 답변해줘.

이제부터 내가 질문하는 것에는 전체코드 생성이라는 말이 없으면 부분적으로 내가 요청한 부분만 대답해줘. 알겠지?

공사수주 리스트의 자료를 수정하려고 한다.

work 테이블의 컬럼은 아래와 같이 구성되어있다.

기본 정보:

num: 기본 키로, 자동 증가하는 정수값입니다.
is_deleted: 삭제 여부를 나타내는 플래그입니다.
work_state: 작업의 상태를 나타내는 문자열입니다.
프로젝트 및 고객 정보:

id, name, nick: 고객 또는 담당자에 대한 식별자 및 이름 정보입니다.
subject: 프로젝트나 작업의 제목입니다.
content, condate1, condate2: 프로젝트나 작업의 상세 내용 및 날짜 정보입니다.
regist_day: 등록 날짜를 기록합니다.
파일 및 문서 관련 정보:

file_name_0 ~ file_name_4: 관련된 파일의 이름을 저장합니다.
file_copied_0 ~ file_copied_4: 파일 복사본의 이름을 저장합니다.
금전 및 청구 관련 정보:

estimate1 ~ estimate4: 견적 관련 정보입니다.
bill1 ~ bill6: 청구서 관련 정보입니다.
deposit1 ~ deposit6: 입금 관련 정보입니다.
receivable: 미수금 관련 정보입니다.
claimamount1 ~ claimamount7, claimbalance1 ~ claimbalance7: 청구금액 및 잔액 정보입니다.
작업 진행 관련 정보:

worklist: 작업 리스트를 저장합니다.
workday, endworkday: 작업 시작일과 종료일을 나타냅니다.
worker, cablestaff, asman: 작업자, 케이블 스태프, A/S 담당자 정보를 포함합니다.
A/S 관련 정보:

asday, asendday, asproday: A/S 관련 날짜 정보입니다.
aslist, asresult, ashistory: A/S 관련 작업 리스트, 결과, 이력 정보를 저장합니다.
청구 및 클레임 정보:

claimperson, claimtel: 클레임을 제기한 사람과 그 연락처입니다.
claimdate1 ~ claimdate6: 클레임 발생일을 기록합니다.
claimfix1 ~ claimfix7: 클레임 수정 관련 정보입니다.
기타 정보:

comment, searchtag, update_log: 코멘트, 검색 태그, 업데이트 로그와 같은 부가 정보를 기록합니다.


## 공급가액,세액 자동만들기 프롬프트
위의 코드에서 4개의 estimate 관련 내용을 한행에 td요소를 8개로 나눠서 날짜부터 금액을 표현하는 것까지 표현하고,
파일첨부는 colspan="1", colspan="7"로 구분해서 사용하자.

그리고 위의 요소는 페이지를 오픈할때,  콤마를 제거하고, 숫자 3자리마다 콤마를 찍어주는 로직을 넣어준다.

그리고, input이 발생할때 위의 요소는 세액은 readonly로 놓고, 공급가액을 넣으면 세액과 금액을 자동으로 계산하고, 반대로 금액을 넣으면 역으로 공급가액과 세액을 계산하는 동적 페이지로 구조를 자바스크립트로 만든다.

$bill6_vat = isset($row['bill6_vat']) ? $row['bill6_vat'] : '';
위의 형식으로 만들어줘.

$bill6_vat = isset($_REQUEST['bill6_vat']) ? $_REQUEST['bill6_vat'] : '';
위의 형식으로 만들어줘.

1. 위의 코드를 $bill_issueDate1, $bill_issueDate2 ...  이런식으로 표현 
2. 위의 코드를 bill_issueDate1, bill_issueDate2 ...  이런식으로 표현 
3. 위의 코드를 bill_issueDate1=?, bill_issueDate2=? ...  이런식으로 표현 

위의 3개를 만족하는 모양 형성해줘.


#컬럼제작
만든 컬럼을 적용해서 하는데, 혹시 등록일자는 기본으로 있어야 할 것 같아. 등록일자(registedate) , is_deleted, updatelog, searchtag는 항상 기본으로 들어가야 한다. 모든 table에 있어야 한다. 

이에 따라 테이블에 4개의 컬럼을 추가하는 slq문장도 필요한다.

위의 코드에서 tbody의 tr요소를 4개 만들건데,
name은 위의 tr요소 행 숫자만큼 col1부터 col13까지 [] 형식으로 만들어줘.

아니다. 현재 php와 mysql의 버전이 json 타입으로 저장할수 없기에 text로 저장하는 대신 json으로 encode, decode해서 사용한다.


위의 코드는 수정되어야 한다. 일단 col18개까지 늘어야 한다.
엑셀은 D열부터 col1에 해당된다. 엑셀수식을 d5는 col1과 같은 것이다. 이렇게 해줘.
1) col7은 col3 * col4 으로 계산한다.
2) col8은 col5*col6*col7 로 계산한다.
3) col10 = col9* 1.2
4) col11 = col8 * col10
5) col12 는 엑셀수식 =ROUND(N5,-3) 이것을 이용한다. N5는 col11이다.
6) col14 는 엑셀수식 =ROUND(J5*P5,-3)
7) col15 , 엑셀수식 =O5+Q5
8) col16 = 엑셀수식 =R5/J5
9) col17 = 엑셀수식 =ROUND(S5,-3)
10) col18 = 엑셀수식 =R5/K5

이렇게 수식을 적용해줘.

위의 코드는 수정되어야 한다. 일단 col20개까지 늘어야 한다.
엑셀은 D열부터 col1에 해당된다. 엑셀수식을 d5는 col1과 같은 것이다. 이렇게 해줘.
1) col6은 =(col7*col8*col9*col9)/1000
2) col9은 col5/2
3) col12 = col6 * col11
4) col11 = col8 * col10
5) col13는 col2 * col3 * col12
6) col15는 col13 * col14
7) col15은 엑셀수식 =O5+Q5
8) col18은  =(col15/col2/col3)+col7
9) col19= 엑셀수식 math.round(col18*1.2*1000)/1000
10) col20=  엑셀수식 math.round(col18/3*1000)/1000

이렇게 수식을 적용해줘.



								<tr>
									<th class="align-middle">+ - Copy</th>
									<th class="align-middle">품목</th>							
									<th class="align-middle">두께(T)</th>							
									<th class="align-middle">폭</th>							
									<th class="align-middle">길이(M)</th>	
									<th class="align-middle">가공비</th>							
									<th class="align-middle">입고가</th>											
									<th class="align-middle">원가합계</th>
									<th class="align-middle bg-danger text-white">판매가 15%</th>																		
								</tr>

위의 price_screenplate의 테이블을 아래의 th요소에 맞게 만들어야 한다.


function addRow(tableBody, rowData) {
    var newRow = $('<tr>');

    // + / - 버튼 추가
    newRow.append('<td class="text-center" style="width:80px;">' +
        '<div class="d-flex justify-content-center mt-1">' + 
        '<button type="button" class="btn btn-primary btn-sm viewNoBtn add-row me-1" data-table="' + tableBody.closest('table').attr('id') + '">+</button>' +
        '<button type="button" class="btn btn-danger btn-sm viewNoBtn remove-row ms-2 me-2">-</button>' +
        '<button type="button" class="btn btn-success btn-sm viewNoBtn copy-row"><i class="bi bi-copy"></i></button>' +
        '</div></td>');    
    
    // col1부터 col8까지 채우기
    for (let i = 1; i <= 8; i++) {
        let colValue = rowData['col' + i] || '';  // 값이 없으면 빈 문자열 사용

        // 첫 번째 열 제외, 모든 열에 텍스트 박스를 추가
        newRow.append('<td><input type="text" name="col' + i + '[]" value="' + colValue + '" class="form-control text-center number-format col' + i + '" autocomplete="off"></td>');
    }

    // 새 행을 테이블에 추가
    tableBody.append(newRow);

    // 숫자 필드에 3자리마다 콤마 추가 (소수점 입력 가능)
    newRow.find('.number-format').on('input change', function() {
        let value = $(this).val().replace(/,/g, '');  // 기존의 콤마를 제거

        // 소수점 포함 숫자 검사
        if (!isNaN(value) && value !== '') {
            // 정수와 소수 부분 분리
            let parts = value.split('.');
            parts[0] = Number(parts[0]).toLocaleString('en');  // 3자리마다 콤마 추가
            
            // 소수점이 있는 경우
            let formattedValue = parts.join('.');
            
            $(this).val(formattedValue);
        }

        // 행 계산 함수 호출
        calculateRow(newRow);
    });

    // 처음 로드될 때도 자동 계산 적용
    calculateRow(newRow);
}

function calculateRow(row) {
    // 소수점 자릿수와 콤마를 포함한 숫자 형식 변환 함수
    function formatNumber(value, decimalPlaces) {
        // value를 소수점 자릿수에 맞게 고정
        let fixedValue = parseFloat(value).toFixed(decimalPlaces);

        // 정수 부분과 소수 부분으로 분리
        let [integerPart, decimalPart] = fixedValue.split('.');

        // 정수 부분에만 콤마 추가
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        // 소수점 이하가 존재하고 0보다 큰 경우, 정수 부분과 결합하여 반환
        if (decimalPart && parseInt(decimalPart) > 0) {
            return `${integerPart}.${decimalPart}`;
        } else {
            return integerPart;  // 소수점 이하가 없거나 0인 경우 정수만 반환
        }
    }

    // col1부터 col8까지의 값을 가져오기
    const col6 = parseFloat(row.find('.col6').val().replace(/,/g, '')) || 0;
    const col7 = parseFloat(row.find('.col7').val().replace(/,/g, '')) || 0;
    const col8 = parseFloat(row.find('.col8').val().replace(/,/g, '')) || 0;

    // 판매가 15% (col9) 계산: col8에 1.15를 곱한 값
    const col9 = col8 * 1.15;
    if (!isNaN(col9)) {
        row.find('.col9').val(formatNumber(col9, 0));
    }

    // 콤마 및 소수점 표시 설정
    if (!isNaN(col6)) {
        row.find('.col6').val(formatNumber(col6, 2));
    }
    if (!isNaN(col7)) {
        row.find('.col7').val(formatNumber(col7, 2));
    }
    if (!isNaN(col8)) {
        row.find('.col8').val(formatNumber(col8, 0));
    }
}


위의 코드를 col1부터 col8까지 만들어야하는데,

숫자 3자리마다 콤마 형태로 나오게 해줘.

자동계산은 원가합계에 해당되는 col7과, col8이 해당되고 이 col7,col8은 수식이 적용되어야 한다.
col7은 = col5 + col6
col8은 =ROUND(col7*1.2,-3)




#연기차단재 관련 생성 프롬프트
<tr>
<th class="align-middle">+ - Copy</th>
<th class="align-middle">품목</th>							
<th class="align-middle">용도</th>							
<th class="align-middle">단위(롤)</th>							
<th class="align-middle">길이(mm)</th>							
<th class="align-middle">폭(mm)</th>							
<th class="align-middle">용차</th>							
<th class="align-middle">가공비</th>	
<th class="align-middle">입고가</th>											
<th class="align-middle">원가합계</th>
<th class="align-middle bg-danger text-white">판매가 15%</th>																		
<th class="align-middle">1롤/50M</th>
</tr>

위의 price_smokeban의 테이블을 아래의 th요소에 맞게 만들어야 한다.
(위의 코드는 참고용이다. 11개의 컬럼을 만들어야 하니까.)
위의 코드를 col1부터 col11까지 만들어야하는데,
숫자 3자리마다 콤마 형태로 나오게 해줘.
자동계산은 원가합계에 해당되는 col9과, col10, col11이 해당되고 이는 수식이 적용되어야 한다.
col9은 = col6/(10*50) + col7+ col8
col10은 =ROUND(col9 * 1.15,-3)
col11은 ==ROUND((col10*50)/(1200/50),-4)


function addRow(tableBody, rowData) {
    var newRow = $('<tr>');

    // + / - 버튼 추가
    newRow.append('<td class="text-center" style="width:80px;">' +
        '<div class="d-flex justify-content-center mt-1">' + 
        '<button type="button" class="btn btn-primary btn-sm viewNoBtn add-row me-1" data-table="' + tableBody.closest('table').attr('id') + '">+</button>' +
        '<button type="button" class="btn btn-danger btn-sm viewNoBtn remove-row ms-2 me-2">-</button>' +
        '<button type="button" class="btn btn-success btn-sm viewNoBtn copy-row"><i class="bi bi-copy"></i></button>' +
        '</div></td>');    
    
    // col1부터 col8까지 채우기
    for (let i = 1; i <= 8; i++) {
        let colValue = rowData['col' + i] || '';  // 값이 없으면 빈 문자열 사용

        // 첫 번째 열 제외, 모든 열에 텍스트 박스를 추가
        newRow.append('<td><input type="text" name="col' + i + '[]" value="' + colValue + '" class="form-control text-center number-format col' + i + '" autocomplete="off"></td>');
    }

    // 새 행을 테이블에 추가
    tableBody.append(newRow);

    // 숫자 필드에 3자리마다 콤마 추가 (소수점 입력 가능)
    newRow.find('.number-format').on('input change', function() {
        let value = $(this).val().replace(/,/g, '');  // 기존의 콤마를 제거

        // 소수점 포함 숫자 검사
        if (!isNaN(value) && value !== '') {
            // 정수와 소수 부분 분리
            let parts = value.split('.');
            parts[0] = Number(parts[0]).toLocaleString('en');  // 3자리마다 콤마 추가
            
            // 소수점이 있는 경우
            let formattedValue = parts.join('.');
            
            $(this).val(formattedValue);
        }

        // 행 계산 함수 호출
        calculateRow(newRow);
    });

    // 처음 로드될 때도 자동 계산 적용
    calculateRow(newRow);
}

function calculateRow(row) {
    // 소수점 자릿수와 콤마를 포함한 숫자 형식 변환 함수
    function formatNumber(value, decimalPlaces) {
        // value를 소수점 자릿수에 맞게 고정
        let fixedValue = parseFloat(value).toFixed(decimalPlaces);

        // 정수 부분과 소수 부분으로 분리
        let [integerPart, decimalPart] = fixedValue.split('.');

        // 정수 부분에만 콤마 추가
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        // 소수점 이하가 존재하고 0보다 큰 경우, 정수 부분과 결합하여 반환
        if (decimalPart && parseInt(decimalPart) > 0) {
            return `${integerPart}.${decimalPart}`;
        } else {
            return integerPart;  // 소수점 이하가 없거나 0인 경우 정수만 반환
        }
    }

    // col1부터 col8까지의 값을 가져오기
    const col6 = parseFloat(row.find('.col6').val().replace(/,/g, '')) || 0;
    const col7 = parseFloat(row.find('.col7').val().replace(/,/g, '')) || 0;
    const col8 = parseFloat(row.find('.col8').val().replace(/,/g, '')) || 0;

    // 판매가 15% (col9) 계산: col8에 1.15를 곱한 값
    const col9 = col8 * 1.15;
    if (!isNaN(col9)) {
        row.find('.col9').val(formatNumber(col9, 0));
    }

    // 콤마 및 소수점 표시 설정
    if (!isNaN(col6)) {
        row.find('.col6').val(formatNumber(col6, 2));
    }
    if (!isNaN(col7)) {
        row.find('.col7').val(formatNumber(col7, 2));
    }
    if (!isNaN(col8)) {
        row.find('.col8').val(formatNumber(col8, 0));
    }
}



######## #개발자 컬럼보기 ########
위의 코드에서 $user_id=='pro'인 경우는 th행을 하나 더 만들건데,
일련번호를 부여해서 개발할때 보려고 한다.
th 2번째 열부터 1로 시작해서 66열까지 1,2,.... 66번까지 col과 비교하면서 개발을 하려고 개발자 아이디인 'pro'만 보이도록 코드를 수정해줘.

######단가 가져오기 fetch_price ##### 제작 프롬프트
단가를 가져오는 코드들을 fetch_price.php를 호출해서 가져오는 방식이면 좋겠다.
이를 호출할때 테이블명과 값을 전달하면 결과를 리턴하는 방식이면 좋겠다.
그러면 위의 9가지 단가들에 대해서 하나의 모듈을 사용하면 효과적일 것 같다.

물론 조건은 파라미터는 하나에서 여러개가 될 수 있다.


## json 저장 안됨 ##
$sql = "SELECT itemList FROM {$DB}.$tablename WHERE JSON_UNQUOTE(JSON_EXTRACT(itemList, '$.code')) IN ($placeholders) AND (is_deleted IS NULL OR is_deleted = 0)";

위의 코드는 잘못되었다. 
php버전과 mysql에서 json을 지원하지 않는 버전을 사용한다.
text로 저장하고, encode, decode로 작업한다.


// 모터 부분
if(True) {
$data = [];
$counter = 0;
	foreach ($decodedEstimateList as $item) {
		if (isset($item['col5']) && !empty($item['col5'])) {
			$counter++;		
			// 각 col 값을 배열에 추가합니다.
			$row = [];
			$row['col1'] = '2.2 <br> 모터';
			$row['col2'] = $item['col3'] ?? '';    // 부호 
			$row['col3'] = $item['col5'] ?? '';   // 실리카
			$row['col4'] = $item['col7'] ?? '';   // SUS
			$row['col5'] = $item['col6'] ;        // 벽마감표시
			$row['col6'] = $item['col8'];       // 오픈 가로
			$row['col7'] = $item['col9'];       // 오픈 세로
			$row['col8'] = $item['col10'];       // 제작 가로
			$row['col9'] = $item['col11'];       // 제작 세로
			
			$row['col10'] = $item['col31'];       // 케이스 500*380

			// $data 배열에 행을 추가합니다.
			$data[] = $row;
		}
	}

모터부분의 내용의 누계를 내려고 한다.


## 모터 150 부터 1000까지 개수 파악하는 프롬프트 ##

전체 col1~col12까지  배열은 필요없고 sum의 개념만 있으면 된다.
$item['col13']은 중량을 나타내는데
이 값에 따라 col1~col7까지 누적해야 한다.
col1의 값은 아래의 엑셀공식이다.
E8은  $item['col4']의 문자열앞에 두문자'KS'이면 '스크린'으로 인식
m8은 중량을 의미하는  $item['col13'];
엑셀공식
=IF(AND(E8="스크린",BK8=4,M8<=150),1,IF(AND(E8="스크린",BK8=5,M8<=123),1,IF(AND(E8="스크린",BK8=6,M8<=104),1,0)))

col2의 값은 아래의 엑셀공식이다.
E8은  $item['col4']의 문자열앞에 두문자'KS'이면 '스크린'으로 인식
m8은 중량을 의미하는  $item['col13'];
bk8은  $item['col53'];
엑셀공식
=IF(AND(E8="스크린",BK8=4,M8>150,M8<=300),1,IF(AND(E8="스크린",BK8=5,M8>123,M8<=246),1,IF(AND(E8="스크린",BK8=6,M8>104,M8<=208),1,IF(AND(E8="철재",BK8=4,M8<=300),1,IF(AND(E8="철재",BK8=5,M8<=246),1,IF(AND(E8="철재",BK8=6,M8<=208),1,0))))))

col3의 값은 아래의 엑셀공식이다.
E8은  $item['col4']의 문자열앞에 두문자'KS'이면 '스크린'으로 인식
m8은 중량을 의미하는  $item['col13'];
bk8은  $item['col53'];
엑셀공식
=IF(AND(E8="스크린",BK8=4,M8>300,M8<=400),1,IF(AND(E8="스크린",BK8=5,M8>246,M8<=327),1,IF(AND(E8="스크린",BK8=6,M8>208,M8<=300),1,IF(AND(E8="철재",BK8=4,M8>300,M8<=400),1,IF(AND(E8="철재",BK8=5,M8>246,M8<=327),1,IF(AND(E8="철재",BK8=6,M8>208,M8<=277),1,0))))))



col4의 값은 아래의 엑셀공식이다.
E8은  $item['col4']의 문자열앞에 두문자'KS'이면 '스크린'으로 인식
m8은 중량을 의미하는  $item['col13'];
bk8은  $item['col53'];
엑셀공식
=IF(AND(E8="스크린",BK8=5,M8>327,M8<=500),1,IF(AND(E8="스크린",BK8=6,M8>300,M8<=424),1,IF(AND(E8="철재",BK8=5,M8>400,M8<=500),1,IF(AND(E8="철재",BK8=5,M8>327,M8<=500),1,IF(AND(E8="철재",BK8=6,M8>277,M8<=424),1,IF(AND(E8="철재",BK8=8,M8<=324),1,0))))))

col5의 값은 아래의 엑셀공식이다.
E8은  $item['col4']의 문자열앞에 두문자'KS'이면 '스크린'으로 인식
m8은 중량을 의미하는  $item['col13'];
bk8은  $item['col53'];
엑셀공식
=IF(AND(BK8=5,M8>500,M8<=600),1,IF(AND(BK8=6,M8>424,M8<=508),1,IF(AND(BK8=8,M8>324,M8<=388),1,0)))
	
	
col6의 값은 아래의 엑셀공식이다.
E8은  $item['col4']의 문자열앞에 두문자'KS'이면 '스크린'으로 인식
m8은 중량을 의미하는  $item['col13'];
bk8은  $item['col53'];
엑셀공식
=IF(AND(E8="철재",BK8=6,M8>600,M8<=800),1,IF(AND(E8="철재",BK8=6,M8>508,M8<=800),1,IF(AND(E8="철재",BK8=8,M8>388,M8<=611),1,0)))

col7의 값은 아래의 엑셀공식이다.
E8은  $item['col4']의 문자열앞에 두문자'KS'이면 '스크린'으로 인식
m8은 중량을 의미하는  $item['col13'];
bk8은  $item['col53'];
엑셀공식
=IF(AND(E8="철재",BK8=6,M8>800,M8<=1000),1,IF(AND(E8="철재",BK8=8,M8>611,M8<=1000),1,0))
	


col8의 값은 아래이다.
$item['col15']의 합
col9의 값은 값은 아래이다.
$item['col16']의 합
col10의 값은 값은 아래이다.
$item['col17']의 합	
col11의 값은 값은 아래이다.
$item['col14']의 합	
col12의 값은 값은 아래이다.
col11 * 4
	
	
## 스크린 발주서 절곡 부분 ##

// 절곡 부분
if(True) {
$data = [];

foreach ($decodedEstimateList as $item) {
  
}

	echo '<div class="d-flex align-items-center justify-content-center m-1">';
	echo '<table class="table" style="border-collapse: collapse;">';
	echo '<thead>';
	echo '<tr>';
	echo '<th rowspan="9" class="text-center"> 2.3 <br> 절곡 </th>';
	echo '<th colspan="2" class="text-center"> (1) 가이드레일 <br> (EGI 1.6T/ SUS 1.2T) </th>';
	echo '<th colspan="2" class="text-center"> (2) 케이스 <br> (EGI 1.6T) </th>';
	echo '<th colspan="3" class="text-center"> (3) 하단마감재 <br> (SUS 1.2T) </th>';
	echo '<th colspan="2" class="text-center"> (4) 연기차단재 </th>';
	echo '</tr>';
	echo '<tr>';	
	echo '<th class="text-center">사이즈</th>';
	echo '<th class="text-center">수량</th>';
	echo '<th class="text-center">사이즈</th>';
	echo '<th class="text-center">수량</th>';
	echo '<th class="text-center">분류</th>';
	echo '<th class="text-center">3000</th>';
	echo '<th class="text-center">4000</th>';
	echo '<th class="text-center">종류</th>';
	echo '<th class="text-center">수량</th>';
	echo '</tr>';
	echo '<tr>';
	echo '<th class="text-center">' . $col1_sum . '</th>';
	echo '<th class="text-center">' . $col2_sum . '</th>';
	echo '<th class="text-center">' . $col3_sum . '</th>';	
	echo '<th class="text-center">' . $col4_sum . '</th>';	
	echo '<th class="text-center">' . $col5_sum . '</th>';	
	echo '<th class="text-center">' . $col6_sum . '</th>';	
	echo '<th class="text-center">' . $col7_sum . '</th>';	
	echo '<th class="text-center">' . $col8_sum . '</th>';	
	echo '<th class="text-center">' . $col9_sum . '</th>';	
	echo '</tr>';
	echo '</thead>';
	echo '</table>';
	echo '</div>';
}


아래의 5개는 배열은 필요없고 sum의 개념만 있으면 된다.
AC8은 $item['col23']은 셔터의 유효 길이를 의미한다.
이 값에 따라 row3_1 ~ row3_5는 행의 누계를  3_1은 3열, 1행을 의미한다. 총 5개를 만들어야 한다.
row3_1의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AC8<=2438,2,IF(AND(AC8>4300,AC8<=5438),2,0))

row3_2의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AND(AC8>2438, AC8<=3000),2,IF(AND(AC8>4300,AC8<=5438),2,IF(AND(AC8>5438,AC8<=6000),4,IF(AND(AC8>6000,AC8<=7300),2,0))))

row3_3의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AND(AC8>3000, AC8<=3500),2,IF(AND(AC8>6000,AC8<=6500),2,0))

row3_4의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AND(AC8>3500, AC8<=4000),2,IF(AND(AC8>6500,AC8<=7000),2,0))

row3_5의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AND(AC8>4000, AC8<=4300),2,IF(AND(AC8>7000,AC8<=7300),2,0))

	

아래의 8개는 배열은 필요없고 sum의 개념만 있으면 된다.
AQ8은 $item['col32']은 셔터의 유효 길이를 의미한다.
이 값에 따라 row5_1 ~ row5_6는 행의 누계를  5_1은 5열, 1행을 의미한다.
row5_1의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AQ8<=1219,1,IF(AND(AQ8>4150,AQ8<=4219),1,IF(AND(AQ8>4219,AQ8<=4719),1,IF(AND(AQ8>4876,AQ8<=5219),1,IF(AND(AQ8>5219,AQ8<=5369),1,IF(AND(AQ8>9026,AQ8<=9219),1,0))))))

row5_2의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AND(AQ8>1219,AQ8<=2438),1,IF(AND(AQ8>4719,AQ8<=4876),2,IF(AND(AQ8>5369,AQ8<=5938),1,IF(AND(AQ8>6000,AQ8<=6438),1,IF(AND(AQ8>6500,AQ8<=6588),1,IF(AND(AQ8>8300,AQ8<=8376),2,IF(AND(AQ8>8376,AQ8<=8438),1,IF(AND(AQ8>8438,AQ8<=8876),2,IF(AND(AQ8>9000,AQ8<=9026),2,IF(AND(AQ8>9219,AQ8<=9438),1,IF(AND(AQ8>10150,AQ8<=10738),1,0)))))))))))

row5_3의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AND(AQ8>2438,AQ8<=3000),1,IF(AND(AQ8>4150,AQ8<=4219),1,IF(AND(AQ8>5369,AQ8<=5438),1,IF(AND(AQ8>5938,AQ8<=6000),2,IF(AND(AQ8>6438,AQ8<=6500),1,IF(AND(AQ8>7000,AQ8<=7150),1,IF(AND(AQ8>8376,AQ8<=8438),2,IF(AND(AQ8>8876,AQ8<=9000),3,IF(AND(AQ8>9438,AQ8<=10150),2,IF(AND(AQ8>10738,AQ8<=11000),1,0))))))))))

row5_4의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AND(AQ8>3000,AQ8<=3500),1,IF(AND(AQ8>4219,AQ8<=4719),1,IF(AND(AQ8>5438,AQ8<=5938),1,IF(AND(AQ8>6438,AQ8<=6500),1,IF(AND(AQ8>6588,AQ8<=7000),2,IF(AND(AQ8>7150,AQ8<=7650),1,IF(AND(AQ8>8300,AQ8<=8376),1,IF(AND(AQ8>9219,AQ8<=9438),2,IF(AND(AQ8>9438,AQ8<=9500),1,0)))))))))

row5_5의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AND(AQ8>3500,AQ8<=4000),1,IF(AND(AQ8>4876,AQ8<=5219),1,IF(AND(AQ8>6000,AQ8<=6438),1,IF(AND(AQ8>7150,AQ8<=7500),1,IF(AND(AQ8>7650,AQ8<=8000),2,IF(AND(AQ8>8000,AQ8<=8150),1,IF(AND(AQ8>8438,AQ8<=8876),1,IF(AND(AQ8>9026,AQ8<=9219),2,IF(AND(AQ8>9500,AQ8<=10000),1,IF(AND(AQ8>10150,AQ8<=10438),2,IF(AND(AQ8>10738,AQ8<=11000),2,0)))))))))))

row5_6의 값은 아래의 엑셀공식이다.
엑셀공식
=IF(AND(AQ8>4000,AQ8<=4150),1,IF(AND(AQ8>5219,AQ8<=5369),1,IF(AND(AQ8>6500,AQ8<=6588),1,IF(AND(AQ8>7000,AQ8<=7150),1,IF(AND(AQ8>7500,AQ8<=7650),1,IF(AND(AQ8>8000,AQ8<=8150),1,IF(AND(AQ8>8150,AQ8<=8300),2,IF(AND(AQ8>9000,AQ8<=9026),1,IF(AND(AQ8>10000,AQ8<=10150),1,IF(AND(AQ8>10438,AQ8<=10738),2,0))))))))))

row5_7의 값은 $item['col39'] 의 합
row5_8의 값은 $item['col41'] 의 합
	





























work 테이블의 아래의 컬럼이 있다.


						<td class="text-center">1차</td>
						<td><input type="date" id="bill_issueDate1" name="bill_issueDate1" value="<?=$bill_issueDate1?>" class="form-control"/></td> <!-- 추가된 열의 input 요소 -->
						<td><input type="text" id="bill1_supply" name="bill1_supply" value="<?=$bill1_supply?>" class="form-control text-end" placeholder="1차 공급가액" onkeyup="calculateVAT('bill', 1); inputNumberFormat(this);" /></td>
						<td><input type="text" id="bill1_vat" name="bill1_vat" value="<?=$bill1_vat?>" class="form-control text-end" placeholder="1차 부가세"  onkeyup=" inputNumberFormat(this);"  /></td>
						<td><input type="text" id="bill1" name="bill1" value="<?=$bill1?>" class="form-control text-end" placeholder="1차 발행금액" onkeyup="calculateSupply('bill', 1); inputNumberFormat(this);"/></td>
						<td><input type="date" id="depositdate1" name="depositdate1" value="<?=$depositdate1?>" class="form-control" placeholder="1차 입금일자" /></td>
						<td><input type="text" id="deposit1" name="deposit1" value="<?=$deposit1?>" class="form-control text-end" placeholder="1차 입금액" onkeyup="calculateTotals(); inputNumberFormat(this);"/></td>
					</tr>
					<tr>
						<td class="text-center">2차</td>
						<td><input type="date" id="bill_issueDate2" name="bill_issueDate2" value="<?=$bill_issueDate2?>" class="form-control"/></td> <!-- 추가된 열의 input 요소 -->
						<td><input type="text" id="bill2_supply" name="bill2_supply" value="<?=$bill2_supply?>" class="form-control text-end" placeholder="2차 공급가액" onkeyup="calculateVAT('bill', 2); inputNumberFormat(this);"/></td>
						<td><input type="text" id="bill2_vat" name="bill2_vat" value="<?=$bill2_vat?>" class="form-control text-end" placeholder="2차 부가세"  onkeyup=" inputNumberFormat(this);"  /></td>
						<td><input type="text" id="bill2" name="bill2" value="<?=$bill2?>" class="form-control text-end" placeholder="2차 발행금액" onkeyup="calculateSupply('bill', 2); inputNumberFormat(this);"/></td>
						<td><input type="date" id="depositdate2" name="depositdate2" value="<?=$depositdate2?>" class="form-control" placeholder="2차 입금일자" /></td>
						<td><input type="text" id="deposit2" name="deposit2" value="<?=$deposit2?>" class="form-control text-end" placeholder="2차 입금액" onkeyup="calculateTotals(); inputNumberFormat(this);"/></td>
					</tr>
					<tr>
						<td class="text-center">3차</td>
						<td><input type="date" id="bill_issueDate3" name="bill_issueDate3" value="<?=$bill_issueDate3?>" class="form-control"/></td> <!-- 추가된 열의 input 요소 -->
						<td><input type="text" id="bill3_supply" name="bill3_supply" value="<?=$bill3_supply?>" class="form-control text-end" placeholder="3차 공급가액" onkeyup="calculateVAT('bill', 3); inputNumberFormat(this);"/></td>
						<td><input type="text" id="bill3_vat" name="bill3_vat" value="<?=$bill3_vat?>" class="form-control text-end" placeholder="3차 부가세"   onkeyup=" inputNumberFormat(this);"  /></td>
						<td><input type="text" id="bill3" name="bill3" value="<?=$bill3?>" class="form-control text-end" placeholder="3차 발행금액" onkeyup="calculateSupply('bill', 3); inputNumberFormat(this);"/></td>
						<td><input type="date" id="depositdate3" name="depositdate3" value="<?=$depositdate3?>" class="form-control" placeholder="3차 입금일자" /></td>
						<td><input type="text" id="deposit3" name="deposit3" value="<?=$deposit3?>" class="form-control text-end" placeholder="3차 입금액" onkeyup="calculateTotals(); inputNumberFormat(this);"/></td>
					</tr>
					<tr>
						<td class="text-center">4차</td>
						<td><input type="date" id="bill_issueDate4" name="bill_issueDate4" value="<?=$bill_issueDate4?>" class="form-control"/></td> <!-- 추가된 열의 input 요소 -->
						<td><input type="text" id="bill4_supply" name="bill4_supply" value="<?=$bill4_supply?>" class="form-control text-end" placeholder="4차 공급가액" onkeyup="calculateVAT('bill', 4); inputNumberFormat(this);"/></td>
						<td><input type="text" id="bill4_vat" name="bill4_vat" value="<?=$bill4_vat?>" class="form-control text-end" placeholder="4차 부가세"  onkeyup=" inputNumberFormat(this);"  /></td>
						<td><input type="text" id="bill4" name="bill4" value="<?=$bill4?>" class="form-control text-end" placeholder="4차 발행금액" onkeyup="calculateSupply('bill', 4); inputNumberFormat(this);"/></td>
						<td><input type="date" id="depositdate4" name="depositdate4" value="<?=$depositdate4?>" class="form-control" placeholder="4차 입금일자" /></td>
						<td><input type="text" id="deposit4" name="deposit4" value="<?=$deposit4?>" class="form-control text-end" placeholder="4차 입금액" onkeyup="calculateTotals(); inputNumberFormat(this);"/></td>
					</tr>
					<tr>
						<td class="text-center">5차</td>
						<td><input type="date" id="bill_issueDate5" name="bill_issueDate5" value="<?=$bill_issueDate5?>" class="form-control"/></td> <!-- 추가된 열의 input 요소 -->
						<td><input type="text" id="bill5_supply" name="bill5_supply" value="<?=$bill5_supply?>" class="form-control text-end" placeholder="5차 공급가액" onkeyup="calculateVAT('bill', 5); inputNumberFormat(this);"/></td>
						<td><input type="text" id="bill5_vat" name="bill5_vat" value="<?=$bill5_vat?>" class="form-control text-end" placeholder="5차 부가세"  /></td>
						<td><input type="text" id="bill5" name="bill5" value="<?=$bill5?>" class="form-control text-end" placeholder="5차 발행금액" onkeyup="calculateSupply('bill', 5); inputNumberFormat(this);"/></td>
						<td><input type="date" id="depositdate5" name="depositdate5" value="<?=$depositdate5?>" class="form-control" placeholder="5차 입금일자" /></td>
						<td><input type="text" id="deposit5" name="deposit5" value="<?=$deposit5?>" class="form-control text-end" placeholder="5차 입금액" onkeyup="calculateTotals(); inputNumberFormat(this);"/></td>
					</tr>
					<tr>
						<td class="text-center">6차</td>
						<td><input type="date" id="bill_issueDate6" name="bill_issueDate6" value="<?=$bill_issueDate6?>" class="form-control"/></td> <!-- 추가된 열의 input 요소 -->
						<td><input type="text" id="bill6_supply" name="bill6_supply" value="<?=$bill6_supply?>" class="form-control text-end" placeholder="6차 공급가액" onkeyup="calculateVAT('bill', 6); inputNumberFormat(this);"/></td>
						<td><input type="text" id="bill6_vat" name="bill6_vat" value="<?=$bill6_vat?>" class="form-control text-end" placeholder="6차 부가세"  onkeyup=" inputNumberFormat(this);"  /></td>
						<td><input type="text" id="bill6" name="bill6" value="<?=$bill6?>" class="form-control text-end" placeholder="6차 발행금액" onkeyup="calculateSupply('bill', 6); inputNumberFormat(this);"/></td>
						<td><input type="date" id="depositdate6" name="depositdate6" value="<?=$depositdate6?>" class="form-control" placeholder="6차 입금일자" /></td>
						<td><input type="text" id="deposit6" name="deposit6" value="<?=$deposit6?>" class="form-control text-end" placeholder="6차 입금액" onkeyup="calculateTotals(); inputNumberFormat(this);"/></td>
					</tr>


6행의 컬럼이름들이 많다. 이 컬럼을 accountList 컬럼으로 넣으려고 하는데,
json형태로 만든 후 encode, decode를 통해 테이블에는 text형태로 저장할 것이다.
php와 mysql이 json 저장을 지원하지 않는 버전이라서 그렇다.

그러면 아래의 코드에서 json의 encode, decode 코드형태를 엿볼 수 있다.

위의 컬럼의 값들을 읽어서 1행부터 col1,col2,col3,col4,col5,col6까지 생성되는 것이다.
예를 들면,
						<td class="text-center">1차</td>
						<td><input type="date" id="bill_issueDate1" name="bill_issueDate1" value="<?=$bill_issueDate1?>" class="form-control"/></td> <!-- 추가된 열의 input 요소 -->
						<td><input type="text" id="bill1_supply" name="bill1_supply" value="<?=$bill1_supply?>" class="form-control text-end" placeholder="1차 공급가액" onkeyup="calculateVAT('bill', 1); inputNumberFormat(this);" /></td>
						<td><input type="text" id="bill1_vat" name="bill1_vat" value="<?=$bill1_vat?>" class="form-control text-end" placeholder="1차 부가세"  onkeyup=" inputNumberFormat(this);"  /></td>
						<td><input type="text" id="bill1" name="bill1" value="<?=$bill1?>" class="form-control text-end" placeholder="1차 발행금액" onkeyup="calculateSupply('bill', 1); inputNumberFormat(this);"/></td>
						<td><input type="date" id="depositdate1" name="depositdate1" value="<?=$depositdate1?>" class="form-control" placeholder="1차 입금일자" /></td>
						<td><input type="text" id="deposit1" name="deposit1" value="<?=$deposit1?>" class="form-control text-end" placeholder="1차 입금액" onkeyup="calculateTotals(); inputNumberFormat(this);"/></td>
					</tr>

위의 코드는 col1은 bill_issueDate1이 되는 것이다.
위의 데이터는 6행을 반복하면 col1의 데이터는 
[{"col1":"2024-09-01"}.......{"col1":""}]

이렇게 되는 것이겠지?

아래의 코드를 참조하자.
<?php   
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';  
$num = isset($_REQUEST['num']) ? $_REQUEST['num'] : '';

print 'accountList를 json형태의 파일 내용을 강제로 생성하기 위한 화면 ';

include '_request.php';

$tablename = 'work';
require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

// PDO에서 버퍼링된 쿼리를 사용하도록 설정
$pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

// 이 스크립트의 메모리 제한 증가
ini_set('memory_limit', '256M');

$sql = "SELECT * FROM " . $DB . "." . $tablename . " ";

try {
    $stmh = $pdo->prepare($sql);
    $stmh->execute();
    $rows = $stmh->fetchAll(PDO::FETCH_ASSOC);

    $dataByNum = [];

    // 데이터를 num별로 그룹화
    foreach ($rows as $row) {
        $num = $row['num'];
        if (!isset($dataByNum[$num])) {
            $dataByNum[$num] = [
                'accountList' => []
            ];
        }
        $dataByNum[$num]['accountList'] = json_decode($row['accountList'], true);
    }

    $updateData = [];

    // num별로 데이터 처리
    foreach ($dataByNum as $num => $lists) {
        $newOrderlist = [];

        foreach ($lists['accountList'] as $item) {
            $newItem = $item;
            $tempCol14 = isset($item['col14']) ? $item['col14'] : '';
            $tempCol15 = isset($item['col15']) ? $item['col15'] : '';

            $newItem['col15'] = $tempCol14;
            $newItem['col16'] = $tempCol15;

            $newOrderlist[] = $newItem;
        }

        $updateData[$num] = [
            'accountList' => json_encode($newOrderlist, JSON_UNESCAPED_UNICODE)
        ];
    }

    // 데이터베이스 업데이트
    $pdo->beginTransaction();

    $sql = "UPDATE " . $DB . ".{$tablename} SET accountList = ? WHERE num = ?";
    $stmh = $pdo->prepare($sql);

    foreach ($updateData as $num => $data) {
        $stmh->execute([$data['accountList'], $num]);
    }

    $pdo->commit();
} catch (PDOException $Exception) {
    $pdo->rollBack();
    print "오류: " . $Exception->getMessage();
}
?>


work 테이블의 위의 컬럼들을 accountList에 저장하는 코드로 만들어줘. 컬럼을 정리하려고 한다.


## json으로 올바르게 나오지 않는경우#####
## 아래의 코드로 하면 된다.

    // JSON이 배열인지 아닌지 여부를 확인하는 방법
    if (typeof accountList === 'string') {
        try {
            accountList = JSON.parse(accountList);
        } catch (e) {
            console.error('JSON 파싱 오류:', e);
            accountList = [];
        }
    }

    if (Array.isArray(accountList)) {
        console.log('accountList is an array:', accountList);
    } else {
        console.log('accountList is not an array, resetting to empty array');
        accountList = [];
    }

    accountList.forEach(function(rowData, index) {
        addBillRow($('#accountListBody'), rowData);
    });	
	
	
##공급가액 아이콘으로 계산하기 ###
	
function addRow_Bill(tableBody, rowData = {}) {
    var newRow = $('<tr>');

    // 첫 번째 열: 일련번호 (자동 생성)
    newRow.append('<td class="text-center" style="width:80px;">' +
        '<div class="d-flex justify-content-center align-items-center "> <span class="serial-number me-2"></span>' + 
        '<button type="button" class="btn btn-outline-primary btn-sm viewNoBtn add-row me-1" data-table="' + tableBody.closest('table').attr('id') + '">+</button>' +
        '<button type="button" class="btn btn-outline-danger btn-sm viewNoBtn remove-row">-</button>' +
        '</div></td>');						

    // col1 (발행일자) - "0000-00-00"을 빈 문자열로 처리
    var col1Value = (rowData.col1 === "0000-00-00") ? '' : rowData.col1;
    newRow.append('<td class="text-center"><input type="date" name="col1[]" class="form-control text-center" value="' + col1Value + '"></td>');

    // col2 (공급가액)
    newRow.append('<td class="text-center"><input type="text" name="col2[]" class="form-control text-end number-format" placeholder="공급가액" onkeyup="inputNumberFormat(this);" value="' + (rowData.col2 || '') + '"></td>');

    // col3 (부가세)
    newRow.append('<td class="text-center"><input type="text" name="col3[]" class="form-control text-end number-format" placeholder="부가세" onkeyup="inputNumberFormat(this);" value="' + (rowData.col3 || '') + '"></td>');

    // col4 (발행금액)
    newRow.append('<td class="text-center"><input type="text" name="col4[]" class="form-control text-end number-format" placeholder="발행금액" onkeyup="inputNumberFormat(this);" value="' + (rowData.col4 || '') + '"></td>');

    // col5 (입금일자) - "0000-00-00"을 빈 문자열로 처리
    var col5Value = (rowData.col5 === "0000-00-00") ? '' : rowData.col5;
    newRow.append('<td class="text-center"><input type="date" name="col5[]" class="form-control text-center " value="' + col5Value + '"></td>');

    // col6 (입금액)
    newRow.append('<td class="text-center"><input type="text" name="col6[]" class="form-control text-end number-format" placeholder="입금액" onkeyup="inputNumberFormat(this);" value="' + (rowData.col6 || '') + '"></td>');

    tableBody.append(newRow);

    // 일련번호를 업데이트
    updateSerialNumbers(tableBody);
  
    // 숫자 필드에 3자리마다 콤마 추가 (소수점 입력 가능)
    newRow.find('.number-format').on('input change', function() {
        let value = $(this).val().replace(/,/g, '');  // 기존의 콤마를 제거

        // 소수점 포함 숫자 검사
        if (!isNaN(value) && value !== '') {
            // 정수와 소수 부분 분리
            let parts = value.split('.');
            parts[0] = Number(parts[0]).toLocaleString('en');  // 3자리마다 콤마 추가
            
            // 소수점이 있는 경우
            let formattedValue = parts.join('.');
            
            $(this).val(formattedValue);
        }

        //계산 함수 호출
        calculateTotals();
    });

    // 처음 로드될 때도 자동 계산 적용    
	calculateTotals();
}


위의 코드에 3번째 요소와 5번째  td요소인 발행합계의 열에 input요소 옆에 계산 아이콘은 배치해서 누르면 공급가액/부가세/발행합계를 그 행에 대해서 자동계산을 하려고 한다.
공급가액 옆의 아이콘을 누르면 공급가액을 기준으로 부가세와 발행금액이 계산되고, 발행금액을 누르면 역으로 공급가액과 부가세를 계산하는 원리다.

<i class="bi bi-calculator-fill"></i> 이 아이콘을 사용할거야.	

1행은 col1은 condate, col2는 estimate1_supply, col3는 estimate1_vat, col4는 estimate1,
col5는 file_name_0, , col6은 file_copied_0
다소 매치가 안될것 같지만, 내가 제공한 코드를 보면 규칙이있으니 참고바랍니다.
2행은 col1은 condate1, col2는 estimate2_supply, col3는 estimate2_vat, col4는 estimate2,
col5는 file_name_1, col6은 file_copied_1
2행은 col1은 condate2, col2는 estimate3_supply, col3는 estimate3_vat, col4는 estimate3,
col5는 file_name_2, col6은 file_copied_2

이렇게 나와야 한다.


#2. 파일 업로드 시 식별자 전송
saveData 함수에서 파일을 전송할 때, 해당 파일의 row_id도 함께 전송하여 파일이 어느 행에 속하는지 알 수 있도록 합니다.

실제 위와 같이 행을 동적으로 추가하고 그 마지막에 첨부파일을 넣는 방식인데, 여러가지 동적으로 행을 만들고 지우고 추가하고 하는 과정에서 내 마음대로 되지 않는구나.

화면의 동적추가 및 기타 동작은 되지만, 서버에서 처리하는 insert.php 에서 제대로 되지 않는다. 이유는 알것 같지만, 막상 코드로 잘 안된다.

문제는 이것이다.

estimateList에서 col5, col6을 json형태로 가져와서 웹에서 front end에서는 잘 되는데 이것이 file을 서버로 전달하면, insert.php에서 파일의 첨부가 있을때, 이것이 1행에서 추가된 것이면 기존의 col5, col6의 1행에 해당되는 값만 수정이 되어야 하는데,
지금은 만약 4행이 있다면 4행 전부가 그 파일이름으로 수정이 되고, 1행은 서버저장 파일이름도 잘못되서 나온다.

결국은 estimateList의 각 행과 upfile로 전달된 file의 첨부가 몇행에서 이뤄졌는지 정보가 전달되어야 정확할 것 같은데 방법이 있을까?

ChatGPT의 말:

ChatGPT
네, 상황을 이해했습니다. estimateList의 각 행과 파일 업로드가 정확히 매칭되지 않아서 문제가 발생하고 있습니다. 이를 해결하기 위해서는 다음과 같은 전략을 사용해야 합니다:

해결책:
행의 고유 식별자 추가: 각 행이 고유한 식별자를 가지도록 하여 파일 업로드와 정확히 매칭할 수 있도록 합니다. 이 식별자는 행이 생성될 때마다 할당되며, insert.php에서 파일 업로드 처리 시 이 식별자를 사용해 해당 행과 파일을 매칭합니다.

파일 업로드 시 식별자 전송: 파일 업로드가 일어날 때 해당 파일이 어느 행에 속하는지 식별자를 함께 전송합니다. 이를 통해 insert.php에서 해당 행만 파일 정보를 업데이트할 수 있습니다.

1. 행의 고유 식별자 추가
각 행에 고유한 data-id 속성을 추가합니다. 이 속성은 자바스크립트에서 행을 생성할 때 무작위 또는 순차적으로 생성됩니다.


#컬럼등 추가했을때 만들어주는 코드
cableworkStatus, certifiedInspector, certifiedLabelAttachedDate

위의 3개의 컬럼을 추가했다.

_row.php 파일은 아래의 형태이다. 추가해줘.
<?php
$num = isset($row['num']) ? $row['num'] : '';
위는 예시이다. 실제 요청한 컬럼만 생성하면 된다.

_request.php 파일은 아래의 형태이다. 추가해줘.
<?php
$num = isset($_REQUEST['num']) ? $_REQUEST['num'] : '';
위는 예시이다. 실제 요청한 컬럼만 생성하면 된다.

insert.php 파일에 서버에 신규생성 및 수정에 대한 선언부분이다.
아래와 같은 형태로 만들어줘.
1) issued_amount = ?, decided_estimate = ?, .....
2) $subject, $content, ....
3) receivable, chargedperson,  .....

위는 예시이다. 실제 요청한 컬럼만 생성하면 된다.


    foreach ($decodedEstimateList as $item) {
        $col54 = floatval($item['col54']);
        $col55 = floatval($item['col55']);
        $col56 = floatval($item['col56']);
        $col57 = floatval($item['col57']);
        $col58 = floatval($item['col58']);
        $col59 = floatval($item['col59']);
        $col60 = floatval($item['col60']);
엑셀수식 :
E8 = $item['col4'] 의 앞 두자리가 'KS' 이면 '스크린'으로 인식한다.
H8은 $item['col8']  , BK8은 $item['col53'] 

$col61은 =IF(AND(E8="스크린",BK8=6,H8>8250,H8<=10200),1,0)		
$col61은 =IF(AND(E8="스크린",BK8=6,H8>8250,H8<=10200),1,0)	
	

                var fileDiv = $('<div>').attr('id', 'file' + i).css({
                    display: 'inline-block',
                    width: 'calc(20% - 10px)', // 5개를 한 줄에 맞추기 위해 20%의 너비 할당 (5개의 이미지가 한 줄에 배치됨)
                    margin: '5px',
                    textAlign: 'center' // 이미지와 버튼을 중앙 정렬
                });


위의 코드는 5개 있을때는 좋은데, 1개, 2개 이미지가 있을때는 서로 겹처보인다. 1개, 2개~ 5개까지 여백이 확보되게 수정해줘.

## 같은 이름의 첨부파일 등록하지 않기

코드 수정:
다음은 파일을 FormData에 추가하기 전에 중복 여부를 검사하는 코드입니다.

javascript
코드 복사
function uploadFiles(files) {
    console.log('업로드 파일 리스트:', files);

    isUploading = true;  // 업로드 중 상태 설정

    var formData = new FormData($('#board_form')[0]);

    // 현재 폼 데이터에 추가된 파일 이름 목록 추적
    var existingFiles = new Set();
    for (var pair of formData.entries()) {
        if (pair[0] === 'upfile[]') {
            existingFiles.add(pair[1].name);
        }
    }

    // 중복되지 않은 파일만 추가
    for (var i = 0; i < files.length; i++) {
        if (!existingFiles.has(files[i].name)) {
            formData.append('upfile[]', files[i]);
            existingFiles.add(files[i].name);  // 추가된 파일 이름을 목록에 저장
            console.log('파일 추가:', files[i].name);  // 디버그
        } else {
            console.log('중복 파일 무시:', files[i].name);  // 디버그
        }
    }

#토글버튼 추가로직 #
위의 코드에서 table을 보이게 안보이게 하는 토글 버튼을 만들건데, 아래의 코드를 참조해서 html 코드와 자바스크립트를 추가해줘.
쿠키에도 저장하고 다시 불러오는 과정도 포함된다.
<style>

#bendTableToggle {
    cursor: pointer;
}
</style>
<div class="container">  
		<div class="row justify-content-center">
		<div class="card w-50">
			<div class="card-body">
				<div class="d-flex justify-content-center align-items-center" >
					<h5 class="card-title text-center">절곡판</h5>
					<span id="bendTableToggle" class="badge bg-secondary ms-3 me-2">
						<i class="bi bi-chevron-down"></i>
					</span>
				</div>
				<table id="item_bendTable" class="table table-bordered table-hover">
					<thead class="table-primary text-center">
						<tr>
							<th class="align-middle">품목</th>
							<th class="align-middle">규격</th>
							<th class="align-middle">두께(T)</th>
							<th class="align-middle">비중</th>
							<th class="align-middle">㎡ / 단가</th>
						</tr>
					</thead>
					<tbody>
						<!-- Additional Rows Go Here -->
					</tbody>
				</table>
			</div>
		</div>
	</div> <!--row-->

$(document).ready(function() {
    initializePage();    

    // Toggle button for bendTable
    $("#bendTableToggle").on("click", function() {
        var showBendTable = getCookie("showBendTable");
        var bendTable = $("#item_bendTable");
        if (showBendTable === "show") {
            bendTable.css("display", "none");
            setCookie("showBendTable", "hide", 10);
        } else {
            bendTable.css("display", "block");
            setCookie("showBendTable", "show", 10);
        }
    });

    // Check the cookie value on page load and set the table visibility
    var showBendTable = getCookie("showBendTable");
    var bendTable = $("#item_bendTable");
    if (showBendTable === "show") {
        bendTable.css("display", "block");
    } else {
        bendTable.css("display", "none");
    }
});	


##발주서 테이블 생성원리 설명자료 ##

이제부터 생성원리에 대한 설명을 시작한다.
tr 1행은 아래와 같이 구성된다. 12개로 나뉜다.
구성품		길이 (mm)	수량	구성품	길이	수량	구성품	길이	수량	구성품	길이	수량

td 1열은 '하단마감재 <br> 
(60*40)' 
td 2열은 길이 3000 또는 4000으로 구성된 자료를 읽어와서 해당수량을 다음열인 3열에 나타내는 것이다.
길이 3000에 대한 수량 col44
길이 4000에 대한 수량 col45


td 4열은 '하단 <br> 보강엘바 <br>
(60*17)' 
td 5열은 길이 3000 또는 4000으로 구성된 자료를 읽어와서 해당수량을 다음열인 6열에 나타내는 것이다.
길이 3000에 대한 수량 col47
길이 4000에 대한 수량 col48


td 7열은 '하단 <br>
보강평철' 
td 8열은 길이 3000 또는 4000으로 구성된 자료를 읽어와서 해당수량을 다음열인 9열에 나타내는 것이다.
길이 3000에 대한 수량 col50
길이 4000에 대한 수량 col51


td 10열은 '하단 <br> 무게평철 <br> 
[50*12T]' 
td 11열은 길이 2000으로 구성된 자료를 읽어와서 해당수량을 다음열인 12열에 나타내는 것이다.
길이 2000에 대한 수량 col52

위의 내용을 코드로 만들어줘.



<!-- 하단 마감재 및 테이블 출력 -->
<?PHP
// 마감 EGI, SUS 분리하기
foreach ($eList as $item) {
    $prodcode = $item['col4'];
    $GuiderailType = $item['col6'];
    $items1 = ['KSS01'];
    $items2 = ['KWE01','KSE01'];        
    
    // $prodcode가 $items1 배열에 있는 경우
    if (in_array($prodcode, $items1)) {
        $GuidrailFinish = 'SUS 1.2T';
        $GuidrailExtraFinish = 'SUS 1.2T';
    }
    // $prodcode가 $items2 배열에 있는 경우
    elseif (in_array($prodcode, $items2)) {
        $GuidrailFinish = 'EGI 1.15T';
        $GuidrailExtraFinish = 'SUS 1.2T';
    } else {
        $GuidrailFinish = 'EGI 1.15T';
        $GuidrailExtraFinish = 'SUS 1.2T';
    }
}

?>

<div class="row m-1 mt-3">
<?php
// 가이드 레일 계산 및 테이블 출력
if (True) {    
    $row3_data = [
        ['length' => 2438, 'sum' => 0],
        ['length' => 3000, 'sum' => 0],
        ['length' => 3500, 'sum' => 0],
        ['length' => 4000, 'sum' => 0],
        ['length' => 4300, 'sum' => 0]
    ];

    foreach ($eList as $item) {
        $AC8 = floatval($item['col23']); // 셔터의 유효 길이
        $railType = trim($item['col6']); // 벽부형, 측면형, 혼합형 여부 판단

        // 혼합형일 경우 1개씩 계산, 나머지는 2개씩 계산
        for ($i = 0; $i < count($row3_data); $i++) {
            $length = $row3_data[$i]['length'];
            if ($AC8 <= $length) {
                $row3_data[$i]['sum'] += ($railType == '혼합형') ? 1 : 2;
                break;
            }
        }
    }

    // 벽면형과 측면형을 혼합형일 때는 두 개씩, 나머지는 하나씩 출력
    $wall_rows = [];
    $side_rows = [];

    foreach ($row3_data as $row) {
        if ($row['sum'] > 0) {
            if ($railType == '혼합형') {
                $wall_rows[] = [
                    'length' => $row['length'],
                    'sum' => $row['sum']
                ];
                $side_rows[] = [
                    'length' => $row['length'],
                    'sum' => $row['sum']
                ];
            } elseif ($railType == '벽면형(120*70)') {
                $wall_rows[] = [
                    'length' => $row['length'],
                    'sum' => $row['sum']
                ];
            } elseif ($railType == '측면형(120*120)') {
                $side_rows[] = [
                    'length' => $row['length'],
                    'sum' => $row['sum']
                ];
            }
        }
    }

    // 벽면형 테이블 출력 (혼합형일 경우에만)
    if ($railType == '혼합형' || $railType == '벽면형(120*70)') {
        echo '<div class="col-sm-9 d-flex align-items-top justify-content-start">';
        echo '<div class="d-flex align-items-center justify-content-start">';
        echo '<table class="table " style="border-collapse: collapse;">';
        echo '<tbody>';
        echo '<tr>';
        echo '<td rowspan="1" colspan="3" class="text-center align-middle lightgray">1.1 벽면형[120*70]</td>';
        echo '<td rowspan="2" class="text-center yellowblackBold " style="width:250px;">입고 LOT NO.</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td rowspan="7"><img src="../img/guiderail/guiderail_' . $prodCode . '_wall_120x70.jpg" alt="벽면형" width="220"></td>';
        echo '<td class="text-center lightgray">세부품명</td>';
        echo '<td class="text-center lightgray">재질</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td class="text-center text-danger ">①마감재</td>';
        echo '<td class="text-center text-danger ">' . $GuidrailFinish . '</td>';
        echo '<td class="text-center "></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-dark ">②가이드레일</td>';
        echo '<td rowspan="3" class="text-center text-dark ">' . $GuidrailFinish . '</td>';
        echo '<td class="text-center "></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-dark ">③C형</td>';
        echo '<td class="text-center "></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-dark ">④D형</td>';
        echo '<td class="text-center "></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-danger ">⑤별도마감재</td>';
        echo '<td class="text-center text-danger ">' . $GuidrailExtraFinish . '</td>';
        echo '<td class="text-center text-primary fw-bold"> 없음 </td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-dark ">하부BASE</td>';
        echo '<td class="text-center text-dark ">EGI 1.55T</td>';
        echo '<td class="text-center "></td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';

        // 작업량 테이블 추가 (벽면형)
        echo '<div class="col-sm-3 d-flex justify-content-start">';
        echo '<div class="d-flex justify-content-start m-1">';
        echo '<table class="table" style="border-collapse: collapse;">';
        echo '<tbody>';
        echo '<tr>';
        echo '<td colspan="2" class="text-center blueBlackBold"> 작업량 </td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center blueBlackBold w100px">길이/규격</td>';
        echo '<td class="text-center blueBlackBold w100px">수량</td>';
        echo '</tr>';

        $wall_sum = 0;
        foreach ($wall_rows as $row) {
            echo '<tr>';
            echo '<td class="text-center">' . $row['length'] . '</td>';
            echo '<td class="text-center">' . $row['sum'] . '</td>';
            echo '</tr>';
            $wall_sum += $row['sum'];
        }

        echo '<tr>';
        echo '<td class="text-center fw-bold ">하부BASE <br> (130*80)</td>';
        echo '<td class="text-center fw-bold ">' . $wall_sum . '</td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }

    // 측면형 테이블 출력 (혼합형일 경우에만)
    if ($railType == '혼합형' || $railType == '측면형(120*120)') {
        echo '<div class="col-sm-9 d-flex align-items-top justify-content-start">';
        echo '<div class="d-flex align-items-center justify-content-start">';
        echo '<table class="table " style="border-collapse: collapse;">';
        echo '<tbody>';
        echo '<tr>';
        echo '<td rowspan="1" colspan="3" class="text-center align-middle lightgray">1.2 측면형[120*120]</td>';
        echo '<td rowspan="2" class="text-center yellowblackBold " style="width:250px;">입고 LOT NO.</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td rowspan="8"><img src="../img/guiderail/guiderail_' . $prodCode . '_side_120x120.jpg" alt="측면형" width="220"></td>';
        echo '<td class="text-center lightgray">세부품명</td>';
        echo '<td class="text-center lightgray">재질</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td class="text-center text-danger ">①②마감재</td>';
        echo '<td class="text-center text-danger ">' . $GuidrailFinish . '</td>';
        echo '<td class="text-center "></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-dark ">③가이드레일</td>';
        echo '<td rowspan="4" class="text-center text-dark ">' . $GuidrailFinish . '</td>';
        echo '<td class="text-center "></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-dark ">④가이드레일</td>';        
        echo '<td class="text-center "></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-dark ">⑤C형</td>';
        echo '<td class="text-center "></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-dark ">⑥D형</td>';
        echo '<td class="text-center "></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-danger ">⑦⑧별도마감재</td>';
        echo '<td class="text-center text-danger ">' . $GuidrailExtraFinish . '</td>';
        echo '<td class="text-center text-primary fw-bold"> 없음 </td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center text-dark ">하부BASE</td>';
        echo '<td class="text-center text-dark ">EGI 1.55T</td>';
        echo '<td class="text-center "></td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';

        // 작업량 테이블 추가 (측면형)
        echo '<div class="col-sm-3 d-flex justify-content-start">';
        echo '<div class="d-flex justify-content-start m-1">';
        echo '<table class="table" style="border-collapse: collapse;">';
        echo '<tbody>';
        echo '<tr>';
        echo '<td colspan="2" class="text-center blueBlackBold"> 작업량 </td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td class="text-center blueBlackBold w100px">길이/규격</td>';
        echo '<td class="text-center blueBlackBold w100px">수량</td>';
        echo '</tr>';

        $side_sum = 0;
        foreach ($side_rows as $row) {
            echo '<tr>';
            echo '<td class="text-center">' . $row['length'] . '</td>';
            echo '<td class="text-center">' . $row['sum'] . '</td>';
            echo '</tr>';
            $side_sum += $row['sum'];
        }

        echo '<tr>';
        echo '<td class="text-center fw-bold ">하부BASE <br> (130*130)</td>';
        echo '<td class="text-center fw-bold ">' . $side_sum . '</td>';
        echo '</tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }
}
?>
</div>

위의 코드를 하단마감재의 내용으로 전부 수정해줘.

하단마감재 계산방법은 아래와 같다. 즉, 혼합형,벽면형, 측면형 표시는 필요가 없다.

이미지는 ../img/bottombar/bottombar_KSS01.jpg
../img/bottombar/bottombar_KSE01.jpg

이런식으로 이름과 관련이미지가 저장되어있다.
KWE01는 KSE01과 같은 형태다.

하단마감재 산출공식은 아래를 참조한다.


<div class="row justify-content-start m-1 mt-2 ">
3-2. 하단마감재 <br>
<?php
if (trim($prodCode) == "KSS01") {
    $result = "하단마감재(SUS 1.55T) + 하단보강엘바(EGI 1.55T) + 하단 보강평철(EGI 1.15T) + 하단 무게평철(50*12T)";
} else {
    $result = "하단마감재(EGI 1.55T) + 하단보강엘바(EGI 1.55T) + 하단 보강평철(EGI 1.15T) + 하단 무게평철(50*12T)";
}
echo $result . '</div>' ;
?>

<!-- 하단마감재  -->
<?php
// 하단 마감재 및 구성품 테이블 출력
if (True) {	
    // 하단 마감재, 하단 보강엘바, 하단 보강평철, 하단 무게평철의 데이터를 저장할 배열
    $item_data = [
        '하단마감재' => [
            'size' => '(60*40)',
            'length_3000' => 0,
            'length_4000' => 0
        ],
        '하단 보강엘바' => [
            'size' => '(60*17)',
            'length_3000' => 0,
            'length_4000' => 0
        ],
        '하단 보강평철' => [
            'size' => '',
            'length_3000' => 0,
            'length_4000' => 0
        ],
        '하단 무게평철' => [
            'size' => '[50*12T]',
            'length_2000' => 0
        ]
    ];

    // 데이터를 누적하여 합산
    foreach ($eList as $item) {
        $item_data['하단마감재']['length_3000'] += intval($item['col44']);
        $item_data['하단마감재']['length_4000'] += intval($item['col45']);
        $item_data['하단 보강엘바']['length_3000'] += intval($item['col47']);
        $item_data['하단 보강엘바']['length_4000'] += intval($item['col48']);
        $item_data['하단 보강평철']['length_3000'] += intval($item['col50']);
        $item_data['하단 보강평철']['length_4000'] += intval($item['col51']);
        $item_data['하단 무게평철']['length_2000'] += intval($item['col52']);
    }

    // 테이블 출력 시작
    echo '<div class="d-flex align-items-center justify-content-center m-1">';
    echo '<table class="table" style="border-collapse: collapse;">';

    // 첫 번째 행: 자재 구성품명, 길이, 수량 표시
    echo '<tr>';
    echo '<td class="text-center lightgray fw-bold">구성품</td>';
    echo '<td class="text-center lightgray fw-bold">길이 (mm)</td>';
    echo '<td class="text-center lightgray fw-bold">수량</td>';
    echo '<td class="text-center lightgray fw-bold">구성품</td>';
    echo '<td class="text-center lightgray fw-bold">길이 (mm)</td>';
    echo '<td class="text-center lightgray fw-bold">수량</td>';
    echo '<td class="text-center lightgray fw-bold">구성품</td>';
    echo '<td class="text-center lightgray fw-bold">길이 (mm)</td>';
    echo '<td class="text-center lightgray fw-bold">수량</td>';
    echo '<td class="text-center lightgray fw-bold">구성품</td>';
    echo '<td class="text-center lightgray fw-bold">길이 (mm)</td>';
    echo '<td class="text-center lightgray fw-bold">수량</td>';
    echo '</tr>';

    // 두 번째 행: 3000mm 길이에 대한 수량 표시
    echo '<tr>';
    // 하단 마감재
    echo '<td class="text-center fw-bold" rowspan="2">하단마감재<br>' . $item_data['하단마감재']['size'] . '</td>';
    echo '<td class="text-center">L : 3,000</td>';
    echo '<td class="text-center text-danger">' . ($item_data['하단마감재']['length_3000'] ?: '-') . '</td>';

    // 하단 보강엘바
    echo '<td class="text-center fw-bold" rowspan="2">하단<br>보강엘바<br>' . $item_data['하단 보강엘바']['size'] . '</td>';
    echo '<td class="text-center">L : 3,000</td>';
    echo '<td class="text-center text-danger">' . ($item_data['하단 보강엘바']['length_3000'] ?: '-') . '</td>';

    // 하단 보강평철
    echo '<td class="text-center fw-bold" rowspan="2">하단<br>보강평철</td>';
    echo '<td class="text-center">L : 3,000</td>';
    echo '<td class="text-center text-danger">' . ($item_data['하단 보강평철']['length_3000'] ?: '-') . '</td>';

    // 하단 무게평철
    echo '<td class="text-center fw-bold" rowspan="2">하단<br>무게평철<br>' . $item_data['하단 무게평철']['size'] . '</td>';
    echo '<td class="text-center">L : 2,000</td>';
    echo '<td class="text-center text-danger">' . ($item_data['하단 무게평철']['length_2000'] ?: '-') . '</td>';
    echo '</tr>';

    // 세 번째 행: 4000mm 길이에 대한 수량 표시
    echo '<tr>';
    // 하단 마감재 (길이 4000)
    echo '<td class="text-center">L : 4,000</td>';
    echo '<td class="text-center text-danger">' . ($item_data['하단마감재']['length_4000'] ?: '-') . '</td>';

    // 하단 보강엘바 (길이 4000)
    echo '<td class="text-center">L : 4,000</td>';
    echo '<td class="text-center text-danger">' . ($item_data['하단 보강엘바']['length_4000'] ?: '-') . '</td>';

    // 하단 보강평철 (길이 4000)
    echo '<td class="text-center">L : 4,000</td>';
    echo '<td class="text-center text-danger">' . ($item_data['하단 보강평철']['length_4000'] ?: '-') . '</td>';

    // 하단 무게평철은 2000mm만 존재하므로 공백 처리
    echo '</tr>';

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
?>



이미지 정보를 하나 갖고 있는 컬럼(imgdata과 절곡의 전개치수를 저장하는 변수 plateList, 
연신율을 저장하는 변수 bendingrateList, 음영정보를 기록하는 colorList, A각 정보를 담고 있는 AList

위의 컬럼은 전부 'text' 형태이고, 아래의 추가적인 정보를 포함해서 bending 테이블을 생성하는 sql 코드 만들어줘.
<?
$num = isset($_REQUEST['num']) ? $_REQUEST['num'] : '';
$memo = isset($_REQUEST['note']) ? $_REQUEST['note'] : '';
$is_deleted = isset($_REQUEST['is_deleted']) ? $_REQUEST['is_deleted'] : null;
$searchtag = isset($_REQUEST['searchtag']) ? $_REQUEST['searchtag'] : '';
$update_log = isset($_REQUEST['update_log']) ? $_REQUEST['update_log'] : '';
$parentnum = isset($_REQUEST['parentnum']) ? $_REQUEST['parentnum'] : '';
$registdate = isset($_REQUEST['registdate']) ? $_REQUEST['registdate'] : '';
?>

php 7.3이전의 버전이라서 json 형태의 mysql 저장이 안되서 text로 저장하고 JSON형태의 파일을 encode, decode해서 사용할 예정입니다.




위의 코드는 write.php 전체의 코드인데,
mysql 버전이 낮아서 json형태로 저장하는 것이 직접되지 않아서,
text형태로 json 디코드, 인코드하는 과정을 거쳐서 서버에 저장한다.
bending 테이블의 컬럼은 text로 선언된 상태이다.



사진관련 동작이 잘되니 좋다.

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

if (!isset($_SESSION["level"]) || $_SESSION["level"] > 5) {
    sleep(1);
    header("Location:" . $WebSite . "login/login_form.php");
    exit;
}

include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php';

// 첫 화면 표시 문구
$title_message = '절곡바라시 기초자료';
?>

<title> <?=$title_message?> </title>
<link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php

$option = isset($_REQUEST['option']) ? $_REQUEST['option'] : '';
$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';
$header = isset($_REQUEST['header']) ? $_REQUEST['header'] : '';
$tablename = 'bending';

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

$num = isset($_REQUEST["num"]) ? $_REQUEST["num"] : 0;

// 수정일 경우
if ($num > 0) {

    try {
        $sql = "SELECT * FROM " . $DB . "." . $tablename . " WHERE num=?";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, $num, PDO::PARAM_STR);
        $stmh->execute();

        $row = $stmh->fetch(PDO::FETCH_ASSOC);
        include '_row.php';

    } catch (PDOException $Exception) {
        print "오류: " . $Exception->getMessage();
    }

	$mode = 'update';

        $memo = '';
        $imgdata = '';
        $itemName = '';
        $inputList = '{}'; // JSON 초기값
        $plateList = '{}'; // JSON 초기값
        $bendingrateList = '{}'; // JSON 초기값
        $colorList = '{}'; // JSON 초기값
        $AList = '{}'; // JSON 초기값
        $sumList = '{}'; // JSON 초기값
        $title_message = '절곡바라시 기초자료 수정';
    
} else {
    include '_request.php';

    $mode = 'insert';
    $memo = '';
    $imgdata = '';
    $itemName = '';
    $inputList = '{}'; // JSON 초기값
    $plateList = '{}'; // JSON 초기값
    $bendingrateList = '{}'; // JSON 초기값
    $colorList = '{}'; // JSON 초기값
    $AList = '{}'; // JSON 초기값
    $sumList = '{}'; // JSON 초기값
    $registdate = date('Y-m-d'); // 현재일자 기록
}
?>

<form id="board_form" name="board_form" method="post" enctype="multipart/form-data">

    <input type="hidden" id="mode" name="mode" value="<?=$mode?>">
    <input type="hidden" id="num" name="num" value=<?=$num?>>
    <input type="hidden" id="tablename" name="tablename" value=<?=$tablename?>>
    <input type="hidden" id="update_log" name="update_log" value=<?=$update_log?>>
    <input type="hidden" id="header" name="header" value="<?=$header?>">
    <input type="hidden" id="registdate" name="registdate" value="<?=$registdate?>">
	<!-- Hidden Fields to store JSON Data -->
	<input type="hidden" id="inputList" name="inputList">
	<input type="hidden" id="bendingrateList" name="bendingrateList">
	<input type="hidden" id="sumList" name="sumList">
	<input type="hidden" id="colorList" name="colorList">
	<input type="hidden" id="AList" name="AList">
	
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-center">
            <span class="text-center fs-5"><?=$title_message?></span>
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="row mt-3">
                            <!-- 기존 이미지 표시 영역 -->
                            <div class="col-sm-3 mt-3">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div id="previewContainer" class="text-center mb-3">
                                            <img src="../img/bending/Lbar.png" alt="L bar" id="currentImage" class="img-fluid">
                                        </div>
                                    </div>
                                </div>
                                <!-- 파일 선택 -->
                                <input type="file" id="upfile" name="upfile[]" multiple style="display:none;">
                                <button class="btn btn-dark btn-sm me-4" type="button" onclick="document.getElementById('upfile').click();">
                                    <i class="bi bi-image"></i>
                                </button>
                                
                                <div class="d-flex justify-content-center">
                                    <!-- 드롭 영역 -->
                                    <div id="dropArea" style="border: 1px dashed #ccc; padding: 5px; width:100%; height:80px; text-align: center;">
                                        여기로 사진을 drop 하세요!
                                    </div>
                                </div>
                                <!-- 파일 목록 표시 -->
                                <div class="d-flex mt-2 justify-content-center">
                                    <div id="displayfile"></div>
                                </div>
                            </div>

                            <!-- JavaScript for handling drag and drop -->
                            <script>
                            document.getElementById('dropArea').addEventListener('dragover', function(event) {
                                event.preventDefault();
                                event.stopPropagation();
                                this.style.borderColor = '#000'; // 드래그 시 테두리 색상 변경
                            });

                            document.getElementById('dropArea').addEventListener('dragleave', function(event) {
                                event.preventDefault();
                                event.stopPropagation();
                                this.style.borderColor = '#ccc'; // 드래그 영역 벗어날 때 테두리 색상 복원
                            });

                            document.getElementById('dropArea').addEventListener('drop', function(event) {
                                event.preventDefault();
                                event.stopPropagation();

                                const files = event.dataTransfer.files;
                                if (files.length > 0) {
                                    handleFiles(files);
                                }
                            });

                            document.getElementById('upfile').addEventListener('change', function(event) {
                                const files = event.target.files;
                                if (files.length > 0) {
                                    handleFiles(files);
                                }
                            });

                            function handleFiles(files) {
                                const file = files[0]; // 첫 번째 파일만 처리
                                const reader = new FileReader();
                                reader.onload = function(event) {
                                    const img = document.createElement('img');
                                    img.src = event.target.result;
                                    img.className = 'img-fluid';

                                    const previewContainer = document.getElementById('previewContainer');
                                    previewContainer.innerHTML = ''; // 기존 이미지 제거
                                    previewContainer.appendChild(img);

                                    // 이미지 데이터를 form 요소에 추가
                                    const hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = 'imageData';
                                    hiddenInput.value = event.target.result;
                                    previewContainer.appendChild(hiddenInput);
                                };
                                reader.readAsDataURL(file);
                            }
                            </script>

                            <div class="col-sm-9">
                                <div class="card">
                                    <div class="card-header text-center">
                                        <div class="d-flex align-items-center justify-content-start">
                                            <span class="text-start fs-6 me-1">등록일 </span>
                                            <input type="date" id="registdate" class="form-control me-3" name="registdate" value="<?=$registdate?>" style="width: 120px;">
                                            <span class="text-center fs-6 me-1">품명 </span>
                                            <input type="text" id="itemName" class="form-control text-start me-3" name="itemName" value="<?=$itemName?>" style="width: 250px;">
                                            <span class="text-center fs-6 me-1">재질 </span>
                                            <select id="material" class="form-control text-center" name="material" style="width: 100px;">
                                                <?php
                                                $options = ['선택','EGI 1.15T', 'EGI 1.55T', 'SUS 1.2T', 'SUS 1.5T']; // 배열에 새로운 옵션 추가
                                                foreach ($options as $option) {
                                                    $selected = ($material === $option) ? 'selected' : '';
                                                    echo "<option value=\"$option\" $selected>$option</option>";
                                                }
                                                ?> 
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="table-container">
                                                <table class="table table-bordered" id="dynamicTable">
                                                    <thead>
                                                        <tr>
                                                            <th class="lightgray" style="width: 150px;">항목</th>
                                                            <th class="text-start" style="width: 940px;"> &nbsp; 값</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id="numberRow">
                                                            <td class="lightgray">번호</td>
                                                            <td>
                                                                <div class="input-container">
                                                                    <span class="form-control text-center" style="width: 40px;">1</span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="lightgray">입력</td>
                                                            <td>
                                                                <div class="input-container">
                                                                    <input type="text" class="form-control yellowBold text-start" name="inputList[]" style="width: 40px;" oninput="calculateSum()" onchange="calculateSum()">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="lightgray">연신율 (-)</td>
                                                            <td>
                                                                <div class="input-container">
                                                                    <input type="text" class="form-control text-start" name="bendingrateList[]" style="width: 40px;" oninput="calculateSum()" onchange="calculateSum()">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="lightgray">합</td>
                                                            <td>
                                                                <div class="input-container">
                                                                    <input type="text" class="form-control orangeBlackBold text-start" name="sumList[]" style="width: 40px;">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="lightgray">음영 </td>
                                                            <td>
                                                                <div class="input-container">
                                                                    <input type="checkbox" class="form-check-input" name="colorList[]" style="width: 20px; height: 20px; margin-left: 10px; margin-right: 20px;"> 
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="lightgray">A각 표시</td>
                                                            <td>
                                                                <div class="input-container">
                                                                    <input type="checkbox" class="form-check-input" name="AList[]" style="width: 20px; height: 20px; margin-left: 10px;  margin-right: 20px;"> 
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-dark btn-sm" id="saveBtn">저장 </button>
                                        <button type="button" class="btn btn-success btn-sm" id="addColumnBtn">열 추가</button>
                                        <button type="button" class="btn btn-danger btn-sm" id="removeColumnBtn">열 제거</button>
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




<style>
.table-container {
    width: 100%;    
}
#dynamicTable {
    table-layout: fixed;
    width: 800px; 
}
#dynamicTable th:first-child,
#dynamicTable td:first-child {
    width: 150px;
    min-width: 150px;
    max-width: 150px;
}
#dynamicTable th:nth-child(2),
#dynamicTable td:nth-child(2) {
    width: 900px;
    min-width: 800px;
    max-width: 800px;
}
.input-container {
    width: 100%;    
    white-space: nowrap;
    display: flex;
    align-items: center;
}
.input-container input,
.input-container span {
    display: inline-block;
    margin-right: 10px;
}
.input-container input[type="text"] {
    width: 40px;	
}
.input-container input[type="checkbox"] {
    width: 20px;
    height: 20px;
    margin-right: 20px;
}
.form-check {
    display: flex;
    align-items: center;
}
</style>

<script>
function calculateSum() {
    const table = document.getElementById('dynamicTable');
    const inputContainers = table.querySelectorAll('tr:nth-child(2) .input-container');
    const bendingrateContainers = table.querySelectorAll('tr:nth-child(3) .input-container');
    const sumContainers = table.querySelectorAll('tr:nth-child(4) .input-container');
    
    let accumulatedSum = 0;

    const inputs = inputContainers[0].querySelectorAll('input[type="text"]');
    const bendingrates = bendingrateContainers[0].querySelectorAll('input[type="text"]');
    const sums = sumContainers[0].querySelectorAll('input[type="text"]');

    for (let i = 0; i < inputs.length; i++) {
        const inputVal = parseFloat(inputs[i].value) || 0;
        const bendingrateVal = parseFloat(bendingrates[i].value) || 0;

        const result = inputVal - bendingrateVal;
        accumulatedSum += result;

        if (sums[i]) {
            sums[i].value = accumulatedSum;
        }
    }
}

function addColumn() {
    const table = document.getElementById('dynamicTable');
    const numberRow = document.getElementById('numberRow').querySelector('.input-container');
    const currentNumber = numberRow.querySelectorAll('span').length + 1;

    // 번호 행에 새로운 숫자 추가
    const newNumber = document.createElement('span');
    newNumber.className = 'form-control text-center';
    newNumber.style.width = '40px';
    newNumber.innerText = currentNumber; // 새 열 번호
    numberRow.appendChild(newNumber);

    // 나머지 행에 새로운 열 추가
    const rows = table.getElementsByTagName('tr');
    for (let i = 2; i < rows.length; i++) {
        const inputContainer = rows[i].querySelector('.input-container');
        if (inputContainer) {
            const inputName = inputContainer.querySelector('input') ? inputContainer.querySelector('input').name : '';
            let additionalClass = i === 2 ? 'form-control yellowBold' : i === 4 ? 'form-control orangeBlackBold' : 'form-control';

            const newInput = document.createElement('input');
            newInput.type = (i === 5 || i === 6) ? 'checkbox' : 'text';
            newInput.className = additionalClass;
            newInput.name = inputName;
            if (newInput.type === 'text') {
                newInput.style.width = '40px';
                newInput.oninput = calculateSum;
                newInput.onchange = calculateSum;
            } else {
                newInput.className = 'form-check-input';
                newInput.style.width = '20px';
                newInput.style.height = '20px';
                newInput.style.marginRight = '20px';

                // 체크박스 라벨 추가
                const label = document.createElement('label');
                label.style.marginRight = '10px';
                inputContainer.appendChild(label);
            }

            inputContainer.appendChild(newInput);
        }
    }
}

function removeColumn() {
    const table = document.getElementById('dynamicTable');
    const numberRow = document.getElementById('numberRow');

    // 번호 제거
    const numbers = numberRow.querySelectorAll('span');
    if (numbers.length > 1) {
        numberRow.querySelector('.input-container').removeChild(numbers[numbers.length - 1]);
    }

    // 나머지 행에서 마지막 열 제거
    const rows = table.getElementsByTagName('tr');
    for (let i = 1; i < rows.length; i++) {
        const inputContainer = rows[i].querySelector('.input-container');
        if (inputContainer) {
            const inputs = inputContainer.querySelectorAll('input');
            if (inputs.length > 1) {
                inputContainer.removeChild(inputs[inputs.length - 1]);

                // 체크박스 라벨 제거
                if (i === 5 || i === 6) {
                    const labels = inputContainer.querySelectorAll('label');
                    if (labels.length > 0) {
                        inputContainer.removeChild(labels[labels.length - 1]);
                    }
                }
            }
        }
    }
    calculateSum();
}

document.getElementById('addColumnBtn').addEventListener('click', addColumn);
document.getElementById('removeColumnBtn').addEventListener('click', removeColumn);
</script>



</form>


<!-- 페이지로딩 -->
<script>
// 페이지 로딩
$(document).ready(function(){
    var loader = document.getElementById('loadingOverlay');
    loader.style.display = 'none';
});

ajaxRequest_write = null;


// 저장 버튼 클릭 시 데이터를 JSON으로 변환하여 hidden input에 저장
document.getElementById('saveBtn').addEventListener('click', function() {
    // 각 리스트들을 JSON으로 변환
    const inputList = [];
    document.querySelectorAll('input[name="inputList[]"]').forEach(input => inputList.push(input.value));
    
    const bendingrateList = [];
    document.querySelectorAll('input[name="bendingrateList[]"]').forEach(input => bendingrateList.push(input.value));
    
    const sumList = [];
    document.querySelectorAll('input[name="sumList[]"]').forEach(input => sumList.push(input.value));
    
    const colorList = [];
    document.querySelectorAll('input[name="colorList[]"]').forEach(input => colorList.push(input.value));
    
    const AList = [];
    document.querySelectorAll('input[name="AList[]"]').forEach(input => AList.push(input.value));

    // JSON 데이터를 hidden input 필드에 저장
    document.getElementById('inputList').value = JSON.stringify(inputList);
    document.getElementById('bendingrateList').value = JSON.stringify(bendingrateList);
    document.getElementById('sumList').value = JSON.stringify(sumList);
    document.getElementById('colorList').value = JSON.stringify(colorList);
    document.getElementById('AList').value = JSON.stringify(AList);

    // 저장 로직 호출 (AJAX)
    saveData();
});

$(document).ready(function(){

    // 창닫기 버튼
    $("#closeBtn").on("click", function() {
        self.close();
    });


});  // end of ready


// 데이터 저장 함수
function saveData() {
    if (ajaxRequest_write !== null) {
        ajaxRequest_write.abort();
    }

    ajaxRequest_write = $.ajax({
        url: "process.php",
        type: "post",
        data: $("#board_form").serialize(),
        success: function(data) {
            console.log(data);

            Toastify({
                text: '저장완료',
                duration: 3000,
                close: true,
                gravity: "top",
                position: "center",
                backgroundColor: "#4fbe87",
            }).showToast();

            setTimeout(function() {
                self.close();
            }, 500);
        },
        error: function(jqxhr, status, error) {
            console.log(jqxhr, status, error);
        }
    });
}

</script>

</body>
</html>

위의 코드는 write.php 전체의 코드인데,
mysql 버전이 낮아서 json형태로 저장하는 것이 직접되지 않아서,
text형태로 json 디코드, 인코드하는 과정을 거쳐서 서버에 저장한다.
bending 테이블의 컬럼은 text로 선언된 상태이다.

위의 코드에서 서버로 보낼때 코드에 문제가 없는가?
예전의 사이트에서는 json으로 저장된 것을 아래와 같은 방식으로 form에 저장한 후 서버에 저장한다.
save를 저장하면 
예시)

    const formData = new FormData(document.getElementById('board_form'));
    formData.set('mode', Number($("#num").val()) < 1 ? 'insert' : 'modify');

    let itemList = [];
    $('#itemListTable tbody tr').each(function() {
        let rowData = {};
        $(this).find('input, select').each(function() {
            let name = $(this).attr('name').replace('[]', '');
            rowData[name] = $(this).val();
        });
        itemList.push(rowData);
    });

    formData.set('itemList', JSON.stringify(itemList));

서버에서 받을때는 아래와 같은처리함.

// 테이블 데이터 JSON 파싱
if (isset($_POST['itemList'])) {
    $itemList_jsondata = json_decode($_POST['itemList'], true);
} else {
    $itemList_jsondata = null;
}

if ($mode == "modify") {
    $updatelog = date("Y-m-d H:i:s") . " - " . $_SESSION["name"] . " " . $updatelog . "&#10";

    try {
        $pdo->beginTransaction();
        $sql = "UPDATE " . $DB . ".{$tablename}  SET 
                is_deleted=?, orderDate=?, secondord=?, secondordman=?, secondordmantel=?, pjnum=?, pjname=?, jobNo=?, spec=?, daesu=?, 
                unit=?, deadlineDate=?, drawDate=?, requestmaDate=?, stackmaDate=?, shearingDate=?, nctDate=?, bendingDate=?, assemblyDate=?, 
                outputDate=?, comment=?, memo=?, unitprice=?, amount=?, invoiceAmount=?, invoiceDate=?, itemList=?, 
                updatelog=?, status=? , searchtag=? , deliverymethod=?, address=? , makeDate=? 
                WHERE num=? LIMIT 1";			

        $stmh = $pdo->prepare($sql);

        $params = [
            $is_deleted, $orderDate, $secondord, $secondordman, $secondordmantel, $pjnum, $pjname, $jobNo, $spec, $daesu,
            $unit, $deadlineDate, $drawDate, $requestmaDate, $stackmaDate, $shearingDate, $nctDate, $bendingDate, $assemblyDate,
            $outputDate, $comment, $memo, $unitprice, $amount, $invoiceAmount, $invoiceDate, json_encode($itemList_jsondata), 
            $updatelog, $status, $searchtag,  $deliverymethod, $address , $makeDate , $num
        ];


위의 자바스크립 코드를 제이쿼리 형식으로 전부 수정해주고,
null에 의한 오류가 안나도록 검사하고 실행하는 코드를 다 넣어주세요.


CREATE TABLE `instock` (
  `num` int NOT NULL AUTO_INCREMENT,
  `is_deleted` tinyint DEFAULT NULL,
  `lot_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `inspection_date` date NOT NULL,
  `supplier` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `item_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `specification` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `unit` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `received_qty` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `material_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `manufacturer` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `remarks` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `purchase_price_excl_vat` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `weight_kg` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `searchtag` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `update_log` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8


-- dbchandj.material_reg definition

CREATE TABLE material_reg (
  num int NOT NULL AUTO_INCREMENT,
  is_deleted tinyint DEFAULT NULL,
  registedate date NOT NULL,
  inoutdate date NOT NULL,
  secondord varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  inout_item_code varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  item_name varchar(50) NOT NULL,
  surang varchar(10) NOT NULL,
  lotnum varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  comment text,
  unitprice varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  searchtag text,
  update_log text,
  secondordnum varchar(10) DEFAULT NULL,
  PRIMARY KEY (num)
) ENGINE=InnoDB AUTO_INCREMENT=248 DEFAULT CHARSET=utf8;

위의 형식을 기반으로, 위의 컬럼명은 무시하고, 

table 'lot_sales'를 생성하려고 합니다.
컬럼은 아래와 같은데요. 한글을 영문화를 해주세요.
num, 등록일, LOT번호, 작성자, 비고, updatelog, searchtag, is_deleted 
num은 고유번호로 autoincrement 적용입니다.

CREATE TABLE `lot_sales` (
  `num` int NOT NULL AUTO_INCREMENT,      -- 고유번호
  `reg_date` date NOT NULL,               -- 등록일
  `lot_number` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, -- LOT번호
  `author` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,     -- 작성자
  `remark` text,                          -- 비고
  `update_log` text,                      -- 업데이트 로그
  `search_tag` text,                      -- 검색 태그
  `is_deleted` tinyint DEFAULT NULL,      -- 삭제 여부
  PRIMARY KEY (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


fetch_modal.php 파일의 내용도 수정하려고 합니다. 위의 컬럼 내용으로
기존의 list.php에서 이 부분이 빠졌는데, 아래의 코드를 완성하고, list.php에서 모달창을 불러오는 과정도 들어가야 합니다.


// 테이블 데이터 JSON 파싱
if (isset($_POST['itemList'])) {
    $itemList_jsondata = json_decode($_POST['itemList'], true);
} else {
    $itemList_jsondata = null;
}
if ($mode == "modify") {
    $updatelog = date("Y-m-d H:i:s") . " - " . $_SESSION["name"] . " " . $updatelog . "&#10";

    try {
        $pdo->beginTransaction();
        $sql = "UPDATE " . $DB . ".{$tablename}  SET itemList=? WHERE num=? LIMIT 1";			

        $stmh = $pdo->prepare($sql);

        $params = [
             json_encode($itemList_jsondata) , $num
        ];


// 셔터박스 자동생성 로직 개발
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
$num = isset($_POST['num']) ? $_POST['num'] : '';
$tablename = isset($_POST['tablename']) ? $_POST['tablename'] : '';

require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
$pdo = db_connect();

if(!isset($DB))
	$DB = 'chandj';

    try {
        $sql = "SELECT * FROM {$DB}.$tablename WHERE num=? ";
        $stmh = $pdo->prepare($sql);
        $stmh->bindValue(1, $num, PDO::PARAM_INT);      
        $stmh->execute();
        $row = $stmh->fetch(PDO::FETCH_ASSOC);

        include '_row.php';
    } catch (PDOException $Exception) {
        echo "오류: ".$Exception->getMessage();
        exit;
}

$title_message = '셔터박스 절곡 전개';

// echo '<pre>';
// print_r($row);
// echo '</pre>';

// 기존에 저장된 값이 있는 경우 해당 값을 설정
$selected_check_typeFlat = isset($row['check_type']) ? $row['check_type'] : '양쪽 점검구';

?>

<style>
.image-container {
	position: relative;
	display: inline-block;
}
.image-container img {
	display: block;
}
	.input-overlay {
		position: absolute;
		background: rgba(255, 255, 255, 0.5);
		border: 1px solid blue;
		width: 50px;
		color: blue;
		/* font-weight: bold; */
	}
	
.modal-body {
    max-height: 750px; /* 원하는 높이 설정 */
    max-width: 1380px; /* 원하는 너비 설정 */
    overflow-x: auto; /* 가로 스크롤 가능 */
    overflow-y: auto; /* 세로 스크롤 가능 */
}
	
</style>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-center">
        <div class="card justify-content-center">
            <div class="card-header text-center">
                <span class="text-center fs-5"><?=$title_message?></span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col text-center">
                        <label class="me-3">
                            <input type="radio" name="check_type_flat" value="양쪽 점검구" <?php if($selected_check_typeFlat === '양쪽 점검구') echo 'checked'; ?> readonly> 양쪽 점검구
                        </label>
                        <label class="me-3">
                            <input type="radio" name="check_type_flat" value="밑면 점검구" <?php if($selected_check_typeFlat === '밑면 점검구') echo 'checked'; ?> readonly> 밑면 점검구
                        </label>
                        <label>
                            <input type="radio" name="check_type_flat" value="후면 점검구" <?php if($selected_check_typeFlat === '후면 점검구') echo 'checked'; ?> readonly> 후면 점검구
                        </label>
                    </div>
                </div>
				<div class="d-flex align-items-center justify-content-center m-2">
					<div class="image-container mb-5">
						<img id="checkImageFlat" src="<?php 
							switch ($selected_check_typeFlat) {
								case '양쪽 점검구':
									echo '../img/box/box_both.png';
									break;
								case '밑면 점검구':
									echo '../img/box/box_bottom.png';
									break;
								case '후면 점검구':
									echo '../img/box/box_back.png';
									break;
								default:
									echo '../img/box/box_both.png'; // 기본값
							}
						?>" alt="Image">
						
						<input type="text" class="input-overlay" id="front_bottom_width_Flat"  value="<?=$front_bottom_width?>"  style="top: 370px; left: 110px;" placeholder="전면 밑">
						<input type="text" class="input-overlay" id="rail_width_Flat"  value="<?=$rail_width?>" style="top: 420px; left: 170px;" placeholder="레일폭">
					</div>
				</div>
                <div class="row justify-content-center text-center">
                    <div class="d-flex align-items-center justify-content-center m-2">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td class="text-center fs-6 fw-bold" colspan="6" >
									 <div class="d-flex align-items-center justify-content-center">
									  <span class="text-center fs-6 ms-1 me-1"> 셔터박스 </span> 
									  <span class="text-center fs-6 ms-1 me-1 text-primary"> 가로(폭) </span> 
										<input type="text" class="form-control fs-6 w50px text-primary ms-1 me-1" id="box_width_Flat" name="box_width_Flat" value="<?=$box_width?>">
									  <span class="text-center fs-6 ms-1 me-1"> x </span> 
									  <span class="text-center fs-6 ms-1 me-1 text-danger"> 세로(높이) </span> 
										<input type="text" class="form-control fs-6 w50px text-danger" id="box_height_Flat" name="box_height_Flat" value="<?=$box_height?>">
									  </div>
									</td>
                                </tr>                           
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-outline-dark btn-sm me-2 closeBtn">
                        <ion-icon name="close-circle-outline"></ion-icon> 닫기
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

위의 코드는 fetch_flat.php 코드이다.

위의 코드에 새로운 내용을 추가할 것이다.
셔터박스의 각 부위에 대한 연산방법을 표현할때 절곡물의 형태에 따라 계산법이 있다.
위의 3가지 유형의 셔터박스의 계산법은 일부는 같고, 일부는 다르다.
그 계산하는 과정을 표현하는 코드는 아래를 참조한다.

	<div class="table-container">
		<table class="table table-bordered" id="dynamicTable">
		   <tbody id="tableBody">
				<!-- 기본적으로 빈 상태로 두고, JS로 데이터를 추가 -->
			</tbody>
		</table>
	</div>
											
해당 테이블의 CSS 코드

<style>
.table-container {
    width: 100%;    
}
#dynamicTable {
    table-layout: fixed;
    width: 1000px; 
}
#dynamicTable th:first-child,
#dynamicTable td:first-child {
    width: 100px;
    min-width: 100px;
    max-width: 100px;
}
#dynamicTable th:nth-child(2),
#dynamicTable td:nth-child(2) {
    width: 900px;
    min-width: 900px;
    max-width: 900px;
}
.input-container {
    width: 100%;    
    white-space: nowrap;
    display: flex;
    align-items: center;
}
.input-container input,
.input-container span {
    display: inline-block;
    margin-right: 10px;
}
.input-container input[type="text"] {
    width: 40px;	
}
.input-container input[type="checkbox"] {
    width: 20px;
    height: 20px;
    margin-right: 10px;
    margin-left: 10px;
}
.form-check {
    display: flex;
    align-items: center;
}
</style>

새로운 테이블은 총 6개의 행으로 구성되며, 각 열의 형성과정은 아래의 코드로 알 수 있다.


function addInitialColumn() {
	
    const tableBody = $('#tableBody');
	
    if (!tableBody.length) return;

    const rowsData = [
        { label: '번호', type: 'span', className: 'form-control text-center', style: 'width: 40px;' },
        { label: '입력', type: 'input', className: 'form-control yellowBold text-center', style: 'width: 40px;', name: 'inputList[]', event: 'calculateSum()' },
        { label: '연신율 (-)', type: 'input', className: 'form-control text-center', style: 'width: 40px;', name: 'bendingrateList[]', event: 'calculateSum()' },
        { label: '합계', type: 'input', className: 'form-control orangeBlackBold text-center', style: 'width: 40px;', name: 'sumList[]' },
        { label: '음영', type: 'checkbox', className: 'form-check-input form-control ', style: 'width: 20px; height: 20px; margin-left: 20px; margin-right: 20px;', name: 'colorList[]' },
        { label: 'A각 표시', type: 'checkbox', className: 'form-check-input  form-control ', style: 'width: 20px; height: 20px; margin-left: 20px; margin-right: 20px;', name: 'AList[]' }
    ];

    rowsData.forEach(function(rowData) {
        const row = $('<tr>');

        const labelCell = $('<td>').addClass('lightgray').text(rowData.label);
        row.append(labelCell);

        const inputCell = $('<td>').addClass('input-container');

        if (rowData.type === 'span') {
            const span = $('<span>').addClass(rowData.className).css('width', '40px').text('1');
            inputCell.append(span);
        } else {
            const input = $('<input>')
                .attr('type', rowData.type)
                .addClass(rowData.className)
                .css('width', ( rowData.type === 'checkbox' ||  rowData.type === 'checkbox') ? '20px' : '40px')
                .css('margin-left', ( rowData.type === 'checkbox' ||  rowData.type === 'checkbox') ? '10px' : '')
                .css('margin-right', ( rowData.type === 'checkbox' ||  rowData.type === 'checkbox') ? '20px' : '')
				.attr('autocomplete', 'off'); // 올바른 autocomplete 설정

            if (rowData.name) input.attr('name', rowData.name);
            if (rowData.event) {
                input.on('input change', calculateSum);
            }

            inputCell.append(input);
        }

        row.append(inputCell);
        tableBody.append(row);
    });
}


내가 구현하는 것은 셔터박스 형태 3가지 종류에 대한 절곡품 1번부터 7번까지 정보를 위의 코드에서 참조해서 테이블 형태로 화면에 보여주고 싶다.
그 생성원리는 아래와 같다.

1번 전면부 : 7개의 숫자로 이뤄지는데 입력값이 20, 15, 55, 4번째요소는 연산을 해야 한다. $box_height값이 된다. , 5번째 요소는 $front_bottom_width 값이다. ,55,17  이것이 자료가 된다.
2번 린텔부는 고정치수이다. 입력값은 5개의 배열이다. 30,55,50,15,20

일단 2개까지만 만들어줘. 추가로 3번부터 7번까지는 코드의 완성도를 보고 제공하겠다.



#로그기록 함수.  common.js 저장됨

function saveMenuLog(title) {        
    
    var formData = new FormData();
    formData.append('menu', title);

    $.ajax({
        enctype: 'multipart/form-data', // file을 서버에 전송하려면 이렇게 해야 함 주의
        processData: false,    
        contentType: false,      
        cache: false,           
        timeout: 600000,             
        url: "/insert_logmenu.php",
        type: "post",        
        data: formData,          
        dataType: "json",     
        success: function(data){
            console.log(data);
        },
        error: function(jqxhr, status, error) {
            console.log(jqxhr, status, error);
            alert("An error occurred: " + error); // Display error message
        }                    
    });
}

$(document).ready(function(){    
   // 방문기록 남김
   var title = '<?php echo $title_message; ?>';
   saveMenuLog(title);
});

## 테이블 기본 생성칼럼

bendingmap 테이블을 생성할 것이다.

<?php
$num = isset($_REQUEST['num']) ? $_REQUEST['num'] : '';
$is_deleted = isset($_REQUEST['is_deleted']) ? $_REQUEST['is_deleted'] : null;
$reg_date = isset($_REQUEST['reg_date']) ? $_REQUEST['reg_date'] : '';

$author = isset($_REQUEST['author']) ? $_REQUEST['author'] : '';
$remark = isset($_REQUEST['remark']) ? $_REQUEST['remark'] : '';
$update_log = isset($_REQUEST['update_log']) ? $_REQUEST['update_log'] : '';
$search_tag = isset($_REQUEST['search_tag']) ? $_REQUEST['search_tag'] : '';
?>
위의 컬럼은 기본적으로 생성한다. 레코드번호, 비고, 업데이트정보, 삭제여부, 작성자, 검색어 정보 등 기본적으로 테이블생성할때 필요한 요소이다.




echo '<td class="text-center"  data-bs-toggle="tooltip" data-bs-placement="top" title="전체적용 클릭">' . htmlspecialchars($row['col1']) . '</td>';
 data-bs-toggle="tooltip" data-bs-placement="bottom" title="화물회사에서 대한의 요청에 대한 다른 사항이 발생하면 기록합니다."
 
 <!-- 부트스트랩 툴팁 -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });    	  
});
</script>


	<tr>
		<td class="text-center fs-6 fw-bold" style="width:150px;">원자재 LOT</td>
		<td colspan="3" class="text-center">
			<input type="text" class="form-control fs-6 text-start noborder-input lotnumInput" id="rawLot" name="rawLot" value="<?=$rawLot?>" autocomplete="off" >
		</td>
	</tr>

위의 코드에서 input요소에 data-searchRaw=  형태로 제이쿼리 data 값을 읽는 형태로 만들고 싶다. 위의 값에 들어갈 것은 
해당 품목의 재질을 전달하고자 한다.
품목과 종류의 조합에 따라 재질이 결정된다.
아래의 정보를 참고해 주세요.
품목 : 연기차단재, 종류 : 화이바원단  => '화이바원단'
품목 : 하단마감재(스크린), 종류 : SUS  => 'SUS 1.2T'
품목 : 하단마감재(스크린), 종류 : EGI  => 'EGI 1.55T'
품목 : L - Bar, 종류 : 스크린용  => 'EGI 1.15T'
품목 : 가이드레일, 종류 : SUS  => 'SUS 1.2T'
품목 : 가이드레일, 종류 : D형  => 'EGI 1.55T'
품목 : 가이드레일, 종류 : C형  => 'EGI 1.55T'
품목 : 가이드레일, 종류 : 본체  => 'EGI 1.55T'
품목 : 가이드레일, 종류 : EGI  => 'EGI 1.55T'
품목 : 케이스, 종류 : 후면커버 => 'EGI 1.55T'
품목 : 케이스, 종류 : 린텔부 => 'EGI 1.55T'
품목 : 케이스, 종류 : 점검구 => 'EGI 1.55T'
품목 : 케이스, 종류 : 전면부 => 'EGI 1.55T'

위의 내용이 전달되고, 이를 찾는 과정을 코드로 만들어줘.

관련코드를 공유한다.



        // 품목명을 매핑하는 배열
        $prodNames = [
            'R' => '가이드레일(벽면형)',
            'S' => '가이드레일(측면형)',
            'G' => '연기차단재',
            'B' => '하단마감재(스크린)',
            'T' => '하단마감재(철재)',
            'L' => 'L - Bar',
            'C' => '케이스'
        ];
		
        // 종류명을 매핑하는 배열
        $specNames = [
            'I' => '화이바원단',
            'S' => 'SUS(마감)',
            'U' => 'SUS마감2',
            'E' => 'EGI(마감)',
            'A' => '스크린용',
            'D' => 'D형',
            'C' => 'C형',
            'M' => '본체',
            'T' => '본체(철재)',
            'B' => '후면커버',
            'L' => '린텔부',
            'P' => '점검구',
            'F' => '전면부'
        ];

        // 모양&길이를 매핑하는 배열
        $slengthNames = [
            '53' => 'W50 × 3000',
            '54' => 'W50 × 4000',
            '83' => 'W80 × 3000',
            '84' => 'W80 × 4000',
            '12' => '1219',
            '24' => '2438',
            '30' => '3000',
            '35' => '3500',
            '40' => '4000',
            '41' => '4150',
            '42' => '4200',
            '43' => '4300'
        ];

위의 코드로 일부 코드가 변경되었다. 아래의 코드에 이 코드를 적용해 주세요.


            // 품목별 선택 가능한 종류 목록 설정
            const options = {
                'G': ['I'],  // 연기차단재 - 화이바원단
                'B': ['S', 'E'],  // 하단마감재(스크린) - SUS, EGI
                'T': ['S', 'E'],  // 하단마감재(철재) - SUS, EGI
                'L': ['A'],  // L - Bar - 스크린용
                'R': ['S', 'D', 'C', 'M', 'E'],  // 가이드레일 - SUS, D형, C형, 본체, EGI
                'C': ['B', 'L', 'P', 'F']  // 케이스 - 후면커버, 린텔부, 점검구, 전면부
            };

            const prodSelect = document.getElementById('prod');
            const specSelect = document.getElementById('spec');
            const slengthSelect = document.getElementById('slength');
            const lotNumberInput = document.getElementById('lot_number');

            // 품목 변경 시
            $(document).on('change', '#prod', function() {
                const selectedProd = this.value;
                const specOptions = options[selectedProd] || [];

                // 종류 select의 옵션 초기화
                specSelect.innerHTML = '<option value="" disabled>종류 선택</option>';

                // 종류 select에 해당하는 품목의 옵션 추가
                specOptions.forEach(spec => {
                    let optionText = '';
                    switch (spec) {
                        case 'I': optionText = '화이바원단'; break;
                        case 'S': optionText = 'SUS'; break;
                        case 'E': optionText = 'EGI'; break;
                        case 'A': optionText = '스크린용'; break;
                        case 'D': optionText = 'D형'; break;
                        case 'C': optionText = 'C형'; break;
                        case 'M': optionText = '본체'; break;
                        case 'B': optionText = '후면커버'; break;
                        case 'L': optionText = '린텔부'; break;
                        case 'P': optionText = '점검구'; break;
                        case 'F': optionText = '전면부'; break;
                    }
					
					
					
위의 코드에서 이런 것을 만들고 싶다.
세부 산출내역서는 각 일련번호에 대한 세부산출서를 나타낸다.
위의 수량, 면적(㎡) 길이(㎜), 면적(㎡) 길이(㎜) 단가, 단가, 즉 4개의 각 행의 요소를 input으로 입력받는다. 이때 class에 noborder-input을 지정해서 입력창의 테두리가 안나오게 할것이다.
4개의 요소의 값을 수동으로 입력해서 저장하고 싶은것이 목적이다.
각 행의 합계는 자동으로 계산되는 로직을 구현하고, 이는 소계, 합계에 영향을 미쳐야 한다.

저장하는 방식은 json형태로 각행을 col1~col4까지 저장하고 이를 json형태로 저장하고, 서버에는 text로 저장하면 된다. php7.3버전은 mysql json 저장방식을 사용할 수 없어서, encode, decode해서 사용한다.

버튼 '수정된 산출내역 저장'을 누르면 위의 수정된 자료가 서버에 전송되고, 저장된다.
최초 로딩될때 서버에 저장된 수정파일이 존재하면, 이 저장된 자료가 최초 로드되어야 한다.

// 일련번호, 검사비
$subtotal += $inspectionFee * $su;	
if($option !== 'option')
{	
    echo '<tr>';
    echo '<td class="text-center" id="dynamicRowspan-' . $counter . '" >' . $column['col1'] . '</td>';
    echo '<td class="text-center"> 검사비 </td>';

    // 수량 입력 필드
    echo '<td class="text-center"><input type="text" class="noborder-input text-center su-input number-input" value="' . number_format($su) . '" data-row="' . $counter . '" data-type="su" oninput="inputNumber(this)" /></td>';
	
    // 단위 및 기타 필드
    echo '<td class="text-center"> SET </td>';

    // 산출식 입력 필드 (예: 수량 * 단가 등으로 계산하는 필드)
    echo '<td class="text-center"><input type="text" class="text-left noborder-input calc-formula-input" value="" data-row="' . $counter . '" data-type="formula" /></td>';
    
    // 면적(㎡) 길이(㎜) 입력 필드
    echo '<td class="text-center"><input type="text" class="noborder-input text-centerarea-length-input number-input" value="" data-row="' . $counter . '" data-type="area_length" oninput="inputNumber(this)" /></td>';
    
    // 면적(㎡) 길이(㎜) 단가 입력 필드
    echo '<td class="text-center"><input type="text" class="noborder-input  text-center  area-price-input number-input" value="" data-row="' . $counter . '" data-type="area_price" oninput="inputNumber(this)" /></td>';
    
    // 단가 입력 필드
    echo '<td class="text-end"><input type="text" class="noborder-input text-end unit-price-input number-input" value="' . number_format($inspectionFee) . '" data-row="' . $counter . '" data-type="unit_price" oninput="inputNumber(this)" /></td>';

    // 합계 필드 (자동 계산, 입력 불가)
    echo '<td class="text-end total-price" id="total-price-' . $counter . '">' . number_format($inspectionFee * $su) . '</td>';
    
    echo '</tr>';	
    $rowCount++;	
}

위의 코드에서 자바스크립트 코드로 이제 행의 합계, 일련번호의 소계, 전체 합계를 자동으로 계산되는 로직을 만들어야 한다.

계산공식은 아래와 같다. 조건에 따라 약간 다른 연산을 해야 한다.

마지막 td의 합계 금액은 연산을 할 셀의 값이 없으면 거기는 곱하기를 하지 않고,
면적(㎡)
길이(㎜) 요소의 값이 있다면, 수량 * 이 값
(면적(㎡) 길이(㎜) 단가) 이 값이 있으면 곱하기

기본적으로 수량 * 단가인데, 단가를 수동으로 입력하면, 수동입력된 단가에 수량을 곱해서 금액에 표시되어야 한다.

소계는 이런 금액들의 합이고, 전체합계는 소계의 합이 되도록 자바스크립트 코드 만들어줘.

## 혼합형 문구를 파싱해서 변수에 넣는 방법

$railtype의 값이 아래와 같이 '혼합형'이란 단어가 들어있으면 아래의 같은 형태가 된다.
예시) 혼합형(120*70)(120*120)

이 경우는 4개의 숫자를 
$rail_length1 =  120;
$rail_width1 = 70;
$rail_length2 =  120;
$rail_width2 = 120;

저장하는 코드를 만들어줘.

    // 감기샤프트 데이터에 품목 번호와 수량을 함께 저장
    $shaft_data = [
        '2인치_300' => ['item_code' =>  'SHAFT2', 'quantity' => intval(0)],
        '4인치_3000' => ['item_code' => 'SHAFT4', 'quantity' => intval(0)],
        '4인치_4500' => ['item_code' => 'SHAFT4', 'quantity' => intval(0)],
        '4인치_6000' => ['item_code' => 'SHAFT4', 'quantity' => intval(0)],
        '5인치_6000' => ['item_code' => 'SHAFT5', 'quantity' => intval(0)],
        '5인치_7000' => ['item_code' => 'SHAFT5', 'quantity' => intval(0)],
        '5인치_8200' => ['item_code' => 'SHAFT5', 'quantity' => intval(0)],
    ];

    // 부속자재 데이터에 품목 번호와 수량을 함께 저장
    $subs_data = [
        '각파이프_3000' => ['item_code' => 'RPIPE3000', 'quantity' => intval(0)],
        '각파이프_6000' => ['item_code' => 'RPIPE6000', 'quantity' => intval(0)],
        '앵글_2500' => ['item_code' => 'ANGLE2500', 'quantity' => intval(0)],
        '마환봉_3000' => ['item_code' => 'POLE3000', 'quantity' => intval(0)],
        '받침용앵글_380' => ['item_code' => 'BBR380', 'quantity' => intval(0)],
        '하단무게평철_12T' => ['item_code' => 'WEIGHT12T', 'quantity' => intval(0)],
    ];
	
	
	
	
위의 3개의 테이블을 생성했다. 이제 모델을 CRUD하는 과정을 코드로 작성하려고 한다.
실제 다른 테이블의 CRUD 작업을 하는 코드를 공유할테니 이를 참조해서 만들어주세요.

루트/models라는 폴더를 만든 후 아래의 파일을 만들것이다.
list.php  모델을 조회하는 기능
write_form.php 모델의 상세조회,입력,수정,삭제 기능
insert.php 서버에서 입력,수정,삭제 기능 수행
_row.php write_form.php에서 저장된 컬럼을 표현한 코드
_request.php insert.php에서 저장할 컬럼을 가져오는 코드

위의 5개의 파일을 만드는 것이다. 
다른 곳에서 사용하는 형태를 제공할것이니 이를 수정보완하면 좋겠다.

• list.php – 모델 목록 및 검색/신규등록
• write_form.php – 모델의 상세보기 및 입력/수정/삭제 폼
• insert.php – 서버측에서 실제 DB INSERT/UPDATE/DELETE 수행
• _request.php – 입력값을 변수로 할당
• _row.php – 조회된 데이터를 폼에 채워 넣기
의 구조로 CRUD 기능을 구현할 수 있습니다.

추후 프론트엔드에서는 모델 선택 시 해당 모델의 부품 및 부품서브를 트리형태(플러스/마이너스 토글)로 확장하여 보여줄 수 있도록 추가하면 BOM 형성 과정을 직관적으로 확인할 수 있습니다.


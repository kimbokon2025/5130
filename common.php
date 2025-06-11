<?php
function specialDate($inputDate) {
    // 날짜 형식을 DateTime 객체로 변환
    $date = new DateTime($inputDate);
    
    // 요일 배열 정의
    $weekdays = ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'];
    
    // 날짜 포맷 'Y년 m월 d일'로 변환
    $formattedDate = $date->format('Y년 n월 j일');
    
    // 요일 가져오기 (0: 일요일 ~ 6: 토요일)
    $weekday = $weekdays[$date->format('w')];
    
    // 최종 결과물 반환
    return $formattedDate . ' ' . $weekday;
}

// 날짜 공백이나 null 등 돌려주는 함수 
function NullCheckDate($requestdate) {
  if ($requestdate != "0000-00-00") {
    $request_year = date("Y", strtotime($requestdate));
    if ($request_year < 2010) {
      $requestdate = null;
    } else {
      $requestdate = date("Y-m-d", strtotime($requestdate));
    }
  } else {
    $requestdate = "";
  }
  return $requestdate;
}
// 날짜 공백이나 null 등 돌려주는 함수 
function isNotNull($datestr) {
  if ($datestr != "0000-00-00" && $datestr != "" && $datestr != null ) {
    $request_year = date("Y", strtotime($datestr));
    if ($request_year < 2010) {
      $datestr = null;
    } else {
      $datestr = date("Y-m-d", strtotime($datestr));
    }
  } else {
    $datestr = "";
  }
  return $datestr;
}

function is_string_valid($str) {
    if (is_null($str) || !isset($str) || trim($str) === '') {
        return false;
    } else {
        return true;
    }
}

 function echo_null($str) {	
	$strval = ($str == "") ? "&nbsp;&nbsp;&nbsp;" : $str ;
	return $strval;		
}

function trans_date($tdate) {
  if($tdate!="0000-00-00" and $tdate!="1900-01-01" and $tdate!="")  $tdate = date("Y-m-d", strtotime( $tdate) );
		else $tdate="";							
	return $tdate;	
}


function conv_num($num) {
$number = (float)str_replace(',', '', $num);
return $number;
}

// 연차일수 계산 회계년도말 기준 함수
function calculateAnnualLeave($hireDate, $fiscalYearEnd) {
    if (empty($hireDate)) {
        return ['G' => 0, 'H' => 0, 'I' => 0, 'J' => 0]; // 값이 없을 경우 기본값
    }

    $hireDate = new DateTime($hireDate);
    $fiscalYearEnd = new DateTime($fiscalYearEnd);
    $fiscalYearStart = clone $fiscalYearEnd;
    $fiscalYearStart->modify('-1 year +1 day');

    // 근속 연수 (G열)
    $serviceYears = ($hireDate < $fiscalYearEnd) ? $hireDate->diff($fiscalYearEnd)->y : 0;

    // 1년 미만 근속자 연차 비례 계산
    $daysWorked = $hireDate->diff($fiscalYearEnd)->days;

    $firstPeriod = 0;
	$alPeriod = 0;    // 년도별 연차
    if ($serviceYears < 1) {
		// 정확한 남은 개월 수 계산
		$diff = $hireDate->diff($fiscalYearEnd);
		$firstPeriod =  $diff->m  ; // 년도를 개월로 변환 후 합산		 
		// 만약 입사일이 1일이면 -1 보정
		if ($hireDate->format('d') == "1") {
			$firstPeriod -= 1;
		}			
        $alPeriod = floor(($daysWorked / 365) * 15 + 0.5); // 비례 계산하여 최소 연차 부여
    }
    else if ($serviceYears == 1) {
		// 정확한 남은 개월 수 계산
		$diff = $hireDate->diff($fiscalYearEnd);
		$firstPeriod =  11 - $diff->m  ; // 년도를 개월로 변환 후 합산		 
		// 만약 입사일이 1일이면 -1 보정
		if ($hireDate->format('d') == "1") {
			$firstPeriod -= 1;
		}		
		$alPeriod = 15 ;		
    }
	else if ($serviceYears > 1) {
    // 근속 연수별 연차 (I열)    
		$alPeriod = min(25, floor(($serviceYears - 1) / 2 + 15));
    }

    // 최종 연차 발생일수 (J열) = 1년 미만 연차 + 2년 미만 연차 + 근속연수별 연차
    $alSum = $firstPeriod + $alPeriod;

    return ['G' => $serviceYears, 'H' => $firstPeriod, 'I' => $alPeriod, 'J' => $alSum];
}

/**
 * 카테고리 이름을 기준으로 다음 레벨의 카테고리 이름 배열을 리턴하는 함수
 *
 * 인자가 없으면 category_l1 전체의 name 배열을,  
 * 인자 1개이면 해당 1단계의 name과 일치하는 항목의 id를 부모로 갖는 category_l2의 name 배열을,
 * 인자 2개이면 category_l1, category_l2를 이름으로 찾고, 그 하위 category_l3의 name 배열을,
 * 인자 3개이면 category_l1, category_l2, category_l3을 이름으로 찾고, 그 하위 category_l4의 name 배열을 리턴합니다.
 *
 * @param string ...$names 각 단계의 카테고리 이름 (선택적)
 * @return array 해당 단계의 카테고리 name 배열
 */
function getCategoryByName(...$names) {
    require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/mydb.php");
    $pdo = db_connect();
    global $DB;
    
    $levelCount = count($names);
    
    if ($levelCount === 0) {
        // 인자가 없으면 1단계 전체의 name만 리턴
        $sql = "SELECT name FROM {$DB}.category_l1 ORDER BY sortOrder, id";
        $st = $pdo->query($sql);
        return $st->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    // 1단계에서 검색
    $sql = "SELECT * FROM {$DB}.category_l1 WHERE name = :name LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->bindValue(':name', $names[0], PDO::PARAM_STR);
    $st->execute();
    $l1 = $st->fetch(PDO::FETCH_ASSOC);
    if (!$l1) {
        return []; // 해당 1단계 이름을 찾지 못하면 빈 배열 리턴
    }
    
    if ($levelCount === 1) {
        // 인자 1개: l1의 id를 부모로 하는 category_l2에서 name만 리턴
        $sql = "SELECT name FROM {$DB}.category_l2 WHERE parent_id = :parent_id ORDER BY sortOrder, id";
        $st = $pdo->prepare($sql);
        $st->bindValue(':parent_id', $l1['id'], PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    // 인자 2개: 먼저 l2 검색 (부모: l1)
    $sql = "SELECT * FROM {$DB}.category_l2 WHERE parent_id = :parent_id AND name = :name LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->bindValue(':parent_id', $l1['id'], PDO::PARAM_INT);
    $st->bindValue(':name', $names[1], PDO::PARAM_STR);
    $st->execute();
    $l2 = $st->fetch(PDO::FETCH_ASSOC);
    if (!$l2) {
        return [];
    }
    
    if ($levelCount === 2) {
        // 인자 2개: l2의 id를 부모로 하는 category_l3에서 name만 리턴
        $sql = "SELECT name FROM {$DB}.category_l3 WHERE parent_id = :parent_id ORDER BY sortOrder, id";
        $st = $pdo->prepare($sql);
        $st->bindValue(':parent_id', $l2['id'], PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    // 인자 3개: 먼저 l3 검색 (부모: l2)
    $sql = "SELECT * FROM {$DB}.category_l3 WHERE parent_id = :parent_id AND name = :name LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->bindValue(':parent_id', $l2['id'], PDO::PARAM_INT);
    $st->bindValue(':name', $names[2], PDO::PARAM_STR);
    $st->execute();
    $l3 = $st->fetch(PDO::FETCH_ASSOC);
    if (!$l3) {
        return [];
    }
    
    if ($levelCount === 3) {
        // 인자 3개: l3의 id를 부모로 하는 category_l4에서 name만 리턴
        $sql = "SELECT name FROM {$DB}.category_l4 WHERE parent_id = :parent_id ORDER BY sortOrder, id";
        $st = $pdo->prepare($sql);
        $st->bindValue(':parent_id', $l3['id'], PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    // 인자가 4개 이상이면 추가 단계는 없으므로 빈 배열 리턴
    return [];
}

/**
 * 모델 선택을 위한 select 태그를 생성하는 함수
 *
 * @param string $selectName select 태그의 name과 id에 사용될 변수명
 * @param mixed  $selectedValue 기존에 선택된 모델 값 (존재하면 해당 값이 선택됨)
 */
function selectModel($selectName, $selectedValue) {
    global $modelsList;

    // $modelsList가 전역에서 아직 설정되지 않았거나, 배열이 아니거나 비어있으면 JSON 파일에서 로드합니다.
    if (!isset($modelsList) || !is_array($modelsList) || empty($modelsList)) {
        $jsonFile = $_SERVER['DOCUMENT_ROOT'].'/models/models.json';
        if(file_exists($jsonFile)) {
            $jsonContent = file_get_contents($jsonFile);
            $modelsList  = json_decode($jsonContent, true);
            if(!is_array($modelsList)) {
                $modelsList = [];
            }
        }
    }

    // 예시: $row['major_category']로부터 대분류를 가져온다고 가정 (상황에 따라 수정하세요)
    $selectedMajor = isset($major_category) ? $major_category : '';

    // select 태그 출력 (name과 id에 $selectName 사용)
    echo '<select id="' . htmlspecialchars($selectName, ENT_QUOTES, 'UTF-8') . '" 
                 name="' . htmlspecialchars($selectName, ENT_QUOTES, 'UTF-8') . '" 
                 class="form-select mx-1 d-block w-auto viewmode viewNoBtn " 
                 style="font-size: 0.8rem; height: 32px;" 
				 data-readonly="true" 
          >';

    echo '<option value="">(모델 선택)</option>';

    foreach ($modelsList as $model) {
        // 대분류(major_category)가 비어있거나 일치할 때만 표시
        if ($selectedMajor === '' || $model['slatitem'] === $selectedMajor) {
            echo '<option value="' . htmlspecialchars($model['model_name'], ENT_QUOTES, 'UTF-8') . '"';
            
            // data-pair에 JSON 파일의 "pair" 값 삽입
            echo ' data-pair="' . htmlspecialchars($model['pair'], ENT_QUOTES, 'UTF-8') . '"';

            // 선택된 값과 일치하면 selected 처리
            if ($selectedValue === $model['model_name']) {
                echo ' selected';
            }
            
            echo '>';
            echo htmlspecialchars($model['model_name'], ENT_QUOTES, 'UTF-8');
            echo '</option>';
        }
    }

    echo '</select>';
}


?>
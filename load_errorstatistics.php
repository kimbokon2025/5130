 <?php 
if(!isset($_SESSION))      
		session_start(); 
if(isset($_SESSION["DB"]))
		$DB = $_SESSION["DB"] ;	
isset($_REQUEST["tabName"])  ? $tabName=$_REQUEST["tabName"] :  $tabName='';   // 신규데이터에 생성할때 임시저장키  
 
  if(isset($_REQUEST["load_confirm"]))   // 초기 당월 차트보이도록 변수를 저장하고 다시 부르면 실행되지 않도록 하기 위한 루틴
	 $load_confirm=$_REQUEST["load_confirm"];	 
  
  if(isset($_REQUEST["display_sel"]))   //목록표에 제목,이름 등 나오는 부분
	 $display_sel=$_REQUEST["display_sel"];	 
	 else
	   $display_sel='bar';	

  if(isset($_REQUEST["item_sel"]))   //목록표에 제목,이름 등 나오는 부분
	 $item_sel=$_REQUEST["item_sel"];	 
	 else
	   $item_sel='년도비교';	  

$sum=array(); 
	
$fromdate=date("Y",time()) ;
$fromdate=$fromdate . "-01-01";
$Transtodate=strtotime($todate);
$Transtodate=date("Y-m-d",$Transtodate);

$readIni = array();   // 환경파일 불러오기
$readIni = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "/steel/settings.ini",false);	
			  					   
$PO=$readIni['PO'];
$CR=$readIni['CR'];
$EGI=$readIni['EGI'];
$HL304=$readIni['HL304'];
$MR304=$readIni['MR304'];
$etcsteel=$readIni['etcsteel'];

$price_per_kg = [
    'CR' => $CR,
    'PO' => $PO,
    'EGI' => $EGI,
    '304 HL' => $HL304,
    '201 2B MR' => '3.0',
    '201 MR' => '3.0',
    '201 HL' => '2.8',
    '304 MR' => $MR304,
    'etcsteel' => $etcsteel
];


$sql = "SELECT EXTRACT(YEAR FROM outdate) AS year, EXTRACT(MONTH FROM outdate) AS month, item, spec, steelnum, bad_choice FROM " . $DB . ".steel WHERE (bad_choice IS NOT NULL AND bad_choice != '' AND bad_choice != '해당없음' ) AND outdate BETWEEN date('$fromdate') AND date('$Transtodate') ORDER BY year, month";	
 
$total_sum = 0;
$total_price = 0 ;
$weight = 0 ;

try{  
    // 레코드 전체 sql 설정
    $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh

		while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {			
			
			$item = trim($row["item"]);
			$spec = trim($row["spec"]);
			$steelnum = intval($row["steelnum"]);
			$bad_choice = $row["bad_choice"];

			$spec_parts = explode('*', $spec);
			$thickness = floatval($spec_parts[0]);
			$width = intval($spec_parts[1]);
			$length = intval($spec_parts[2]);

			$weight = $thickness * $width * $length/1000;
          if($steelnum !== 0)  // 수량이 1이상일때 잔재는 제외함
		  {
			if (array_key_exists($item, $price_per_kg)) {
				$price = intval($price_per_kg[$item]);
			} else {
				$price = intval($price_per_kg['etcsteel']);
			}
			$total_price = $weight * $price * $steelnum * 7.93;
			$total_sum += $total_price;
		  }
		  else
		  {
			$total_price = 0 ;
			$total_sum += $total_price;			  
		  }
		}

} catch (PDOException $Exception) {
    print "오류: ".$Exception->getMessage();
}	

// 최종 결과를 포맷팅하여 출력
$formatted_total_sum = number_format($total_sum, 0, '.', ',');

	echo '		  
		  <div class="bg-primary text-white ">
			<div class="card-body text-center">
			  <h6 class="card-title">부적합 비용</h6>
			  <span class="card-text mb-1">' . $formatted_total_sum . '원</span>
			</div>		  
	</div>';


	
	$sql = "SELECT EXTRACT(YEAR FROM outdate) AS year, EXTRACT(MONTH FROM outdate) AS month, item, spec, steelnum, bad_choice FROM " . $DB . ".steel WHERE (bad_choice IS NOT NULL AND bad_choice != '' AND bad_choice != '해당없음') AND outdate BETWEEN date('$fromdate') AND date('$Transtodate') ORDER BY year, month";

//  다음 월별/년도별 합계를 계산하기 위해 다음과 같이 코드를 수정합니다.
$monthly_totals = [];
 
$total_sum = 0;

try{  
    // 레코드 전체 sql 설정
    $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh

while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
	
	        $item = trim($row["item"]);
			$spec = trim($row["spec"]);
			$steelnum = $row["steelnum"];
			$bad_choice = $row["bad_choice"];

			$spec_parts = explode('*', $spec);
			$thickness = $spec_parts[0];
			$width = $spec_parts[1];
			$length = $spec_parts[2];

			$weight = $thickness * $width * $length/1000;
          if((int)$steelnum !== 0)  // 수량이 1이상일때 잔재는 제외함
		  {
			if (array_key_exists($item, $price_per_kg)) {
				$price = intval($price_per_kg[$item]);
			} else {
				$price = intval($price_per_kg['etcsteel']);
			}

			$total_price = ($weight * $price * $steelnum * 7.93);
			$total_sum += $total_price;
		  }
		  else
		  {
			$total_price = 0 ;
			$total_sum += $total_price;			  
		  }	

    $year = $row["year"];
    $month = $row["month"];

    if (!isset($monthly_totals["$year-$month"])) {
        $monthly_totals["$year-$month"] = 0;
    }

    $monthly_totals["$year-$month"] += $total_price;
}

} catch (PDOException $Exception) {
    print "오류: ".$Exception->getMessage();
}	
  
?>
    <div id="mychart" style="width: 100%; height: 200px;"></div>
	

<script>
$(document).ready(function(){	

      // PHP에서 계산된 월별/년도별 합계를 JavaScript로 전달
        const monthly_totals = <?php echo json_encode($monthly_totals); ?>;

        // x축 라벨 및 데이터 시리즈 준비
        const categories = Object.keys(monthly_totals);
        const data = Object.values(monthly_totals).map(total => parseFloat(total.toFixed(2)));

        // 그래프 생성
        Highcharts.chart('mychart', {
            chart: {
                type: 'column'
            },
            title: {
                text: '원자재 월별 부적합 비용'
            },
            xAxis: {
                categories: categories,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: '비용 (원)'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:6px">{point.key}</span><table>',
					pointFormatter: function() {
						return '<tr><td style="color:' + this.series.color + ';padding:0">' + this.series.name + ': </td>' +
							'<td style="padding:0"><b>' + Highcharts.numberFormat(this.y, 0, '.', ',') + ' 원</b></td></tr>';
					},
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0,
                    borderWidth: 0.5
                }
            },
            series: [{
                name: '부적합 비용',
                data: data
            }]
        });
      
});

	
function generateChartColors() {
  const baseColors = [
		'rgba(128, 128, 128, 0.2)',
		'rgba(54, 162, 235, 0.2)',
		'rgba(130, 130, 130, 0.2)',
		'rgba(75, 192, 192, 0.2)',
		'rgba(132, 132, 132, 0.2)',
		'rgba(205, 100, 25, 0.2)',
		'rgba(134, 134, 134, 0.2)',
		'rgba(95, 452, 60, 0.2)',
		'rgba(136, 136, 136, 0.2)',
		'rgba(255, 99, 132, 0.2)',
		'rgba(138, 138, 138, 0.2)',				
		'rgba(255, 159, 64, 0.2)' ,					
		'rgba(126, 126, 126, 0.2)',
		'rgba(54, 162, 235, 0.2)',
		'rgba(128, 128, 128, 0.2)',
		'rgba(75, 192, 192, 0.2)',
		'rgba(130, 130, 130, 0.2)',
		'rgba(205, 100, 25, 0.2)',
		'rgba(132, 132, 132, 0.2)',
		'rgba(95, 452, 60, 0.2)',
		'rgba(134, 134, 134, 0.2)',
		'rgba(255, 99, 132, 0.2)',
		'rgba(136, 136, 136, 0.2)',				
		'rgba(255, 159, 64, 0.2)'	
  ];

  // borderColor는 같은 색상이지만 투명도가 다릅니다.
  const borderColors = baseColors.map(color => color.replace(/0.2\)$/, '1)'));

  // backgroundColors와 borderColors를 순서대로 번갈아 가며 확장합니다.
  const backgroundColors = [], borderColorsExtended = [];
  for (let i = 0; i < baseColors.length; i++) {
    backgroundColors.push(baseColors[i], baseColors[i]); // 같은 색상을 두 번 추가
    borderColorsExtended.push(borderColors[i], borderColors[i]); // 같은 색상을 두 번 추가
  }

  return { backgroundColors, borderColors: borderColorsExtended };
}



   </script> 
  
  
  </body>

  
  </html>
  
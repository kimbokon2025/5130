<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

$tablename = 'output'; 
$today = date("Y-m-d");
$monthAgo = date("Y-m-d", strtotime("-1 month"));

$sql = "SELECT outdate, screen_m2, slat_m2 FROM chandj.$tablename WHERE outdate BETWEEN date('$monthAgo') AND date('$today') AND is_deleted = '0' ";

try {
    $stmh = $pdo->prepare($sql);
    $stmh->execute();
    $rows = $stmh->fetchAll(PDO::FETCH_ASSOC);	
	// var_dump($rows);

	$chartData = [];
	foreach ($rows as $row) {
		$date = date("Y-m-d", strtotime($row['outdate'] ?? ''));
		$area1 = intval(trim($row['screen_m2'] ?? '')); // Ensure the value is numeric
		$area2 = intval(trim($row['slat_m2'] ?? ''));   // Ensure the value is numeric

		// 해당 날짜 키가 없으면 0으로 초기화
		if (!isset($chartData[$date])) {
			$chartData[$date] = 0;
		}

		$chartData[$date] += $area1 + $area2;
	}


    $jsonChartData = json_encode($chartData);

} catch (PDOException $Exception) {
    print "오류: " . $Exception->getMessage();
}

// var_dump($jsonChartData);
?>

<div id="salesChart_screen" style="height: 200px;"></div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const screenData = <?= $jsonChartData ?>;
	// console.log(screenData);
    const sortedLabels = Object.keys(screenData).sort((a, b) => new Date(a) - new Date(b));
    const sortedData = sortedLabels.map(label => parseFloat(screenData[label]));

    Highcharts.chart('salesChart_screen', {
        chart: {
            type: 'line'
        },
        title: {
            text: '(스크린+스라트) 면적'
        },
		xAxis: {
			categories: sortedLabels,
			crosshair: true,
			labels: {
				formatter: function() {
					const date = new Date(this.value);
					return (date.getMonth() + 1) + '/' + date.getDate() ;
				}
			}
		},


        yAxis: {
            min: 0,
            title: {
                text: '면적 (m²)'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormatter: function() {
                return '<tr><td style="color:' + this.series.color + ';padding:0">' + this.series.name + ': </td>' +
                    '<td style="padding:0;"><b>' + Highcharts.numberFormat(this.y, 2, '.', ',') + ' m²</b></td></tr>';
            },
            footerFormat: '</table>',
            shared: true,
            useHTML: true,
            style: {
                padding: '1px',
                minWidth: '180px'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: true
            }
        },
        series: [{
            name: '면적',
            data: sortedData
        }]
    });
});
</script>

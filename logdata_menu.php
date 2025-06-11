<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");  

include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php' ;
$title_message = '메뉴접속 로그인 기록 ';
?>
<title> <?=$title_message?> </title>	
</head> 

<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/myheader.php'; ?>  

<?php     

// 기간을 정하는 구간	 
$todate=date("Y-m-d");   // 현재일자 변수지정   	
$common=" order by num desc limit 1000 ";  // 출고예정일이 현재일보다 클때 조건	

$sql = "select * from " . $DB . ".logdata_menu " . $common; 			
$nowday=date("Y-m-d");   // 현재일자 변수지정   
$counter=0;	

// $_REQUEST 배열에서 'search' 파라미터 확인
$search = isset($_REQUEST["search"]) ? $_REQUEST["search"] : '';


if($search=="" )
  {
	$sql = "select * from " . $DB . ".logdata_menu " . $common; 								 
   }
 elseif($search!="" )
	{				
		$sql=" select * from " . $DB . ".logdata_menu where (data like '%$search%') " . $common; 				
	 }   
			 
   $num_arr=array();
   $data_arr=array();

 try{      
   $stmh = $pdo->query($sql);            // 검색조건에 맞는글 stmh
   $rowNum = $stmh->rowCount();  


   while($row = $stmh->fetch(PDO::FETCH_ASSOC)) {	

			  $num_arr[$counter]=$row["num"];
			  $data_arr[$counter]=$row["data"];
   		
	   $counter++;	   
	 } 	 
   } catch (PDOException $Exception) {   
    print "오류: ".$Exception->getMessage();    
}  		

$total_row = $counter;	 
?>
		
<form id="board_form" name="board_form" method="post" >  

<div class="container">  	
	
		<input type="hidden" id="voc_alert" name="voc_alert" value="<?=$voc_alert?>" size="5" > 							
				
	<div class="card mt-1 mb-1">  
		<div class="card-body">  
		
		<div class="d-flex mt-3 mb-2 justify-content-center">  
			<h5>  <?=$title_message?> </h5> 
			<button type="button" class="btn btn-dark btn-sm mx-3"  onclick='location.reload();' title="새로고침"> <i class="bi bi-arrow-clockwise"></i> </button>  			
		</div>	
		<div class="d-flex mt-3 mb-1 justify-content-center  align-items-center">   				
			▷  <?= $total_row ?> &nbsp; 			
			<div class="inputWrap">
				<input type="text" id="search" class="form-control mx-1" style="width:150px;" name="search" autocomplete="off" value="<?=$search?>" placeholder="검색어" onkeydown="JavaScript:SearchEnter();" >
				<button class="btnClear"></button>
			</div>				
			<button id="searchBtn" type="button" class="btn btn-dark btn-sm mx-1"><i class="bi bi-search"></i> 검색 </button>			
		</div>  
	   
				
		</div> <!--card-body-->
   </div> <!--card -->
 				
	<div class="card mt-1 mb-1">  
		<div class="card-header  justify-content-center text-center">  
				<H5> 메뉴접속 로그  </H5>
		</div>		
			
		<div class="card-body">  
			 <div id="grid" >	  
			  </div>   
		</div> 	   
	
   </div> 	   
   </div> 	   
   
  </body>

  </html>	 
  
<!-- 페이지로딩 -->
<script>
// 페이지 로딩
$(document).ready(function(){	
    var loader = document.getElementById('loadingOverlay');
    loader.style.display = 'none';
});
</script>
  
<script>


$(document).ready(function(){
		
$("#searchBtn").click(function(){ 		  
	 document.getElementById('board_form').submit(); 
});		
	
var arr1 = <?php echo json_encode($num_arr);?> ;
var arr2 = <?php echo json_encode($data_arr);?> ; 

var rowNum = "<? echo $counter; ?>" ; 	
let row_count = 200;
const COL_COUNT = 2;

	const data = [];
	const columns = [];
	
	for (let i = 0; i < row_count; i += 1) {
	  const row = { name: i };
	  for (let j = 0; j < COL_COUNT; j += 1) {
		row[`num`] = arr1[i] ;						 						
		row['details'] = arr2[i] ;			

	  }
		data.push(row);
	}	


	 class CustomTextEditor {
	  constructor(props) {
		const el = document.createElement('input');
		const { maxLength } = props.columnInfo.editor.options;

		el.type = 'text';
		el.maxLength = maxLength;
		el.value = String(props.value);

		this.el = el;
	  }

	  getElement() {
		return this.el;
	  }

	  getValue() {
		return this.el.value;
	  }

	  mounted() {
		this.el.select();
	  }
	}	

const grid = new tui.Grid({
	  el: document.getElementById('grid'),
	  data: data,
	  bodyHeight: 700,					  					
	  columns: [ 				   
		{
		  header: '번호',
		  name: 'num',
		  sortingType: 'desc',
		  sortable: true,
		  width:150,
		  editor: {
			type: CustomTextEditor,
			options: {
			  maxLength: 20
			}
		  },	 		
		  align: 'center'
		},
		{
		  header: '로그인 시간 및 이력',
		  name: 'details',
		  width:1000,						  
		  editor: 'text',
			align: 'center'
		  // sortingType: 'desc',
		  // sortable: true,          
		  // editingEvent :  'Click'		  
		}
						
	  ],
	columnOptions: {
			resizable: true
		  },
	  rowHeaders: ['rowNum','checkbox'],
	  pageOptions: {
		useClient: false,
		perPage: 20
	  },
	});	
	
	grid.on('dblclick', (e) => {	      
   
});		
 	
	// 키처리
	grid.on('keydown', (e) => {	
	const keyCode = e.keyboardEvent.code;	
	
	console.log(e);
	console.log(keyCode);
	
	  // const cell = grid.getActiveCell();
	  // const editorType = grid.getEditorType(cell);

	  // if (editorType !== 'text') {
		// return;
	  // }

	  // if ((keyCode >= 48 && keyCode <= 90) || // 숫자 및 알파벳
		  // (keyCode >= 96 && keyCode <= 105) || // 숫자 패드
		  // (keyCode === 229)) { // 한글 입력
		// cell.startEditing();
	  // }
   
});		
 	
		
	// grid.on('focusChange', function(ev) {
	  // const { rowKey, columnName } = ev;	  
	  // const cell = grid.getValue(rowKey, columnName);
	  // console.log(cell);
	  // console.log(rowKey);
	  // console.log(columnName);
	  // grid.startEditing(rowKey, columnName, false);
	// });

	// grid.on('beforeKeyDown', function(ev) {
	  // const { keyCode } = ev;
	  // const cell = grid.getActiveCell();
	  // const editorType = grid.getEditorType(cell);

	  // if (editorType !== 'text') {
		// return;
	  // }

	  // if ((keyCode >= 48 && keyCode <= 90) || // 숫자 및 알파벳
		  // (keyCode >= 96 && keyCode <= 105) || // 숫자 패드
		  // (keyCode === 229)) { // 한글 입력
		// cell.startEditing();
	  // }
	// });


});



   </script> 
 


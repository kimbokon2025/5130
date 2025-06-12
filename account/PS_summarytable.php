<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/session.php");

// HTML 헤더 로드
include $_SERVER['DOCUMENT_ROOT'] . '/load_header.php';

?>

<script>
var loader = document.getElementById('loadingOverlay');
if(loader) loader.style.display = 'none';

$(document).ready(function () {
	// showShiningText('프로그램 개발 중입니다...', '80px');
	showShiningText();
});
</script>

</body>
</html> 
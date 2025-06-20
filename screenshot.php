<body>
<script src="./js/html2canvas.js"></script>
<!-- 전체 부분-->
<button onclick=bodyShot()>bodyShot</button>
<!-- 일부분 부분-->
<button onclick=partShot()>partShot</button>
<body>
<div class="container" id='container'>
<!-- 로컬에서 불러온 파일 -->
<img src="./img/title_as.png">

<img src="./img/ok.png">
<!-- <img src="https://source.unsplash.com/user/erondu/400x400"> -->
</div>
<p> dkasdfjkasdfkadjfskasdf </p>
</body>
<!-- 결과화면을 그려줄 canvas -->
<canvas id="canvas" width="900" height="700"style="border:1px solid #d3d3d3;"></canvas>


<script>
function bodyShot() {
//전체 스크린 샷하기
html2canvas(document.body)
//document에서 body 부분을 스크린샷을 함.
.then(
function (canvas) {
//canvas 결과값을 drawImg 함수를 통해서
//결과를 canvas 넘어줌.
//png의 결과 값
drawImg(canvas.toDataURL('image/png'));

//appendchild 부분을 주석을 풀게 되면 body
//document.body.appendChild(canvas);

//특별부록 파일 저장하기 위한 부분.
saveAs(canvas.toDataURL(), 'file-name.png');
}).catch(function (err) {
console.log(err);
});
}

function partShot() {
//특정부분 스크린샷
html2canvas(document.getElementById("container"))
//id container 부분만 스크린샷
.then(function (canvas) {
//jpg 결과값
drawImg(canvas.toDataURL('image/jpeg'));
//이미지 저장
saveAs(canvas.toDataURL(), 'file-name.jpg');
}).catch(function (err) {
console.log(err);
});
}

function drawImg(imgData) {
console.log(imgData);
//imgData의 결과값을 console 로그롤 보실 수 있습니다.
return new Promise(function reslove() {
//내가 결과 값을 그릴 canvas 부분 설정
var canvas = document.getElementById('canvas');
var ctx = canvas.getContext('2d');
//canvas의 뿌려진 부분 초기화
ctx.clearRect(0, 0, canvas.width, canvas.height);

var imageObj = new Image();
imageObj.onload = function () {
ctx.drawImage(imageObj, 10, 10);
//canvas img를 그리겠다.
};
imageObj.src = imgData;
//그릴 image데이터를 넣어준다.

}, function reject() { });

}
function saveAs(uri, filename) {
var link = document.createElement('a');
if (typeof link.download === 'string') {
link.href = uri;
link.download = filename;
document.body.appendChild(link);
link.click();
document.body.removeChild(link);
} else {
window.open(uri);
}
}
</script>

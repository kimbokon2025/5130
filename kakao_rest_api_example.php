<?php //공통 : Config
$REST_API_KEY   = "a05cf8e20a592a3c7d518203a097f08e"; // 내 애플리케이션 > 앱 설정 > 요약 정보
$CLIENT_SECRET  = "lPE16DCvWsduAWTue9b51vocFCxxA9Y8"; // 내 애플리케이션 > 제품 설정 > 카카오 로그인 > 보안
$REDIRECT_URI   = urlencode("http://8440.co.kr/kakao_rest_api_example.php");
?>


<?php //공통 : API Call Function
function Call($callUrl, $method, $headers = array(), $data = array(), $returnType="jsonObject")
{
    echo "<pre>".$callUrl."</pre>";
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $callUrl);
        if ($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($ch, CURLOPT_POST, false);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HTTP200ALIASES, array(400));
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo "<pre>".$status_code.":".$response."</pre>";
        
        if ($returnType=="jsonObject") return json_decode($response);
        else return $response;     
    } catch (Exception $e) {
        echo $e;
    }    
}
?>

<h1>1. 카카오 로그인 및 프로필 조회 예제</h1>
<pre>
- [KOE101, KOE004] 내 애플리케이션>제품 설정>카카오 로그인 > 활성화 설정 : ON
- [KOE006] 내 애플리케이션>제품 설정>카카오 로그인 > Redirect URI : http://8440.co.kr/kakao_rest_api_example.php
</pre>
<a href="https://kauth.kakao.com/oauth/authorize?client_id=<?=$REST_API_KEY?>&response_type=code&redirect_uri=<?=$REDIRECT_URI?>"><img src="//k.kakaocdn.net/14/dn/btqCn0WEmI3/nijroPfbpCa4at5EIsjyf0/o.jpg" width="222" /></a>

<?php 
if (!isset($_GET["code"])) die(); //code 받기 전이면 수행 안함

function getToken($REST_API_KEY, $REDIRECT_URI, $CLIENT_SECRET) //로그인 : 토큰 조회
{
    $code = $_GET["code"]; //Redirect URI로 돌아올 때, 받아온 파라메터 
    $callUrl = "https://kauth.kakao.com/oauth/token?grant_type=authorization_code&client_id=".$REST_API_KEY."&redirect_uri=".$REDIRECT_URI."&code=".$code."&client_secret=".$CLIENT_SECRET;
    $res = Call($callUrl, "POST");
    if($res->error_code == "KOE320") die("[KOE320] code 받은 후, 새로고침하면 code 재사용 불가 에러 : 다시 로그인 시도 할 것");
    return $res;
}
function getProfile($ACCESS_TOKEN) //로그인 : 플로필 조회
{
    $callUrl = "https://kapi.kakao.com/v2/user/me";
    $headers[] = "Authorization: Bearer ".$ACCESS_TOKEN;
    $res = Call($callUrl, "POST", $headers);
    if($res->properties == "") die("내 애플리케이션>제품 설정>카카오 로그인> 동의항목 : profile 필수 설정");
    return $res;
}    
?>

<?php 
$ACCESS_TOKEN = getToken($REST_API_KEY, $REDIRECT_URI, $CLIENT_SECRET)->access_token; // 토큰 조회 및 토큰 저장
?>
<?php 
getProfile($ACCESS_TOKEN); // 프로필 조회
?>

<h1>2. 카카오 친구목록 조회 및 카카오톡 메시지 예제</h1>
<pre>
* 로그인한 사용자의 전체 친구 목록을 표시하고 선택한 후, 발송하는 방식은 카카오 링크 참조 (REST API 불가)
* 친구에게 테스트 메시지 발송을 위해서는 "내 애플리케이션>앱 설정>팀 관리"에 카톡친구를 등록해야함.
* 친구 API, 메시지 API는 카톡 친구이며 발신자, 수신자 모두 앱에 로그인하여 <font color="red">권한 동의</font>한 경우만 사용 가능

- [KOE205] 내 애플리케이션>제품 설정>카카오 로그인> 동의항목 : friends,talk_message 선택 동의 or 이용 중 동의 설정
</pre>
<a href="https://kauth.kakao.com/oauth/authorize?client_id=<?=$REST_API_KEY?>&response_type=code&redirect_uri=<?=$REDIRECT_URI?>&scope=friends,talk_message" class="btn btn-primary"><h2>친구목록 조회와 메세지 발송 권한 획득</h2></a>

<?php
   function sendMessage($ACCESS_TOKEN, $data)
    {
        $callUrl = "https://kapi.kakao.com/v2/api/talk/memo/default/send";
        $headers = array('Content-type:application/x-www-form-urlencoded;charset=utf-8');
        $headers[] = "Authorization: Bearer " . $ACCESS_TOKEN;
        return Call($callUrl, "POST", $headers, $data);
    }

    function sendScrap($ACCESS_TOKEN, $request_url)
    {
        $callUrl = "https://kapi.kakao.com/v2/api/talk/memo/scrap/send";
        $headers = array('Content-type:application/x-www-form-urlencoded;charset=utf-8');
        $data = 'request_url='.urlencode($request_url);
        $headers[] = "Authorization: Bearer " . $ACCESS_TOKEN;
        return Call($callUrl, "POST", $headers, $data);
    }

    function sendCustomTemplate($ACCESS_TOKEN, $template_id)
    {
        $callUrl = "https://kapi.kakao.com/v2/api/talk/memo/send?template_id=".$template_id;
        $headers = array('Content-type:application/x-www-form-urlencoded;charset=utf-8');
        $headers[] = "Authorization: Bearer " . $ACCESS_TOKEN;
        return Call($callUrl, "POST", $headers);
    }

    function getFriendsList($ACCESS_TOKEN)
    {
        $callUrl = "https://kapi.kakao.com/v1/api/talk/friends";
        $headers = array();
        $headers[] = "Authorization: Bearer " . $ACCESS_TOKEN;
        return Call($callUrl, "GET", $headers);
    }

    function sendMessageForFriend($ACCESS_TOKEN, $receiver_uuids, $template)
    {
        $callUrl = "https://kapi.kakao.com/v1/api/talk/friends/message/default/send";
        $headers = array('Content-type:application/x-www-form-urlencoded;charset=utf-8');
        $headers[] = "Authorization: Bearer " . $ACCESS_TOKEN;
        $data = $template."&receiver_uuids=".json_encode($receiver_uuids);
        return Call($callUrl, "POST", $headers, $data);
    }

    function sendScrapForFriend($ACCESS_TOKEN, $receiver_uuids, $request_url)
    {
        $callUrl = "https://kapi.kakao.com/v1/api/talk/friends/message/scrap/send";
        $headers = array('Content-type:application/x-www-form-urlencoded;charset=utf-8');
        $headers[] = "Authorization: Bearer " . $ACCESS_TOKEN;
        $data = 'request_url='.urlencode($request_url)."&receiver_uuids=".json_encode($receiver_uuids);
        return Call($callUrl, "POST", $headers, $data);
    }

    function sendCustomTemplateForFriend($ACCESS_TOKEN, $receiver_uuids, $template_id)
    {
        $callUrl = "https://kapi.kakao.com/v1/api/talk/friends/message/send?template_id=".$template_id;
        $headers = array('Content-type:application/x-www-form-urlencoded;charset=utf-8');
        $headers[] = "Authorization: Bearer " . $ACCESS_TOKEN;
        $data = "receiver_uuids=".json_encode($receiver_uuids);
        return Call($callUrl, "POST", $headers, $data);
    }
?>

<?php

// "image_url": "http://mud-kage.kakao.co.kr/dn/NTmhS/btqfEUdFAUf/FjKzkZsnoeE4o19klTOVI1/openlink_640x640s.jpg",
$data_feed = 'template_object={
        "object_type": "feed",
        "content": {
                "title": "미래기업 카톡메시지",
                "description": "월요일입니다. 금일부터 마스크 일부 해제입니다. \n 이젠 카톡메시지 역시 미래기업.",
                
                "image_url": "http://8440.co.kr/img/200.jpg",
                "image_width": 640,"image_height": 640,
                "link": {"web_url": "http://8440.co.kr","mobile_web_url": "http://8440.co.kr"}
        },
        "social": {"like_count": 100,"comment_count": 100,"shared_count": 100},
        "buttons": [
            {
                "title": "(주)미래기업 카톡",
                "link": {"web_url": "http://8440.co.kr","mobile_web_url": "http://8440.co.kr"}
            }
        ]
    }';

// 로그인 승인되면 바로 메시지 보내는 것 중지 아래 명령어
// sendMessage($ACCESS_TOKEN, $data_feed);
// sendScrap($ACCESS_TOKEN,"http://8440.co.kr"); //도메인에 등록된 주소 설정 시, 스크랩서버가 접근하여 수집한 미리보기 내용(og태그)을 메시지에 표시함.
//sendCustomTemplate($ACCESS_TOKEN,"88645"); //도구 > 메시지 템플릿에서 템플릿 등록 필수

$res = getFriendsList($ACCESS_TOKEN);

var_dump($res);

$receiver_uuids = array();
foreach ($res->elements as $obj)
{
    echo $obj->profile_nickname;
	if($obj->profile_nickname!="미래 안현섭차장")  // 안현섭 차장이 아닐경우 메시지 보내기
		$receiver_uuids[] = $obj->uuid;
}

echo "카톡 친구 : " . count($receiver_uuids) ;

// 카톡친구 수가 0보다 크면 count($receiver_uuids[]
if(count($receiver_uuids)>0)
 sendMessageForFriend($ACCESS_TOKEN, $receiver_uuids, $data_feed);
// sendScrapForFriend($ACCESS_TOKEN, $receiver_uuids,"http://8440.co.kr"); //도메인에 등록된 주소 설정 시, 스크랩서버가 접근하여 수집한 미리보기 내용(og태그)을 메시지에 표시함.
// sendCustomTemplateForFriend($ACCESS_TOKEN, $obj->uuid,"88645"); //도구 > 메시지 템플릿에서 템플릿 등록 필수

?>

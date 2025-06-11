<?php
if(!isset($_SESSION))      
		session_start(); 
$level = isset($_SESSION["level"]) ? $_SESSION["level"] : '';
$user_name = isset($_SESSION["name"]) ? $_SESSION["name"] : '';
$user_id = isset($_SESSION["userid"]) ? $_SESSION["userid"] : '';
$DB = isset($_SESSION["DB"]) ? $_SESSION["DB"] : 'chandj';
$eworks_lv = isset($_SESSION["eworks_lv"]) ? $_SESSION["eworks_lv"] : '';
$eworks_level = isset($_SESSION["eworks_level"]) ? $_SESSION["eworks_level"] : '';
$position = isset($_SESSION["position"]) ? $_SESSION["position"] : '';
$mycompany = $_SESSION["mycompany"] ?? '';
$mypart = $_SESSION["mypart"] ?? '';
$authority = $_SESSION["authority"] ?? '';  // ACCOUNT 는 회계권한부여

// 결재정보

$first_approval_id = $_SESSION["first_approval_id"] ?? ''; 
$first_approval_name = $_SESSION["first_approval_name"] ?? ''; 

$WebSite = "https://5130.co.kr/";
$root_dir = "https://5130.co.kr";
?>
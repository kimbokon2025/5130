<?php
session_start();
?> 
 
 <html>
 <head>
 <meta charset="UTF-8">
 <link rel="stylesheet" type="text/css" href="./css/common.css">
<link rel="stylesheet" type="text/css" href="./css/func.css">	 
 </head>

<?php
  function latest_article($table,$loop,$char_limit)
  {
    require_once("../lib/mydb.php");
    $pdo=db_connect();

      try{
          $sql="select * from chandj.$table order by num desc limit $loop";
          $stmh=$pdo->query($sql);

          While($row=$stmh->fetch(PDO::FETCH_ASSOC))
          {
              $num=$row["num"];
              $len_subject=strlen($row["subject"]);
              $subject=$row["subject"];
              if($len_subject>$char_limit)
              {
                  $subject=mb_substr($row["subject"],0,$char_limit,'utf-8');
                  $subject=$subject . "...";   // 글자수가 초과하면 ...으로 표기됨
              }
              $regist_day=substr($row["regist_day"],0,10);
              $page=1;
			  
              echo("<div class='col1'> <a href='./$table/view.php?num=$num&page=$page'>$subject</a>
              </div><div class='col2'>$regist_day</div>
              <div class='clear'></div>");

          } 
      } catch (PDOException $Exception) {
          print "오류: ". $Exception->getMessage();

      }
  }
  ?>
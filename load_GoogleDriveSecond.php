<?php
// 첨부파일 있는 것 불러오기 
$savefilename_arr=array(); 
$realname_arr=array(); 
$item = 'attached';

if(empty($num))
{
	// 신규데이터 작성시 키값지정 parentid값이 없으면 데이터 저장안됨 2024_11_21_14_30_15_123 형태
	$microtime = microtime(true);
	$milliseconds = sprintf("%03d", ($microtime - floor($microtime)) * 1000);
	$timekey = date("Y_m_d_H_i_s", $microtime) . '_' . $milliseconds;

	$SearchKey = $timekey;
}
  else
	  $SearchKey = $num;

$sql = "SELECT * FROM {$DB}.picuploads WHERE tablename=? AND item = ? AND parentnum = ?";
try {
    $stmh = $pdo->prepare($sql);
    $stmh->execute([$tablename, $item, $SearchKey]);
    while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
        $picname = $row["picname"];
        $realname = $row["realname"];
        $realname_arr[] = $realname; // realname 배열에 추가

        if (preg_match('/^[a-zA-Z0-9_-]{25,}$/', $picname)) {
            // Google Drive 파일 ID로 처리
            $fileId = $picname;

            try {
                // Google Drive 파일 정보 가져오기
                $file = $service->files->get($fileId, ['fields' => 'webViewLink, thumbnailLink']);
                $thumbnailUrl = $file->thumbnailLink ?? "https://drive.google.com/uc?id=$fileId";
                $webViewLink = $file->webViewLink;
                $savefilename_arr[] = [
                    'thumbnail' => $thumbnailUrl,
                    'link' => $webViewLink,
                    'fileId' => $fileId,
                    'realname' => $realname // realname 포함
                ];
            } catch (Exception $e) {
                error_log("Google Drive 파일 정보 가져오기 실패: " . $e->getMessage());
                $savefilename_arr[] = [
                    'thumbnail' => "https://drive.google.com/uc?id=$fileId",
                    'link' => null,
                    'fileId' => $fileId,
                    'realname' => $realname // realname 포함
                ];
            }
        } else {
            // Google Drive에서 파일 이름으로 검색
            try {
                $query = sprintf("name='%s' and trashed=false", addslashes($picname)); // 파일 이름으로 검색
                $response = $service->files->listFiles([
                    'q' => $query,
                    'fields' => 'files(id, webViewLink, thumbnailLink)',
                    'pageSize' => 1
                ]);

                if (count($response->files) > 0) {
                    $file = $response->files[0];
                    $fileId = $file->id; // 검색된 파일의 ID
                    $thumbnailUrl = $file->thumbnailLink ?? "https://drive.google.com/uc?id=$fileId";
                    $webViewLink = $file->webViewLink;
                    $savefilename_arr[] = [
                        'thumbnail' => $thumbnailUrl,
                        'link' => $webViewLink,
                        'fileId' => $fileId,
                        'realname' => $realname // realname 포함
                    ];

                    // 데이터베이스 업데이트: 검색된 파일 ID 저장
                    $updateSql = "UPDATE {$DB}.picuploads SET picname = ? WHERE item = ? AND parentnum = ? AND picname = ?";
                    $updateStmh = $pdo->prepare($updateSql);
                    $updateStmh->execute([$fileId, $item, $SearchKey, $picname]);
                } else {
                    error_log("Google Drive에서 파일을 찾을 수 없습니다: " . $picname);
                    $savefilename_arr[] = [
                        'thumbnail' => null,
                        'link' => null,
                        'fileId' => null,
                        'realname' => $realname // realname 포함
                    ];
                }
            } catch (Exception $e) {
                error_log("Google Drive 파일 검색 실패: " . $e->getMessage());
                $savefilename_arr[] = [
                    'thumbnail' => null,
                    'link' => null,
                    'fileId' => null,
                    'realname' => $realname // realname 포함
                ];
            }
        }
    }
} catch (PDOException $Exception) {
    print "오류: " . $Exception->getMessage();
}

// 첨부이미지 불러오기 
$saveimagename_arr=array(); 
$realimagename_arr=array(); 
$item = 'image';

$sql = "SELECT * FROM {$DB}.picuploads WHERE tablename=? AND item = ? AND parentnum = ?";
try {
    $stmh = $pdo->prepare($sql);
    $stmh->execute([$tablename, $item, $SearchKey]);
    while ($row = $stmh->fetch(PDO::FETCH_ASSOC)) {
        $picname = $row["picname"];
        $realname = $row["realname"];
        $rotation = $row["rotation"];
        $realimagename_arr[] = $realname; // realname 배열에 추가        

        if (preg_match('/^[a-zA-Z0-9_-]{25,}$/', $picname)) {
            // Google Drive 파일 ID로 처리
            $fileId = $picname;

            try {
                // Google Drive 파일 정보 가져오기
                $file = $service->files->get($fileId, ['fields' => 'webViewLink, thumbnailLink']);
                $thumbnailUrl = $file->thumbnailLink ?? "https://drive.google.com/uc?id=$fileId";
                $webViewLink = $file->webViewLink;
                $saveimagename_arr[] = [
                    'thumbnail' => $thumbnailUrl,
                    'link' => $webViewLink,
                    'fileId' => $fileId,
					'realname' => $realname, // realname 포함
					'rotation' => $rotation // rotation 정보
                ];
            } catch (Exception $e) {
                error_log("Google Drive 파일 정보 가져오기 실패: " . $e->getMessage());
                $saveimagename_arr[] = [
                    'thumbnail' => "https://drive.google.com/uc?id=$fileId",
                    'link' => null,
                    'fileId' => $fileId,
					'realname' => $realname, // realname 포함
					'rotation' => $rotation // rotation 정보
                ];
            }
        } else {
            // Google Drive에서 파일 이름으로 검색
            try {
                $query = sprintf("name='%s' and trashed=false", addslashes($picname)); // 파일 이름으로 검색
                $response = $service->files->listFiles([
                    'q' => $query,
                    'fields' => 'files(id, webViewLink, thumbnailLink)',
                    'pageSize' => 1
                ]);

                if (count($response->files) > 0) {
                    $file = $response->files[0];
                    $fileId = $file->id; // 검색된 파일의 ID
                    $thumbnailUrl = $file->thumbnailLink ?? "https://drive.google.com/uc?id=$fileId";
                    $webViewLink = $file->webViewLink;
                    $saveimagename_arr[] = [
                        'thumbnail' => $thumbnailUrl,
                        'link' => $webViewLink,
                        'fileId' => $fileId,
                        'realname' => $realname, // realname 포함
                        'rotation' => $rotation // rotation 정보
                    ];

                    // 데이터베이스 업데이트: 검색된 파일 ID 저장
                    $updateSql = "UPDATE {$DB}.picuploads SET picname = ? WHERE item = ? AND parentnum = ? AND picname = ?";
                    $updateStmh = $pdo->prepare($updateSql);
                    $updateStmh->execute([$fileId, $item, $SearchKey, $picname]);
                } else {
                    error_log("Google Drive에서 파일을 찾을 수 없습니다: " . $picname);
                    $saveimagename_arr[] = [
                        'thumbnail' => null,
                        'link' => null,
                        'fileId' => null,
                        'realname' => $realname, // realname 포함
                        'rotation' => $rotation // rotation 정보
                    ];
                }
            } catch (Exception $e) {
                error_log("Google Drive 파일 검색 실패: " . $e->getMessage());
                $saveimagename_arr[] = [
                    'thumbnail' => null,
                    'link' => null,
                    'fileId' => null,
					'realname' => $realname, // realname 포함
					'rotation' => $rotation // rotation 정보
                ];
            }
        }
    }
} catch (PDOException $Exception) {
    print "오류: " . $Exception->getMessage();
} 
 
?>
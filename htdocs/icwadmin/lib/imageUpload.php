<?php

//변수 정리
$arrRtn     = array(
    'code'  => 500,
    'msg'   => ''
);

try {
    if (!isset($_FILES['upload']['error']) || is_array($_FILES['upload']['error'])) {
        $arrRtn['code'] = 501;
        throw new Exception('이미지 등록 중 오류가 발생했습니다.(code 501)');
    }

    switch ($_FILES['upload']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            //throw new RuntimeException('No file sent.');
            break;
        case UPLOAD_ERR_INI_SIZE:
        default:
            $arrRtn['code'] = 503;
            throw new Exception('이미지 등록 중 오류가 발생했습니다.(code 503)');
    }

    if (empty($_FILES['upload']['error'])) {
        // You should also check filesize here.

        // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
                $finfo->file($_FILES['upload']['tmp_name']),
                array(
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif'
                ),
                true
            )) {
            $arrRtn['code'] = 505;
            throw new Exception('이미지는 jpg, jpeg, png, gif 파일만 가능합니다.(code 505)');
        }

        // You should name it uniquely.
        // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
        $yyyymmdd           = date('Ymd');
        $hhiiss             = date('his');
        $rand               = rand(1, 9999);
        $upload_filename    = "/bbs_notice_{$yyyymmdd}_{$hhiiss}_{$rand}";
        $filename           = "{$upload_filename}.{$ext}";
        $path               = __DIR__ .'/../../uploads';
        $user_path          = $path .'/bbs';
        if (!is_dir($user_path)) {
            mkdir($user_path, 0700, true);
        }

        if (!move_uploaded_file(
            $_FILES['upload']['tmp_name'],
            sprintf($user_path .'/%s.%s',
                $upload_filename,
                $ext
            )
        )) {
            $arrRtn['code'] = 506;
            throw new Exception('이미지 등록 중 오류가 발생했습니다.(code 506)');
        }
    }

} catch (Exception $e) {
    $arrRtn['code'] = $e->getCode();
    $arrRtn['msg']  = $e->getMessage();

} finally {
    echo '{"filename" : "'.$filename.'", "uploaded" : "1", "url" : "/uploads/bbs/'.$filename.'"}';
}
?>
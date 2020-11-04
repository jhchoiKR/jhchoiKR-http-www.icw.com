<?php
//config
require_once __DIR__ .'/../inc/config.php';

//로그인 체크
check_login();

$arrRtn = array(
    'code'  => 500,
    'msg'   => ''
);

try {
    //트랜잭션
    $_mysqli->begin_transaction();

    //파라미터
    $seq     = !empty($_POST['adminSeq']) ? $_POST['adminSeq'] : 0;

    //변수
    $seqData = implode(',', $seq);

    //필수 파라미터 체크
    if(!is_numeric($seq)) {
        $seq = 0;
    }

    $query = "
        DELETE FROM 
            ADMINS
        WHERE 1=1
            AND SEQ IN ({$seqData})
    ";
    //p($query);
    $result = $_mysqli->query($query);
    if (!$result) {
        $code           = 501;
        $msg            = "삭제 중 오류가 발생했습니다.(code {$code})\n관리자에게 문의해 주세요.";
        throw new mysqli_sql_exception($msg, $code);
    }

    //커밋
    $_mysqli->commit();

    //성공
    $arrRtn['code']    = 200;
    $arrRtn['msg']     = "삭제되었습니다.";

} catch (mysqli_sql_exception $e) {
    $_mysqli->rollback();
    $arrRtn['code']    = $e->getCode();
    $arrRtn['msg']     = $e->getMessage();

} catch (Exception $e) {
    $_mysqli->rollback();
    $arrRtn['code']    = $e->getCode();
    $arrRtn['msg']     = $e->getMessage();

} finally {
    echo json_encode($arrRtn);
}
?>


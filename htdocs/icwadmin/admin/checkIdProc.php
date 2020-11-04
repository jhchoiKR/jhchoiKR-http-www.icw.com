<?php

//config
require_once __DIR__ .'/../inc/config.php';

//로그인 체크
check_login();

$arrRtn = array(
    'code'   => 500,
    'msg'    => '',
    'result' => ''
);

try {
    //트랜잭션
    $_mysqli->begin_transaction();

    //파라미터
    $user_id = $_POST['user_id'] ? $_POST['user_id'] : '';

    $query = "
        SELECT 
            COUNT(1) AS CNT
        FROM ADMINS
        WHERE 1=1
            AND USER_ID = '{$user_id}'
    ";
    //p($query);
    $result = $_mysqli->query($query);
    if (!$result) {
        $code           = 501;
        $msg            = "아이디 조회 중 오류가 발생했습니다.(code {$code})\n관리자에게 문의해 주세요.";
        throw new mysqli_sql_exception($msg, $code);
    }

    $row = $result->fetch_array();
    $arrRtn['result'] = $row['CNT'];

} catch (mysqli_sql_exception $e) {
    $_mysqli->rollback();
    $arrRtn['code']     = $e->getCode();
    $arrRtn['msg']      = $e->getMessage();

} catch (Exception $e) {
    $_mysqli->rollback();
    $arrRtn['code']     = $e->getCode();
    $arrRtn['msg']      = $e->getMessage();

} finally {
    echo json_encode($arrRtn);
}
?>
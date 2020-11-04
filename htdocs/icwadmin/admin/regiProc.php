<?php
//config
require_once __DIR__ .'/../inc/config.php';

//로그인 체크
check_login();

//변수 정리
$arrRtn        = array(
    'code'  => 500,
    'msg'   => ''
);

try {
    //트랜잭션
    $_mysqli->begin_transaction();

    //파라미터 정리
    $user_id        = isset($_POST['inputId'])      ? $_POST['inputId']     : '';
    $user_pw        = isset($_POST['inputPw'])      ? $_POST['inputPw']     : '';
    $user_nm        = isset($_POST['inputName'])    ? $_POST['inputName']   : '';
    $email          = isset($_POST['inputEmail'])   ? $_POST['inputEmail']  : '';
    $phone          = isset($_POST['inputPhone'])   ? $_POST['inputPhone']  : '';
    $use_yn         = isset($_POST['inputUse'])     ? $_POST['inputUse']    : '';

    //세션
    $_se_admin_seq        = SE_SEQ;
    $_se_admin_id         = SE_ID;
    $_se_admin_nm         = SE_NM;

    //변수
    $ip         = IP;

    //필수 파라미터 체크
    if (empty($user_id) || empty($user_pw) || empty($user_nm)) {
        $code    = 404;
        $msg     = '필수 항목을 입력해 주세요';
        throw new Exception($msg, $code);
    }

    //DB 변수
    $pw         = password_hash($user_pw, PASSWORD_DEFAULT);
    $user_id    = $_mysqli->real_escape_string($user_id);
    $user_nm    = $_mysqli->real_escape_string($user_nm);
    $email      = $_mysqli->real_escape_string($email);
    $phone      = $_mysqli->real_escape_string($phone);
    $use_yn     = $_mysqli->real_escape_string($use_yn);

    //DB
    $query  = "
        INSERT INTO ADMINS
            (USER_ID, USER_PW, USER_NM, EMAIL, PHONE,
             USE_YN, CREATED_BY, CREATED_IP)
        VALUES
            ('{$user_id}', '{$pw}', '{$user_nm}', '{$email}', '{$phone}',
             '{$use_yn}', '{$_se_admin_nm}', '{$ip}')
    ";
    //p($query);
    $result = $_mysqli->query($query);
    if (!$result) {
        $code           = 501;
        $msg            = "등록 중 오류가 발생했습니다.(code {$code})\n관리자에게 문의해 주세요.";
        throw new mysqli_sql_exception($msg, $code);
    }

    //커밋
    $_mysqli->commit();

    //성공
    $arrRtn['code']    = 200;
    $arrRtn['msg']     = "등록되었습니다.";

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

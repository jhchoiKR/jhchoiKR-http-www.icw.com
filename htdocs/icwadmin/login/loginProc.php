<?php
//config
require_once __DIR__ .'/../inc/config.php';
//변수 정리
$arrRtn        = array(
    'code'  => 500,
    'msg'   => ''
);
try {
    //파라미터 정리
    $user_id        = isset($_POST['inputId'])      ? $_POST['inputId']     : '';
    $user_pw        = isset($_POST['inputPw'])      ? $_POST['inputPw']     : '';
    //필수 파라미터 체크
    if (empty($user_id) || empty($user_pw)) {
        $code    = 404;
        $msg     = '필수 항목을 입력해 주세요';
        throw new Exception($msg, $code);
    }
    //DB
    $query  = "
        SELECT * FROM ADMINS
        WHERE 1=1
            AND USER_ID = '{$user_id}'
        LIMIT 1
    ";
    //p($query);
    $result = $_mysqli->query($query);
    if ($result) {
        $_dbAdmins = $result->fetch_assoc();
        //비밀번호 확인
        if (password_verify($user_pw, $_dbAdmins['USER_PW'])) {
            //승인여부 체크
            if ($_dbAdmins['USE_YN'] != 'Y') {
                $code    = 501;
                $msg     = "승인 대기 중입니다.";
                throw new mysqli_sql_exception($msg, $code);
            }
            //세션 생성
            $_SESSION['_se_admin_seq']        = $_dbAdmins['SEQ'];
            $_SESSION['_se_admin_id']         = $_dbAdmins['USER_ID'];
            $_SESSION['_se_admin_nm']         = $_dbAdmins['USER_NM'];
            //로그인 시간 남기기
            $sub_query  = "
                UPDATE ADMINS SET
                    LOGIN_AT = NOW()
                WHERE 1=1
                    AND SEQ = {$_dbAdmins['SEQ']}
            ";
            //p($sub_query);
            $sub_result = $_mysqli->query($sub_query);
            //성공
            $arrRtn['code']    = 200;
            $arrRtn['msg']     = "로그인 성공";
            echo json_encode($arrRtn);
            exit;
        } else {
            $code    = 502;
            $msg     = "아이디와 비밀번호가 일치하지 않습니다.\n다시 확인 후 시도해 주세요.";
            throw new mysqli_sql_exception($msg, $code);
        }
    } else {
        $code    = 503;
        $msg     = "로그인 중 오류가 발생했습니다.(code {$code})\n관리자에게 문의해 주세요.";
        throw new mysqli_sql_exception($msg, $code);
    }
} catch (mysqli_sql_exception $e) {
    $arrRtn['code']    = $e->getCode();
    $arrRtn['msg']     = $e->getMessage();
    echo json_encode($arrRtn);
} catch (Exception $e) {
    $arrRtn['code']    = $e->getCode();
    $arrRtn['msg']     = $e->getMessage();
    echo json_encode($arrRtn);
}

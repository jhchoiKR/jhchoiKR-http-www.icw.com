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
    //파라미터 정리
    $seq            = isset($_GET['seq'])               ? $_GET['seq']              : 0;
    $page           = isset($_GET['page'])              ? $_GET['page']             : 1;
    $searchKey      = isset($_GET['searchKey'])         ? $_GET['searchKey']        : '';
    $searchValue    = isset($_GET['searchValue'])       ? $_GET['searchValue']      : '';

    //세션
    $_se_admin_seq        = SE_SEQ;
    $_se_admin_id         = SE_ID;
    $_se_admin_nm         = SE_NM;

    //변수 정리
    $ip         = IP;

    //필수 파라미터 체크
    if (!is_numeric($seq)) {
        $seq  = 0;
    }

    //DB
    $query  = "
        SELECT 
            * 
        FROM ADMINS
        WHERE 1=1
            AND SEQ = {$seq}
        LIMIT 1
    ";
    //p($query);
    $_adminsResult  = $_mysqli->query($query);
    if (!$_adminsResult) {
        $code    = 501;
        $msg     = "데이터를 불러오는 중 오류가 발생했습니다.(code {$code})\n관리자에게 문의해 주세요.";
        throw new mysqli_sql_exception($msg, $code);
    }
    $_arrAdmins = $_adminsResult->fetch_assoc();

} catch (mysqli_sql_exception $e) {
    $arrRtn['code']    = $e->getCode();
    $arrRtn['msg']     = $e->getMessage();
    echo json_encode($arrRtn);

} catch (Exception $e) {
    $arrRtn['code']    = $e->getCode();
    $arrRtn['msg']     = $e->getMessage();
    echo json_encode($arrRtn);

}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <?php
    include_once __DIR__ .'/../common/head.php';
    ?>
    <style>
        .pwCheck_zone {
            margin-left: 320px;
            position: absolute;
            margin-top: -29px;
        }

        #alert-success {
            color:green;
        }

        #alert-danger {
            color: red;
        }
    </style>
    <script>
        $(document).ready(function () {

            //비밀번호 체크 유효성 검사
            $("#alert-success").hide();
            $("#alert-danger").hide();

            $("#inputPw, #inputPwre").keyup(function () {
                var user_pw   = $("#inputPw").val();
                var user_pwre = $("#inputPwre").val();

                if(user_pw != '' || user_pwre != '') {
                    if(user_pw == user_pwre) {
                        $("#alert-success").show();
                        $("#alert-danger").hide();
                    } else {
                        $("#alert-success").hide();
                        $("#alert-danger").show();
                    }
                }
            });
        });

        $(function () {
            //취소
            $("#btnCancel").on("click", function () {
                location.href = "list.php";
            });

            //수정
            $("#btnModi").on("click", function () {
                var url = "modiProc.php";
                var formData = new FormData($("#frm")[0]);

                if($("#inputPw").val() != $("#inputPwre").val()) {
                    alert("비밀번호가 일치하지 않습니다.");
                    return false;
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        var json = JSON.parse(data);

                        alert(json.msg);
                        if (json.code == 200) {
                            location.href = "list.php";
                        }
                    },
                    beforeSend:function(){
                        $(".wrap-loading").removeClass("display-none");
                    },
                    complete:function(){
                        $(".wrap-loading").addClass("display-none");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            });
        });
    </script>
</head>
<body>
<?php
//loading
include_once __DIR__ .'/../common/loading.php';
?>
<nav class="nav list-gnb">
    <?php
    //nav
    include_once __DIR__ .'/../common/nav.php';
    ?>
</nav>

<div class="create-container">
    <ol class="breadcrumb">
        <li>관리자 관리</li>
        <li>수정</li>
    </ol>

    <form id="frm">
        <input type="hidden" name="seq" value="<?=$_arrAdmins['SEQ'];?>" />
        <div class="form-group row">
            <label for="inputId" class="col-sm-2 col-form-label">* 아이디</label>
            <div class="col-sm-10">
                <input type="text" class="form-control sm" id="inputId" name="inputId" value="<?=$_arrAdmins['USER_ID'];?>" placeholder="아이디를 입력해주세요" maxlength="20" readonly/>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPw" class="col-sm-2 col-form-label">* 비밀번호</label>
            <div class="col-sm-10">
                <input type="password" class="form-control sm" id="inputPw" name="inputPw" placeholder="비밀번호를 입력해주세요" maxlength="20" />
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPwre" class="col-sm-2 col-form-label">* 비밀번호 확인</label>
            <div class="col-sm-10">
                <input type="password" class="form-control sm" id="inputPwre" placeholder="비밀번호를 입력해주세요" maxlength="20" />
                <div class="pwCheck_zone">
                    <div id="alert-success">* 비밀번호가 일치합니다.</div>
                    <div id="alert-danger">* 비밀번호가 일치하지 않습니다.</div>
                </div>
            </div>
        </div>
        <div class="form-group row">
            <label for="inputName" class="col-sm-2 col-form-label">* 이름</label>
            <div class="col-sm-10">
                <input type="text" class="form-control sm" id="inputName" name="inputName" value="<?=$_arrAdmins['USER_NM'];?>" placeholder="이름을 입력해주세요" maxlength="20" />
            </div>
        </div>
        <div class="form-group row">
            <label for="inputEmail" class="col-sm-2 col-form-label">이메일</label>
            <div class="col-sm-10">
                <input type="text" class="form-control sm" id="inputEmail" name="inputEmail" value="<?=$_arrAdmins['USER_EMAIL'];?>" placeholder="이메일을 입력해주세요" maxlength="50" />
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPhone" class="col-sm-2 col-form-label">휴대폰 번호</label>
            <div class="col-sm-10">
                <input type="text" class="form-control sm" id="inputPhone" name="inputPhone" value="<?=$_arrAdmins['USER_MOBILE'];?>" placeholder="휴대폰 번호를 입력해주세요" maxlength="20" />
            </div>
        </div>
        <div class="form-group row">
            <label for="inputUseY" class="col-sm-2 col-form-label">승인 여부</label>
            <div class="col-sm-10">
                <input type="radio" id="inputUseY" name="inputUse" value="Y" <?php echo ($_arrAdmins['USE_YN']=='Y') ? 'checked="checked"' : '';?> />
                <label for="inputUseY">승인</label>
                <input type="radio" id="inputUseN" name="inputUse" value="N" <?php echo ($_arrAdmins['USE_YN']=='N') ? 'checked="checked"' : '';?> />
                <label for="inputUseN">미승인</label>
            </div>
        </div>

        <div class="form-submit row">
            <button type="button" id="btnCancel" class="btn btn-secondary">취소</button>
            <button type="button" id="btnModi" class="btn btn-primary">수정</button>
        </div>
    </form>
</div>
</body>

</html>
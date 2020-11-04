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

    //세션
    $_se_admin_seq        = SE_SEQ;
    $_se_admin_id         = SE_ID;
    $_se_admin_nm         = SE_NM;

    //변수
    $ip         = IP;

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
        .idCheck_zone {
            position: absolute;
            margin-left: 320px;
            margin-top: -38px;
        }

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
            //아이디 체크용 변수
            var idCheck = 1;

            //아이디 중복검사
            $("#btnIdCheck").click(function () {
                var url     = "checkIdProc.php";
                var user_id = $("#inputId").val();

                if(user_id.trim() == "") {
                    alert("아이디를 입력하세요.");
                    $("#user_id").focus();
                    return false;
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: {"user_id" : user_id},
                    dataType: "text",
                    success: function (data) {
                        if(data.result == 1) {
                            alert("중복된 아이디입니다.");
                            idCheck = 1;
                        } else {
                            alert("사용가능한 아이디입니다.");
                            idCheck = 0;
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            });

            //비밀번호 체크 유효성 검사
            $("#alert-success").hide();
            $("#alert-danger").hide();

            $("#inputPw, #inputPwre").keyup(function () {
                var user_pw = $("#inputPw").val();
                var user_pwre = $("#inputPwre").val();

                if(user_pw != "" || user_pwre != "") {
                    if(user_pw == user_pwre) {
                        $("#alert-success").show();
                        $("#alert-danger").hide();
                    } else {
                        $("#alert-success").hide();
                        $("#alert-danger").show();
                    }
                }
            });

            //취소
            $("#btnCancel").on("click", function () {
                location.href = "list.php";
            });

            //등록
            $("#btnRegi").on("click", function () {
                var url = "regiProc.php";
                var formData = new FormData($("#frm")[0]);

                if(idCheck == 1) {
                    alert("아이디 중복검사를 해주세요.");
                    $("#btnIdCheck").focus();
                    return false;
                }

                if($("#inputPw").val() != $("#inputPwre").val()) {
                    alert("비밀번호가 일치하지 않습니다.");
                    $("#inputPw").focus();
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
        <li>등록</li>
    </ol>

    <form id="frm">
        <div class="form-group row">
            <label for="inputId" class="col-sm-2 col-form-label">* 아이디</label>
            <div class="col-sm-10">
                <input type="text" class="form-control sm" id="inputId" name="inputId" placeholder="아이디를 입력해주세요" maxlength="20" />
                <div class="idCheck_zone">
                    <button type="button" class="btn btn-primary" id="btnIdCheck">중복체크</button>
                </div>
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
                <input type="text" class="form-control sm" id="inputName" name="inputName" placeholder="이름을 입력해주세요" maxlength="20" />
            </div>
        </div>
        <div class="form-group row">
            <label for="inputEmail" class="col-sm-2 col-form-label">이메일</label>
            <div class="col-sm-10">
                <input type="text" class="form-control sm" id="inputEmail" name="inputEmail" placeholder="이메일을 입력해주세요" maxlength="50" />
            </div>
        </div>
        <div class="form-group row">
            <label for="inputPhone" class="col-sm-2 col-form-label">휴대폰 번호</label>
            <div class="col-sm-10">
                <input type="text" class="form-control sm" id="inputPhone" name="inputPhone" placeholder="휴대폰 번호를 입력해주세요" maxlength="20" />
            </div>
        </div>
        <div class="form-group row">
            <label for="inputUseY" class="col-sm-2 col-form-label">사용 여부</label>
            <div class="col-sm-10">
                <input type="radio" id="inputUseY" name="inputUse" value="Y" checked="checked" />
                <label for="inputUseY">사용</label>
                <input type="radio" id="inputUseN" name="inputUse" value="N" />
                <label for="inputUseN">사용 안 함</label>
            </div>
        </div>

        <div class="form-submit row">
            <button type="button" id="btnCancel" class="btn btn-secondary">취소</button>
            <button type="button" id="btnRegi" class="btn btn-primary">등록</button>
        </div>
    </form>
</div>
</body>

</html>
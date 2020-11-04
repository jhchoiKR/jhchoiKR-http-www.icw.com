<!DOCTYPE html>
<html lang="ko">
<head>
    <?php
    include_once __DIR__ .'/../common/head.php';
    ?>
    <style>
        .card-body {
            background-color: #E0EBFF;
        }
    </style>
    <script>
        $(function () {
            $("#inputId").focus();

            //비밀번호란 엔터
            $("#inputPw").on("keyup", function(e) {
                if (e.keyCode == 13) {
                    $("#btnLogin").trigger("click");
                }
            });

            //로그인
            $("#btnLogin").on("click", function () {
                var url = "loginProc.php";
                var formData = new FormData($("#frm")[0]);

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        var json = JSON.parse(data);

                        if (json.code == 200) {
                            location.href = "/icwadmin/dashboard/index.php";
                        } else {
                            alert(json.msg);
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
<div class="login-container card">
    <div class="card-body" style="padding-top: 36px; padding-bottom: 36px">
        <h5 class="card-title">
            <a href="" target="_blank">
                <img/>
            </a>
        </h5>
        <p class="card-text text-danger">관리자 로그인</p>

        <form id="frm">
            <div class="form-group">
                <input type="text" id="inputId" name="inputId" class="form-control" placeholder="아이디를 입력해 주세요" required="required" />
            </div>
            <div class="form-group">
                <input type="password" id="inputPw" name="inputPw" class="form-control" placeholder="비밀번호를 입력해 주세요" required="required" />
            </div>
            <button type="button" id="btnLogin" class="btn btn-primary">로그인</button>
        </form>
    </div>
</div>
</body>
</html>

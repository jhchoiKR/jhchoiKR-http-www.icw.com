<?php
//config
require_once __DIR__ .'/../inc/config.php';
require_once __DIR__ .'/../lib/paging.php';

//로그인 체크
//check_login();

//변수 정리
$arrRtn        = array(
    'code'  => 500,
    'msg'   => ''
);

try {
    //파라미터 정리
    $page           = !empty($_GET['page'])             ? $_GET['page']             : 1;
    $searchKey      = isset($_GET['searchKey'])         ? $_GET['searchKey']        : '';
    $searchValue    = isset($_GET['searchValue'])       ? $_GET['searchValue']      : '';

    //세션
    $_se_admin_seq        = SE_SEQ;
    $_se_admin_id         = SE_ID;
    $_se_admin_nm         = SE_NM;

    //필수 파라미터 체크
    if (!is_numeric($page)) {
        $page  = 1;
    }

    //변수 정리
    $ip             = IP;
    $size           = PAGING_SIZE;
    $offset         = ($page - 1) * $size;
    $scale          = PAGING_SCALE;
    $resultKey      = '';
    $resultValue    = '';
    $check_id       = '';
    $check_name     = '';
    $where          = '';

    //검색 조건
    if ( $searchValue ) {
        $sqlValue   = $_mysqli->real_escape_string($searchValue);
        $resultValue = str_replace('"', '&#34', $searchValue);
        $where .= "AND {$searchKey} LIKE '%{$sqlValue}%' ";
    }

    //DB
    $query  = "
        SELECT 
            SQL_CALC_FOUND_ROWS
            * 
        FROM ADMINS
        WHERE 1=1
            {$where}
        ORDER BY SEQ DESC
        LIMIT {$offset}, {$size}
    ";
    $_adminsResult  = $_mysqli->query($query);
    if (!$_adminsResult) {
        $code    = 501;
        $msg     = "데이터를 불러오는 중 오류가 발생했습니다.(code {$code})\n관리자에게 문의해 주세요.";
        throw new mysqli_sql_exception($msg, $code);
    }

    $sub_result     = $_mysqli->query("SELECT FOUND_ROWS() AS total");
    $sub_dbarray    = $sub_result->fetch_array();
    $total          = $sub_dbarray['total'];
    $sub_result->free();

    if (!$sub_result) {
        $code    = 502;
        $msg     = "데이터를 불러오는 중 오류가 발생했습니다.(code {$code})\n관리자에게 문의해 주세요.";
        throw new mysqli_sql_exception($msg, $code);
    }

    //페이징
    $arrParams  = array(
        'searchKey'     => $searchKey,
        'searchValue'   => $searchValue
    );
    $_pg    = new PAGING($total, $page, $arrParams, $size, $scale);

    //검색 조건 체크
    switch ($searchKey) {
        case "USER_ID" :
            $resultKey  = "아이디";
            $check_id   = "checked";
            break;
        case "USER_NM" :
            $resultKey  = "이름";
            $check_name = "checked";
            break;
        default :
            $resultKey  = "아이디";
            $check_id   = "checked";
            break;
    };

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
        .table thead tr th {
            text-align: center;
        }

        .table tbody tr td {
            text-align: center;
        }

        span {
            cursor: default;
        }

    </style>
    <script>
        $(function() {
            //reset
            $("#btnReset").click(function () {
               location.href = "list.php"
            });

            //등록
            $("#btnRegi").on("click", function () {
                location.href = "regiForm.php";
            });
        });

        //항목 선택
        function changeActive(value) {
            var keyValue = $(value).attr('value');
            $("input:radio[id="+keyValue+"]").prop('checked', true);

            $("#btnSearchKey").text($("input:radio[id="+keyValue+"]").attr("data-title"));

        }

        $(document).ready(function () {

            //현재 화면의 체크박스 개수
            var checkBoxCount = $("input:checkbox[id=checkSeq]").length;

            //체크박스 전체 선택 및 전체 해제
            $("#th_checkAll").click(function () {
                if($("#th_checkAll").is(":checked")) {
                    $(".checkSeq").prop("checked", true);
                } else {
                    $(".checkSeq").prop("checked", false);
                }
            });

            //체크박스를 하나라도 선택시 전체 체크박스 선택 해제
            $(".checkSeq").click(function () {
                if($("input[id=checkSeq]:checked").length == checkBoxCount) {
                    $("#th_checkAll").prop("checked", true);
                } else {
                    $("#th_checkAll").prop("checked", false);
                }
            });

            //리스트 삭제
            $("#btnDelete").click(function () {
                var url = "deleteProc.php";
                var checkBoxValues = [];

                if($("input[name=seq]:checked").length < 1) {
                    alert("삭제할 항목을 하나 선택하세요!");
                    return false;
                }

                $("input[name=seq]:checked").each(function () {
                    checkBoxValues.push($(this).val());
                });

                var result = confirm('정말 삭제하시겠습니까?');

                if(result) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {'adminSeq' : checkBoxValues},
                        dataType: "json",
                        success: function (data) {
                            alert(data.msg);

                            if(data.code == 200) {
                                location.href = "list.php";
                            }
                        },
                        beforeSend:function(){
                            $(".wrap-loading").removeClass("display-none");
                        },
                        complete:function(){
                            $(".wrap-loading").addClass("display-none");
                        },
                        error: function (jqxHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
                } else {
                    //삭제취소
                }
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
<div class="list-container board">
    <ol class="breadcrumb">
        <li>관리자</li>
        <li>관리자 관리</li>
    </ol>

    <form id="frm" method="get" action="list.php">
        <div class="filter">
            <p class="result">
                총 <?=$total;?>개
            </p>

            <div class="filter-search">
                <div class="dropdown">
                    <!-- todo: dropdown-menu에서 선택된 데이터로 보여주기 -->
                    <button class="btn dropdown-toggle" type="button" id="btnSearchKey" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                        <?=$resultKey?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="keyword">
                        <div class="dropdown-item" value="USER_ID" onclick="javaScript:changeActive(this)">
                            <input type="radio" name="searchKey" id="USER_ID" value="USER_ID" data-title="아이디" <?=$check_id?>>
                            <span>아이디</span>
                        </div>
                        <div class="dropdown-item" value="USER_NM" onclick="javaScript:changeActive(this)">
                            <input type="radio" name="searchKey" id="USER_NM" value="USER_NM" data-title="이름" <?=$check_name?>>
                            <span>이름</span>
                        </div>
                    </div>
                </div>
                <input type="text" class="form-control" name="searchValue" value="<?=$resultValue?>" placeholder="검색어를 입력해주세요">
                <button class="btn btn-dark">검색</button>
                <button type="button" class="btn btn-secondary refresh" id="btnReset">검색 초기화</button>
            </div>

            <div class="edit-board">
                <button type="button" class="btn btn-secondary" id="btnDelete">선택 삭제</button>
                <button type="button" class="btn btn-primary" id="btnRegi">등록</button>
            </div>
        </div>
    </form>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">
                <input type="checkbox" id="th_checkAll" class="checkSeq"/>
            </th>
            <th scope="col">번호</th>
            <th scope="col">이름</th>
            <th scope="col">아이디</th>
            <th scope="col">휴대폰 번호</th>
            <th scope="col">등록일시</th>
            <th scope="col">사용여부</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($_adminsResult) {
            $no     = $total - $offset;

            while ($_dbAdmins = $_adminsResult->fetch_assoc()) {

                switch ($_dbAdmins['USE_YN']) {
                    case 'Y' :
                        $use_yn = "승인";
                        break;
                    case 'N' :
                        $use_yn = "미승인";
                        break;
                    default :
                        break;
                };

                echo <<<TR
        <tr>
            <td>
                <input type="checkbox" class="checkSeq" id="checkSeq" name="seq" value="{$_dbAdmins['SEQ']}" />
            </td>
            <td>{$no}</td>
            <td><a href="modiForm.php?seq={$_dbAdmins['SEQ']}">{$_dbAdmins['USER_NM']}</a></td>
            <td>{$_dbAdmins['USER_ID']}</td>
            <td>{$_dbAdmins['USER_MOBILE']}</td>
            <td>{$_dbAdmins['CREATED_AT']}</td>
            <td>$use_yn</td>
        </tr>
TR;
                $no--;
            }
            $_adminsResult->free();
        }

        if (!$total) {
            echo <<<TR
        <tr>
            <td colspan="7" style="text-align: center;">등록된 관리자가 없습니다.</td>
        </tr>
TR;
        }
        ?>
        </tbody>
    </table>

    <ul class="pagination pagination-sm justify-content-center">
        <?=$_pg->getPaging();?>
    </ul>
</div>
<?php
//footer
include_once __DIR__ .'/../common/footer.php';
?>
</body>
</html>

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
    $_se_admin_seq = SE_SEQ;
    $_se_admin_id  = SE_ID;
    $_se_admin_nm  = SE_NM;

    //변수 정리
    $arrShare      = array();

    //DB 공유하기 카운트
    $query = "
        SELECT 
            SHARE_TYPE, COUNT(*) AS CNT
        FROM SHARE_CNTS
        WHERE 1=1
            GROUP BY SHARE_TYPE
            ORDER BY CNT DESC
            LIMIT 2;
    ";
    //p($query);
    $result = $_mysqli->query($query);
    if ($result) {
        while ($_db = $result->fetch_assoc()) {

            switch ($_db['SHARE_TYPE']) {
                case 'normal' :
                    $share_nm = '일반';
                    break;
                case 'kakao' :
                    $share_nm = '카카오';
                    break;
                default :
                    break;
            }

            $arrShare[$share_nm] = $_db['CNT'];

        }

        $arrKey      = array_keys($arrShare);
        $share_label = "'". implode("','", $arrKey) ."'";
        $share_cnt   = implode(",", $arrShare);

    } else {
        $code    = 501;
        $msg     = "데이터를 불러오는 중 오류가 발생했습니다.(code {$code})\n관리자에게 문의해 주세요.";
        throw new mysqli_sql_exception($msg, $code);
    }
    $result->free();


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
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <script src="../assets/chartjs/utils.js"></script>
</head>

<body>
<nav class="nav list-gnb">
    <?php
    include_once __DIR__ .'/../common/nav.php';
    ?>
</nav>

<div class="list-container board">
    <ol class="breadcrumb">
        <li>관리자</li>
        <li>대시보드</li>
    </ol>

    <div class="row">
        <div class="col-6">
            <p>공유 현황</p><br>
            <div class="d-flex">
                <?php
                    if (is_array($arrShare)) {
                        $all_cnt = array_sum($arrShare);
                        echo <<<SPAN
                            총 공유 횟수 : $all_cnt /
SPAN;
                        foreach ($arrShare as $key=>$value) {
                            $cnt  = number_format($value);

                            echo <<<SPAN
                                {$key} : {$cnt} /
SPAN;
                        }
                    }
                ?>
            </div>
            <div class="mt-5">
                <canvas id="pieChart" width="800" height="200"></canvas>
            </div>
        </div>

    </div>
</div>
</body>
<script>
    //파이 차트 데이터
    var pieData = {
        datasets: [{
            data: [<?=$share_cnt?>],
            backgroundColor: [
                '#2e85f7',
                '#FEE500'
            ],
        }],
        labels: [<?=$share_label?>],
    }

    window.onload = function () {
        //파이 차트 생성
        var pieCtx = document.getElementById('pieChart');
        var pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: pieData
        });

    }

</script>
</html>
<?php
function p($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}

function alert($msg) {
    echo '
        <script type="text/javascript">
        //<![CDATA[
            alert("'. $msg .'");
        //]]>
        </script>
    ';
}

function alertBack($msg) {
    echo '
        <script type="text/javascript">
        //<![CDATA[
            alert("'. $msg .'");
            history.back();
        //]]>
        </script>
    ';
}

function alertReplace($msg, $url) {
    echo '
        <script type="text/javascript">
        //<![CDATA[
            alert("'. $msg .'");
            location.replace("'. $url .'");
        //]]>
        </script>
    ';
}

function check_login($isCheck=true) {
    if ($isCheck && !ISLOGIN) {
        $msg    = '로그인 후 이용해 주세요.';
        $url    = '/';
        alertReplace($msg, $url);
        exit;
    }
}

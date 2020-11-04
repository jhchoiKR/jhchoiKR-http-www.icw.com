<?php
/**
 * menu active 설정
 */

//변수 정리
$_iMenuCnt  = 10;
$_arrMenu   = array();
$_aClass    = array();
$_url       = $_SERVER['REQUEST_URI'];
$_arrUrl    = explode('/', $_url);

for ($i=0; $i<$_iMenuCnt; $i++) {
    //변수 초기화
    $_arrMenu[$i]   = '';
    $_aClass[$i]    = '';
}

switch ($_arrUrl[2]) {
    case 'dashboard':
        $_aClass[0] = 'active';
        break;
    case 'admin' :
        $_aClass[1] = 'active';
        break;
    default:
        break;
}
?>
<div class="logo">
    <a href="/icwadmin/dashboard/index.php">
        <h5>관리자</h5>
    </a>
</div>
<a class="nav-link <?=$_aClass[0];?>" href="/icwadmin/dashboard/index.php">대시보드</a>
<a class="nav-link <?=$_aClass[1];?>" href="/icwadmin/admin/list.php">관리자 관리</a>
<div class="user">
    <h6><?=SE_NM;?>님</h6>
    <a class="nav-link" href="/icwadmin/login/logout.php">로그아웃</a>
</div>

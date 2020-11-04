<?php
//config
$config                 = array();

//로컬 개발 IP
$config['ip']['dev']    = array(
    '127.0.0.1',
);

//테스트 장비 IP
$config['ip']['test']   = array(
    '',
);

//실 장비 IP
$config['ip']['real']   = array(
    '10.41.222.167',
);

//ip check
$config['isDev']        = in_array( $_SERVER['SERVER_ADDR'], $config['ip']['dev'] );
$config['isTest']       = in_array( $_SERVER['SERVER_ADDR'], $config['ip']['test'] );
$config['isReal']       = in_array( $_SERVER['SERVER_ADDR'], $config['ip']['real'] );

//database
if ( $config['isDev'] ) {
    define('DBHOST',        'localhost');
    define('DBUSERNAME',    'root');
    define('DBPASSWD',      'jhc237223');
    define('DBNAME',        'DBICW');
    define('WWW',           '//');
    define('M',             '//');
    define('UPLOAD',        '/');
}
if ( $config['isTest'] ) {
    define('DBHOST',        'localhost');
    define('DBUSERNAME',    '');
    define('DBPASSWD',      '');
    define('DBNAME',        '');
    define('WWW',           '//');
    define('M',             '//');
    define('UPLOAD',        '/');
}
if ( $config['isReal'] ) {
    define('DBHOST',        'localhost');
    define('DBUSERNAME',    '');
    define('DBPASSWD',      '');
    define('DBNAME',        '');
    define('WWW',           '//');
    define('M',             '//');
    define('UPLOAD',        '/upload');
}

//캐시
define('CSSYYYYMMDD',       '20200328');
define('JSYYYYMMDD',        '20200328');
define('IP',                $_SERVER['REMOTE_ADDR']);

//페이징
define('PAGING_SIZE',       10);
define('PAGING_SCALE',      10);

//로그인 여부
if (!empty($_SESSION['_se_admin_seq'])) {
    define('ISLOGIN',       TRUE);
    define('SE_SEQ',        $_SESSION['_se_admin_seq']);
    define('SE_ID',         $_SESSION['_se_admin_id']);
    define('SE_NM',         $_SESSION['_se_admin_nm']);

} else {
    define('ISLOGIN',       FALSE);
}

//DB include
require_once __DIR__ .'/../inc/dbconnect.php';

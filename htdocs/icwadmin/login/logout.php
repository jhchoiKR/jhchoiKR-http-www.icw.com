<?php
//config
require_once __DIR__ .'/../inc/config.php';

session_destroy();

header('Location:/wvmadmin/login/loginForm.php');

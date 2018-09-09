<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/site.config.php');
$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_REQUEST = filter_input_array(INPUT_REQUEST, FILTER_SANITIZE_STRING);
$_SESSION = filter_input_array(INPUT_SESSION, FILTER_SANITIZE_STRING);
$_COOKIE = filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_STRING);
$_ENV = filter_input_array(INPUT_ENV, FILTER_SANITIZE_STRING);
function __autoload($classe){
require_once(ROOT_CLASS.$classe.".class.php");
}

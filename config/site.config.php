<?PHP
error_reporting(1);
ini_set('display_errors', 1 );
DEFINE('DIR_ROOT',$_SERVER['DOCUMENT_ROOT'].'/');
DEFINE('DIR_DB',DIR_ROOT.'db/');
DEFINE('UP_ARQ',DIR_ROOT.'uploads/arquivos/');
DEFINE('URL_SITE',$_SERVER['REQUEST_SCHEME'].'://' . $_SERVER['HTTP_HOST'].'/');
DEFINE('URL_CSS',$_SERVER['REQUEST_SCHEME'].'://' . $_SERVER['HTTP_HOST'].'/css/');
DEFINE('URL_JS',$_SERVER['REQUEST_SCHEME'].'://' . $_SERVER['HTTP_HOST'].'/js/');
DEFINE('URL_ICONES',$_SERVER['REQUEST_SCHEME'].'://' . $_SERVER['HTTP_HOST'].'/icones/');
DEFINE('URL_SML',$_SERVER['REQUEST_SCHEME'].'://' . $_SERVER['HTTP_HOST'].'/uploads/smilies/');
DEFINE('URL_ARC',$_SERVER['REQUEST_SCHEME'].'://' . $_SERVER['HTTP_HOST'].'/uploads/arquivos/');
DEFINE('URL_AVT',$_SERVER['REQUEST_SCHEME'].'://' . $_SERVER['HTTP_HOST'].'/uploads/avatar/');
DEFINE('URL_IMAGENS','icones/');
###############CLASSES E FUNCOES########################
DEFINE('ROOT_CLASS',DIR_ROOT.'libs/classes/');
DEFINE('ROOT_CHANGE',DIR_ROOT.'changelog/');
DEFINE('ROOT_FUNC',DIR_ROOT.'libs/funcoes/');
DEFINE('ROOT_PAG',DIR_ROOT.'paginas/');
DEFINE('ROOT_INC',DIR_ROOT.'inc/');
DEFINE('ROOT_SML',DIR_ROOT.'uploads/smilies/');
DEFINE('ROOT_ARC',DIR_ROOT.'uploads/arquivos/');
DEFINE('ROOT_AVT',DIR_ROOT.'uploads/avatar/');
###############CONFIG CACHE#############################
DEFINE('CACHE_IMAGENS',DIR_ROOT.'cache/imagens/');
DEFINE('CACHE_ARQUIVOS',DIR_ROOT.'cache/arquivos/');
###############Google Recaptcha#########################
DEFINE('RECAPTCHA_PUBLIC_KEY','6LdgWRkUAAAAAEfXWsnl0OJth9YoV7Ci5K82mpRv');
###############SITE#####################################
DEFINE('SITE_TITULO', 'PaPo ReTo 4.0');
DEFINE('CHARSET', 'utf-8');
DEFINE('DEVELOPER', TRUE);
DEFINE('MODO_CACHE', false);
DEFINE('TEMPO_SESSAO',3600);
DEFINE('TEMPO_QUIQUE',60);
DEFINE('TEMPO_BLOQUE',3600);
DEFINE('TEMPO_SALA',3600);
DEFINE('CSF_TOKEN','Lord_'.md5($_SERVER['REMOTE_ADDR'].$_SERVER["HTTP_USER_AGENT"]));

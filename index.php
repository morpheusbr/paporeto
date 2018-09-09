<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/libs/funcoes/leitor.func.php');
require_once (ROOT_FUNC.'site.func.php');
Sessao::startSession();
Cookie::init();
$imagecache = new ImageCache();
$code = md5($_SERVER['REQUEST_URI'].GeraIP());
$cache = new Cache ($code.'.html','1 min');
$imagecache->cached_image_directory=CACHE_IMAGENS;
@$uri=multiexplode(array('/','?'),$_SERVER['REQUEST_URI']);
if(strlen($uri[1])>1):
if(file_exists(ROOT_PAG.$uri[1].'.php')):
include_once (ROOT_PAG.$uri[1].'.php');
else:
include_once (ROOT_PAG.'404.php');
endif;
else:
include_once (ROOT_PAG.'padrao.php');
endif;
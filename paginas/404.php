<?php
require_once (ROOT_INC.'topo.inc.php');
$html.='<div class="menu">Erro 404.</div>';
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'atencao.png').'">A Página solicitada não existe';
require_once (ROOT_INC.'rodape.inc.php');
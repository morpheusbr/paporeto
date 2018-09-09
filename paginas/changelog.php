<?php
require_once (ROOT_INC.'topo.inc.php');
$html.='<div class="menu"><img src="'.$imagecache->cache(URL_IMAGENS.'code.png').'">Change Log</div>';
$arquivo = file_get_contents('changelog/changelog.json');
$json = json_decode($arquivo);
foreach($json as $registro):
if ($cor == "linha1"){$cor = "linha2";}else{$cor = "linha1";}
$html.='<div class="'.$cor.'">
<img src="'.$imagecache->cache(URL_IMAGENS.'code.png').'"><strong>Codnome:</strong> ' . $registro->codnome. ' <br/>
<img src="'.$imagecache->cache(URL_IMAGENS.'versao.png').'"><strong>Versão:</strong> ' . $registro->versao . '<br/>
<img src="'.$imagecache->cache(URL_IMAGENS.'mode.png').'"><strong>Modificações:</strong> ' . $registro->modificacoes . '<br/>
<img src="'.$imagecache->cache(URL_IMAGENS.'data.png').'"><strong>Data:</strong> ' . $registro->data. '</div>';
endforeach;
$html.='<p align="center"><img src="icones/inicio.png"><a href="index">Página Inicial</a></p>';
require_once (ROOT_INC.'rodape.inc.php');
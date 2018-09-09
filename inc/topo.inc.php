<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/libs/funcoes/leitor.func.php');
$page = new Pagina (SITE_TITULO.' - '.Subtitulo());
$page->description (Site('descricao'));
$page->keywords (Site('chave'));
$page->robots (true);
$page->charset (CHARSET);
$page->link (array(
URL_CSS.'estilo.css',
URL_CSS.'paginacao.css',
URL_ICONES.'favicon.ico'));
$page->body ();
$html='<div class="topo"><center><img src="'.URL_IMAGENS.'logo.png"></center></div>';
LimpaSite();
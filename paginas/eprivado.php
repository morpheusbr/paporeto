<?php
require_once (ROOT_INC.'topo.inc.php');
if(VerificaSessao()):
if(strlen(utf8_decode($_GET['id']))>0):
$html.=Avisos();
$html.='<div class="menu"><img src="'.$imagecache->cache(URL_IMAGENS.'mensagem.png').'">Mensagem Privada Para '.GeraApelido($_GET['id']).'</div>';
if(MeuID()!=$_GET['id'] AND User($_GET['id'])):
$form = new Form('mensagem');	
$html.= $form->header('mensagem','POST','conversa?usuario='.$_GET['id'].'&token='.$_GET['token']);
$html.= $form->field('text', 'texto', 'Sua Mensagem:', array('width'=>100, 'maxlength'=>250)).'<br/>';
$html.= $form->field('file', 'arquivo', 'Anexo:').'<br/>';
$html.= $form->field ('hidden', 'para',$_GET['id']);
$html.= $form->field('submit', 'Enviar');
$html.= $form->close();
endif;
$html.=Links();
else:
header("Location: 404");
endif;
else:
header("Location: index");
die();  
endif;
require_once (ROOT_INC.'rodape.inc.php');
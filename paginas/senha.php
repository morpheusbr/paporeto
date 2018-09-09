<?php
require_once (ROOT_INC.'topo.inc.php');
if(VerificaSessao()):
$html.=Avisos();
$html.='<div class="menu">Está sala privada e protegida por senha</div>';
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST'):
if(LogaSala($_GET['sala'],$_POST['senha'])):
header("Location: chat?sala=".$_GET['sala'].'&token='.$_GET['token']);
else:
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Senha da sala incorreta!';
endif;
endif;
$form = new Form('senha');	
$html.= $form->header('senha','POST','senha?sala='.$_GET['sala'].'&token='.$_GET['token']);
$html.= $form->field('password', 'senha', 'Senha:', array('width'=>250, 'maxlength'=>60));
$html.= $form->field('submit', 'Entrar');
$html.= $form->close().'</center>';
$html.=Links();
else:
header("Location: index");
die();  
endif;
$html.='</center>';
require_once (ROOT_INC.'rodape.inc.php');
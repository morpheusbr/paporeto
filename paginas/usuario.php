<?php
require_once (ROOT_INC.'topo.inc.php');
if(VerificaSessao()):
if(strlen(utf8_decode($_GET['id']))>0):
if(Bloquiar($_GET['id'],$_GET['sala'],DadosUsuario('ip',$_GET['id']),DadosUsuario('navegador',$_GET['id']),$_GET['acao'])):
header("Location: chat?sala=".$_GET['sala'].'&token='.$_GET['token']);
endif;
$html.=Avisos();
$html.='<p align="center">'.GeraAvatarP($_GET['id']).'</p>';
$html.='<div class="linha2"><strong>Apelido:</strong> '.GeraApelido($_GET['id']).'</div>';
$html.='<div class="linha1"><strong>Sexo:</strong> '.GeraSexo($_GET['id']).'</div>';
if(Admin(MeuID())):
$html.='<div class="linha2"><strong>IP:</strong> '.DadosUsuario('ip',$_GET['id']).'</div>';
$html.='<div class="linha1"><strong>Navegador:</strong> '.DadosUsuario('navegador',$_GET['id']).'</div>';
endif;
if((Admin(MeuID()) OR DonoSala(MeuID(),$sala)) AND $_GET['sala']>0):
$html.='<div class="linha2"><a href="usuario?id='.$_GET['id'].'&sala='.$_GET['sala'].'&acao=quicar&token='.$_GET['token'].'">Quicar</a></div>';
$html.='<div class="linha1"><a href="usuario?id='.$_GET['id'].'&sala='.$_GET['sala'].'&acao=bloq&token='.$_GET['token'].'">Bloquiar</a></div>';
if(Admin(MeuID()) AND $_GET['sala']>0):
$html.='<div class="linha2"><a href="usuario?id='.$_GET['id'].'&sala='.$_GET['sala'].'&acao=bloqip&token='.$_GET['token'].'">Bloquiar IP</a></div>';
endif;
endif;
if(MeuID()!=$_GET['id'] AND $_GET['sala']>0):
$html.='<center>';
$form = new Form('mensagem');	
$html.= $form->header('mensagem','POST','chat?sala='.$_GET['sala'].'&token='.$_GET['token']);
$html.= $form->field('checkbox','pvt', '', array('1'=>'Privado')).'<br/>';
$html.= $form->field('text', 'texto', 'Sua Mensagem:<br/>', array('width'=>100, 'maxlength'=>250)).'<br/>';
$html.= $form->field ('hidden', 'para',$_GET['id']);
$html.= $form->field('submit', 'Enviar');
$html.= $form->close();
$html.='</center>';
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
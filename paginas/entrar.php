<?php
require_once (ROOT_INC.'topo.inc.php');
$html.='<div class="menu">Entrar</div>';
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST'):
if (strtolower($_POST['captcha']) != strtolower($_SESSION['captcha'])):
$html.='<div class="erro"><img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Codigo de Verificação nao confere!</div>';
else:
if(LoginOK($_POST['login'],$_POST['senha'])==true):
$token=md5(GeraIP().GeraNav().time()); 
if(GravaoSessaoEquipe($_POST['login'],$_POST['senha'],$token)==true):
header("Location: salas?token={$token}");
else:
$html.='<div class="erro"><img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Erro ao tentar logar entre em contato com o programador do site!</div>';
endif;
else:
$html.='<div class="erro"><img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">O login e senha informados não confere!</div>';
endif;
endif;
endif;
$form = new Form('logar');	

$html.= $form->header('conta','POST','entrar');
$html.= $form->field('text', 'login', 'Login:', array('width'=>100, 'maxlength'=>60)).'<br/>';
$html.= $form->field('password', 'senha', 'Senha:', array('width'=>100, 'maxlength'=>60)).'<br/>';
$html.= '<img src="captcha/captcha.php" alt="captcha" /><br/>';
$html.= $form->field('text', 'captcha', 'Verificação:', array('width'=>100, 'maxlength'=>60)).'<br/>';
$html.= $form->field('submit', 'Logar');
$html.= $form->close();
$html.='<div class="atencao"><img src="'.$imagecache->cache(URL_IMAGENS.'atencao.png').'">Se você e novo por aqui e não possui conta fixa, clique <a href="cadastrar">Aqui</a> entrar!</div>';
$html.='<p align="center"><img src="icones/inicio.png"><a href="index">Página Inicial</a></p>';
require_once (ROOT_INC.'rodape.inc.php');
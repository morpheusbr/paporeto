<?php
require_once (ROOT_INC.'topo.inc.php');
$html.='<div class="menu">Cadastrar</div>';
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST'):
if (strtolower($_POST['captcha']) != strtolower($_SESSION['captcha'])):
$html.='<div class="erro"><img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Codigo de Verificação nao confere!</div>';
elseif(ApelidoEmUso($_POST['apelido'])>0):
$html.='<div class="erro"><img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Este apelido já está em uso escolha outro</div>';
elseif(strlen(utf8_decode($_POST['apelido']))<3 OR strlen(utf8_decode($_POST['apelido']))>60):
$html.='<div class="erro"><img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">O Apelido deve ter no minimo 3 caracteres e no maximo 60</div>';
else:
$token=md5(GeraIP().GeraNav().time()); 
if(GravaoSessao($_POST['apelido'],$_POST['cor_texto'],$_POST['cor_fundo'],$_POST['cor_apelido'],$token)):	
header("Location: salas?token={$token}");
else:
$html.='<div class="erro"><img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Não foi possivel entrar no chat</div>';
endif;
endif;
endif;
$html.='<div class="atencao"><img src="'.$imagecache->cache(URL_IMAGENS.'atencao.png').'">Caso queira continuar com a mesma conta de Úsuario após cadastrar no <strong>NerdsBR</strong>, por favor ative sua conta!</div>';
$html.='<div class="atencao"><img src="'.$imagecache->cache(URL_IMAGENS.'atencao.png').'">Os Úsuarios com perfil <strong>Vistante</strong> são removidos de nosso sistema após <strong>1 hora</strong> de inatividade !</div>';
$form = new Form('chat');	
$html.= $form->header('entrar','POST','cadastrar');
$html.= $form->field('text', 'apelido', 'Apelido:', array('width'=>100, 'maxlength'=>60)).'<br/>';
$html.= $form->field('select', 'cor_texto', 'Cor do Texto:', array('#848484'=>'Cinza','#FF9601'=>'Laranja','#04A201'=>'Verde','#F3FF01'=>'Amarelo','#FF0101'=>'Vermelho', '#2A63FF'=>'Azul','#FF01F7'=>'Rosa')).'<br/>';
$html.= $form->field('select', 'cor_fundo', 'Cor fundo do Texto:', array('#D6D5D5'=>'Cinza Leve','#FFE0C3'=>'Laranja Leve','#A1FFA3'=>'Verde Leve','#FBFFC1'=>'Amarelo Leve','#FFBFBF'=>'Vermelho Leve', '#CCE0FF'=>'Azul Leve','#FCD2FF'=>'Rosa Leve')).'<br/>';
$html.= $form->field('select', 'cor_apelido', 'Cor do Apelido:', array('#848484'=>'Cinza','#FF9601'=>'Laranja','#04A201'=>'Verde','#F3FF01'=>'Amarelo','#FF0101'=>'Vermelho', '#2A63FF'=>'Azul','#FF01F7'=>'Rosa')).'<br/>';
$html.= '<img src="captcha/captcha.php" alt="captcha" /><br/>';
$html.= $form->field('text', 'captcha', 'Verificação:', array('width'=>100, 'maxlength'=>60)).'<br/>';
$html.= $form->field('submit', 'Entrar');
$html.= $form->close();
$html.='<div class="atencao"><img src="'.$imagecache->cache(URL_IMAGENS.'atencao.png').'">Caso ja tenha uma conta fixa , clique <a href="entrar">Aqui</a> para logar!</div>';
$html.='<p align="center"><img src="icones/inicio.png"><a href="index">Página Inicial</a></p>';
require_once (ROOT_INC.'rodape.inc.php');
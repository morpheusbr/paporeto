<?php
require_once (ROOT_INC.'topo.inc.php');
if(VerificaSessao()):
RemoverSala($_GET['sala_id'],$_GET['acao']);
$html.='<p align="center">Olá '.GeraApelido(MeuID()).'! Escolha uma sala :)</p>';
$html.=Avisos();
$html.='<div class="menu"><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">Menu</div>';
$html.='<div class="linha1"><a href="configuracoes?token='.$_GET['token'].'"><strong><img src="'.$imagecache->cache(URL_IMAGENS.'configuracoes.png').'">Configurações</strong></a></div>';
$db= new SQLite('site');
$db->query("SELECT nome, id FROM salas WHERE dono=? AND fixa=? ORDER BY nome ASC",array(0,1)); 
$html.='<div class="menu"><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">Salas Publicas</div>';
while (list($nome, $id) = $db->fetch('row')) :
if ($cor == "linha1"){$cor = "linha2";}else{$cor = "linha1";}
if(Admin(MeuID())):
$del='<a href="salas?sala_id='.$id.'&acao=remover&token='.$_GET['token'].'"><strong>[X]</strong></a>';
else:
$del='';
endif; 
$html.='<div class="'.$cor.'"><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">'.GeraLinkSala(MeuID(),$id,'chat?sala='.$id.'&token='.$_GET['token'],$nome).'<a href="online?sala='.$id.'&token='.$_GET['token'].'">('.OnlineSala($id).')</a>'.$del.'</div>';
endwhile;
$html.='<div class="menu"><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">Salas Privadas</div>';
if(ContSalas(0,0)>0):
$db->query("SELECT nome, id FROM salas WHERE dono>? AND fixa=? ORDER BY nome ASC",array(0,0)); 
while (list($nome, $id) = $db->fetch('row')) :  
if ($cor == "linha1"){$cor = "linha2";}else{$cor = "linha1";}
if(Admin(MeuID()) OR DonoSala(MeuID(),$id)):
$del='<a href="salas?sala_id='.$id.'&acao=remover&token='.$_GET['token'].'"><strong>[X]</strong></a>';
else:
$del='';
endif; 
$html.='<div class="'.$cor.'"><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">'.GeraLinkSala(MeuID(),$id,'chat?sala='.$id.'&token='.$_GET['token'],$nome).'<a href="online?sala='.$id.'&token='.$_GET['token'].'">('.OnlineSala($id).')</a>'.$del.'</div>';
endwhile;
else:
$html.='<div class="atencao"><img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Não foram criadas salas privadas até o momento!!</div>';
endif;
$html.='<p align="center"><a href="online?token='.$_GET['token'].'"><img src="'.$imagecache->cache(URL_IMAGENS.'online.png').'"><strong>Online('.OnlineSite().')</strong></a></p>';
$html.=Links();
else:
header("Location: index");
die();  
endif;
$html.='</center>';
require_once (ROOT_INC.'rodape.inc.php');
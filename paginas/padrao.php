<?php
require_once (ROOT_INC.'topo.inc.php');
$html.='<center>Bem Vindo ao '.SITE_TITULO.'!</center>';
$db= new SQLite('site');
$db->query("SELECT nome, id FROM salas WHERE dono=? AND fixa=? ORDER BY nome ASC",array(0,1)); 
$html.='<div class="menu"><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">Salas Publicas</div>';
while (list($nome, $id) = $db->fetch('row')) :
if ($cor == "linha1"){$cor = "linha2";}else{$cor = "linha1";}
$html.='<div class="'.$cor.'"><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">'.GeraLinkSala(MeuID(),$id,'cadastrar',$nome).'<a href="cadastrar">('.OnlineSala($id).')</a></div>';
endwhile;
if(ContSalas(0,0)>0):
$html.='<div class="menu"><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">Salas Privadas</div>';
$db->query("SELECT nome, id FROM salas WHERE dono>? AND fixa=? ORDER BY nome ASC",array(0,0)); 
while (list($nome, $id) = $db->fetch('row')) :  
if ($cor == "linha1"){$cor = "linha2";}else{$cor = "linha1";}
 
$html.='<div class="'.$cor.'"><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">'.GeraLinkSala(MeuID(),$id,'cadastrar',$nome).'<a href="cadastrar">('.OnlineSala($id).')</a></div>';
endwhile;
else:
endif;
if(OnlineSite()>0):
$html.='<div class="menu"><img src="'.$imagecache->cache(URL_IMAGENS.'inicio.png').'">Quem está online?</div>';
$db= new SQLite('site');
$db->query("SELECT usuario,sala_id FROM online ORDER BY tempo DESC");
while (list($usuario, $sala_id) = $db->fetch('row')) :  
if ($cor == "linha1"){$cor = "linha2";}else{$cor = "linha1";}
$html.='<div class="'.$cor.'">'.GeraAvatar($usuario).GeraApelido($usuario).' conversando na sala <strong>'.NomeSala($sala_id).'</strong></div>';
endwhile;
else:
endif;
$html.='<p align="center"><img src="'.$imagecache->cache(URL_IMAGENS.'dow.png').'"><br/><a href="changelog"><img src="'.$imagecache->cache(URL_IMAGENS.'change.png').'"></a></p>';
require_once (ROOT_INC.'rodape.inc.php');
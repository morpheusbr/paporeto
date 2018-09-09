<?php
require_once (ROOT_INC.'topo.inc.php');
if(VerificaSessao()):
$html.=Avisos();
$html.='<div class="menu">'.Subtitulo().'</div>';
$db= new SQLite('site');
if($_GET['sala']>0):
$db->query("SELECT usuario,sala_id FROM online WHERE sala_id=? ORDER BY tempo DESC",array($_GET['sala']));
else:
$db->query("SELECT usuario,sala_id FROM online ORDER BY tempo DESC");
endif; 
while (list($usuario, $sala_id) = $db->fetch('row')) {  
if ($cor == "linha1"){$cor = "linha2";}else{$cor = "linha1";}
$html.='<div class="'.$cor.'">'.GeraAvatar($usuario).''.GeraApelido($usuario).'<br/><strong>Sala:</strong> '.NomeSala($sala_id).'</div>';
}
$html.=Links();
else:
header("Location: index");
die();  
endif;
$html.='</center>';
require_once (ROOT_INC.'rodape.inc.php');
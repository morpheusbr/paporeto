<?php
require_once (ROOT_INC.'topo.inc.php');
if(VerificaSessao()):
RemoverItem('smilies',$_GET['sml_id'],$_GET['acao']);
$html.=Avisos();
$html.='<div class="menu">'.Subtitulo().'</div>';
$db= new SQLite('site');
//A quantidade de valor a ser exibida
$quantidade = 10;
//a pagina atual
$pagina     = (isset($_GET['pagina'])) ? (int)$_GET['pagina'] : 1;
//Calcula a pagina de qual valor será exibido
$inicio     = ($quantidade * $pagina) - $quantidade;
$db->query("SELECT id,codigo,url FROM smilies ORDER BY codigo ASC LIMIT {$inicio}, {$quantidade}");  
while (list($id,$codigo,$url) = $db->fetch('row')) {  
if ($cor == "linha1"){$cor = "linha2";}else{$cor = "linha1";}
if(Admin(MeuID())):
$del='<a href="smilies?sml_id='.$id.'&acao=remover&token='.$_GET['token'].'"><strong>[X]</strong></a>';
else:
$del='';
endif;
$html.='<div class="'.$cor.'"><img src="'.URL_SML.$url.'"/>'.$del.'<br/><strong>Código:</strong> '.$codigo.'</div>';
}
$total=$db->value("SELECT COUNT(*) FROM smilies");
$totalPagina= ceil($total/$quantidade);
$exibir = 3;
$anterior  = (($pagina - 1) == 0) ? 1 : $pagina - 1;
$posterior = (($pagina+1) >= $totalPagina) ? $totalPagina : $pagina+1;
$html.='<div class="pagination"><a class="first" href="smilies?pagina=1&token='.$_GET['token'].'">&laquo;</a>';
$html.='<a class="prev" href="smilies?pagina='.$anterior.'&token='.$_GET['token'].'">&lt;</a>';
for($i = $pagina-$exibir; $i <= $pagina-1; $i++){
if($i > 0)
$html.='<a href="smilies?pagina='.$i.'&token='.$_GET['token'].'"> '.$i.' </a>';
}
$html.='<span class="current">'.$pagina.'</span>';
for($i = $pagina+1; $i < $pagina+$exibir; $i++){
if($i <= $totalPagina)
$html.='<a class="current" href="smilies?pagina='.$i.'&token='.$_GET['token'].'"> '.$i.' </a>';
}
$html.='<a class="next" href="smilies?pagina='.$posterior.'&token='.$_GET['token'].'">&gt;</a>';
$html.='<a class="last" href="smilies?pagina='.$totalPagina.'&token='.$_GET['token'].'">&raquo;</a></div>';
$html.=Links();
else:
header("Location: index");
die();  
endif;
require_once (ROOT_INC.'rodape.inc.php');
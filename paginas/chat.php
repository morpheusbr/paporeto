<?php
require_once (ROOT_INC.'topo.inc.php');
if(VerificaSessao()):
VerificaBloqueio(MeuID(),$_GET['sala']);
VerificaSenhaSala($_GET['sala']);
SalaExiste($_GET['sala']);
$html.=Avisos();
$html.='<div class="menu">'.Subtitulo().' - '.NomeSala($_GET['sala']).'</div>';
EntraChat($_GET['sala']);
RemoverItem('mensagens',@$_GET['msg_id'],@$_GET['acao']);
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST'):
if(strlen(utf8_decode($_POST['texto']))<3 OR strlen(utf8_decode($_POST['texto']))>500):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Seu texto deve conter no minimo 3 caracteres e no maximo 500!';
else:
if(EnviarMensagem($_POST['texto'],$_POST['para'],$_POST['pvt'],$_GET['sala'])):	
header("Location: chat?sala={$_GET['sala']}&token={$_GET['token']}");
else:
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Não foi possivel enviar a mensagem';
endif;
endif;
endif;
$db= new SQLite('site');
$db->query("SELECT id,usuario,para,texto,privado,anexo,tempo FROM mensagens WHERE sala_id=? ORDER BY tempo DESC LIMIT 20",array($_GET['sala'])); 
$html.='<p align="center"><strong><a href="chat?sala='.$_GET['sala'].'&token='.$_GET['token'].'&tempo='.time().'"><img src="'.$imagecache->cache(URL_IMAGENS.'atualizar.png').'">Atualizar</a> <a href="online?sala='.$_GET['sala'].'&token='.$_GET['token'].'"><img src="'.$imagecache->cache(URL_IMAGENS.'online.png').'">Online('.OnlineSala($_GET['sala']).')</a></strong></p>';
$form = new Form('mensagem');	
$html.= $form->header('mensagem','POST','chat?sala='.$_GET['sala'].'&token='.$_GET['token']);
$html.= $form->field('text', 'texto', 'Sua Mensagem:', array('width'=>100, 'maxlength'=>250)).'<br/>';
$html.= $form->field('checkbox','pvt', '', array('1'=>'Privado:')).'<br/>';
$html.=SelecionaPvt($_GET['sala']).'<br/>';
$html.= $form->field('file', 'arquivo', 'Anexo:').'<br/>';
$html.= $form->field('submit', 'Enviar');
$html.= $form->close();

if(ContMsgSala($_GET['sala'])>0):

while (list($id,$usuario,$para,$texto,$privado,$anexo,$tempo) = $db->fetch('row')) { 
if(Admin(MeuID())):
$del='<a href="chat?sala='.$_GET['sala'].'&msg_id='.$id.'&acao=remover&token='.$_GET['token'].'"><strong>[X]</strong></a>';
else:
$del='';
endif;
if(strlen(utf8_decode($anexo))>10):
if(in_array(end(multiexplode(array('.'),$anexo)),array('gif', 'jpg', 'jpeg', 'png'))):
$previ='<img src="'.URL_ARC.$anexo.'" width="150" height="150" /><br/>';
else:
$previ='';
endif;
$link='<br/>'.$previ.'<a href="'.URL_ARC.$anexo.'"><strong>Baixar Anexo</strong></a>';
else:
$link='';
endif;

if($privado>0 AND $para==MeuID()):
$html.='<div style="background:'.DadosUsuario('cor_fundo',$usuario).';">'.GeraAvatar($usuario).'Ptv de '.GeraApelido($usuario).' para você: <small>'.date("H:i:s",$tempo).'</small><br/><font style="color:'.DadosUsuario('cor_texto',$usuario).';"><div class="quebra">'.Texto($texto).$link.'</font>'.$del.'</div>';
elseif($privado>0 AND $usuario==MeuID()):
$html.='<div style="background:'.DadosUsuario('cor_fundo',$usuario).';">'.GeraAvatar($usuario).'Ptv para '.GeraApelido($para).': <small>'.date("H:i:s",$tempo).'</small><br/><font style="color:'.DadosUsuario('cor_texto',$usuario).';"><div class="quebra">'.Texto($texto).$link.'</div></font>'.$del.'</div>';
else:
if($para>0):
$html.='<div style="background:'.DadosUsuario('cor_fundo',$usuario).';">'.GeraAvatar($usuario).''.GeraApelido($usuario).' para '.GeraApelido($para).': <small>'.date("H:i:s",$tempo).'</small><br/><font style="color:'.DadosUsuario('cor_texto',$usuario).';">'.Texto($texto).$link.'</font>'.$del.'</div>';
else:
$html.='<div style="background:'.DadosUsuario('cor_fundo',$usuario).';">'.GeraAvatar($usuario).''.GeraApelido($usuario).' para <strong>Todos</strong>: <small>'.date("H:i:s",$tempo).'</small><br/><font style="color:'.DadosUsuario('cor_texto',$usuario).';">'.Texto($texto).$link.'</font>'.$del.'</div>';
endif;
endif;    
}
else:
$html.='<div class="atencao"><img src="'.$imagecache->cache(URL_IMAGENS.'atencao.png').'">Nenhuma mensagem foi enviada!!</div>';
endif;
$html.='<p align="center"><strong><a href="chat?sala='.$_GET['sala'].'&token='.$_GET['token'].'&tempo='.time().'"><img src="'.$imagecache->cache(URL_IMAGENS.'atualizar.png').'">Atualizar</a> <a href="online?sala='.$_GET['sala'].'&token='.$_GET['token'].'"><img src="'.$imagecache->cache(URL_IMAGENS.'online.png').'">Online('.OnlineSala($_GET['sala']).')</a></strong></p>';
$html.=Links();
else:
header("Location: index");
die();  
endif;
$html.='</center>';
require_once (ROOT_INC.'rodape.inc.php');
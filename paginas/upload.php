<?php
require_once (ROOT_INC.'topo.inc.php');
$html.='<center>';
$html.='<h2 class="title">'.Subtitulo().'</h2>';
$html.=Links();
if(VerificaSessao()):
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post' AND CSRF::validate($_POST)) {
$upload = new Upload('arquivo');
$upload->set_upload_path(ROOT_ARC);
$upload->set_max_size('5MB');
$upload->set_allowed_types('jpeg|jpg|png|gif');
$arquivo=md5(time()).'.'.$upload->get_ext();
$upload->set_name($arquivo);
if ($upload->run() !== false) {
if(EnviarMensagem($_POST['texto'],$_POST['para'],$_POST['pvt'],$_GET['sala'],$arquivo)){
header("Location: chat?sala={$_GET['sala']}");    
}else{
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Não foi possivel enviar o arquivo procure o programador do site!!';}
}else{

$erro=$upload->get_errors();

$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">'.$erro[0].'!!';}
}
$form = new Form('upload');	
$html.= $form->header('upload','post','upload?sala='.$_GET['sala']);
$html.= $form->field('checkbox','pvt', '', array('1'=>'PVT'));
$html.= $form->field('text', 'texto', 'Sua Mensagem', array('width'=>250, 'maxlength'=>250));
$html.=SelecionaPvt($_GET['id']);
$html.= $form->field('file', 'arquivo', 'Anexo:');
$html.= $form->field('submit', 'Enviar Arquivo');
$html.= $form->close();
$html.=Links();
else:
header("Location: entrar");
die();  
endif;
$html.='</center>';
require_once (ROOT_INC.'rodape.inc.php');
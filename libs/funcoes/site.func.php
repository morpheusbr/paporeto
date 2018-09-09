<?php
function json_decode_nice($json, $assoc = TRUE){
    $json = str_replace(array("\n","\r"),"\\n",$json);
    $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
    $json = preg_replace('/(,)\s*}$/','}',$json);
    return json_decode($json,$assoc);
}
function SalaExiste($sala){
$db= new SQLite('site');
$id=$db->value("SELECT id FROM salas WHERE id=?",array($sala));
if($id>0):
return true;
else:
header("Location: salas?token={$_GET['token']}");
endif;	
}
function LogaSala($sala,$senha){
$db= new SQLite('site');
$senha2=$db->value("SELECT senha FROM salas WHERE id=?",array($sala));
if($senha===$senha2):
Sessao::set('senha',$senha2);
return true;
else:
return false;
endif;
}
function VerificaSenhaSala($sala){
$db= new SQLite('site');
$senha=$db->value("SELECT senha FROM salas WHERE id=?",array($sala));
if(SenhaSala($sala)):
if($senha===Sessao::get('senha')):
else:
header("Location: senha?sala=".$sala.'&token='.$_GET['token']);
exit;
endif;
else:
endif;
}
function SenhaSala($sala){
$db= new SQLite('site');
$senha=$db->value("SELECT senha FROM salas WHERE id=?",array($sala));
if(strlen(utf8_decode($senha))>0):
return true;
else:
return false;
endif;	
}
function TipoBloque($tipo){
if($tipo=='1'):
return 'Quicou';
elseif($tipo=='2'):
return 'Bloquiou';
elseif($tipo=='3'):
return 'Bloquiou ip';
endif;	
}
function CriarSalaPublica($sala){
$db= new SQLite('site');
$dados=array(
'dono'=>'0',
'nome'=>$sala,
'fixa'=>'1',
);
$ex=$db->insert('salas', $dados);
if($ex):
return true;
else:
return false;
endif;	
}
function CriarSalaPrivada($sala,$senha=''){
$db= new SQLite('site');
$dados=array(
'dono'=>MeuID(),
'nome'=>$sala,
'senha'=>$senha,
'fixa'=>'0',
'tempo'=>time(),
);
$ex=$db->insert('salas', $dados);
if($ex):
return true;
else:
return false;
endif;	
}
function Favoritos($id,$acao){
$db= new SQLite('site');
if($acao=='add'):
$dados=array(
'usuario'=>MeuID(),
'favorito'=>$id
);
$ex=$db->insert('favoritos', $dados);
if($ex):
return true;
else:
return false;
endif;
elseif($acao=='del'):
$ex=$db->statement("DELETE FROM favoritos WHERE favorito=? AND usuario=?",array($id,MeuID()));
if($ex):
return true;
else:
return false;
endif;
else:
return false;
endif;
}
function Bloquiar($id,$sala,$ip,$nav,$acao){
$db= new SQLite('site');
if($acao=='quicar'):
$tempo=time()+TEMPO_QUIQUE;
$dados=array(
'tipo'=>1,
'de'=>MeuID(),
'em'=>$id,
'sala'=>$sala,
'ip'=>$ip,
'navegador'=>$navegador,
'tempo'=>$tempo,
);
$ex=$db->insert('bloqueios', $dados);
if($ex):
$dados=array(
'usuario'=>0,
'texto'=>'<em>'.GeraApelidoL(MeuID()).' quicou '.GeraApelidoL($id).' da sala</em>',
'sala_id'=>$sala,
'tempo'=>time(),
);
$db->insert('mensagens', $dados);
return true;
else:
return false;
endif;
elseif($acao=='bloq'):
$tempo=time()+TEMPO_BLOQUE;
$dados=array(
'tipo'=>2,
'de'=>MeuID(),
'em'=>$id,
'sala'=>$sala,
'ip'=>$ip,
'navegador'=>$navegador,
'tempo'=>$tempo,
);
$ex=$db->insert('bloqueios', $dados);
if($ex):
$dados=array(
'usuario'=>0,
'texto'=>'<em>'.GeraApelidoL(MeuID()).' bloquiou '.GeraApelidoL($id).' de entrar nesta sala</em>',
'sala_id'=>$sala,
'tempo'=>time(),
);
$db->insert('mensagens', $dados);
return true;
else:
return false;
endif;
elseif($acao=='bloqip'):
$tempo=time()+TEMPO_BLOQUE;
$dados=array(
'tipo'=>3,
'de'=>MeuID(),
'em'=>$id,
'sala'=>$sala,
'ip'=>$ip,
'navegador'=>$navegador,
'tempo'=>$tempo,
);
$ex=$db->insert('bloqueios', $dados);
if($ex):
$dados=array(
'usuario'=>0,
'texto'=>'<em>'.GeraApelidoL(MeuID()).' bloquiou o ip de '.GeraApelidoL($id).' para esta sala!</em>',
'sala_id'=>$sala,
'tempo'=>time(),
);
$db->insert('mensagens', $dados);
return true;
else:
return false;
endif;
endif;	
}
function VerificaIp($sala){
$db= new SQLite('site');
$tipo=$db->value("SELECT tipo FROM bloqueios WHERE ip=? AND sala=?",array(GeraIP(),$sala));	
if($tipo=='3'):
return true;
else:
return false;  
endif;
}
function VerificaBloq($id,$sala){
$db= new SQLite('site');
$tipo=$db->value("SELECT tipo FROM bloqueios WHERE em=? AND sala=?",array($id,$sala));	
if($tipo=='2'):
return true;
else:
return false;  
endif;
}
function VerificaQuique($id,$sala){
$db= new SQLite('site');
$tipo=$db->value("SELECT tipo FROM bloqueios WHERE em=? AND sala=?",array($id,$sala));	
if($tipo=='1'):
return true;
else:
return false;  
endif;
}
function VerificaBloqueio($id,$sala){
$db= new SQLite('site');
$tipo=$db->value("SELECT tipo FROM bloqueios WHERE em=? AND sala=?",array($id,$sala));
$ip=$db->value("SELECT tipo FROM bloqueios WHERE ip=? AND sala=?",array(GeraIP(),$sala));	
if($tipo=='1'):
header("Location: salas");
die();  
elseif($tipo=='2'):
header("Location: salas");
die();  
elseif($ip=='3'):
header("Location: salas");
die();
else:  
endif;
}
function GeraLinkSala($id,$sala,$link,$nome){
$db= new SQLite('site');
$tipo=$db->value("SELECT tipo FROM bloqueios WHERE em=? AND sala=?",array($id,$sala));	
$ip=$db->value("SELECT tipo FROM bloqueios WHERE ip=? AND sala=?",array(GeraIP(),$sala));
if($tipo=='1'):
return '<strong>'.$nome.'</strong> <small>(Você foi quicado desta sala)</small>';
die();  
elseif($tipo=='2'):
return '<strong>'.$nome.'</strong> <small>(Seu Apelido está banido nesta sala)</small>';
die();  
elseif($ip=='3'):
return '<strong>'.$nome.'</strong> <small>(Seu Ip está banido nesta sala)</small>';
die();  
else:
return '<a href="'.$link.'"><strong>'.$nome.'</strong></a>';
endif;
}
function EnviarSmilie($codigo,$arquivo){
$db= new SQLite('site');
$dados=array(
'codigo'=>$codigo,
'url'=>$arquivo,
);
$ex=$db->insert('smilies', $dados);
if($ex):
return true;
else:
return false;
endif;
}
function Smilies($texto){
$db= new SQLite('site');
$db->query("SELECT codigo, url FROM smilies");  
while (list($codigo, $url) = $db->fetch('row')) {  
$texto = str_replace($codigo,'<img src="'.URL_SML.$url.'" title="'.$codigo.'"/>',$texto);
}
return $texto;	
}
function Links(){
global $imagecache;
$uri=multiexplode(array('/','?'),$_SERVER['REQUEST_URI']);
if($_GET['sala']>0):
$link='?sala='.$_GET['sala'].'&token='.$_GET['token'];
else:
$link='?token='.$_GET['token'].'';
endif;
if($uri[1]=='salas'):
$menu='';
else:
$menu='<a href="salas?token='.$_GET['token'].'"><strong><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">Salas</strong></a> ';
endif;
if($uri[1]=='chat'):
$chat='';
else:
if($_GET['sala']>0):
$chat='<a href="chat?sala='.$_GET['sala'].'&token='.$_GET['token'].'"><strong><img src="'.$imagecache->cache(URL_IMAGENS.'sala.png').'">Voltar</strong></a> ';
else:
$chat='';
endif;
endif;
if($uri[1]=='smilies'):
$smilies='';
else:
$smilies='<a href="smilies'.$link.'"><strong><img src="'.$imagecache->cache(URL_IMAGENS.'smilies.png').'">Smilies</strong></a> ';
endif;
if($uri[1]=='bbcodes'):
$bbcodes='';
else:
$bbcodes='<a href="bbcodes'.$link.'"><strong><img src="'.$imagecache->cache(URL_IMAGENS.'bbcodes.png').'">BBCodes</strong></a> ';
endif;
return '<center>'.$menu.$chat.$config.$smilies.$bbcodes.'<a href="sair?token='.$_GET['token'].'"><strong><img src="'.$imagecache->cache(URL_IMAGENS.'sair.png').'">Sair</strong></a></center>';
}
function Avisos(){
global $imagecache;
if(User(MeuID())==false):
$at='<div class="atencao"><img src="'.$imagecache->cache(URL_IMAGENS.'atencao.png').'">Sua conta esta em modo <strong>Visitante</strong>, ative a conta permanente para reservar seu apelido e ter acesso a todos os recursos do Chat.<br/><a href="configuracoes?m=conta&token='.$_GET['token'].'"><strong>Clique aqui para Ativar</strong></a></div>';
else:
endif;
return $at;
}
function GeraAvatar($id){
if(file_exists(ROOT_AVT.DadosUsuario('avatar',$id)) AND strlen(DadosUsuario('avatar',$id))>30):
return '<img width="24" height="24" src="'.URL_AVT.DadosUsuario('avatar',$id).'" />';
else:
return '<img width="24" height="24" src="'.URL_IMAGENS.'semfoto.png" />';
endif;    
}
function GeraAvatarP($id){
if(file_exists(ROOT_AVT.DadosUsuario('avatar',$id)) AND strlen(DadosUsuario('avatar',$id))>30):
return '<img width="82" height="82" src="'.URL_AVT.DadosUsuario('avatar',$id).'" />';
else:
return '<img width="82" height="82" src="'.URL_IMAGENS.'semfoto.png" />';
endif;    
}
function AtualizarAvatar($arquivo){
$db= new SQLite('site');
$ex=$db->statement("UPDATE sessao SET avatar=? WHERE id = ?",array($arquivo,MeuID()));	
if($ex):
return true;
else:
return false;
endif;
}
function AtivarConta($senha){
$db= new SQLite('site');
$ex=$db->statement("UPDATE sessao SET senha=?,fixo=? WHERE id = ?",array(md5($senha),1,MeuID()));	
if($ex):
return true;
else:
return false;
endif;
}
function AtualizaSenha($senha){
$db= new SQLite('site');
$ex=$db->statement("UPDATE sessao SET senha=? WHERE id = ?",array(md5($senha),MeuID()));	
if($ex):
return true;
else:
return false;
endif;
}
function SenhaAntigaOK($senha){
$db= new SQLite('site');
$n=$db->value("SELECT id FROM sessao WHERE senha=? AND id=?",array(md5($senha),MeuID()));
if($n>0):
return true;
else:
return false;
endif;
}
function GeraSexo($id){
$db= new SQLite('site');
$n=$db->value("SELECT sexo FROM sessao WHERE id=?",array($id));
if($n=='M'):
return 'Masculino';
elseif($n=='F'):
return 'Feminino';
elseif($n=='G'):
return 'Gay';
elseif($n=='L'):
return 'Lesbica';
else:
return 'Não Definido';
endif;
}
function AtualizaApelido($apelido){
$db= new SQLite('site');
$ex=$db->statement("UPDATE sessao SET apelido=? WHERE id = ?",array($apelido,MeuID()));	
if($ex):
return true;
else:
return false;
endif;
}
function AtualizaSexo($sexo){
$db= new SQLite('site');
$ex=$db->statement("UPDATE sessao SET sexo=? WHERE id = ?",array($sexo,MeuID()));	
if($ex):
return true;
else:
return false;
endif;
}
function AtualizaCores($cor_texto,$cor_fundo,$cor_apelido){
$db= new SQLite('site');
$ex=$db->statement("UPDATE sessao SET cor_texto=?,cor_fundo=?,cor_apelido=? WHERE id = ?",array($cor_texto,$cor_fundo,$cor_apelido,MeuID()));	
if($ex):
return true;
else:
return false;
endif;
}
function RemoverItem($item,$id,$acao){
if(Admin(MeuID())>0 AND $acao=='remover'):
$db= new SQLite('site');
$db->statement("DELETE FROM {$item} WHERE id=?",array($id));
endif;	
}
function RemoverItemPrivadas($item,$id,$acao){
if((Admin(MeuID())>0 OR DonoSala(MeuID(),$id)) AND $acao=='remover'):
$db= new SQLite('site');
$db->statement("DELETE FROM {$item} WHERE id=?",array($id));
endif;	
}
function RemoverConversaPrivadas($usuario,$acao){
if((Admin(MeuID())>0 OR DonoSala(MeuID(),$usuario)) AND $acao=='remover'):
$db= new SQLite('site');
$db->statement("DELETE FROM {$item} WHERE (usuario=? AND para=?) OR (para=? AND usuario=?)",array($usuario,MeuID()));
endif;	
}
function RemoverSala($id,$acao){
if((Admin(MeuID())>0 OR DonoSala(MeuID(),$id)) AND $acao=='remover'):
$db= new SQLite('site');
$db->statement("DELETE FROM salas WHERE id=?",array($id));
endif;	
}
function RemoverBloqueio($id,$acao){
if(Admin(MeuID())>0  AND $acao=='remover'):
$db= new SQLite('site');
$db->statement("DELETE FROM bloqueios WHERE id=?",array($id));
endif;	
}
function User($id){
$db= new SQLite('site');
$n=$db->value("SELECT id FROM sessao WHERE id=? AND fixo=?",array($id,1));
if($n>0):
return true;
else:
return false;
endif;
}
function DonoSala($id,$sala){
$db= new SQLite('site');
$n=$db->value("SELECT id FROM salas WHERE dono=? AND id=?",array($id,$sala));
if($n>0):
return true;
else:
return false;
endif;
}
function Admin($id){
$db= new SQLite('site');
$n=$db->value("SELECT id FROM sessao WHERE id=? AND admin=?",array($id,1));
if($n>0):
return true;
else:
return false;
endif;
}
function LoginOK($login,$senha){
$db= new SQLite('site');
$n=$db->value("SELECT id FROM sessao WHERE apelido=? AND senha=?",array($login,md5($senha)));
if($n>0):
return true;
else:
return false;
endif;
}
function GravaoSessaoEquipe($login,$senha,$token){
$db= new SQLite('site');   
$ex=$db->statement("UPDATE sessao SET token=?,navegador=?,tempo=?,ip=? WHERE apelido=? AND senha=?",array($token,GeraNav(),time(),GeraIP(),$login,md5($senha)));
if($ex):
return true;
else:
return false;
endif;
}
function LimpaSite(){
$db= new SQLite('site');
$tempo=time()-TEMPO_SESSAO;
$quique=time()-TEMPO_QUIQUE;
$bloq=time()-TEMPO_BLOQUE;
$temposala=time()-TEMPO_SALA;
@$db->statement("DELETE FROM salas WHERE fixa=? AND dono>? AND tempo<?",array(0,0,$temposala));
@$db->statement("DELETE FROM bloqueios WHERE tipo=? AND tempo<?",array(1,$quique));
@$db->statement("DELETE FROM bloqueios WHERE tipo=? AND tempo<?",array(2,$bloq));
@$db->statement("DELETE FROM bloqueios WHERE tipo=? AND tempo<?",array(3,$bloq));
@$db->statement("DELETE FROM mensagens WHERE tempo<?",array($tempo));
@$db->statement("DELETE FROM online WHERE tempo<?",array($tempo));
@$db->statement("DELETE FROM sessao WHERE fixo=? AND tempo<?",array(0,$tempo));	
@$db->statement("UPDATE sessao SET tempo = ? WHERE id = ?",array(time(),MeuID()));
if(@$_GET['sala']>0):
@$db->statement("UPDATE salas SET tempo = ? WHERE id = ?",array(time(),$_GET['sala']));
endif;
@$db->statement("UPDATE online SET tempo = ? WHERE usuario = ?",array(time(),MeuID()));
}
function SelecionaPvt($id){
global $form;
$db= new SQLite('site');
$tempo=time()-TEMPO_SESSAO;
$db->query("SELECT usuario FROM online WHERE sala_id=? AND tempo>? AND usuario!=?",array($id,$tempo,MeuID())); 
while (list($usuario) = $db->fetch('row')):  
if(UsuarioExiste($usuario)>0):
$para[$usuario]=GeraApelidoL($usuario);
endif;
endwhile;
return $form->field('select', 'para', 'Para:',$para);	
}
function SelecionaFavorito(){
global $form;
$db= new SQLite('site');
$db->query("SELECT favorito FROM favoritos WHERE usuario=?",array(MeuID())); 
while (list($favorito) = $db->fetch('row')):  
if(UsuarioExiste($favorito)>0):
$para[$favorito]=GeraApelidoL($favorito);
endif;
endwhile;
return $form->field('select', 'para', 'Para:',$para);	
}
function GeraApelido($id,$idmsg=''){
if(DadosUsuario('sexo',$id)=='M' AND strlen(utf8_decode(DadosUsuario('cor_apelido',$id)))<7):
$cor='#0163FF';
elseif(DadosUsuario('sexo',$id)=='F' AND strlen(utf8_decode(DadosUsuario('cor_apelido',$id)))<7):
$cor='#EF04FF';
elseif(DadosUsuario('sexo',$id)=='G' AND strlen(utf8_decode(DadosUsuario('cor_apelido',$id)))<7):
$cor='#FF0174';
elseif(DadosUsuario('sexo',$id)=='L' AND strlen(utf8_decode(DadosUsuario('cor_apelido',$id)))<7):
$cor='#AD64FF';
else:
$cor=DadosUsuario('cor_apelido',$id);
endif;    
if(Admin($id)):
$apelido='<strong style="color:'.$cor.';">'.Filtro::text(DadosUsuario('apelido',$id)).'</strong>';
elseif(DadosUsuario('id',$id)>0):
$apelido='<strong style="color:'.$cor.';">'.Filtro::text(DadosUsuario('apelido',$id)).'</strong>';
else:
$apelido='<strong><em>MobyChat</em></strong>';
endif;
$uri=multiexplode(array('/','?'),$_SERVER['REQUEST_URI']);
if($uri[1]=='conversas'):
if($_GET['sala']>0):
$link='<a href="conversas?menu=ler&id='.$idmsg.'&sala='.$_GET['sala'].'&token='.$_GET['token'].'">';
else:
$link='<a href="conversas?menu=ler&id='.$idmsg.'&token='.$_GET['token'].'">';
endif;
else:
if($_GET['sala']>0):
$link='<a href="usuario?id='.$id.'&sala='.$_GET['sala'].'&token='.$_GET['token'].'">';
else:
$link='<a href="usuario?id='.$id.'&token='.$_GET['token'].'">';
endif;
endif;

return $link.$apelido.'</a>';
}
function GeraApelidoL($id){
if(Admin($id)):
return DadosUsuario('apelido',$id);
elseif(DadosUsuario('id',$id)>0):
return DadosUsuario('apelido',$id);
else:
return 'MobyChat';
endif;
}
function MeuID(){
$db= new SQLite('site');
$dd=$db->value("SELECT id FROM sessao WHERE token=?",array($_GET['token']));
return $dd;
}
function UsuarioExiste($id){
$db= new SQLite('site');
$dd=$db->value("SELECT COUNT(*) FROM sessao WHERE id=?",array($id));
return $dd;
}
function SalaEmUso($sala){
$db= new SQLite('site');
$dd=$db->value("SELECT COUNT(*) FROM salas WHERE nome=?",array($sala));
return $dd;
}
function ApelidoEmUso($apelido){
$db= new SQLite('site');
$dd=$db->value("SELECT COUNT(*) FROM sessao WHERE apelido=?",array($apelido));
return $dd;
}
function SalaAtual(){
$db= new SQLite('site');
$dd=$db->value("SELECT id FROM online WHERE usuario=?",array(MeuID()));
return $dd;
}
function OnlineSite(){
$db= new SQLite('site');
$tempo=time()-TEMPO_SESSAO;
$num = $db->value("SELECT COUNT(*) FROM online WHERE tempo>?",array($tempo));	
if($num>0):
return $num;
else:
return 0;
endif;
}
function ContSalas($d=0,$t=0){
$db= new SQLite('site');
$num = $db->value("SELECT COUNT(*) FROM salas WHERE dono=? AND fixa=?",array($d,$t));	
if($num>0):
return $num;
else:
return 0;
endif;
}
function OnlineSala($id){
$db= new SQLite('site');
$tempo=time()-TEMPO_SESSAO;
$num = $db->value("SELECT COUNT(*) FROM online WHERE sala_id=? AND tempo>?",array($id,$tempo));	
if($num>0):
return $num;
else:
return 0;
endif;
}
function ContMsgSala($id){
$db= new SQLite('site');
$num = $db->value("SELECT COUNT(*) FROM mensagens WHERE sala_id=?",array($id));	
if($num>0):
return $num;
else:
return 0;
endif;
}
function EntraChat($id){
$db= new SQLite('site');
if(OnlineExiste()==true):
$db->statement("UPDATE online SET sala_id = ?,tempo=? WHERE usuario = ?",array($id,time(),MeuID()));
else:
$dados=array(
'usuario'=>MeuID(),
'sala_id'=>$id,
'tempo'=>time(),
);
$ex=$db->insert('online', $dados);
endif;
}
function EnviarMensagem($texto,$para,$pvt,$id,$arquivo=''){
$db= new SQLite('site');
$dados=array(
'usuario'=>MeuID(),
'para'=>$para,
'privado'=>$pvt,
'texto'=>$texto,
'sala_id'=>$id,
'anexo'=>$arquivo,
'tempo'=>time(),
);
$ex=$db->insert('mensagens', $dados);
if($ex):
return true;
else:
return false;
endif;	
}
function GravaoSessao($apelido,$cor_texto='',$cor_fundo='',$cor_apelido='',$token){
$db= new SQLite('site');    
if(SessaoExiste()==true):
$ex=$db->statement("UPDATE sessao SET apelido=?,cor_texto=?,cor_fundo=?,cor_apelido=?,token=?,navegador=?,tempo=? WHERE ip = ?",array($apelido,$cor_texto,$cor_fundo,$cor_apelido,$token,GeraNav(),time(),GeraIP()));
if($ex):
return true;
else:
return false;
endif;
else:
$dados=array(
'apelido'=>$apelido,
'cor_texto'=>$cor_texto,
'cor_fundo'=>$cor_fundo,
'cor_apelido'=>$cor_apelido,
'token'=>$token,
'ip'=>GeraIP(),
'navegador'=>GeraNav(),
'tempo'=>time(),
);
$ex=$db->insert('sessao', $dados);
if($ex):
return true;
else:
return false;
endif;
endif;
}

function OnlineExiste(){
$db= new SQLite('site');
$dd=$db->value("SELECT COUNT(*) FROM online WHERE usuario=?",array(MeuID()));	
if($dd>0):
return true;
else:
return false;
endif;
}
function SessaoExiste(){
$db= new SQLite('site');
$ses=$_GET['token'];
if(!empty($ses)):
$dd=$db->value("SELECT id FROM sessao WHERE ip=? AND token=?",array(GeraIP(),$ses));	
if($dd>0):
return true;
else:
return false;
endif;
else:
return false;
endif;
}
function NomeSala($id){
$db= new SQLite('site');
$dd=$db->value("SELECT nome FROM salas WHERE id=?",array($id));	
return $dd;
}
function VerificaSessao(){
$db= new SQLite('site');
$tempo=time()-TEMPO_SESSAO;
$ses=$_GET['token'];
if(!empty($ses)):
$dd=$db->value("SELECT id FROM sessao WHERE token=? AND ip=? AND navegador=? AND tempo>?",array($ses,GeraIP(),GeraNav(),$tempo));	
if($dd>0):
return true;
else:
return false;
endif;
else:
return false;
endif;
}
function multiexplode ($delimiters,$string) {
$ready = str_replace($delimiters, $delimiters[0], $string);
$launch = explode($delimiters[0], $ready);
return  $launch;
}
function DadosViaIp($item){
$db= new SQLite('site');
$ses=Cookie::get('token');
if(!empty($ses)):
$dd=$db->value("SELECT {$item} FROM sessao WHERE ip=? AND token=?",array(GeraIP(),$ses));
return 	$dd;
else:
return 	false;
endif;
}
function DadosUsuario($item,$id){
$db= new SQLite('site');
$dd=$db->value("SELECT {$item} FROM sessao WHERE id=?",array($id));
return 	$dd;
}
function GeraNav()
{
return Detect::browser().'('.Detect::os().')';
}
function ListaArray($dados){
$item='';    
foreach ($dados as $key => $value) {
$item.="<strong>{$key}</strong>: {$value}<br/>"; 
}
return $item;	
}
function Rodape($cachep){
global $page,$html,$cache;
if(DEVELOPER==true AND Admin(MeuID())):
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);
$html.='</div><div class="rodape"><center><strong>&copy; MobyChat</strong><br/><small>Desenvolvido por Lord Morpheus</small></center></div>';
$html.='<center><h5>Modo Depuração: On</h5></center><br/>';
$html.='<strong>Cookies:</strong><br/><code>'.ListaArray($_COOKIE).'</code>';
$html.='<strong>Sessao:</strong><br/><code>'.ListaArray($_SESSION).'</code>';
$html.='<strong>Tempo de Execução:</strong>  '. $elapsed_time. ' segundo(s)<br/>';
$html.='<strong>Memoria Usada:</strong> '.round(((memory_get_peak_usage(true) / 1024) / 1024), 2).'Mb';
$html= $page->display ($html);  
return $html;
$cache->clear();
ob_end_flush();
elseif($cachep==false):
$html.='</div><div class="rodape"><center><strong>&copy; MobyChat</strong><br/><small>Desenvolvido por Lord Morpheus</small></center></div>';
$html= $page->display ($html);  
return  $html;
$cache->clear();
ob_end_flush();
else:
$html.='</div><div class="rodape"><center><strong>&copy; MobyChat</strong><br/><small>Desenvolvido por Lord Morpheus</small></center></div>';
$html= $page->display ($html);  
$cache->page($html); 
return $html; 
$cache->clear();
ob_end_flush();
endif;  	
}
function Subtitulo(){
@$uri=multiexplode(array('/','?'),$_SERVER['REQUEST_URI']);
$sub=ucfirst(str_replace(array('_','-'),array(' ',' '),$uri[1]));
if(strlen($uri[1])>1):
return $sub;
else:
return 'Bem Vindo';
endif;
}
function GeraIP() {
$ipaddress = '';
if (isset($_SERVER['HTTP_CLIENT_IP']))
$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
else if (isset($_SERVER['HTTP_CF_CONNECTING_IP']))
$ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
else if (isset($_SERVER['HTTP_INCAP_CLIENT_IP']))
$ipaddress = $_SERVER['HTTP_INCAP_CLIENT_IP'];
else if (isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']))
$ipaddress = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
else if(isset($_SERVER['HTTP_X_FORWARDED']))
$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
else if(isset($_SERVER['HTTP_FORWARDED']))
$ipaddress = $_SERVER['HTTP_FORWARDED'];
else if(isset($_SERVER['REMOTE_ADDR']))
$ipaddress = $_SERVER['REMOTE_ADDR'];
else
$ipaddress = '0.0.0.0';
return $ipaddress;
}
function MostraPagina ($content,$links) { 
global $page; 
$html = ''; 
$yui = new YUI(750); 
$html .= $yui->page('left', 160,$links); 
$html .= $yui->header('<h1>' . $page->title() . '</h1>'); 
$html .= $yui->body($content); 
$html .= $yui->footer('Copyright &copy; ' . date('Y') . ' ' . Site('titulo')); 
$html .= $yui->close(); 
unset ($yui); 
echo $page->display($html); 
ob_end_flush(); 
} 
function Site($dados){
$db= new SQLite('site');
$dd=$db->value("SELECT {$dados} FROM site");
return $dd;
}
function Texto($texto)
{
$bbcode = new BBCode; 
$parsedown = new Parsedown;
$parsedown->setMarkupEscaped(true);
$texto=$parsedown->line($texto);
$texto=Smilies($texto);
$texto=$bbcode->toHTML($texto);
return $texto;
}

<?php
require_once (ROOT_INC.'topo.inc.php');
if(VerificaSessao()):
$html.=Avisos();
switch ($_GET['m']):
default:
$html.='<div class="menu">Menu Configurações</div>';
if(User(MeuID())):
$html.='<div class="linha2"><a href="configuracoes?m=senha&token='.$_GET['token'].'"><strong>Alterar Senha</strong></a></div>';
$html.='<div class="linha1"><a href="configuracoes?m=sala&token='.$_GET['token'].'"><strong>Criar Sala</strong></a></div>';
else:
$html.='<div class="linha1"><a href="configuracoes?m=conta&token='.$_GET['token'].'"><strong>Ativar Conta Permanete</strong></a></div>';
endif;
$html.='<div class="linha2"><a href="configuracoes?m=sexo&token='.$_GET['token'].'"><strong>Alterar Sexo</strong></a></div>';
$html.='<div class="linha1"><a href="configuracoes?m=apelido&token='.$_GET['token'].'"><strong>Alterar Apelido</strong></a></div>';
$html.='<div class="linha2"><a href="configuracoes?m=cores&token='.$_GET['token'].'"><strong>Alterar Cores</strong></a></div>';
if(User(MeuID())):
$html.='<div class="linha1"><a href="configuracoes?m=avatar&token='.$_GET['token'].'"><strong>Enviar Avatar</strong></a></div>';
endif;
if(Admin(MeuID())):
$html.='<div class="linha2"><a href="configuracoes?m=equipe&token='.$_GET['token'].'"><strong>Gerenciar Site</strong></a></div>';
endif;
break;
case'equipe':
if(Admin(MeuID())):
$html.='<div class="menu">Configuração do site</div>';
$html.='<div class="linha1"><a href="configuracoes?m=smilie&token='.$_GET['token'].'"><strong>Enviar Smilies</strong></a></div>';
$html.='<div class="linha2"><a href="configuracoes?m=sala_publica&token='.$_GET['token'].'"><strong>Criar Sala Publica</strong></a></div>';
$html.='<div class="linha1"><a href="configuracoes?m=bloqueios&token='.$_GET['token'].'"><strong>Penalidades</strong></a></div>';
endif;
break;
case 'bloqueios':
if(Admin(MeuID())):
RemoverBloqueio($_GET['id'],$_GET['acao']);
$html.='<div class="menu">Bloqueios</div>';
$db= new SQLite('site');
$db->query("SELECT id,de,em,sala,tipo,ip,navegador,tempo FROM bloqueios ORDER BY tempo DESC"); 
while (list( $id,$de,$em,$sala,$tipo,$ip,$navegador,$tempo) = $db->fetch('row')) :
if ($cor == "linha1"){$cor = "linha2";}else{$cor = "linha1";}
$html.='<div class="'.$cor.'">'.GeraApelido($de).' '.TipoBloque($tipo).' '.GeraApelido($em).'<a href="configuracoes?m=bloqueios&id='.$id.'&acao=remover"><strong>[X]</strong></a><br/><strong>Bloquiado Até:</strong> '.date("H:i:s",$tempo).'<br/><strong>Ip:</strong> '.$ip.'<br/><strong>Navegador:</strong> '.$navegador.'<br/><strong>Sala:</strong> '.NomeSala($sala).'</div>';
endwhile;
endif;
break;
case 'sala_publica':
if(Admin(MeuID())):
$html.='<div class="menu">Criar Sala Privada</div>';
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST'):
if(SalaEmUso(trim($_POST['sala']))>0):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Está sala já existe!!';
elseif(strlen(utf8_decode($_POST['sala']))<3 OR strlen(utf8_decode($_POST['sala']))>60):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">O nome da sala deve ter no minimo 3 caracteres e no maximo 60';
else:
if(CriarSalaPublica(trim($_POST['sala']))):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'ok.png').'">Sala criada com sucesso!!';
else:
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Erro ao criar sala!!';
endif;
endif;
endif;
$form = new Form('sala');	
$html.= $form->header('sala','POST','configuracoes?m=sala_publica&token='.$_GET['token'].'');
$html.= $form->field('text', 'sala', 'Nome da Sala:', array('width'=>150, 'maxlength'=>60)).'<br/>';
$html.= $form->field('submit', 'Criar sala');
$html.= $form->close();
endif;
break;
case 'sala':
$html.='<div class="menu">Criar Sala Privada</div>';
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST'):
if(SalaEmUso(trim($_POST['sala']))>0):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Está sala já existe!!';
elseif(strlen(utf8_decode($_POST['sala']))<3 OR strlen(utf8_decode($_POST['sala']))>60):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">O nome da sala deve ter no minimo 3 caracteres e no maximo 60';
else:
if(CriarSalaPrivada(trim($_POST['sala']),trim($_POST['senha']))):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'ok.png').'">Sala criada com sucesso!!';
else:
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Erro ao criar sala!!';
endif;
endif;
endif;
$form = new Form('sala');	
$html.= $form->header('sala','POST','configuracoes?m=sala&token='.$_GET['token'].'');
$html.= $form->field('text', 'sala', 'Nome da Sala:', array('width'=>150, 'maxlength'=>60)).'<br/>';
$html.= $form->field('password', 'senha', 'Senha da Sala:<small>(Apenas quem tem a senha tera acesso)</small>', array('width'=>60, 'maxlength'=>60)).'<br/>';
$html.= $form->field('submit', 'Criar sala');
$html.= $form->close();
break;
case 'smilie':
$html.='<div class="menu">Enviar Smilie</div>';
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post' AND CSRF::validate($_POST)) {
$upload = new Upload('arquivo');
$upload->set_upload_path(ROOT_SML);
$upload->set_max_size('5MB');
$upload->set_allowed_types('jpeg|jpg|png|gif');
$arquivo=md5(time()).'.'.$upload->get_ext();
$upload->set_name($arquivo);
if ($upload->run() !== false) {
if(EnviarSmilie($_POST['codigo'],$arquivo)){
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'ok.png').'">Smilie enviado com sucesso!!';   
}else{
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Não foi possivel enviar o arquivo procure o programador do site!!';
}
}else{
$erro=$upload->get_errors();
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">'.$erro[0].'!!';}
}
$form = new Form('smilie');	
$html.= $form->header('smilie','post','configuracoes?m=smilie&token='.$_GET['token'].'');
$html.= $form->field('text', 'codigo', 'Codigo:', array('width'=>60, 'maxlength'=>60)).'<br/>';
$html.= $form->field('file', 'arquivo', 'Smilie:').'<br/>';
$html.= $form->field('submit', 'Enviar Avatar');
$html.= $form->close();
break;
case 'sexo':
$html.='<div class="menu">Escolha seu Sexo</h5></div>';
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST'):
if(AtualizaSexo($_POST['sexo'])):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'ok.png').'">Dados Atualizados com sucesso!!';
else:
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Erro ao atualizar dados!!';
endif;
endif;
$form = new Form('sexo');	
$html.= $form->header('sexo','POST','configuracoes?m=sexo&token='.$_GET['token'].'');
$html.= $form->field('select', 'sexo', 'Sexo:', array('M'=>'Masculino','F'=>'Feminino','G'=>'Gay','L'=>'Lesbica')).'<br/>';
$html.= $form->field('submit', 'Atualizar');
$html.= $form->close();
break;
case 'conta':
$html.='<div class="menu">Ativar Conta Permanente</div>';
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST' AND User(MeuID())==false):
if(strlen(utf8_decode($_POST['senha']))<5 OR strlen(utf8_decode($_POST['senha']))>9):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">A senha deve ter no minimo 4 caracteres e no maximo 8!!';
elseif($_POST['senha']!=$_POST['rsenha']):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">As senhas não são iguais!!';
else:
if(AtivarConta($_POST['senha'])):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'ok.png').'">Dados Atualizados com sucesso!!';
else:
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Erro ao atualizar dados!!';
endif;
endif;
endif;
$form = new Form('senha');	
$html.= $form->header('conta','POST','configuracoes?m=conta&token='.$_GET['token'].'');
$html.= $form->field('password', 'senha', 'Nova Senha:', array('width'=>250, 'maxlength'=>8)).'<br/>';
$html.= $form->field('password', 'rsenha', 'Repita a Nova Senha:', array('width'=>250, 'maxlength'=>8)).'<br/>';
$html.= $form->field('submit', 'Atualizar');
$html.= $form->close();
break;
case 'senha':
$html.='<div class="menu">Mudar Senha</div>';
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST'):
if(SenhaAntigaOK($_POST['senha_antiga'])==false):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Senha antiga não confere!!';
elseif(strlen(utf8_decode($_POST['senha']))<5 OR strlen(utf8_decode($_POST['senha']))>9):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">A senha deve ter no minimo 4 caracteres e no maximo 8!!';
elseif($_POST['senha']!=$_POST['rsenha']):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">As senhas não são iguais!!';
else:
if(AtualizaSenha($_POST['senha'])):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'ok.png').'">Dados Atualizados com sucesso!!';
else:
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Erro ao atualizar dados!!';
endif;
endif;
endif;
$form = new Form('senha');	
$html.= $form->header('senha','POST','configuracoes?m=senha&token='.$_GET['token'].'');
$html.= $form->field('password', 'senha_antiga', 'Senha Antiga:', array('width'=>250, 'maxlength'=>8)).'<br/>';
$html.= $form->field('password', 'senha', 'Nova Senha:', array('width'=>250, 'maxlength'=>8)).'<br/>';
$html.= $form->field('password', 'rsenha', 'Repita a Nova Senha:', array('width'=>250, 'maxlength'=>8)).'<br/>';
$html.= $form->field('submit', 'Atualizar');
$html.= $form->close();
break;
case 'apelido':
$html.='<div class="menu">Alterar Apelido</div>';
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST'):
if(ApelidoEmUso(trim($_POST['apelido']))>0):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Apelido já está sendo usado!!';
elseif(strlen(utf8_decode($_POST['apelido']))<3 OR strlen(utf8_decode($_POST['apelido']))>60):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">O Apelido deve ter no minimo 3 caracteres e no maximo 60';
else:
if(AtualizaApelido(trim($_POST['apelido']))):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'ok.png').'">Dados Atualizados com sucesso!!';
else:
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Erro ao atualizar dados!!';
endif;
endif;
endif;
$form = new Form('apelido');	
$html.= $form->header('apelido','POST','configuracoes?m=apelido&token='.$_GET['token'].'');
$html.= $form->field('text', 'apelido', 'Novo Apelido:', array('width'=>250, 'maxlength'=>60)).'<br/>';
$html.= $form->field('submit', 'Entrar');
$html.= $form->close();
break;
case 'cores':
$html.='<div class="menu">Atualizar Cores</div>';
if (CSRF::validate($_POST) AND $_SERVER["REQUEST_METHOD"] == 'POST'):
if(AtualizaCores($_POST['cor_texto'],$_POST['cor_fundo'],$_POST['cor_apelido'])):
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'ok.png').'">Dados Atualizados com sucesso!!';
else:
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Erro ao atualizar dados!!';
endif;
endif;
$form = new Form('configuracoes');	
$html.= $form->header('configuracoes','POST','configuracoes?m=cores&token='.$_GET['token'].'');
$html.= $form->field('select', 'cor_texto', 'Cor do Texto:', array('#848484'=>'Cinza','#FF9601'=>'Laranja','#04A201'=>'Verde','#F3FF01'=>'Amarelo','#FF0101'=>'Vermelho', '#2A63FF'=>'Azul','#FF01F7'=>'Rosa')).'<br/>';
$html.= $form->field('select', 'cor_fundo', 'Cor fundo do Texto:', array('#D6D5D5'=>'Cinza Leve','#FFE0C3'=>'Laranja Leve','#A1FFA3'=>'Verde Leve','#FBFFC1'=>'Amarelo Leve','#FFBFBF'=>'Vermelho Leve', '#CCE0FF'=>'Azul Leve','#FCD2FF'=>'Rosa Leve')).'<br/>';
$html.= $form->field('select', 'cor_apelido', 'Cor do Apelido:', array('#848484'=>'Cinza','#FF9601'=>'Laranja','#04A201'=>'Verde','#F3FF01'=>'Amarelo','#FF0101'=>'Vermelho', '#2A63FF'=>'Azul','#FF01F7'=>'Rosa')).'<br/>';
$html.= $form->field('submit', 'Atualizar');
$html.= $form->close();
break;
case 'avatar':
$html.='<div class="menu">Enviar Avatar</div>';
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post' AND CSRF::validate($_POST)) {
$upload = new Upload('arquivo');
$upload->set_upload_path(ROOT_AVT);
$upload->set_max_size('5MB');
$upload->set_allowed_types('jpeg|jpg|png|gif');
$arquivo=md5(time()).'.'.$upload->get_ext();
$upload->set_name($arquivo);
if ($upload->run() !== false) {
$image = new Imagem;
$image->url(URL_AVT.$arquivo);
$image->resize (64,64);
$image->save(ROOT_AVT.$arquivo);
if(AtualizarAvatar($arquivo)){
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'ok.png').'">Avatar Alterado com sucesso!!';   
}else{
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">Não foi possivel enviar o arquivo procure o programador do site!!';
}
}else{
$erro=$upload->get_errors();
$html.='<img src="'.$imagecache->cache(URL_IMAGENS.'erro.png').'">'.$erro[0].'!!';}
}
$form = new Form('avatar');	
$html.= $form->header('avatar','post','configuracoes?m=avatar&token='.$_GET['token'].'');
$html.= $form->field('file', 'arquivo', 'Avatar:').'<br/>';
$html.= $form->field('submit', 'Enviar Avatar');
$html.= $form->close();
break;
endswitch;
$html.=Links();
else:
header("Location: index");
die();  
endif;
require_once (ROOT_INC.'rodape.inc.php');
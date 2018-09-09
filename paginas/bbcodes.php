<?php
require_once (ROOT_INC.'topo.inc.php');
if(VerificaSessao()):
$html.=Avisos();
Avisos();
$html.='<div class="menu">'.Subtitulo().'</div>';
$html.='<div class="linha1">[b]Texto Aqui[/b]<br/><strong>Exemplo:</strong> <b>Texto Aqui</b></div>';
$html.='<div class="linha2">[i]Texto Aqui[/i]<br/><strong>Exemplo:</strong> <i>Texto Aqui</i></div>';
$html.='<div class="linha1">[code]Codigo Aqui[/code]<br/><strong>Exemplo:</strong> <pre><code>Codigo Aqui</code></pre></div>';
$html.='<div class="linha2">[size=12]Texto Aqui[/size]<br/><strong>Exemplo:</strong> <span style="font-size:12%">Texto Aqui</span></div>';
$html.='<div class="linha1">[s]Texto Aqui[/s]<br/><strong>Exemplo:</strong> <del>Texto Aqui</del></div>';
$html.='<div class="linha2">[center]Texto Aqui[/center]<br/><strong>Exemplo:</strong> <div style="text-align:center;">Texto Aqui</div></div>';
$html.='<div class="linha1">[color=blue]Texto Aqui[/color]<br/><strong>Exemplo:</strong> <span style="color:blue;">Texto Aqui</span></div>';
$html.='<div class="linha2">[email]Seu Email Aqui[/email]<br/><strong>Exemplo:</strong> <a href="mailto:fabio.dj.rs@gmail.com">Seu Email Aqui</a></div>';
$html.='<div class="linha1">[url]url aqui[/url]<br/><strong>Exemplo:</strong> <a href="http://mobychat.com">http://mobychat.com</a></div>';
$html.='<div class="linha2">[url=link aqui]Titulo[/url]<br/><strong>Exemplo:</strong> <a href="http://mobychat.com">Titulo</a></div>';
$html.='<div class="linha1">[img]link da imagem[/img]<br/><strong>Exemplo:</strong> <img src="icones/logo.png"/></div>';
$html.='<div class="linha2">[youtube]link do youtube[/youtube]<br/><strong>Exemplo:</strong> <div class="embed-responsive embed-responsive-4by3"><iframe class="embed-responsive-item" type="text/html" src="http://www.youtube.com/embed/xVPMJApIBUE" frameborder="0"></iframe></div></div>';
$html.=Links();
else:
header("Location: index");
die();  
endif;
require_once (ROOT_INC.'rodape.inc.php');
<?php
$html.='<center>Participe Pelo<br/><a href="https://chat.whatsapp.com/DkMt1pPDVEVCFzm2BfsXAU"><img src="'.$imagecache->cache(URL_IMAGENS.'whats.png').'">Grupo Whats</a></center>';
if(DEVELOPER==true AND Admin(MeuID())):
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);
$html.='<div class="rodape"><center><strong>&copy; '.SITE_TITULO.'</strong><br/><small>Desenvolvido por Lord Morpheus</small></center></div>';
$html.='<div class="erro">Modo Depuração: Ativado<br/>';
$html.='<strong>Cookies:</strong><br/><code>'.ListaArray($_COOKIE).'</code>';
$html.='<strong>Sessao:</strong><br/><code>'.ListaArray($_SESSION).'</code>';
$html.='<strong>Tempo de Execução:</strong>  '. $elapsed_time. ' segundo(s)<br/>';
$html.='<strong>Memoria Usada:</strong> '.round(((memory_get_peak_usage(true) / 1024) / 1024), 2).'Mb</div>';
$html2= $page->display ($html);  
echo $html2;
$cache->clear();
ob_end_flush();
elseif(MODO_CACHE==false):
$html.='<div class="rodape"><center><strong>&copy; '.SITE_TITULO.'</strong><br/><small>Desenvolvido por Lord Morpheus</small></center></div>';
$html2= $page->display ($html);  
echo  $html2;
$cache->clear();
ob_end_flush();
else:
$html.='<div class="rodape"><center><strong>&copy; '.SITE_TITULO.'</strong><br/><small>Desenvolvido por Lord Morpheus</small></center></div>';
$html2= $page->display ($html);  
$cache->page($html2); 
echo $html2; 
$cache->clear();
ob_end_flush();
endif;  
?>
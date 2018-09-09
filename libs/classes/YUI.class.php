<?php
class YUI {
private $doc;
private $showgrid;
private $column = false;
private $colors = array('border:2px solid #3F80BA; background-color:#538EC3; color:#FFFFFF;', 'border:2px solid #13B322; background-color:#64CD6E; color:#FFFFFF;', 'border:2px solid #990000; background-color:#FFDD00; color:#000000;', 'border:2px solid #24003F; background-color:#63639B; color:#FFFFFF;', 'border:2px solid #FF8C00; background-color:#FBE858; color:#233E8E;', 'border:2px solid #BE3A3E; background-color:#C64F52; color:#FFFFFF;', 'border:2px solid #886644; background-color:#CC9D59; color:#000000;', 'border:2px solid #CCCCCC; background-color:#DEDEDE; color:#000000;', 'border:2px solid #83A8CC; background-color:#F5F9FE; color:#003366;', 'border:2px solid #666666; background-color:#333333; color:#FFFFFF;', 'border:2px solid #AABBDD; background-color:#000033; color:#FFAA33;', 'border:2px solid #1B7E6F; background-color:#1A287A; color:#D1C923;', 'border:2px solid #FFDD33; background-color:#FAFAFA; color:#E01900;', 'border:2px solid #3D2500; background-color:#EE8800; color:#FFFFFF;', 'border:2px solid #1F563C; background-color:#53C64E; color:#FBFF24;'); // used in $this->div()
function __construct ($width, $showgrid=false) {
global $page;
$page->link('<link rel="stylesheet" type="text/css" href="'.URL_CSS.'yui.css" />');
if ($showgrid) $page->link('<style type="text/css">.showgrid { background:url("'.URL_ICONES.'grid.png") repeat scroll 0 0 transparent; }</style>');
switch ($width) {
case ('750'): $this->doc = 'doc'; break;
case ('950'): $this->doc = 'doc2'; break;
case ('100%'): $this->doc = 'doc3'; break;
case ('974'): $this->doc = 'doc4'; break;
default: 
$this->doc = 'custom-doc';
$page->link('<style type="text/css">#custom-doc{margin:auto;text-align:left;width:' . round($width/13, 3) . 'em;*width:' . round($width/13.3333, 3) . 'em;min-width:' . $width . 'px;}</style>');
break;
}
$this->showgrid = ($showgrid) ? ' class="showgrid"' : '';
}
public function page ($dir='', $width='', $content='') {
if (empty($dir)) return '<div id="' . $this->doc . '"' . $this->showgrid . '>';
if ($dir == 'left') {
if ($width == 160) $col = 'yui-t1';
elseif ($width == 180) $col = 'yui-t2';
elseif ($width == 300) $col = 'yui-t3';
} elseif ($dir == 'right') {
if ($width == 180) $col = 'yui-t4';
elseif ($width == 240) $col = 'yui-t5';
elseif ($width == 300) $col = 'yui-t6';
}
if (!isset($col)) return '<div id="' . $this->doc . '"' . $this->showgrid . '>';
$this->column = $content;
if (!empty($this->showgrid)) $col .= ' showgrid';
return '<div id="' . $this->doc . '" class="' . $col . '">';
}
public function header ($content) {
return "\n  " . '<div id="hd">' . $this->content($content) . '</div>';
}
public function body ($content) {
$html = "\n  ";
$html .= '<div id="bd">';
if ($this->column !== false) {
$html .= '<div id="yui-main"><div class="yui-b">' . $this->content($content) . '</div></div>';
$html .= '<div class="yui-b">' . $this->content($this->column) . '</div>';
} else {
$html .= $this->content($content);
}
$html .= '</div>';
return $html;
}
public function footer ($content) {
return "\n  " . '<div id="ft">' . $this->content($content) . '</div>';
}
public function close() {
return '</div>';
}
public function row ($content1) {
return "\n  " . '<div class="yui-g">' . $this->content($content1) . '</div>';
}
public function half ($content1, $content2) {
$html = "\n  ";
$html .= '<div class="yui-g">';
$html .= '<div class="yui-u first">' . $this->content($content1) . '</div>';
$html .= '<div class="yui-u">' . $this->content($content2) . '</div>';
$html .= '</div>';
return $html;
}
public function third ($content1, $content2, $content3) {
$html = "\n  ";
$class = 'yui-gb'; // 1/3, 1/3, 1/3
if (empty($content2)) $class = 'yui-gc'; // 2/3, 1/3
elseif (empty($content3)) $class = 'yui-gd'; // 1/3, 2/3
$html .= '<div class="' . $class . '">';
$html .= '<div class="yui-u first">' . $this->content($content1) . '</div>';
if ($class != 'yui-gc') $html .= '<div class="yui-u">' . $this->content($content2) . '</div>';
if ($class != 'yui-gd') $html .= '<div class="yui-u">' . $this->content($content3) . '</div>';
$html .= '</div>';
return $html;
}
public function quarter ($content1, $content2, $content3, $content4) {
$html = "\n  ";
if (empty($content2) && empty($content3)) { // 3/4, 1/4
$html .= '<div class="yui-ge">';
$html .= '<div class="yui-u first">' . $this->content($content1) . '</div>';
$html .= '<div class="yui-u">' . $this->content($content4) . '</div>';
$html .= '</div>';
} elseif (empty($content3) && empty($content4)) { // 1/4, 3/4
$html .= '<div class="yui-gf">';
$html .= '<div class="yui-u first">' . $this->content($content1) . '</div>';
$html .= '<div class="yui-u">' . $this->content($content2) . '</div>';
$html .= '</div>';
} else { // 1/4, 1/4, 1/4, 1/4
$html = '<div class="yui-g">';
$html .= '<div class="yui-g first">';
$html .= '<div class="yui-u first">' . $this->content($content1) . '</div>';
$html .= '<div class="yui-u">' . $this->content($content2) . '</div>';
$html .= '</div>';
$html .= '<div class="yui-g">';
$html .= '<div class="yui-u first">' . $this->content($content3) . '</div>';
$html .= '<div class="yui-u">' . $this->content($content4) . '</div>';
$html .= '</div>';
$html .= '</div>';
}
return $html;
}
public function div ($text='', $filler='') {
$dummy = array();
$dummy[] = 'Mas devo explicar-lhe como toda esta idéia equivocada de denunciar o prazer e louvar a dor nasceu e vou dar-lhe um relato completo do sistema, e expor os ensinamentos reais do grande explorador da verdade, o mestre-construtor de seres humanos felicidade. Ninguém rejeita, não gosta ou evita o prazer em si, porque é prazer, mas porque aqueles que não sabem como buscar prazer racionalmente encontram conseqüências extremamente dolorosas. Nem mais há alguém que ame, ou procure ou deseje obter dor de si mesmo, porque é dor, mas porque ocasionalmente ocorrem circunstâncias em que o trabalho ea dor podem lhe proporcionar algum grande prazer. Para tomar um exemplo trivial, qual de nós realiza sempre um exercício físico laborioso, exceto para obter alguma vantagem dele? Mas quem tem o direito de criticar um homem que escolhe desfrutar um prazer que não tem conseqüências irritantes, ou aquele que evita uma dor que não produz nenhum prazer resultante? Por outro lado, denunciamos com justa indignação e desagrado os homens que são tão seduzidos e desmoralizados pelos encantos do prazer do momento, tão cegos pelo desejo, que não podem prever a dor e o aborrecimento que devem acontecer; E culpa igual pertence àqueles que falham em seu dever por meio da fraqueza da vontade, que é o mesmo que dizer, por meio do encolhimento do trabalho e da dor. Estes casos são perfeitamente simples e fáceis de distinguir. Numa hora livre, quando o nosso poder de escolha é destravado e quando nada impede que sejamos capazes de fazer o que mais gostamos, todo prazer é bem-vindo e toda dor é evitada. Mas em certas circunstâncias e devido às reivindicações de dever ou às obrigações de negócios que freqüentemente ocorrerá que os prazeres têm de ser repudiado e aborrecimentos aceitos. O homem sábio, portanto, sempre se mantém nestas questões para este princípio de seleção: ele rejeita prazeres para assegurar outros prazeres maiores, ou então ele sofre dores para evitar piores dores.';
$dummy[] = 'As línguas europeias são membros da mesma família. A sua existência separada é um mito. Para a ciência, música, desporto, etc, a Europa usa o mesmo vocabulário. As línguas diferem apenas em sua gramática, sua pronúncia e suas palavras mais comuns. Todo mundo percebe por que uma nova linguagem comum seria desejável: um poderia se recusar a pagar os tradutores caros. Para conseguir isso, seria necessário ter gramática uniforme, pronúncia e palavras mais comuns. Se várias línguas se fundem, a gramática da linguagem resultante é mais simples e regular que a das línguas individuais. A nova linguagem comum será mais simples e regular do que as línguas europeias existentes. Será tão simples quanto Occidental; Na verdade, será Occidental. A uma pessoa inglesa, parecerá como o inglês simplificado, porque um amigo cético de Cambridge me disse o que Occidental é.';
$dummy[] = 'Uma serenidade maravilhosa tomou posse de toda a minha alma, como essas doces manhãs de primavera que eu desfruto com todo o meu coração. Estou sozinho, e sinto o encanto da existência neste lugar, que foi criado para a bem-aventurança de almas como a minha. Estou tão feliz, meu querido amigo, tão absorto no requintado senso de mera existência tranquila, que negligencio meus talentos. Eu seria incapaz de desenhar um único golpe no momento presente; E ainda sinto que nunca fui um artista maior do que agora. Quando, enquanto o lindo vale repleta de vapor à minha volta, e o sol meridiano atinge a superfície superior da folhagem impenetrável de minhas árvores, e apenas alguns raios vagabundos invadem o santuário interior, Fluxo de gotejamento; E, enquanto me encontro perto da terra, mil plantas desconhecidas são notadas por mim: quando ouço o zumbido do pequeno mundo entre os talos, e me familiarizo com as incontáveis ​​formas indescritíveis dos insetos e moscas, então sinto a A presença do Todo-Poderoso, que nos formou à sua própria imagem, eo sopro do amor universal que nos sustenta e sustenta, enquanto flutua à nossa volta numa eternidade de bem-aventurança; E então, meu amigo, quando a escuridão se espalha pelos meus olhos, e o céu e a terra parecem habitar em minha alma e absorver seu poder, como a forma de uma amante amada, então eu penso com saudade, Oh, eu poderia descrever essas concepções , Poderia imprimir no papel tudo o que está vivendo tão cheio e quente dentro de mim, que poderia ser o espelho da minha alma, como a minha alma é o espelho do Deus infinito! Ó meu amigo - mas é demais para a minha força - eu afundar sob o peso do esplendor dessas visões!';
if (is_numeric($filler)) {
$num = rand(0, 2);
$string = $dummy[$num];
if ($filler > strlen($string)) while ($filler > strlen($string)) $string .= ' ' . $string;
$text .= '<br /><br />' . substr($string, 0, $filler);
}
$num = rand(0, count($this->colors)-1);
$color = $this->colors[$num];
unset($this->colors[$num]);
shuffle($this->colors);
return '<div style="padding:20px 10px; opacity:0.5; filter:alpha(opacity=50); ' . $color . '"><div style="opacity:1; filter:alpha(opacity=100);">' . $text . '</div></div>';
}
private function content ($content) {
return '<div class="body">' . $content . '</div>';
}
}
?>
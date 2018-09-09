<?php
class RSS {
public $encoding = 'utf-8';
private $channel = array();
private $items = array();
public function __construct ($title, $link, $description) {
$this->channel['title'] = $title;
$this->channel['link'] = $link;
$this->channel['description'] = $description;
}
public function channel ($elements) {
foreach ($elements as $key => $value) $this->channel[$key] = $value;
}
public function item ($title, $elements) {
$item = array();
$item['title'] = $title;
foreach ($elements as $key => $value) $item[$key] = $value;
$this->items[] = $item;
}
public function display () {
$xml = '<?xml version="1.0" encoding="' . $this->encoding . '"?>' . "\n";
$xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
$xml .= '<channel>' . "\n";
foreach ($this->channel as $key => $value) $xml .= '  ' . $this->tag($key, $value) . "\n";
foreach ($this->items as $item) {
$xml .= "  <item>\n";
foreach ($item as $key => $value) $xml .= '    ' . $this->tag($key, $value) . "\n";
$xml .= "  </item>\n";
}
$xml .= '</channel>' . "\n";
$xml .= '</rss>';
header("Content-Type: application/rss+xml");
echo $xml;
exit;
}
private function tag ($key, $values) {
$tag = '';
list($value, $attributes) = $this->values($values);
if (in_array($key, array('pubDate', 'lastBuildDate'))) $value = $this->date($value);
if ($key == 'image') {
$tag .= '<' . $key . '>';
$tag .= '<url>' . $values['url'] . '</url>';
$tag .= '<title>' . $values['title'] . '</title>';
$tag .= '<link>' . $values['link'] . '</link>';
if (isset($values['width'])) $tag .= '<width>' . $values['width'] . '</width>';
if (isset($values['height'])) $tag .= '<height>' . $values['height'] . '</height>';
if (isset($values['description'])) $tag .= '<description>' . $values['description'] . '</description>';
$tag .= '</' . $key . '>';
} elseif ($key == 'textInput') {
$tag .= '<' . $key . '>';
$tag .= '<title>' . $values['title'] . '</title>';
$tag .= '<description>' . $values['description'] . '</description>';
$tag .= '<name>' . $values['name'] . '</name>';
$tag .= '<link>' . $values['link'] . '</link>';
$tag .= '</' . $key . '>';
} elseif ($key == 'skipHours') {
$tab .= '<' . $key . '>';
if (!is_array($values)) $values = array($values);
foreach ($values as $hour) $tab .= '<hour>' . $hour . '</hour>';
$tab .= '</' . $key . '>';
} elseif ($key == 'skipDays') {
$tab .= '<' . $key . '>';
if (!is_array($values)) $values = array($values);
foreach ($values as $day) $tab .= '<day>' . $day . '</day>';
$tab .= '</' . $key . '>';
} else {
if (!empty($value)) {
$tag .= '<' . $key . $attributes . '>' . $value . '</' . $key . '>';
} else {
$tag .= '<' . $key . $attributes . ' />';
}
}
return $tag;
}
private function values ($array) {
if (!is_array($array)) return array($array, '');
$value = (isset($array['value'])) ? $array['value'] : '';
unset ($array['value']);
$attributes = '';
foreach ($array as $k => $v) {
$attributes .= " {$k}=\"" . addslashes($v) . '"';
}
return array($value, $attributes);
}
private function date ($date) {
return date(DATE_RFC2822, strtotime($date));
}
}
?>
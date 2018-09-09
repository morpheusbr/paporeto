<?php
class Cache {
protected $cache_uri;
private $file = false;
private $type;
private $encoding;
private $expires;
private $modified;
public function __construct ($url='', $expire=false) {
$this->cache_uri = CACHE_ARQUIVOS;
if (!empty($url)) {
$url = trim($url);
if ($url[0] == '/') $url = substr($url, 1);
$type = explode('.', $url);
$type = array_pop($type);
if (!in_array($type, array('html', 'xml', 'rss', 'atom', 'css', 'js', 'txt'))) return false;
$encoding = $this->encoding();
$this->file = $this->cache_uri . $url . ($encoding != 'none' ? '.' . $encoding : '');
if ($type == 'xml' && strpos($this->file, 'rss') !== false) $type = 'rss';
if ($type == 'xml' && strpos($this->file, 'atom') !== false) $type = 'atom';
$this->type = $type;
$this->encoding = $encoding;
$this->expires = $this->expires($expire);
$this->conditional_get();
return true;
}
return false;
}
public function enforce ($url='') {
if (empty($url)) $url = str_replace(array($this->cache_uri, '.gzip', '.deflate'), '', $this->file);
if (substr($_SERVER['REQUEST_URI'], 1, strlen($url)) != $url) {
header('Location: ' . URL_SITE . $url, true, 301);
exit;
}
}
public function page ($html) {
if ($this->file) {
if ($this->encoding != 'none') $html = gzencode($html, 9, ($this->encoding == 'gzip') ? FORCE_GZIP : FORCE_DEFLATE);
$this->save ($this->file, $html);
$this->conditional_get();
}
}
public function clear () {
$this->remove ($this->cache_uri);
}
protected function remove ($dir) {
if (strpos($dir, URL_SITE) === false) return false; // get out of here before we do some major damage
if (is_file($dir)) return unlink($dir);
if (!is_dir($dir) || !$handle = opendir($dir)) return false;
while (false !== ($file = readdir($handle))) {
if ($file == '.' || $file == '..') continue;
$fullpath = $dir . $file;
if (is_dir($fullpath)) {
$this->remove ($fullpath.'/');
} else {
unlink($fullpath);
}
}
closedir($handle);
return rmdir($dir);
}
private function save ($file, $content) {
if (!is_dir(dirname($file))) mkdir(dirname($file), 0755, true);
if ($fp = fopen($file, 'wb')) {
fwrite($fp, $content);
fclose($fp);
return true;
}
return false;
}
private function encoding () {
$supported = (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
$gzip = strstr($supported, 'gzip');
$deflate = strstr($supported, 'deflate');
$encoding = $gzip ? 'gzip' : ($deflate ? 'deflate' : 'none');
if (isset($_SERVER['HTTP_USER_AGENT']) && !strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') && preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
$version = floatval($matches[1]);			
if ($version < 6) $encoding = 'none';				
if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1')) $encoding = 'none';
}
return $encoding; // either 'gzip', 'deflate', or 'none'
}
private function expires ($time) {
if (empty($time) || is_numeric($time)) {
$this->modified = $time;
return false;
}
if ($time === true) return -3600; // an hour ago
$time = explode(' ', $time);
if (!is_numeric($time[0])) return false;
if (!isset($time[1])) return $time[0]; // assumed to be in seconds
$interval = substr(strtolower($time[1]), 0, 3);
$time = $time[0];
switch ($interval) {
case 'sec': $time = $time; break; // no further processing needed
case 'min': $time = $time * 60; break; // 60 seconds
case 'hou': $time = $time * 3600; break; // 60 minutes
case 'day': $time = $time * 86400; break; // 24 hours
case 'mon': $time = $time * 2592000; break; // 30 days
case 'yea': $time = $time * 31536000; break; // 365 days
}
return $time; // in seconds
}
private function conditional_get () {
$lastmod = ($this->file && file_exists($this->file)) ? filemtime($this->file) : false;
if (!$lastmod) return false;
if ($this->expires !== false && ($lastmod + $this->expires) < time()) {
$this->remove($this->file);
return false;
} elseif (!empty($this->modified) && $lastmod < $this->modified) {
$this->remove($this->file);
return false;
}
while (ob_get_level()) ob_end_clean();
$etag = '"' . $lastmod . '-' . md5($this->file) . '"';
$lastmod = gmdate('D, d M Y H:i:s \G\M\T', $lastmod); // A GMT RFC 2822 Formated Date
$ifmod = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lastmod : null;
$iftag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == $etag : null;
$match = (($ifmod || $iftag) && ($ifmod !== false && $iftag !== false)) ? true : false;
header("ETag: {$etag}"); // ETag is sent even with 304 header
if ($match) {
// header('HTTP/1.0 304 Not Modified');
header('Content-Type:', true, 304);
exit;
}
if ($fp = fopen($this->file, 'rb')) {
header("Last-Modified: {$lastmod}"); // Last-Modified doesn't need to be sent with 304 response
if ($this->encoding != 'none') header("Content-Encoding: " . $this->encoding);
switch ($this->type) {
case 'html': header("Content-Type: text/html"); break;
case 'atom': header("Content-Type: application/atom+xml"); break;
case 'rss': header("Content-Type: application/rss+xml"); break;
case 'xml': header("Content-Type: text/xml"); break;
case 'css': header("Content-Type: text/css"); break;
case 'txt': header("Content-Type: text/plain"); break;
case 'js': header("Content-Type: text/javascript"); break;
}
header("Content-Length: " . filesize($this->file));
fpassthru($fp);
fclose($fp);
exit;
}
}
}
?>
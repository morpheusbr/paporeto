<?php
class Email {
private $crlf = "\n";
private $from = '';
private $to = '';
private $subject = '';
private $text = '';
private $html = '';
private $headers = '';
private $mime_boundary = '';
private $html_images = array();
private $attachments = array();
private $smtp = array();
private $debug = false;
function __construct ($from='', $debug='') {
$this->smtp['connected'] = false;
if (is_array($from)) {
list($name, $address) = each($from);
$this->from = $name . ' <' . $address . '>';
$this->smtp['from'] = $address;
} else {
if (empty($from)) $from = ini_get('sendmail_from');
$this->from = $from;
$this->smtp['from'] = $from;
}
if ($debug === true) $this->debug = true;
}
public function smtp ($server='localhost', $port=25, $username='', $password='') {
$this->crlf = "\r\n";
$this->smtp['fp'] = fsockopen($server, $port, $errno, $errstr, 30);
if ($this->smtp['fp'] === false) {
$this->smtp['log']['fsockopen'] = '(220) ' . $errno . ' - ' . $errstr;
if ($this->debug) echo 'fsockopen ' . $this->smtp['log']['fsockopen'] . '<br /><br />';
} else {
$this->log('connection', 220);
fputs($this->smtp['fp'], "HELO {$_SERVER['REMOTE_ADDR']}" . $this->crlf); // or $_SERVER['HTTP_HOST'] ?
if ($this->log('helo_response', 250)) {
if (!empty($username)) {
fputs($this->smtp['fp'], "AUTH LOGIN" . $this->crlf);
if ($this->log('auth_login', 334)) {
fputs($this->smtp['fp'], base64_encode($username) . $this->crlf);
if ($this->log('auth_username', 334)) {
fputs($this->smtp['fp'], base64_encode($password) . $this->crlf);
if ($this->log('auth_password', 235)) {
$this->smtp['connected'] = true;
} // password
} // username
} // request
} else { // if auth_login (334) 502 unimplemented you don't need a 'username' and 'password'
$this->smtp['connected'] = true;
}
} // response
} // handle
if ($this->smtp['connected'] === false) $this->crlf = "\n"; // Back to the default
return $this->smtp['connected'];
}
public function attach ($filename, $filepath) {
$this->attachments[$filename] = $filepath;
}
public function send ($to, $subject, $text, $html='', $directory='') {
$time = time();
$date = date('r', $time);
if (is_array($to)) {
list($name, $address) = each($to);
$this->to = $name . ' <' . $address . '>';
$this->smtp['to'] = $address;
} else {
$this->to = $to;
$this->smtp['to'] = $to;
}
$this->subject = $subject;
$this->text = str_replace('<br />', $this->crlf, wordwrap(nl2br($text), 76, '<br />', true));
$this->html = $html;
if (!empty($directory)) $this->images ($directory);
$this->mime_boundary = sha1($date).rand(1000,9999);
$this->headers['Date'] = $date;
$this->headers['From'] = $this->from;
$this->headers['Return-Path'] = '<' . $this->smtp['from'] . '>';
if ($this->smtp['connected']) {
$this->headers['To'] = $this->to;
$this->headers['Subject'] = $this->subject;
}
$result = $this->email_message();
$this->to = '';
$this->subject = '';
$this->text = '';
$this->html = '';
$this->headers = '';
$this->mime_boundary = '';
$this->html_images = array();
$this->attachments = array();
return $result;
}
public function close () {
if ($this->smtp['connected']) {
fputs($this->smtp['fp'], "QUIT" . $this->crlf);
$result = $this->log('quit', 221);
fclose($this->smtp['fp']);
}
}
public function debug () {
return $this->smtp['log'];
}
private function email_message () {
if (empty($this->html) && empty($this->attachments)) {
if ($this->debug) {
echo '<pre>' . htmlspecialchars($this->headers() . $this->text) . '</pre><br /><br />';
} else {
return $this->email($this->text);
}
} else {
$this->headers['MIME-Version'] = '1.0';
$message = '';
if (!empty($this->attachments)) {
$this->headers['Content-Type'] = 'mixed';
$message .= '--mixed-' . $this->mime_boundary . $this->crlf;
}
$message .= $this->insertText();
if (!empty($this->attachments)) {
foreach ($this->attachments as $name => $path) {
$message .= '--mixed-' . $this->mime_boundary . $this->crlf;
$message .= $this->insertAttachment($name, $path);
}
$message .= '--mixed-' . $this->mime_boundary . '--' . $this->crlf . $this->crlf;
}
$message .= $this->crlf . '-- End --';
if ($this->debug) {
echo '<pre>' . htmlspecialchars($this->headers() . $message) . '</pre><br /><br />';
} else {
return $this->email($message);
}
}
return false;
}
private function insertText () {
$msgText = '';
if (!empty($this->html)) {
if (empty($this->attachments)) { // because we set it to 'mixed', remember?
$this->headers['Content-Type'] = 'alternative';
} else {
$msgText .= 'Content-Type: multipart/alternative; boundary="alternative-' . $this->mime_boundary . '"' . $this->crlf . $this->crlf;
}
$msgText .= '--alternative-' . $this->mime_boundary . $this->crlf;
}
$msgText .= 'Content-Type: text/plain; charset=iso-8859-1' . $this->crlf;
$msgText .= 'Content-Transfer-Encoding: 7bit' . $this->crlf . $this->crlf;
$msgText .= $this->text . $this->crlf . $this->crlf;
if (!empty($this->html)) {
$msgText .= '--alternative-' . $this->mime_boundary . $this->crlf;
$msgText .= $this->insertHtml();
$msgText .= '--alternative-' . $this->mime_boundary . '--' . $this->crlf . $this->crlf;
}
return $msgText;
}
private function insertHtml () {
$msgHtml = '';
$images = !empty($this->html_images);
if ($images) {
foreach ($this->html_images as $name => $value) {
$quoted = preg_quote($name);
$cid = preg_quote($value['cid']);
$this->html = preg_replace("#src=\"$quoted\"|src='$quoted'#", "src=\"cid:$cid\"", $this->html);
$this->html = preg_replace("#background=\"$quoted\"|background='$quoted'#", "background=\"cid:$cid\"", $this->html);
}
$msgHtml .= 'Content-Type: multipart/related; boundary="related-' . $this->mime_boundary . '"' . $this->crlf . $this->crlf;
$msgHtml .= '--related-' . $this->mime_boundary . $this->crlf;
}
$msgHtml .= 'Content-Type: text/html; charset=iso-8859-1' . $this->crlf;
$msgHtml .= 'Content-Transfer-Encoding: quoted-printable' . $this->crlf . $this->crlf;
$msgHtml .= $this->quotedPrintableEncode($this->html) . $this->crlf . $this->crlf;
if ($images) {
foreach ($this->html_images as $name => $value) {
$msgHtml .= '--related-' . $this->mime_boundary . $this->crlf;
$msgHtml .= 'Content-Type: ' . $value['type'] . $this->crlf;
$msgHtml .= 'Content-Transfer-Encoding: base64' . $this->crlf;
$msgHtml .= 'Content-Disposition: inline; filename="' . $name . '";' . $this->crlf;
$msgHtml .= 'Content-ID: <' . $value['cid'] . '>' . $this->crlf . $this->crlf;
$msgHtml .= $this->base64Encode($value['data']) . $this->crlf . $this->crlf;
}
$msgHtml .= '--related-' . $this->mime_boundary . '--' . $this->crlf . $this->crlf;
}
return $msgHtml;
}
private function quotedPrintableEncode($input, $line_max=76) { // for HTML
$lines = preg_split("/\r?\n/", $input);
$eol = $this->crlf;
$escape = '=';
$output = '';
while(list(, $line) = each($lines)) {
$linlen = strlen($line);
$newline = '';
for ($i=0; $i<$linlen; $i++) {
$char = substr($line, $i, 1);
$dec = ord($char);
if (($dec == 32) AND ($i == ($linlen - 1))){ // convert space at eol only
$char = '=20';
} elseif($dec == 9) {
; // Do nothing if a tab.
} elseif(($dec == 61) OR ($dec < 32 ) OR ($dec > 126)) {
$char = $escape . strtoupper(sprintf('%02s', dechex($dec)));
}
if ((strlen($newline) + strlen($char)) >= $line_max) { // $eol is not counted
$output .= $newline . $escape . $eol; // soft line break; " =\r\n" is okay
$newline = '';
}
$newline .= $char;
}
$output .= $newline . $eol;
}
$output = substr($output, 0, -1 * strlen($eol)); // Don't want last $eol
return $output;
}
private function base64Encode ($input) { // for Attachments and Images
return rtrim(chunk_split(base64_encode($input), 76, $this->crlf));
}
private function images ($directory) {
$image_types = array('jpg'=>'image/jpeg', 'gif'=>'image/gif', 'png'=>'image/png', 'swf'=>'application/x-shockwave-flash');
if (!empty($this->html)) {
$extensions = array_keys($image_types);
preg_match_all('/(?:"|\')([^"\']+\.('.implode('|', $extensions).'))(?:"|\')/Ui', $this->html, $matches);
foreach ($matches[1] as $image) {
if (file_exists($directory . $image)) {
$html_images[] = $image;
$this->html = str_replace($image, basename($image), $this->html);
}
}
if (!empty($html_images)) {
// If duplicate images are embedded, they may show up as attachments, so remove them.
$html_images = array_unique($html_images);
sort($html_images);
foreach ($html_images as $img) {
if ($image = file_get_contents($directory . $img)) {
$ext = preg_replace('#^.*\.(\w{3,4})$#e', 'strtolower("$1")', $img);
$name = basename($img);
$type = $image_types[$ext];
$cid = '-' . str_replace('.'.$ext, '', $name) . '-' . $this->mime_boundary;
$this->html_images[$name] = array('cid'=>$cid, 'data'=>$image, 'type'=>$type);
}
}
}
}
}
private function insertAttachment ($name, $path) {
$attach = '';
$file = $path . $name;
$handle = fopen($file, 'rb');
$data = fread($handle, filesize($file));
fclose($handle);
$filetype = mime_content_type($file);
$attach .= 'Content-Type: ' . $filetype . '; name="' . $name . '"' . $this->crlf;
$attach .= 'Content-Transfer-Encoding: base64' . $this->crlf;
$attach .= 'Content-Description: ' . $name . $this->crlf;
$attach .= 'Content-Disposition: attachment; filename="' .$name. '"' . $this->crlf . $this->crlf;
$attach .= $this->base64Encode($data) . $this->crlf . $this->crlf;
return $attach;
}
private function headers () {
foreach ($this->headers as $name => $value) {
if ($name == 'Content-Type') {
$headers[] = $name . ': multipart/' . $value . '; boundary="' . $value . '-' . $this->mime_boundary . '"';
$headers[] = 'Message-ID: <-' . $_SERVER['HTTP_HOST'] . '-' . $this->mime_boundary . '>';
} else {
$headers[] = $name . ': ' . $value;
}
}
return implode("\r\n", $headers) . "\r\n\r\n";
}
private function email ($message) {
$sent_email = false;
if ($this->smtp['connected']) {
fputs($this->smtp['fp'], "MAIL FROM: <{$this->smtp['from']}>" . $this->crlf);
if ($this->log('mail_from_response', 250)) {
fputs($this->smtp['fp'], "RCPT TO: <{$this->smtp['to']}>" . $this->crlf);
if ($this->log('mail_' . $this->smtp['to'] . '_response', 250)) {
fputs($this->smtp['fp'], "DATA" . $this->crlf);
if ($this->log('data', 354)) {
fputs($this->smtp['fp'], $this->headers() . $message . $this->crlf . '.' . $this->crlf);
if ($this->log('message', 250)) { // implode($this->crlf, $email)
$sent_email = true;
}
}
} else {
fputs($this->smtp['fp'], "RSET" . $this->crlf); $this->log('reset_mail', 250);
}
}
} else { // not connected to smtp
if (mail($this->to, $this->subject, $message, $this->headers())) {
$sent_email = true;
}
}
return $sent_email;
}
private function log ($desc, $code) {
$response = fgets($this->smtp['fp'], 256);
$this->smtp['log'][$desc] = $desc . ' (' . $code . ') ' . $response;
if ($this->debug) echo $this->smtp['log'][$desc] . '<br /><br />';
if(substr($response,0,3) != $code) return false;
return true;
}
}
?>
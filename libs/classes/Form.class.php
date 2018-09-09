<?php
class Form {
private $required = array();
private $values = array();
private $check = array();
private $process = array();
private $upload = array();
private $filesize = false;
private $vars = array();
private $errors = array();
private $headervars = '';
private $prompt = '';
function __construct ($class='') {
if (!empty($class)) $this->headervars = ' class="' . $class . '"';
}
public function required ($required) {
if (is_array($required)) {
foreach ($required as $value) {
$this->required[] = $value;
// if ($value == 'recaptcha') include_once (BASE . 'include/functions/recaptcha.php');
}
}
}
public function values ($values=array()) {
if (!empty($values) && is_array($values)) foreach ($values as $name => $value) $this->values[$name] = $value;
return $this->values;
}
public function check ($check) {
if (is_array($check)) foreach ($check as $name => $filter) $this->check[$name] = $filter;
}
public function process ($process) {
if (is_array($process)) foreach ($process as $name => $filter) $this->process[$name] = $filter;
}
public function upload ($upload, $filesize=5) {
if (is_array($upload)) foreach ($upload as $name => $filter) $this->upload[$name] = $filter;
if ($filesize > 0) $this->filesize = $filesize;
}
public function validate ($form, $function='escape_data') {
if (empty($function) || !function_exists($function)) $function = false;
$validate = new Validacao;
if (in_array('recaptcha', $this->required)) {
$error = $validate->recaptcha();
if (!empty($error)) $this->errors['recaptcha'] = $error;
}
$validate->jquery($form, $this->check, $this->required, $this->upload);
list($vars, $errors, $eject) = $validate->form(array('post'=>$form), $this->check, $this->required);
$this->errors = array_merge($this->errors, $errors);
foreach ($vars as $name => $value) {
if (is_array($value)) {
foreach ($value as $key => $data) {
$this->vars[$name][$key] = ($function) ? $function($data) : $data; // to ship out
$this->values[$name][$key] = $data; // to preselect
}
} else {
$this->vars[$name] = ($function) ? $function($value) : $value; // to ship out
$this->values[$name] = $value; // to preselect
}
}
if (isset($_POST['process'])) {
foreach ($this->process as $name => $filter) {
foreach ($_POST['process'] as $id) {
$value = (isset($_POST[$name][$id]) && !empty($_POST[$name][$id])) ? $validate->data($filter, $_POST[$name][$id]) : $validate->data($filter, ''); // so we at least get the default value of $filter
$this->vars['process'][$id][$name] = ($function) ? $function($value) : $value; // ship only, no preselect
}
}
}
foreach ($this->upload as $file => $types) {
list($filename, $error) = $validate->upload($file, $types);
if (!empty($filename)) {
$this->vars[$file] = $filename;
} elseif (!empty($error)) {
if (in_array($file, $this->required)) $this->errors[$file] = $error;
trigger_error("The file name '{$_FILES[$file]['name']}', type '{$_FILES[$file]['type']}', size '{$_FILES[$file]['size']}', and tmp_name '{$_FILES[$file]['tmp_name']}' had the following error: {$error}");
}
}
unset($validate);
return array($this->vars, $this->errors, $eject);
}
public function error ($name='', $message='') {
if (!empty($name)) $this->errors[$name] = $message;
return $this->errors;
}
public function prompt ($size) {
$this->prompt = (int) $size . 'px';
}
public function header ($name, $method='post', $action='') {
global $page;
if (empty($method) || $method != 'get') $method = 'post';
if (!empty($name) && empty($action) && $method == 'post') {
$action = $page->url('add', '', 'submitted', $name);
}
$form = "\n  " . '<form id="'.$name.'" method="'.$method.'" action="'.$action.'" enctype="multipart/form-data"';
if ($this->filesize && $method == 'post') {
$form .= ' enctype="multipart/form-data"';
$maxsize = $this->hidden('MAX_FILE_SIZE', $this->filesize * 1024000, '');
}
$form .= $this->headervars . '>';
$form .= '<fieldset style="border:0px; padding:0px;"><legend style="display:none;">&nbsp;</legend>';
if (isset($maxsize)) $form .= $maxsize;
$form .= $this->hidden($name, 'true', '');
if (!empty($this->prompt)) {
$page->link('<style type="text/css"> #' . $name . ' label.prompt { width:' . $this->prompt . '; display:inline-block; } </style>');
}
return $form;
}
public function field ($type, $name, $prompt='', $options='', $key='',$texto='') {
$field = "\n  ";
if (!in_array($type, array('calendar', 'checkbox', 'file', 'hidden', 'hierselect', 'multicheck', 'multiselect', 'multitext', 'password', 'radio', 'select', 'submit', 'text', 'textarea')) || empty($name)) return $field;
if (!in_array($type, array('hidden', 'multicheck', 'multiselect', 'multitext', 'submit'))) {
$field .= '<span id="' . $name . 'Error">';
if (isset($this->errors[$name])) {
if (in_array($type, array('hierselect', 'select'))) $this->errors[$name] = 'Please make a selection.';
$field .= '<label class="error">' . $this->errors[$name] . '</label>';
}
$field .= '</span>';
if (in_array($name, $this->required) && !empty($prompt)) $prompt = '<span class="required">*</span> ' . $prompt;
}
if ($type == 'checkbox') {
return $field . $this->$type($name, $prompt, $options);
} elseif ($type == 'hidden') {
return $this->$type($name, $prompt, $options);
} elseif ($type == 'multicheck') {
return $this->$type($name, $prompt, $options, $key);
} elseif ($type == 'multitext') {
return $this->$type($name, $prompt, $options, $key);
}elseif ($type == 'textarea') {
return $this->$type($name, $options,$texto);
} elseif ($type == 'radio') {
return $field . $this->$type($name, $prompt, $options);
} elseif ($type == 'submit') {
return $this->$type($name, $prompt);
} else {
if (!empty($prompt)) {
$br = (substr($prompt, -6) == '<br />') ? '<br />' : '';
if (!empty($br)) $prompt = substr($prompt, 0, -6);
$field .= '<label for="' . $name . '" class="prompt">' . trim($prompt) . '</label>' . $br;
}
return $field . $this->$type($name, $options);
}
}
public function recaptcha () {
$field = "\n  ";
if (!function_exists('recaptcha_get_html')) return $field;
$field .= '<div id="recaptchaError">';
if (isset($this->errors['recaptcha'])) $field .= '<label class="error">' . $this->errors['recaptcha'] . '</label>';
$field .= '</div>';
$field .= recaptcha_get_html (RECAPTCHA_PUBLIC_KEY);
return $field;
}
public function close ($html='') {
$form = $html;
$form .= '</fieldset>';
$form .= "\n  " . '<input type="hidden" name="'.CSRF::TOKEN_NAME.'" value="'.CSRF::getToken().'"/></form>';
return $form;
}
#--Private Form Functions--#
private function calendar ($name, $options) {
global $page;
$page->jquery('$("#' . $name . '").datepicker({onClose:function(){$(this).valid();}});');
return $this->text($name, array('width'=>100, 'maxlength'=>10));
return $form;
}
private function checkbox ($name, $prompt, $options) {
$field = '';
if (is_array($options)) {
list($value, $description) = each($options); // only one at a time
if (!empty($prompt)) $field .= '<label for="' . $name . '" class="prompt">' . $prompt . '</label>';
$field .= '<label><input type="checkbox" id="'.$name.'" name="'.$name.'" value="'.$value.'"' . $this->defaultValue('checkbox', $name, $value) . ' /> ' . $description . '</label>';
}
return $field;
}
private function file ($name, $options) {
$width = (isset($options['size'])) ? $options['size'] : 50;
return '<input type="file" id="'.$name.'" name="'.$name.'" size="'.$width.'" />';
}
private function hidden ($name, $prompt, $options) {
if ($name == 'process') $name = 'process[]';
$id = (isset($options['id'])) ? ' id="' . $options['id'] . '"' : ' '; // for javascript
return '<input type="hidden" name="'.$name.'" value="'.$prompt.'"' . $id . '/>';
}
private function hierselect ($name, $options) {
global $page;
$json = array();
foreach ($options[0] as $id => $main) {
$hier = (isset($options['empty'])) ? array() : array(''=>'&nbsp;');
if (isset($options[$id])) foreach ($options[$id] as $key => $value) $hier[$key] = $value;
$json[$id] = $hier;
}
$page->jquery('$("#' . $name . '").hierSelect("' . $options['hier'] . '", ' . json_encode($json) . ');');
$select = $options[0];
if (isset($options['empty'])) $select['empty'] = false;
return $this->select($name, $select);
}
private function multicheck ($name, $prompt, $options, $key) {
$field = '<input type="checkbox" name="'.$name.'['.$key.']" value="'.$options.'" />';
if (!empty($prompt)) $field = "<label>{$field} {$prompt}</label>";
return $field;
}
private function multiselect ($name, $options) {
$size = (count($options) < 15) ? count($options) : 15;
$field = '<select id="'.$name.'" name="'.$name.'[]" multiple="multiple" size="'.$size.'">';
foreach ($options as $key => $value) if (!empty($key)) $field .= '<option value="'.$key.'"' . $this->defaultValue('select', $name, $key) . '>'.$value.'</option>';
$field .= '</select>';
return $field;
}
private function multitext ($name, $prompt, $options, $key) {
$class = (isset($options['class'])) ? ' class="' . $options['class'] . '"' : ' ';
$width = 'style="width:250px;"';
if (isset($options['width'])) $width = 'style="width:' . $options['width'] . 'px;"';
if (isset($options['size'])) $width = 'size="' . $options['size'] . '"';
$maxlength = (isset($options['maxlength'])) ? $options['maxlength'] : 50;
$value = (isset($options['value']) && !empty($options['value'])) ? stripslashes(htmlspecialchars(htmlspecialchars_decode($options['value']))) : '';
$field = (!empty($prompt)) ? '<label class="prompt">' . $prompt . '</label>' : '';
return $field . '<input type="text"' . $class . 'name="'.$name.'['.$key.']" '.$width.' maxlength="'.$maxlength.'" value="'.$value.'" />';
}
private function password ($name, $options) {
$width = 'style="width:100px;"';
if (isset($options['width'])) $width = 'style="width:' . $options['width'] . 'px;"';
if (isset($options['size'])) $width = 'size="' . $options['size'] . '"';
$maxlength = (isset($options['maxlength'])) ? $options['maxlength'] : 20;
return '<input type="password" id="'.$name.'" name="'.$name.'" '.$width.' maxlength="'.$maxlength.'" />';
}
private function radio ($name, $prompt, $options) {
$field = '';
if (!empty($prompt)) $field .= '<label class="prompt">' . trim($prompt) . '</label>';
$radios = array();
foreach ($options as $value => $description) {
$radios[] = '<label><input type="radio" name="'.$name.'" value="'.$value.'"' . $this->defaultValue('radio', $name, $value) . ' /> ' . $description . '</label>';
}
if (!empty($prompt)) {
$combine = (!empty($this->prompt)) ? '<br /><label class="prompt">&nbsp;</label>' : '&nbsp;&nbsp;&nbsp;';
$field .= implode($combine, $radios);
} else {
$field .= implode('<br />', $radios);
}
return $field;
}
private function select ($name, $options='') {
$field = '<select id="'.$name.'" name="'.$name.'"';
if (isset($onchange)) $field .= $onchange;
if (in_array($name, $this->required)) $field .= ' title="Faça uma seleção."';
$field .= (isset($options['empty'])) ? '>' : '><option value="">Selecione</option>';
foreach ($options as $key => $value) {
if ($key != 'empty') $field .= '<option value="'.$key.'"' . $this->defaultValue('select', $name, $key) . '>'.$value.'</option>';
}
$field .= '</select>';
return $field;
}
private function submit ($name, $prompt) {
$field = "\n  ";
if (!empty($prompt)) $field .= '<label class="prompt">' . $prompt . '</label>';
return $field . '<input type="submit" name="submit" class="submit" value="'.$name.'" />';
}
private function text ($name, $options) {
$width = 'style="width:200px;"';
if (isset($options['width'])) $width = 'style="width:' . $options['width'] . 'px;"';
if (isset($options['size'])) $width = 'size="' . $options['size'] . '"';
$maxlength = (isset($options['maxlength'])) ? $options['maxlength'] : 50;
return '<input type="text" id="'.$name.'" name="'.$name.'" '.$width.' maxlength="'.$maxlength.'"' . $this->defaultValue('text', $name) . ' />';
}
private function textarea ($name, $options,$texto) {
$cols = (isset($options['cols'])) ? $options['cols'] : 60;
$rows = (isset($options['rows'])) ? $options['rows'] : 7;
$width = (isset($options['width'])) ? $options['width'] : 400;
$height = (isset($options['height'])) ? $options['height'] : 200;
if (isset($options['cols']) && !isset($options['width'])) $width = false;
if (isset($options['rows']) && !isset($options['height'])) $height = false;
$dimensions = ' cols="' . $cols . '" rows="' . $rows . '"'; // because these are required attributes
if ($width || $height) {
$dimensions .= ' style="';
if ($width) $dimensions .= 'width:' . $width . 'px;';
if ($height) $dimensions .= 'height:' . $height . 'px;';
$dimensions .= '"';
}
return '<textarea id="'.$name.'" name="'.$name.'"' . $dimensions . '>' . $texto . '</textarea>';
}
private function defaultValue ($type, $name, $select='') {
$default = '';
if (isset($this->values[$name]) || isset($_POST[$name])) {
$selected = (isset($_POST[$name])) ? $_POST[$name] : $this->values[$name];
if (empty($select)) { // then this is a text field
if (!isset($_POST[$name])) $selected = htmlspecialchars_decode($selected);
$value = stripslashes(htmlspecialchars($selected));
} else { // we may preselect this option
if (is_array($selected)) {
$value = (in_array($select, $selected)) ? true : false;
} else {
$value = ($selected == $select) ? true : false;
}
}
if (!empty($value)) {
switch ($type) {
case 'checkbox': $default .= ' checked="checked"'; break;
case 'radio': $default .= ' checked="checked"'; break;
case 'select': $default .= ' selected="selected"'; break;
case 'text': $default .= ' value="' . $value . '"'; break;
case 'textarea': $default .= $value; break;
}
}
}
return $default;
}
}
?>
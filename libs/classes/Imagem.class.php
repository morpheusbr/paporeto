<?php
class Imagem {
private $data = false; // the original - never changes
private $width;
private $height;
private $type;
private $image; // created and updated in $this->resize()
public function url ($website) {
$website = str_replace(' ', '%20', trim($website));
if ($info = @getimagesize($website)) {
switch ($info[2]) {
case 1: $this->type = 'gif'; break;
case 2: $this->type = 'jpg'; break;
case 3: $this->type = 'png'; break;
default: return false; break; // image type not supported
}
$this->width = $info[0];
$this->height = $info[1];
$data = file_get_contents ($website);
if ($data !== FALSE) {
$this->data = $data;
return true;
}
}
return false;
}
public function type () {
$type = '';
if (!empty($this->type)) $type = '.' . $this->type;
return $type;
}
public function convert ($type) {
if (in_array($type, array('jpg', 'gif', 'png'))) {
$this->type = $type;
return $this->resize();
}
return false;
}
public function resize ($width='', $height='') { // constrains proportions within $width and $height
if ($this->data === FALSE) return false;
if (empty($width)) {
$width = $this->width;
$height = $this->height;
} else {
list ($width, $height) = $this->proportional ($width, $height);
}
$this->image = ''; // to clean the slate if necessary
$this->image = @imagecreatefromstring($this->data);
$image = imagecreatetruecolor ($width, $height);
if ($this->type == 'png') {
imagealphablending ($image, false);
imagesavealpha ($image, true);
$transparent = imagecolorallocatealpha ($image, 255, 255, 255, 127);
imagefilledrectangle ($image, 0, 0, $width, $height, $transparent);
} else {
$white = imagecolorallocate ($image, 255, 255, 255);
imagefilledrectangle ($image, 0, 0, $width, $height, $white);
}
imagecopyresampled ($image, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
$this->image = $image;
return true;
}
public function square ($pixels) { // generates a square image of $pixels size
if ($this->data === FALSE) return false;
$width = $height = $pixels;
$x = $y = 0;
$size = $this->width;
if ($this->height > $this->width) {
$y = round(($this->height - $this->width) / 2);
} elseif ($this->width > $this->height) {
$x = round(($this->width - $this->height) / 2);
$size = $this->height;
}
$this->image = ''; // to clean the slate if necessary
$this->image = @imagecreatefromstring($this->data);
$image = imagecreatetruecolor ($width, $height);
if ($this->type == 'png') {
imagealphablending ($image, false);
imagesavealpha ($image, true);
$transparent = imagecolorallocatealpha ($image, 255, 255, 255, 127);
imagefilledrectangle ($image, 0, 0, $width, $height, $transparent);
} else {
$white = imagecolorallocate ($image, 255, 255, 255);
imagefilledrectangle ($image, 0, 0, $width, $height, $white);
}
imagecopyresampled ($image, $this->image, 0, 0, $x, $y, $width, $height, $size, $size);
$this->image = $image;
return true;
}
public function save ($location, $quality=80) {
if (!is_dir(dirname($location))) mkdir(dirname($location), 0755, true);
if (!empty($this->image)) {
if ($this->type == 'jpg') {
return imagejpeg ($this->image, $location, $quality);
} elseif ($this->type == 'gif') {
return imagegif ($this->image, $location);
} elseif ($this->type == 'png') {
if ($quality >= 90) {
$quality = 0;
} else {
$quality = abs(round($quality / 10) - 9);
}
return imagepng ($this->image, $location, $quality);
}
} elseif (!empty($this->data)) {
if (file_put_contents ($location, $this->data)) return true;
}
return false;
}
public function display ($quality=80) {
if (!empty($this->image)) {
if ($this->type == 'jpg') {
header ('Content-type: image/jpeg');
return imagejpeg ($this->image, NULL, $quality);
} elseif ($this->type == 'gif') {
header ('Content-type: image/gif');
return imagegif ($this->image);
} elseif ($this->type == 'png') {
if ($quality >= 90) {
$quality = 0;
} else {
$quality = abs(round($quality / 10) - 9);
}
header ('Content-type: image/png');
return imagepng ($this->image, NULL, $quality);
}
} elseif (!empty($this->data)) {
switch ($this->type) {
case 'jpg': header ('Content-type: image/jpeg'); break;
case 'gif': header ('Content-type: image/gif'); break;
case 'png': header ('Content-type: image/png'); break;
}
echo $this->data;
return true;
}
return false;
}
private function proportional ($width, $height) {
if (empty($height)) $height = $width * .8;
$use = (($width / $height) <= ($this->width / $this->height)) ? 'width' : 'height';
$max_dimension = ($use == 'width') ? $width : $height;
$ratio = ($this->width >= $this->height) ? $max_dimension / $this->width : $max_dimension / $this->height;
if($this->width > $max_dimension || $this->height > $max_dimension) { 
$width = round($this->width * $ratio); 
$height = round($this->height * $ratio); 
} else { // the resize is larger than the original
$width = round($this->width); 
$height = round($this->height);
}
return array ($width, $height); 
}
}
?>
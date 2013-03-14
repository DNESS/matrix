#!/usr/bin/php -q
<?php
 
if(isset($argv[1]) && strlen($argv[1])) {
$file = $argv[1];
}else{
echo 'Please Specify a File';
exit(1);
}
 
$img = imagecreatefromstring(file_get_contents($file));
list($width, $height) = getimagesize($file);
 
$scale = 10;
 
$chars = array(
    'P', '\'', '-', '+',
    '.', '*', ' ', ' ',
    ' ',
);
 
$chars = array_reverse($chars);
 
$c_count = count($chars);
 
for($y = 0; $y <= $height - $scale - 1; $y += $scale) {
    for($x = 0; $x <= $width - ($scale / 2) - 1; $x += ($scale / 2)) {
        $rgb = imagecolorat($img, $x, $y);
        $r = (($rgb >> 16) & 0xFF);
        $g = (($rgb >> 8) & 0xFF);
        $b = ($rgb & 0xFF);
        $sat = ($r + $g + $b) / (255 * 3);
        echo $chars[ (int)( $sat * ($c_count - 1) ) ];
    }
    echo PHP_EOL;
}
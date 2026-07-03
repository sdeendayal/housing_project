<?php
$img = imagecreatetruecolor(100, 100);
$color = imagecolorallocate($img, 0, 150, 255);
imagefill($img, 0, 0, $color);
imagejpeg($img, 'e:/sports/housing_project/test_image.jpg');
imagedestroy($img);
echo "Image created successfully!";

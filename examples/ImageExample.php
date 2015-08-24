<?php

use Naucon\Image\Image;
$image = new Image();

// create image in 320x213
$imageWriter = $image->create(320,213);

// or load image data
$imageData = file_get_contents(__DIR__ . '/example.png');   // from a string
$imageWriter = $image->load($imageData);

// or open image file
$imageWriter = $image->open(__DIR__ . '/example.png');

echo 'width: ' . $imageWriter->getWidth();  // width: 320
echo '<br/>';
echo 'height: ' . $imageWriter->getHeight();    // height: 213

$imageWriter->transparentColor(0,0,0)
    ->negative()        // invert colors
    ->grayscale()       // grayscale colors
    ->brightness(100)   // brightness -255 to 255
    ->contrast(50)      // contras -100 to 100
    ->gamma(1.0, 2.0)   // gamma correction
    ->colorize(0,255,0) // colorize to green
    ->outline()         // highlight the edges
    ->emboss()          // emboss image
    ->blur()            // blur image
    ->blurGaussian()    // blur image with gaussian method
    ->sharpen()         // sharpen image
    ->sketchy()         // sketchy effect
    ->smooth(8)         // smooth image
    ->pixelation(3)     // pixelation effect
    ->alphaBlending(true)   // enable alpha blending mode
    ->antialiase(true)      // enable antialiase
    ->scale(100,100)        // scale image (equally)
    ->save(__DIR__ . '/tmp/new_image.png');     // save image as file

// or dump image
//$imageWriter->dump('gif');

unset($imageWriter);        // close image resource




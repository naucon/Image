# naucon Image Package

## About

This package provides simple image processing class for php to create and modify GIF, PNG, JPEG images.
The image class is a oop implementation of the functions from the GD php-extension. GD library 2.0 and the GD php-extension is required.

### Features

* create image
* load image data
* open image files
* return image width/height
* scale image
* filter
    * grayscale
    * brightness
    * contrast
    * gamma
    * colorize
    * negative
    * outline
    * emboss
    * blur (gaussian)
    * sharpen
    * sketchy
    * smooth
    * pixelation
    * alphaBlending
    * antialiase

### Compatibility

* PHP5.3
* GDlib 2.0
* ext-GD


## Installation

install the latest version via composer 

    composer require naucon/image


## Basic Usage

The `Image` class is a oop implementation of the functions from the GD php-extension.
Images (GIF, PNG, JPEG) can be created, loaded or opened. This images can be scaled, drawn or filter can be applyed.

To use the `Image` class create a instance of `Image`.

    use Naucon\Image\Image;
    $image = new Image();

Afterwards a image can be created, loaded, opened. To create a image call `create()` and provide the dimensions of the image.

    // create image in 320x213
    $imageWriter = $image->create(320,213);

To load a image from binary data call `load($binary)`.

    // or load image data
    $imageData = file_get_contents(__DIR__ . '/example.png');   // from a string
    $imageWriter = $image->load($imageData);

To open a existing image file call `open($path)` with a absolute file path to the image.

    // or open image file
    $imageWriter = $image->open(__DIR__ . '/example.png');

The methods `create()`, `load()`, `open()` return a instance of `ImageWriter`. The actual image processing is done with this instance.
From now on we work with this instance.

To retrieve width and height of a image call `getWidth()` and `getHeight()`.

    echo 'width: ' . $imageWriter->getWidth();  // width: 320
    echo '<br/>';
    echo 'height: ' . $imageWriter->getHeight();    // height: 213

To scale a image call `scale()` with a width and height. The image will be scaled equally.

    $imageWriter->scale(100,100);        // scale image (equally)

One or more Filters can be applyed on the image.

    $imageWriter
        ->transparentColor(0,0,0)
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
        ->scale(100,100);        // scale image (equally)

Processed images can be saved to a file ...

    // scale and save file
    $imageWriter
        ->transparentColor(0,0,0)
        ->scale(100,100)        // scale image (equally)
        ->save(__DIR__ . '/tmp/new_image.png');     // save image as file

... or dumped to output buffer.Therefor a image format muss be specified.

    // scale and dump image
    $imageWriter
        ->transparentColor(0,0,0)
        ->scale(100,100)        // scale image (equally)
        ->dump('gif');     // dump to output buffer

With destruct of the `ImageWriter` instance the image resource will be closed.

    unset($imageWriter);        // close image resource



## License

The MIT License (MIT)

Copyright (c) 2015 Sven Sanzenbacher

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

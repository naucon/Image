<?php
/*
 * Copyright 2015 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Image;

use Naucon\Image\Exception\ImageWriterException;

/**
 * Image Writer Class
 *
 * @package    Image
 * @author     Sven Sanzenbacher
 */
class ImageWriter
{
    /**
     * @var         resource                    image resource handle
     */
    protected $imageResource = null;

    /**
     * @var         int                         image quality 0-100, default 75
     */
    protected $imageQuality = 75;


    /**
     * Constructor
     *
     * @param       resource        $imageResource      image resource
     * @throws      ImageWriterException
     */
    public function __construct($imageResource)
    {
        // check if given image resource is valid
        if ($this->isImageResource($imageResource)) {
            $this->imageResource = $imageResource;
        } else {
            throw new ImageWriterException('Given image resource is not of type gd.');
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->close();
    }



    /**
     * @access      protected
     * @param       resource        $imageResource      image resource
     * @return      bool
     */
    protected function isImageResource($imageResource)
    {
        if (is_resource($imageResource)
            && get_resource_type($imageResource) == 'gd'
        ) {
            return true;
        }
        return false;
    }

    /**
     * @access      public
     * @return      void
     */
    protected function close()
    {
        if (!is_null($this->imageResource)) {
            imagedestroy($this->imageResource);
            $this->imageResource = null;
        }
    }

    /**
     * @return    int            image width in px
     */
    public function getWidth()
    {
        if (!is_null($this->imageResource)) {
            return imagesx($this->imageResource);
        }
        return false;
    }

    /**
     * @return    int            image height in px
     */
    public function getHeight()
    {
        if (!is_null($this->imageResource)) {
            return imagesy($this->imageResource);
        }
        return false;
    }

    /**
     * @param       int     $red        red 0-255
     * @param       int     $green      green 0-255
     * @param       int     $blue       blue 0-255
     * @return      int                 internal color index
     */
    public function getColorIndex($red, $green, $blue)
    {
        // TODO issue with gif and limited color rang
        if (!is_null($this->imageResource)) {
            return imagecolorallocate($this->imageResource, (int)$red, (int)$green, (int)$blue);
        }
        return false;
    }

    /**
     * @param       int     $red        red 0-255
     * @param       int     $green      green 0-255
     * @param       int     $blue       blue 0-255
     * @return      bool
     */
    public function hasColor($red, $green, $blue)
    {
        // TODO issue with gif and limited color rang
        if (!is_null($this->imageResource)) {
            $colorIndex = imagecolorexact($this->imageResource, (int)$red, (int)$green, (int)$blue);
            if ($colorIndex >= 0) // if color do not exist - color index have the value -1
            {
                return true;
            }
        }
        return false;
    }

    /**
     * @param       int     $red        red 0-255
     * @param       int     $green      green 0-255
     * @param       int     $blue       blue 0-255
     * @return      int                 internal color index
     */
    public function getClosestColorIndex($red, $green, $blue)
    {
        // TODO issue with gif and limited color rang
        if (!is_null($this->imageResource)) {
            return imagecolorclosest($this->imageResource, (int)$red, (int)$green, (int)$blue);
        }
        return false;
    }

    /**
     * @param       int     $red        red 0-255
     * @param       int     $green      green 0-255
     * @param       int     $blue       blue 0-255
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function transparentColor($red, $green, $blue)
    {
        if (!is_null($this->imageResource)) {
            if (($colorIndex = $this->getColorIndex($red, $green, $blue)) >= 0
                && ($newColorIndex = imagecolortransparent($this->imageResource, $colorIndex)) >= 0) {

            } else {
                // if color do not exist - color index have the value -1
                throw new ImageWriterException('Transparent color failed.');
            }
        }
        return $this;
    }

    /**
     * reverses all colors of the image
     *
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function negative()
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_NEGATE)) {
                throw new ImageWriterException('Negative filter failed.');
            }
        }
        return $this;
    }

    /**
     * converts colors to grayscale
     *
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function grayscale()
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_GRAYSCALE)) {
                throw new ImageWriterException('Grayscale filter failed.');
            }
        }
        return $this;
    }

    /**
     * changes brightness of the image
     *
     * @param       int     $value      brightness -255 to +255
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function brightness($value)
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_BRIGHTNESS, (int)$value)) {
                throw new ImageWriterException('Brightness filter failed.');
            }
        }
        return $this;
    }

    /**
     * change contrast of the image
     *
     * @param       int     $value      contrast -100 to +100
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function contrast($value)
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_CONTRAST, (int)$value)) {
                throw new ImageWriterException('Contrast filter failed.');
            }
        }
        return $this;
    }

    /**
     * gamma correction
     *
     * @param       float       $inputgamma         gamma input
     * @param       float       $outputgamma        gamma output
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function gamma($inputgamma, $outputgamma)
    {
        if (!is_null($this->imageResource)) {
            if (!imagegammacorrect($this->imageResource, (float)$inputgamma, (float)$outputgamma)) {
                throw new ImageWriterException('Gamma correction failed.');
            }
        }
        return $this;
    }

    /**
     * colorize image to a specific color
     *
     * @param       int     $red        red 0-255
     * @param       int     $green      green 0-255
     * @param       int     $blue       blue 0-255
     * @param       int     $alpha      alpha channel 0-127
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function colorize($red, $green, $blue, $alpha = 0)
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_COLORIZE, (int)$red, (int)$green, (int)$blue, (int)$alpha)) {
                throw new ImageWriterException('Colorize filter failed.');
            }
        }
        return $this;
    }

    /**
     * highlight/outline the edges
     *
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function outline()
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_EDGEDETECT)) {
                throw new ImageWriterException('Outline filter failed.');
            }
        }
        return $this;
    }

    /**
     * embosses the image
     *
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function emboss()
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_EMBOSS)) {
                throw new ImageWriterException('Emboss filter failed.');
            }
        }
        return $this;
    }

    /**
     * blurs the image
     *
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function blur()
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_SELECTIVE_BLUR)) {
                throw new ImageWriterException('Blur filter failed.');
            }
        }
        return $this;
    }


    /**
     * blurs the image using the gaussian method
     *
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function blurGaussian()
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_GAUSSIAN_BLUR)) {
                throw new ImageWriterException('Gaussian blur filter failed.');
            }
        }
        return $this;
    }

    /**
     * blurs the image using the gaussian method
     *
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function sharpen()
    {
        if (!is_null($this->imageResource)) {
            $matrix = array(
                array(-1.2, -1, -1.2),
                array(-1, 20, -1),
                array(-1.2, -1, -1.2));
            $divisor = array_sum(array_map('array_sum', $matrix));
            $offset = 0;
            if (!imageconvolution($this->imageResource, $matrix, $divisor, $offset)) {
                throw new ImageWriterException('Sharpen filter failed.');
            }
        }
        return $this;
    }

    /**
     * sketchy effect (mean removal)
     *
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function sketchy()
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_MEAN_REMOVAL)) {
                throw new ImageWriterException('Sketchy filter failed.');
            }
        }
        return $this;
    }

    /**
     * makes the image smoother
     *
     * @param       int     $value      smoothness level
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function smooth($value)
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_SMOOTH, (int)$value)) {
                throw new ImageWriterException('Smooth filter failed.');
            }
        }
        return $this;
    }

    /**
     * applies pixelation effect to the image
     *
     * @param       int     $blockSize      block size
     * @param       bool    $advanced       advanced pixelation effect (default is false)
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function pixelation($blockSize, $advanced = false)
    {
        if (!is_null($this->imageResource)) {
            if (!imagefilter($this->imageResource, IMG_FILTER_PIXELATE, (int)$blockSize, (bool)$advanced)) {
                throw new ImageWriterException('Pixelation filter failed.');
            }
        }
        return $this;
    }

    /**
     * specify alpha blending mode
     *
     * @param       bool        $enable     Alpha blending true or false
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function alphaBlending($enable = true)
    {
        if (!is_null($this->imageResource)) {
            if (!imagealphablending($this->imageResource, (bool)$enable)) {
                throw new ImageWriterException('Alpha blending failed.');
            }
        }
        return $this;
    }

    /**
     * specify anti aliasing
     *
     * @param       bool        $enable     Antialiasing true or false
     * @return      ImageWriter
     * @throws      ImageWriterException
     */
    public function antialiase($enable = true)
    {
        if (!is_null($this->imageResource)) {
            if (!imageantialias($this->imageResource, (bool)$enable)) {
                throw new ImageWriterException('Antialiasing failed.');
            }
        }
        return $this;
    }

    /**
     * image quality (for Jpeg)
     *
     * @return      int
     */
    public function getQuality()
    {
        return $this->imageQuality;
    }

    /**
     * image quality (for Jpeg)
     *
     * @param       int     $quality        image quality 0-100
     * @return      ImageWriter
     */
    public function setQuality($quality)
    {
        $this->imageQuality = (int)$quality;
        return $this;
    }

//    /**
//     * @param    resource    image resource
//     * @return    void
//     */
//    public function setRectangle()
//    {
//        if ( $this->isImageResource($resource) )
//        {
//        return imagefilledrectangle($imageResource, $outerPositionX, $outerPositionY, $innerPositionX, $innerPositionY, $colorIndex);
//        }
//    }

//    /**
//     * @param    resource    image resource
//     * @return    void
//     */
//    public function setLine()
//    {
//        if ( $this->isImageResource($resource) )
//        {
//            return imageline($imageResource, $startPositionX, $startPositionY, $endPositionX, $endPositionY, $colorIndex);
//        }
//    }

//    /**
//     * @access    private
//     * @param    resource    image resource
//     * @return bool
//     */
//    public function setPolygon()
//    {
//        if ( $this->isImageResource($resource) )
//        {
//            // $positoins enthält als Value abwechseln x und y coordinaten
//            return imagefilledpolygon($imageResource, $positions=array(), $vertices=3, $colorIndex);
//        }
//    }

//    /**
//     * @access    private
//     * @param    resource    image resource
//     * @return bool
//     */
//    public function setEllipse()
//    {
//        if ( $this->isImageResource($resource) )
//        {
//            return imagefilledellipse($imageResource, $centerPositionX, $centerPositionY, $imageWidth, $imageHeight, $colorIndex);
//        }
//    }

//    /**
//     * @param    string     $text        text
//     * @return    string
//     */
//    public function setText($text)
//    {
//        $fontsize = 12; // GD1 in px or GD2 in pt
//        $angle = 0;
//        $fontfile = '';	// font path
//        $options = array('linespacing' => 0);
//
//        list($lowerLeftCornerX,$lowerLeftCornerY,$lowerRightCornerX,$lowerRightCornerY,$upperRightCornerX,$upperRightCornerY,$upperLeftCornerX,$upperLeftCornerY) = imageftbbox($fontsize,$angle,$fontfile,$text,$options);
//    }

//    /**
//     * @param    string     $text        text
//     * @return    string
//     */
//    public function setText($text)
//    {
//        $fontsize = 12; // GD1 in px or GD2 in pt
//        $angle = 0;
//        $fontfile = '';	// font path
//        $options = array('linespacing' => 0);
//
//        imagefttext($imageResource, $fontsize, $angle, $positionX, $positionY, $fontfile, $text, $options);
//    }

//    /**
//     * @param    resource    source image resource
//     * @param    int            source image position x
//     * @param    int            source image position y
//     * @param    resource    optional target image resource
//     * @param    int            optional target image position x
//     * @param    int            optional target image position y
//     * @return    resource
//     */
//    public function copy()
//    {
//        imagecopy($imageScaledResource, $imageResource, 0, 0, 0, 0);
//        // $transparency = 0-100 Übergang
//        imagecopymerge($imageScaledResource, $imageResource, 0, 0, 0, 0, $transparency);
//        // 0,0,0,0 = position destination x und y sowie source x und y
//        imagecopyresampled($imageScaledResource, $imageResource, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $imageWidth, $imageHeight );
//    }

//    /**
//     * @param    resource    source image resource
//     * @param    int            source image position x
//     * @param    int            source image position y
//     * @param    resource    optional target image resource
//     * @param    int            optional target image position x
//     * @param    int            optional target image position y
//     * @return    resource
//     */
//    public function crop()
//    {
//        imagecopy($imageScaledResource, $imageResource, 0, 0, 0, 0);
//        // $transparency = 0-100 Übergang
//        imagecopymerge($imageScaledResource, $imageResource, 0, 0, 0, 0, $transparency);
//        // 0,0,0,0 = position destination x und y sowie source x und y
//        imagecopyresampled($imageScaledResource, $imageResource, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $imageWidth, $imageHeight );
//    }

    /**
     * scale image
     *
     * @param       int     $width      width in px
     * @param       int     $height     height in px
     * @return      ImageWriter
     */
    public function scale($width, $height)
    {
        if (!is_null($this->imageResource)) {
            $newImageWidth = (int)$width;
            $newImageHeight = (int)$height;

            $imageWidth = $this->getWidth();
            $imageHeight = $this->getHeight();

            if ($imageWidth > 0
                && $imageHeight > 0
                && $newImageWidth > 0
                && $newImageHeight > 0
            ) {
                $scaleFactor = min(($newImageWidth / $imageWidth), ($newImageHeight / $imageHeight)); // lowest scale factor

                $newImageWidth = (int)floor(($scaleFactor * $imageWidth)); // rounding down
                $newImageHeight = (int)floor(($scaleFactor * $imageHeight)); // rounding down
            }

            if ($newImageWidth > 0
                && $newImageHeight > 0
            ) {
                $imageScaledResource = imagecreatetruecolor($newImageWidth, $newImageHeight);
                imagecopyresampled($imageScaledResource, $this->imageResource, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $imageWidth, $imageHeight);
                // imagecopyresampled() has better quality as imagecopyresized()
                //imagecopyresized($imageScaledResource, $this->imageResource, 0, 0, 0, 0, $newImageWidth, $newImageHeight, $imageWidth, $imageHeight);
                imagedestroy($this->imageResource);
                $this->imageResource = $imageScaledResource;
            }
        }
        return $this;
    }

    /**
     * save image
     *
     * @param       string|\SplFileInfo     $pathname
     * @return      bool
     * @throws      ImageWriterException
     */
    public function save($pathname)
    {
        if (!is_null($this->imageResource)) {
            if ($pathname instanceof \SplFileInfo) {
                $fileInfo = $pathname;
            } else {
                $fileInfo = new \SplFileInfo($pathname);
            }

            $result = false;
            switch ($fileInfo->getExtension()) {
                case 'jpe':
                case 'jpg':
                case 'jpeg':
                    $result = imagejpeg($this->imageResource, $fileInfo->getPathname(), $this->getQuality());
                    break;
                case 'png':
                    $result = imagepng($this->imageResource, $fileInfo->getPathname());
                    break;
                case 'gif':
                    $result = imagegif($this->imageResource, $fileInfo->getPathname());
                    break;
            }

            if ($result === false) {
                throw new ImageWriterException('Image could not be saved. Make sure path is writeable.');
            }
            $this->close();
            return $result;
        }
        return false;
    }

    /**
     * dump image
     *
     * @param       string      $type       file type
     * @return      bool
     * @throws      ImageWriterException
     */
    public function dump($type = 'png')
    {
        if (!is_null($this->imageResource)) {
            $result = false;
            switch (strtolower($type)) {
                case 'jpe':
                case 'jpg':
                case 'jpeg':
                    $result = imagejpeg($this->imageResource, null, $this->getQuality());
                    break;
                case 'png':
                    $result = imagepng($this->imageResource, null);
                    break;
                case 'gif':
                    $result = imagegif($this->imageResource, null);
                    break;
            }

            if ($result === false) {
                throw new ImageWriterException('Image could not be dumped. Make sure path is writeable.');
            }
            $this->close();
            return $result;
        }
        return false;
    }
}
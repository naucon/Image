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

use Naucon\Image\Exception\ImageException;

/**
 * Image Class
 * A simple image class to modify a image through the GD lib.
 *
 * @package    Image
 * @author     Sven Sanzenbacher
 *
 * @example    ImageExample.php
 */
class Image
{
    /**
     * @var         array                    information of installed GD library
     */
    protected $gdInfo = null;



    /**
     * Constructor
     *
     */
    public function __construct()
    {
        if (!$this->hasExtension()) {
            throw new ImageException('GD lib Unsupported image resource.');
        }
    }



    /**
     * @return      bool                    has GD extension
     */
    protected function hasExtension()
    {
        if (function_exists('gd_info')) {
            return true;
        }
        return false;
    }

    /**
     * @return      array                    information of installed GD library
     */
    public function getInfo()
    {
        if (is_null($this->gdInfo)) {
            $this->gdInfo = gd_info();
        }
        return $this->gdInfo;
    }

    /**
     * @return      string                    GD library version
     */
    public function getVersion()
    {
        $gdInfo = $this->getInfo();
        if (isset($gdInfo['GD Version'])) {
            return $gdInfo['GD Version'];
        }
        return false;
    }

    /**
     * @param       string      $format     file extension of image format
     * @return      bool                    is supported format
     */
    public function isSupportedFormat($format)
    {
        $gdInfo = $this->getInfo();
        switch (strtolower($format)) {
            case 'gif':
                if (isset($gdInfo['GIF Read Support'])
                    && isset($gdInfo['GIF Create Support'])
                    && $gdInfo['GIF Read Support']
                    && $gdInfo['GIF Create Support']
                ) {
                    return true;
                }
                break;
            case 'png':
                if (isset($gdInfo['PNG Support'])
                    && $gdInfo['PNG Support']
                ) {
                    return true;
                }
                break;
            case 'jpe':
            case 'jpg':
            case 'jpeg':
                if ((isset($gdInfo['JPEG Support']) && $gdInfo['JPEG Support'])
                    || (isset($gdInfo['JPG Support']) && $gdInfo['JPG Support'])
                ) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * create new image
     *
     * @param       int     $width      image width
     * @param       int     $height     image height
     * @return      ImageWriter
     * @throws      ImageException
     */
    public function create($width, $height)
    {
        if ($width > 0 && $height > 0) {
            if ($imageResource = imagecreatetruecolor((int)$width, (int)$height) )
            {
                return new ImageWriter($imageResource);
            } else {
                throw new ImageException('Image could not be created.');
            }
        } else {
            throw new ImageException('Image width and height must be greater than 0.');
        }
    }

    /**
     * create image from a image data string
     *
     * @param       string      $string         image data string
     * @return      ImageWriter
     * @throws      ImageException
     */
    static public function load($string)
    {
        if (!empty($string)) {

            if (($decodedString = base64_decode($string,true)) !== false) {
                $string = $decodedString;
            }

            if ($imageResource = imagecreatefromstring($string)) {
                return new ImageWriter($imageResource);
            } else {
                throw new ImageException('Given image data could not be loaded.');
            }
        } else {
            throw new ImageException('Given image data are empty.');
        }
    }

    /**
     * create image from a given image file
     *
     * @param       string|\SplFileInfo     $pathname
     * @return      ImageWriter|bool
     * @throws      ImageException
     */
    public function open($pathname)
    {
        if ($pathname instanceof \SplFileInfo) {
            $fileInfo = $pathname;
        } else {
            $fileInfo = new \SplFileInfo($pathname);
        }

        if ($fileInfo->isReadable()) {
            switch (strtolower($fileInfo->getExtension())) {
                case 'jpe':
                case 'jpg':
                case 'jpeg':
                    $imageResource = imagecreatefromjpeg($fileInfo->getRealPath());
                    break;
                case 'png':
                    $imageResource = imagecreatefrompng($fileInfo->getRealPath());
                    break;
                case 'gif':
                    $imageResource = imagecreatefromgif($fileInfo->getRealPath());
                    imagecolortransparent($imageResource, imagecolorallocate($imageResource, 0, 0, 0));
                    break;
                default:
                    throw new ImageException('Image type is unkown or unsupported.');
            }

            if ($imageResource !== false) {
                $imageWriter = new ImageWriter($imageResource);
            } else {
                throw new ImageException('Given image could not be opened.');
            }

        } else {
            throw new ImageException('Image do not exist or is not readable.');
        }

        return $imageWriter;
    }
}
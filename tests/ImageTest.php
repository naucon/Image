<?php
/*
 * Copyright 2015 Sven Sanzenbacher
 *
 * This file is part of the naucon package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Naucon\Image\Test;

use Naucon\Image\Image;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return void
     */
    public static function setUpBeforeClass()
    {
        @unlink(__DIR__.'/tmp/example_100_100.gif');
        @unlink(__DIR__.'/tmp/example_100_100.jpg');
        @unlink(__DIR__.'/tmp/example_100_100.png');
    }

    /**
     * @return void
     */
    public static function tearDownAfterClass()
    {
        @unlink(__DIR__.'/tmp/example_100_100.gif');
        @unlink(__DIR__.'/tmp/example_100_100.jpg');
        @unlink(__DIR__.'/tmp/example_100_100.png');
    }

    /**
     * @return void
     */
    public function setUp()
    {
        if (!function_exists('gd_info')) {
            $this->markTestSkipped('GDlib ist not installed.');
        }
    }

    /**
     * @return    array
     */
    public function imageProvider()
    {
        return array(
            array('example.jpg'),
            array('example.gif'),
            array('example.png')
        );
    }

    /**
     * @return          void
     */
    public function testGetInfo()
    {
        $image = new Image();
        $this->assertArrayHasKey('GD Version', $image->getInfo());
    }

    /**
     * @return          void
     */
    public function testGetVersion()
    {
        $image = new Image();
        $image->getVersion();
    }

    /**
     * @return          void
     */
    public function testIsSupportedFormat()
    {
        $image = new Image();
        $this->assertTrue($image->isSupportedFormat('gif'));
        $this->assertTrue($image->isSupportedFormat('jpg'));
        $this->assertTrue($image->isSupportedFormat('png'));
    }

    /**
     * @return          void
     */
    public function testCreate()
    {
        $image = new Image();
        $imageWriter = $image->create(100,100);
        $this->assertInstanceOf('Naucon\Image\ImageWriter',$imageWriter);
    }

    /**
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testLoad($filename)
    {
        $image = new Image();
        $imageWriter = $image->load(file_get_contents(__DIR__ . '/' . $filename));
        $this->assertInstanceOf('Naucon\Image\ImageWriter',$imageWriter);
    }

    /**
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testLoadEncoded($filename)
    {
        $image = new Image();
        $imageWriter = $image->load(base64_encode(file_get_contents(__DIR__ . '/' . $filename)));
        $this->assertInstanceOf('Naucon\Image\ImageWriter',$imageWriter);
    }

    /**
     * @expectedException   Naucon\Image\Exception\ImageException
     * @return          void
     */
    public function testLoadEmptyString()
    {
        $imageData = '';

        $image = new Image();
        $imageWriter = $image->load($imageData);
        $this->assertInstanceOf('Naucon\Image\ImageWriter',$imageWriter);
    }

    /**
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testOpen($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter',$imageWriter);
    }

    /**
     * @expectedException   Naucon\Image\Exception\ImageException
     * @return              void
     */
    public function testOpenUnsupported()
    {
        $image = new Image();
        $this->assertFalse($image->open(__DIR__ . '/example.txt'));
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testClose($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $imageWriter->__destruct();
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testGetWidthAndHeight($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertEquals(320,$imageWriter->getWidth());
        $this->assertEquals(213,$imageWriter->getHeight());
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testGetColorIndex($filename)
    {
        if ($filename!='example.gif') {
            $image = new Image();
            $imageWriter = $image->open(__DIR__ . '/' . $filename);
            $this->assertEquals(16777215,$imageWriter->getColorIndex(255,255,255));  // black
            $this->assertEquals(0,$imageWriter->getColorIndex(0,0,0));        // white
            $this->assertEquals(16711680,$imageWriter->getColorIndex(255,0,0));      // red
            unset($imageWriter);
        }
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testHasColor($filename)
    {
        if ($filename!='example.gif') {
            $image = new Image();
            $imageWriter = $image->open(__DIR__ . '/' . $filename);
            $this->assertTrue($imageWriter->hasColor(255,255,255));  // black
            $this->assertTrue($imageWriter->hasColor(0,0,0));        // white
            $this->assertTrue($imageWriter->hasColor(255,0,0));  // red
            unset($imageWriter);
        }
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testGetClosestColorIndex($filename)
    {
        if ($filename!='example.gif') {
            $image = new Image();
            $imageWriter = $image->open(__DIR__ . '/' . $filename);
            $this->assertEquals(16777215,$imageWriter->getClosestColorIndex(255,255,255));  // black
            $this->assertEquals(0,$imageWriter->getClosestColorIndex(0,0,0));               // white
            $this->assertEquals(16711680,$imageWriter->getClosestColorIndex(255,0,0));      // red
            unset($imageWriter);
        }
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testTransparentColor($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->transparentColor(255,255,255));  // black
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testNegative($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->negative());  // reverses colors
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testGrayscale($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->grayscale());  // converts colors to  grayscale
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testBrightness($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->brightness(-255));  // mix brightness
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->brightness(0));  // no change
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->brightness(255));  // max brightness
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testContrast($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->contrast(-100));  // max contrast
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->contrast(0));  // no change
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->contrast(100));  // mix contrast
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testGamma($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->gamma(1.0, 2.0));  // gamma correction
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->gamma(1.0, 1.0));  // gamma correction
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->gamma(2.0, 1.0));  // gamma correction
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testColorize($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->colorize(255,0,0));  // colorize to red without alpha
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->colorize(0,255,0));  // colorize to green without alpha
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->colorize(255,0,0,0));  // mix alpha
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->colorize(255,0,0,127));  // mix alpha
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testOutline($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->outline());  // highlight the edges
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testEmboss($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->emboss());  // emboss image
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testBlur($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->blur());  // blur image
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testBlurGaussian($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->blurGaussian());  // blur with gaussian method
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testSharpen($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->sharpen());  // sharpen
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testSketchy($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->sketchy());  // sketchy effect
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testSmooth($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->smooth(-8));  // smooth image
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->smooth(8));  // smooth image
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testPixelation($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->pixelation(3));  // pixelation effect
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->pixelation(3,true));  // advanced pixelation effect
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testAlphaBlending($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->alphaBlending(true));  // enable alpha blending mode
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->alphaBlending(false));  // disable alpha blending mode
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testAntialiase($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->antialiase(true));  // enable antialiase
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->antialiase(false));  // disable antialiase
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testScale($filename)
    {
        $image = new Image();
        $imageWriter = $image->open(__DIR__ . '/' . $filename);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->scale(100,100));
        $this->assertEquals(100,$imageWriter->getWidth());
        $this->assertEquals(66,$imageWriter->getHeight());
        unset($imageWriter);
    }

    /**
     * @depends         testOpen
     * @dataProvider    imageProvider
     * @param           string                  filename
     * @return          void
     */
    public function testScaleSave($filename)
    {
        $sourceFileInfo = new \SplFileInfo(__DIR__ . '/' . $filename);

        $image = new Image();
        $imageWriter = $image->open($sourceFileInfo);
        $this->assertInstanceOf('Naucon\Image\ImageWriter', $imageWriter->scale(100,100));
        $imageWriter->getWidth();
        $imageWriter->getHeight();

        $targetFile = __DIR__
            . '/tmp/'
            . rtrim($sourceFileInfo->getBasename($sourceFileInfo->getExtension()),'.')
            . '_100_100'
            . '.' . $sourceFileInfo->getExtension();
        $this->assertTrue($imageWriter->save($targetFile));
        $this->assertFileExists($targetFile);
        unset($imageWriter);
    }

    /**
     * @depends         testCreate
     * @return          void
     */
    public function testDump()
    {
        $image = new Image();
        $imageWriter = $image->create(1,1);

        $this->expectOutputString(file_get_contents(__DIR__ . '/pixel.gif'));
        $this->assertTrue($imageWriter->dump('gif'));
        unset($imageWriter);
    }
}
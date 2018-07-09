<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Seccode;

use Exception;
use Leevel\Support\Str;

/**
 * 验证码
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.27
 *
 * @version 1.0
 */
class Seccode implements ISeccode
{
    /**
     * 验证码
     *
     * @var string
     */
    protected $code;

    /**
     * 宽度.
     *
     * @var int
     */
    protected $resolvedWidth;

    /**
     * 高度.
     *
     * @var int
     */
    protected $resolvedHeight;

    /**
     * 字体路径.
     *
     * @var string
     */
    protected $resolvedFontPath;

    /**
     * 中文字体路径.
     *
     * @var string
     */
    protected $resolvedChineseFontPath;

    /**
     * 背景路径.
     *
     * @var string
     */
    protected $resolvedBackgroundPath;

    /**
     * 字体颜色.
     *
     * @var array
     */
    protected $fontColor = [];

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        // 验证码宽度
        'width' => 160,

        // 验证码高度
        'height' => 60,

        // 随机背景图形
        'adulterate' => true,

        // 随机倾斜度
        'tilt' => true,

        // 随机颜色
        'color' => true,

        // 随机大小
        'size' => true,

        // 文字阴影
        'shadow' => true,

        // 英文字体路径
        'font_path' => '',

        // 中文字体路径
        'chinese_font_path' => '',

        // 背景图路径
        'background_path' => '',

        // 启用背景图像
        'background' => true,
    ];

    /**
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setOption(string $name, $value): void
    {
        $this->option[$name] = $value;
    }

    /**
     * 设置验证码
     *
     * @param mixed  $code
     * @param bool   $autoCode
     * @param string $autoType
     *
     * @return $this
     */
    public function display($code = null, $autoCode = true, $autoType = self::ALPHA_UPPERCASE)
    {
        if (is_int($code) && $autoCode) {
            $this->autoCode($code, $autoType);
        } else {
            $code && $this->code($code);
        }

        $resImage = imagecreatefromstring($this->makeBackground());

        if ($this->option['adulterate']) {
            $this->makeAdulterate($resImage);
        }

        $this->makeTtfFont($resImage);

        if (function_exists('imagepng')) {
            header('Content-type: image/png');
            imagepng($resImage);
        } else {
            header('Content-type: image/jpeg');
            imagejpeg($resImage, '', 100);
        }

        imagedestroy($resImage);
    }

    /**
     * 设置验证码
     *
     * @param string $code
     *
     * @return $this
     */
    public function code($code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * 返回验证码
     *
     * @return $this
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 返回宽度.
     *
     * @return int
     */
    public function getWidth()
    {
        if (null !== $this->resolvedWidth) {
            return $this->resolvedWidth;
        }

        if ($this->option['width'] < static::MIN_WIDTH) {
            $this->setOption('width', static::MIN_WIDTH);
        } elseif ($this->option['width'] > static::MAX_WIDTH) {
            $this->setOption('width', static::MAX_WIDTH);
        }

        return $this->resolvedWidth = $this->option['width'];
    }

    /**
     * 返回高度.
     *
     * @return int
     */
    public function getHeight()
    {
        if (null !== $this->resolvedHeight) {
            return $this->resolvedHeight;
        }

        if ($this->option['height'] < static::MIN_HEIGHT) {
            $this->setOption('height', static::MIN_HEIGHT);
        } elseif ($this->option['height'] > static::MAX_HEIGHT) {
            $this->setOption('height', static::MAX_HEIGHT);
        }

        return $this->resolvedHeight = $this->option['height'];
    }

    /**
     * 返回英文字体路径.
     *
     * @return string
     */
    public function getFontPath()
    {
        if (null !== $this->resolvedFontPath) {
            return $this->resolvedFontPath;
        }

        return $this->resolvedFontPath = $this->option['font_path'] ?:
            $this->getDefaultFontPath();
    }

    /**
     * 返回中文字体路径.
     *
     * @return string
     */
    public function getChineseFontPath()
    {
        if (null !== $this->resolvedChineseFontPath) {
            return $this->resolvedChineseFontPath;
        }

        return $this->resolvedChineseFontPath = $this->option['chinese_font_path'] ?:
            $this->getDefaultChineseFontPath();
    }

    /**
     * 返回背景图路径.
     *
     * @return string
     */
    public function getBackgroundPath()
    {
        if (null !== $this->resolvedBackgroundPath) {
            return $this->resolvedBackgroundPath;
        }

        return $this->resolvedBackgroundPath = $this->option['background_path'] ?:
            $this->getDefaultBackgroundPath();
    }

    /**
     * 创建背景图像.
     *
     * @return string
     */
    protected function makeBackground()
    {
        $resImage = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        $resColor = imagecolorallocate($resImage, 255, 255, 255);

        if (false === $this->makeBackgroundWithImage($resImage)) {
            $this->makeBackgroundDefault($resImage);
        }

        ob_start();

        if (function_exists('imagepng')) {
            imagepng($resImage);
        } else {
            imagejpeg($resImage, '', 100);
        }

        imagedestroy($resImage);
        $background = ob_get_contents();

        ob_end_clean();

        return $background;
    }

    /**
     * 创建随机背景图形.
     *
     * @param resource $resImage
     */
    protected function makeAdulterate(&$resImage)
    {
        $lineNum = $this->getHeight() / 10;

        if ($lineNum < 1) {
            return;
        }

        for ($i = 0; $i <= $lineNum; $i++) {
            $resColor = $this->option['color'] ?
                imagecolorallocate(
                    $resImage,
                    mt_rand(0, 255),
                    mt_rand(0, 255),
                    mt_rand(0, 255)
                ) :
                imagecolorallocate(
                    $resImage,
                    $this->fontColor[0],
                    $this->fontColor[1],
                    $this->fontColor[2]
                );

            $x = $this->mtRand(0, $this->getWidth());
            $y = $this->mtRand(0, $this->getHeight());

            if (mt_rand(0, 1)) {
                imagearc(
                    $resImage,
                    $x,
                    $y,
                    $this->mtRand(0, $this->getWidth()),
                    $this->mtRand(0, $this->getHeight()),
                    mt_rand(0, 360),
                    mt_rand(0, 360),
                    $resColor
                );
            } else {
                imageline(
                    $resImage,
                    $x,
                    $y,
                    0 + mt_rand(0, 0),
                    0 + $this->mtRand(0, $this->mtRand($this->getHeight(), $this->getWidth())),
                    $resColor
                );
            }
        }
    }

    /**
     * 创建字体信息.
     *
     * @param resource $resImage
     */
    protected function makeTtfFont(&$resImage)
    {
        if (!function_exists('imagettftext')) {
            throw new Exception(
                'Function imagettftext is not exits.'
            );
        }

        list($font, $code, $widthTotal) = $this->getFontOption();

        // deg2rad() 函数将角度转换为弧度 cos 是 cosine 的简写
        // 表示余弦函数
        $x = $this->mtRand(
            $font[0]['tilt'] > 0 ?
                cos(deg2rad(90 - $font[0]['tilt'])) * $font[0]['zheight'] :
                1,
            $this->getWidth() - $widthTotal
        );

        // 是否启用随机颜色
        !$this->option['color'] &&
            $resTextColor = imagecolorallocate(
                $resImage,
                $this->fontColor[0],
                $this->fontColor[1],
                $this->fontColor[2]
            );

        for ($i = 0; $i < count($font); $i++) {
            if ($this->option['color']) {
                $this->fontColor = [
                    mt_rand(0, 255),
                    mt_rand(0, 255),
                    mt_rand(0, 255),
                ];

                $this->option['shadow'] &&
                    $resTextShadowColor = imagecolorallocate(
                        $resImage,
                        255 - $this->fontColor[0],
                        255 - $this->fontColor[1],
                        255 - $this->fontColor[2]
                    );

                $resTextColor = imagecolorallocate(
                    $resImage,
                    $this->fontColor[0],
                    $this->fontColor[1],
                    $this->fontColor[2]
                );
            } elseif ($this->option['shadow']) {
                $resTextShadowColor = imagecolorallocate(
                    $resImage,
                    255 - $this->fontColor[0],
                    255 - $this->fontColor[1],
                    255 - $this->fontColor[2]
                );
            }

            $y = $font[0]['tilt'] > 0 ?
                $this->mtRand(
                    $font[$i]['height'],
                    $this->getHeight()
                ) :
                $this->mtRand(
                    $font[$i]['height'] - $font[$i]['hd'],
                    $this->getHeight() - $font[$i]['hd']
                );

            $this->option['shadow'] &&
                imagettftext(
                    $resImage,
                    $font[$i]['size'],
                    $font[$i]['tilt'],
                    $x + 1,
                    $y + 1,
                    $resTextShadowColor,
                    $font[$i]['font'],
                    $code[$i]
                );

            imagettftext($resImage,
                $font[$i]['size'],
                $font[$i]['tilt'],
                $x,
                $y,
                $resTextColor,
                $font[$i]['font'],
                $code[$i]
            );

            $x += $font[$i]['width'];
        }
    }

    /**
     * 返回字体参数.
     *
     * @return array
     */
    protected function getFontOption()
    {
        $code = $this->getCode();
        $ttf = $this->getTtf();

        if ($this->isChinese($code)) {
            $code = str_split($code, 3);
            $codeLength = count($code);
        } else {
            $codeLength = strlen($code);
        }

        $font = [];
        $widthTotal = 0;

        for ($i = 0; $i < $codeLength; $i++) {
            if (!isset($font[$i])) {
                $font[$i] = [];
            }

            $font[$i]['font'] = $ttf[array_rand($ttf)];
            $font[$i]['tilt'] = $this->option['tilt'] ? mt_rand(-30, 30) : 0;
            $font[$i]['size'] = $this->getWidth() / 6;

            $this->option['size'] &&
                $font[$i]['size'] = $this->mtRand(
                    $font[$i]['size'] - $this->getWidth() / 40,
                    $font[$i]['size'] + $this->getWidth() / 20
                );

            $resBox = imagettfbbox(
                $font[$i]['size'],
                0,
                $font[$i]['font'],
                $code[$i]
            );

            $font[$i]['zheight'] = max($resBox[1], $resBox[3]) -
                min($resBox[5], $resBox[7]);

            $resBox = imagettfbbox(
                $font[$i]['size'],
                $font[$i]['tilt'],
                $font[$i]['font'],
                $code[$i]
            );

            $font[$i]['height'] = max($resBox[1], $resBox[3]) -
                min($resBox[5], $resBox[7]);

            $font[$i]['hd'] = $font[$i]['height'] - $font[$i]['zheight'];

            $font[$i]['width'] =
                (max($resBox[2], $resBox[4]) - min($resBox[0], $resBox[6])) +
                mt_rand(0, $this->getWidth() / 8);

            $font[$i]['width'] =
                $font[$i]['width'] > $this->getWidth() / $codeLength ?
                $this->getWidth() / $codeLength :
                $font[$i]['width'];

            $widthTotal += $font[$i]['width'];
        }

        return [
            $font,
            $code,
            $widthTotal,
        ];
    }

    /**
     * 创建图片背景图像.
     *
     * @param resource $resImage
     *
     * @return bool
     */
    protected function makeBackgroundWithImage(&$resImage)
    {
        $background = false;

        if ($this->option['background'] &&
            function_exists('imagecreatefromjpeg') &&
            function_exists('imagecolorat') &&
            function_exists('imagecopymerge') &&
            function_exists('imagesetpixel') &&
            function_exists('imageSX') &&
            function_exists('imageSY')) {
            if (!is_dir($this->getBackgroundPath())) {
                throw new Exception(
                    sprintf(
                        'Background path %s is not exists.',
                        $this->getBackgroundPath()
                    )
                );
            }

            $background = glob($this->getBackgroundPath().'/*.*');

            if ($background) {
                $resBackground = imagecreatefromjpeg($background[array_rand($background)]);
                $resColorIndex = imagecolorat($resBackground, 0, 0);
                $color = imagecolorsforindex($resBackground, $resColorIndex);
                $resColorIndex = imagecolorat($resBackground, 1, 0);

                imagesetpixel($resBackground, 0, 0, $resColorIndex);

                $color[0] = $color['red'];
                $color[1] = $color['green'];
                $color[2] = $color['blue'];

                imagecopymerge(
                    $resImage,
                    $resBackground,
                    0,
                    0,
                    $this->mtRand(0, 200 - $this->getWidth()),
                    $this->mtRand(0, 80 - $this->getHeight()),
                    imagesx($resBackground),
                    imagesy($resBackground),
                    100
                );

                imagedestroy($resBackground);

                $background = true;

                $this->fontColor = $color;
            }
        }

        return $background;
    }

    /**
     * 创建默认背景图像.
     *
     * @param resource $resImage
     */
    protected function makeBackgroundDefault(&$resImage)
    {
        for ($i = 0; $i < 3; $i++) {
            $start[$i] = mt_rand(200, 255);
            $end[$i] = mt_rand(100, 150);
            $step[$i] = ($end[$i] - $start[$i]) / $this->getWidth();
            $color[$i] = $start[$i];
        }

        for ($i = 0; $i < $this->getWidth(); $i++) {
            $resColor = imagecolorallocate(
                $resImage,
                $color[0],
                $color[1],
                $color[2]
            );

            imageline(
                $resImage,
                $i,
                0,
                $i - ($this->option['tilt'] ? mt_rand(-30, 30) : 0),
                $this->getHeight(),
                $resColor
            );

            $color[0] += $step[0];
            $color[1] += $step[1];
            $color[2] += $step[2];
        }

        $color[0] -= 20;
        $color[1] -= 20;
        $color[2] -= 20;

        $this->fontColor = $color;

        unset($color);
    }

    /**
     * 返回验证字体.
     *
     * @return array
     */
    protected function getTtf()
    {
        $fontPath = $this->isChinese($this->getCode()) ?
            $this->getChineseFontPath() :
            $this->getFontPath();

        if (!is_dir($fontPath)) {
            throw new Exception(
                sprintf('Font path %s is not exits', $fontPath)
            );
        }

        $ttf = glob($fontPath.'/*.*');

        if (empty($ttf)) {
            throw new Exception('Font not found');
        }

        return $ttf;
    }

    /**
     * 自动产生验证码
     *
     * @param int    $size
     * @param string $autoType
     *
     * @return bool
     */
    protected function autoCode($size, $autoType = self::ALPHA_UPPERCASE)
    {
        if ($size < 1) {
            throw new Exception(
                sprintf('Code must be greater than %d', 0)
            );
        }

        if (!in_array($autoType, $this->getAllowedAutoType(), true)) {
            throw new Exception(
                sprintf(
                    'Code type must be these %s',
                    implode(',', $this->getAllowedAutoType())
                )
            );
        }

        $this->code(
            Str::{'rand'.ucwords(Str::camelize($autoType))}($size)
        );
    }

    /**
     * 返回允许自动验证码类型.
     *
     * @return array
     */
    protected function getAllowedAutoType()
    {
        return [
            static::ALPHA_NUM,
            static::ALPHA_NUM_LOWERCASE,
            static::ALPHA_NUM_UPPERCASE,
            static::ALPHA,
            static::ALPHA_LOWERCASE,
            static::ALPHA_UPPERCASE,
            static::NUM,
            static::CHINESE,
        ];
    }

    /**
     * 是否为中文.
     *
     * @param string $code
     *
     * @return bool
     */
    protected function isChinese($code)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $code);
    }

    /**
     * 文件后缀
     *
     * @param string $fileName
     *
     * @return string
     */
    protected function ext($fileName)
    {
        return trim(substr(strrchr($fileName, '.'), 1, 10));
    }

    /**
     * 返回英文字体路径.
     *
     * @return string
     */
    protected function getDefaultFontPath()
    {
        return __DIR__.'/font';
    }

    /**
     * 返回中文字体路径.
     *
     * @return string
     */
    protected function getDefaultChineseFontPath()
    {
        return '';
    }

    /**
     * 返回背景图路径.
     *
     * @return string
     */
    protected function getDefaultBackgroundPath()
    {
        return __DIR__.'/background';
    }

    /**
     * 生成随机数.
     *
     * @param int $numFirst
     * @param int $numSecond
     *
     * @return int
     */
    protected function mtRand(int $numFirst, int $numSecond): int
    {
        if ($numFirst > $numSecond) {
            list($numSecond, $numFirst) = [$numFirst, $numSecond];
        }

        return mt_rand($numFirst, $numSecond);
    }
}

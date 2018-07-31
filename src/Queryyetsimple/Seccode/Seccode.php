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

use InvalidArgumentException;
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
     *
     * @return $this
     */
    public function setOption(string $name, $value)
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 设置验证码
     *
     * @param mixed  $code
     * @param string $outPath
     * @param bool   $autoCode
     * @param string $autoType
     *
     * @return $this
     */
    public function display($code = null, ?string $outPath = null, $autoCode = true, $autoType = self::ALPHA_UPPERCASE)
    {
        if (is_int($code) && $autoCode) {
            $this->autoCode($code, $autoType);
        } else {
            $code && $this->code($code);
        }

        $resImage = imagecreatefromstring($this->makeBackground());

        if ($this->option['adulterate']) {
            #$this->makeAdulterate($resImage);
        }

        $this->makeTtfFont($resImage);

        if ($outPath) {
            $dirname = dirname($outPath);

            if (!is_dir($dirname)) {
                mkdir($dirname, 0777, true);
            }

            imagepng($resImage, $outPath, 9);
        } else {
            // @codeCoverageIgnoreStart
            header('Content-type: image/png');
            imagepng($resImage);
            // @codeCoverageIgnoreEnd
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
    protected function normalizeWidth()
    {
        if ($this->option['width'] < static::MIN_WIDTH) {
            return static::MIN_WIDTH;
        }
        if ($this->option['width'] > static::MAX_WIDTH) {
            return static::MAX_WIDTH;
        }

        return $this->option['width'];
    }

    /**
     * 返回高度.
     *
     * @return int
     */
    protected function normalizeHeight()
    {
        if ($this->option['height'] < static::MIN_HEIGHT) {
            return static::MIN_HEIGHT;
        }
        if ($this->option['height'] > static::MAX_HEIGHT) {
            return static::MAX_HEIGHT;
        }

        return $this->option['height'];
    }

    /**
     * 创建背景图像.
     *
     * @return string
     */
    protected function makeBackground()
    {
        $resImage = imagecreatetruecolor($this->normalizeWidth(), $this->normalizeHeight());
        $resColor = imagecolorallocate($resImage, 255, 255, 255);

        if (false === $this->makeBackgroundWithImage($resImage)) {
            $this->makeBackgroundDefault($resImage);
        }

        ob_start();

        imagepng($resImage);

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
        $width = $this->normalizeWidth();
        $height = $this->normalizeHeight();

        $lineNum = $height / 10;

        for ($i = 0; $i <= $lineNum; $i++) {
            $resColor = $this->option['color'] ?
                imagecolorallocate(
                    $resImage,
                    $this->mtRand(0, 255),
                    $this->mtRand(0, 255),
                    $this->mtRand(0, 255)
                ) :
                imagecolorallocate(
                    $resImage,
                    $this->fontColor[0],
                    $this->fontColor[1],
                    $this->fontColor[2]
                );

            $x = $this->mtRand(0, $width);
            $y = $this->mtRand(0, $height);

            if (mt_rand(0, 1)) {
                imagearc(
                    $resImage,
                    $x,
                    $y,
                    $this->mtRand(0, $width),
                    $this->mtRand(0, $height),
                    $this->mtRand(0, 360),
                    $this->mtRand(0, 360),
                    $resColor
                );
            } else {
                imageline(
                    $resImage,
                    $x,
                    $y,
                    0 + $this->mtRand(0, 0),
                    0 + $this->mtRand(0, $this->mtRand($height, $width)),
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
            // @codeCoverageIgnoreStart
            throw new InvalidArgumentException(
                'Function imagettftext is not exits.'
            );
            // @codeCoverageIgnoreEnd
        }

        list($font, $code, $widthTotal) = $this->getFontOption();

        $width = $this->normalizeWidth();
        $height = $this->normalizeHeight();

        // deg2rad() 函数将角度转换为弧度 cos 是 cosine 的简写
        // 表示余弦函数
        $x = $this->mtRand(
            $font[0]['tilt'] > 0 ?
                cos(deg2rad(90 - $font[0]['tilt'])) * $font[0]['zheight'] :
                1,
            $width - $widthTotal
        );

        // 是否启用随机颜色
        !$this->option['color'] && $resTextColor = imagecolorallocate(
            $resImage,
            $this->fontColor[0],
            $this->fontColor[1],
            $this->fontColor[2]
        );

        for ($i = 0; $i < count($font); $i++) {
            if ($this->option['color']) {
                $this->fontColor = [
                    $this->mtRand(0, 255),
                    $this->mtRand(0, 255),
                    $this->mtRand(0, 255),
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
                    $height
                ) :
                $this->mtRand(
                    $font[$i]['height'] - $font[$i]['hd'],
                    $height - $font[$i]['hd']
                );

            $this->option['shadow'] &&
                imagettftext($resImage,
                    $font[$i]['size'],
                    $font[$i]['tilt'],
                    (int) ($x + 1),
                    (int) ($y + 1),
                    $resTextShadowColor,
                    $font[$i]['font'],
                    $code[$i]
                );

            imagettftext($resImage,
                $font[$i]['size'],
                $font[$i]['tilt'],
                (int) ($x),
                (int) ($y),
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
        $width = $this->normalizeWidth();

        for ($i = 0; $i < $codeLength; $i++) {
            if (!isset($font[$i])) {
                $font[$i] = [];
            }

            $font[$i]['font'] = $ttf[array_rand($ttf)];
            $font[$i]['tilt'] = $this->option['tilt'] ? $this->mtRand(-30, 30) : 0;
            $font[$i]['size'] = $width / 6;

            $this->option['size'] &&
                $font[$i]['size'] = $this->mtRand(
                    $font[$i]['size'] - $width / 40,
                    $font[$i]['size'] + $width / 20
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
                $this->mtRand(0, (int) ($width / 8));

            $font[$i]['width'] =
                $font[$i]['width'] > $width / $codeLength ?
                $width / $codeLength :
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
        $backgroundPath = $this->option['background_path'];
        $width = $this->normalizeWidth();
        $height = $this->normalizeHeight();

        if ($this->option['background'] &&
            function_exists('imagecreatefromjpeg') &&
            function_exists('imagecolorat') &&
            function_exists('imagecopymerge') &&
            function_exists('imagesetpixel') &&
            function_exists('imageSX') &&
            function_exists('imageSY')) {
            if (!is_dir($backgroundPath)) {
                throw new InvalidArgumentException(
                    sprintf('Background path %s is not exists.', $backgroundPath)
                );
            }

            $background = glob($backgroundPath.'/*.*');

            if ($background) {
                $resBackground = imagecreatefromjpeg($background[array_rand($background)]);
                $resColorIndex = imagecolorat($resBackground, 0, 0);
                $color = imagecolorsforindex($resBackground, $resColorIndex);
                $resColorIndex = imagecolorat($resBackground, 1, 0);

                imagesetpixel($resBackground, 0, 0, $resColorIndex);

                $color[0] = (int) ($color['red']);
                $color[1] = (int) ($color['green']);
                $color[2] = (int) ($color['blue']);

                imagecopymerge(
                    $resImage,
                    $resBackground,
                    0,
                    0,
                    $this->mtRand(0, 200 - $width),
                    $this->mtRand(0, 80 - $height),
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
        $width = $this->normalizeWidth();
        $height = $this->normalizeHeight();

        for ($i = 0; $i < 3; $i++) {
            $start[$i] = $this->mtRand(200, 255);
            $end[$i] = $this->mtRand(100, 150);
            $step[$i] = ($end[$i] - $start[$i]) / $width;
            $color[$i] = $start[$i];
        }

        for ($i = 0; $i < $width; $i++) {
            $resColor = imagecolorallocate(
                $resImage,
                (int) ($color[0]),
                (int) ($color[1]),
                (int) ($color[2])
            );

            imageline(
                $resImage,
                $i,
                0,
                $i - ($this->option['tilt'] ? $this->mtRand(-30, 30) : 0),
                $height,
                $resColor
            );

            $color[0] += $step[0];
            $color[1] += $step[1];
            $color[2] += $step[2];
        }

        $color[0] -= 20;
        $color[1] -= 20;
        $color[2] -= 20;

        $color[0] = (int) ($color[0]);
        $color[1] = (int) ($color[1]);
        $color[2] = (int) ($color[2]);

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
            $this->option['chinese_font_path'] :
            $this->option['font_path'];

        if (!is_dir($fontPath)) {
            throw new InvalidArgumentException(
                sprintf('Font path %s is not exits.', $fontPath)
            );
        }

        $ttf = glob($fontPath.'/*.*');

        if (empty($ttf)) {
            throw new InvalidArgumentException('Font files not found.');
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
            throw new InvalidArgumentException(
                sprintf('Code must be greater than %d.', 0)
            );
        }

        if (!in_array($autoType, $this->getAllowedAutoType(), true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Code type must be these %s.',
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
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', (string) ($code));
    }

    /**
     * 生成随机数.
     *
     * @param number $numFirst
     * @param number $numSecond
     *
     * @return int
     */
    protected function mtRand($numFirst, $numSecond)
    {
        $numFirst = (int) ($numFirst);
        $numSecond = (int) ($numSecond);

        if ($numFirst > $numSecond) {
            list($numSecond, $numFirst) = [$numFirst, $numSecond];
        }

        return (int) (mt_rand($numFirst, $numSecond));
    }
}

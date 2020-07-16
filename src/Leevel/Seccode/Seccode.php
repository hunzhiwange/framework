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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Seccode;

use InvalidArgumentException;
use Leevel\Filesystem\Helper\create_directory;
use function Leevel\Filesystem\Helper\create_directory;

/**
 * 验证码.
 */
class Seccode
{
    /**
     * 图像最大宽度.
     *
     * @var int
     */
    const MAX_WIDTH = 999;

    /**
     * 图像最大高度.
     *
     * @var int
     */
    const MAX_HEIGHT = 999;

    /**
     * 图像最小宽度.
     *
     * @var int
     */
    const MIN_WIDTH = 16;

    /**
     * 图像最小高度.
     *
     * @var int
     */
    const MIN_HEIGHT = 16;

    /**
     * 随机字母数字.
     *
     * @var string
     */
    const ALPHA_NUM = 'alpha_num';

    /**
     * 随机小写字母数字.
     *
     * @var string
     */
    const ALPHA_NUM_LOWERCASE = 'alpha_num_lowercase';

    /**
     * 随机大写字母数字.
     *
     * @var string
     */
    const ALPHA_NUM_UPPERCASE = 'alpha_num_uppercase';

    /**
     * 随机字母.
     *
     * @var string
     */
    const ALPHA = 'alpha';

    /**
     * 随机小写字母.
     *
     * @var string
     */
    const ALPHA_LOWERCASE = 'alpha_lowercase';

    /**
     * 随机大写字母.
     *
     * @var string
     */
    const ALPHA_UPPERCASE = 'alpha_uppercase';

    /**
     * 随机数字.
     *
     * @var string
     */
    const NUM = 'num';

    /**
     * 随机字中文.
     *
     * @var string
     */
    const CHINESE = 'chinese';

    /**
     * 验证码.
     *
     * @var string
     */
    protected ?string $code = null;

    /**
     * 字体颜色.
     *
     * @var array
     */
    protected array $fontColor = [];

    /**
     * 配置.
     *
     * - width:验证码宽度
     * - height:验证码高度
     * - tilt:随机倾斜度
     * - color:随机颜色
     * - size:随机大小
     * - font_path:英文字体路径
     * - chinese_font_path:中文字体路径
     *
     * @var array
     */
    protected array $option = [
        'width'             => 160,
        'height'            => 60,
        'adulterate'        => true,
        'tilt'              => true,
        'color'             => true,
        'size'              => true,
        'font_path'         => '',
        'chinese_font_path' => '',
    ];

    /**
     * 构造函数.
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
    }

    /**
     * 设置验证码.
     *
     * @param mixed $code
     */
    public function display(mixed $code = null, ?string $outPath = null, bool $autoCode = true, string $autoType = self::ALPHA_UPPERCASE): void
    {
        if (is_int($code) && $autoCode) {
            $this->autoCode($code, $autoType);
        } else {
            $code && $this->setCode($code);
        }

        $resImage = imagecreatefromstring($this->makeBackground());

        $this->makeTtfFont($resImage);

        if ($outPath) {
            create_directory(dirname($outPath));
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
     * 设置验证码.
     *
     * @return \Leevel\Seccode\Seccode
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * 返回验证码.
     *
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * 返回宽度.
     */
    protected function normalizeWidth(): int
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
     */
    protected function normalizeHeight(): int
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
     */
    protected function makeBackground(): string
    {
        $resImage = imagecreatetruecolor($this->normalizeWidth(), $this->normalizeHeight());
        imagecolorallocate($resImage, 255, 255, 255);

        $this->makeBaseBackground($resImage);

        ob_start();
        imagepng($resImage);
        imagedestroy($resImage);
        $background = ob_get_contents() ?: '';
        ob_end_clean();

        return $background;
    }

    /**
     * 创建字体信息.
     *
     * @param resource $resImage
     *
     * @throws \InvalidArgumentException
     */
    protected function makeTtfFont(&$resImage): void
    {
        if (!function_exists('imagettftext')) {
            // @codeCoverageIgnoreStart
            throw new InvalidArgumentException('Function imagettftext is not exits.');
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
        $resTextColor = null;
        if (!$this->option['color']) {
            $resTextColor = imagecolorallocate(
                $resImage,
                $this->fontColor[0],
                $this->fontColor[1],
                $this->fontColor[2]
            );
        }

        for ($i = 0; $i < count($font); $i++) {
            if ($this->option['color']) {
                $this->fontColor = [
                    $this->mtRand(0, 255),
                    $this->mtRand(0, 255),
                    $this->mtRand(0, 255),
                ];

                $resTextColor = imagecolorallocate(
                    $resImage,
                    $this->fontColor[0],
                    $this->fontColor[1],
                    $this->fontColor[2]
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

            imagettftext($resImage,
                $font[$i]['size'],
                $font[$i]['tilt'],
                (int) ($x),
                (int) ($y),
                (int) $resTextColor,
                $font[$i]['font'],
                $code[$i]
            );

            $x += $font[$i]['width'];
        }
    }

    /**
     * 返回字体参数.
     */
    protected function getFontOption(): array
    {
        $code = (string) $this->getCode();
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
     * 创建背景图像.
     *
     * @param resource $resImage
     */
    protected function makeBaseBackground(&$resImage): void
    {
        $width = $this->normalizeWidth();
        $height = $this->normalizeHeight();
        $color = $step = [];

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
    }

    /**
     * 返回验证字体.
     *
     * @throws \InvalidArgumentException
     */
    protected function getTtf(): array
    {
        $fontPath = $this->isChinese($this->getCode() ?: '') ?
            $this->option['chinese_font_path'] :
            $this->option['font_path'];

        if (!is_dir($fontPath)) {
            $e = sprintf('Font path %s is not exits.', $fontPath);

            throw new InvalidArgumentException($e);
        }

        $ttf = glob($fontPath.'/*.*');
        if (empty($ttf)) {
            throw new InvalidArgumentException('Font files not found.');
        }

        return $ttf;
    }

    /**
     * 自动产生验证码.
     *
     * @throws \InvalidArgumentException
     */
    protected function autoCode(int $size, string $autoType = self::ALPHA_UPPERCASE): void
    {
        if ($size < 1) {
            $e = sprintf('Code must be greater than %d.', 0);

            throw new InvalidArgumentException($e);
        }

        if (!in_array($autoType, $this->getAllowedAutoType(), true)) {
            $e = sprintf(
                'Code type must be these %s.',
                implode(',', $this->getAllowedAutoType())
            );

            throw new InvalidArgumentException($e);
        }

        $randMethod = 'Leevel\\Support\\Str\\rand_'.$autoType;
        if (!function_exists($randMethod)) {
            class_exists($randMethod);
        }

        $this->setCode($randMethod($size));
    }

    /**
     * 返回允许自动验证码类型.
     */
    protected function getAllowedAutoType(): array
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
     */
    protected function isChinese(string $code): bool
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $code) > 0;
    }

    /**
     * 生成随机数.
     *
     * @param number $numFirst
     * @param number $numSecond
     */
    protected function mtRand($numFirst, $numSecond): int
    {
        $numFirst = (int) $numFirst;
        $numSecond = (int) $numSecond;
        if ($numFirst > $numSecond) {
            list($numSecond, $numFirst) = [$numFirst, $numSecond];
        }

        return (int) mt_rand($numFirst, $numSecond);
    }
}

// import fn.
class_exists(create_directory::class);

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

namespace Leevel\Filesystem;

use RuntimeException;

/**
 * 图像处理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.19
 *
 * @version 1.0
 */
class Image
{
    /**
     * 创建缩略图.
     *
     * @param string $image
     * @param string $thumbName
     * @param string $type
     * @param number $maxWidth
     * @param number $maxHeight
     * @param bool   $interlace
     * @param bool   $fixed
     * @param number $quality
     *
     * @return mixed
     */
    public static function thumb($image, $thumbName, $type = '', int $maxWidth = 200, int $maxHeight = 50, $interlace = true, $fixed = false, $quality = 100)
    {
        // 获取原图信息
        $info = static::getImageInfo($image);

        if (false !== $info) {
            $srcWidth = $info['width'];
            $srcHeight = $info['height'];

            $type = empty($type) ? $info['type'] : $type;
            $type = strtolower($type);

            $interlace = $interlace ? 1 : 0;
            $scale = min($maxWidth / $srcWidth, $maxHeight / $srcHeight); // 计算缩放比例

            if (true === $fixed) {
                $width = $maxWidth;
                $height = $maxHeight;
            } else {
                // 超过原图大小不再缩略
                if ($scale >= 1) {
                    $width = $srcWidth;
                    $height = $srcHeight;
                } else { // 缩略图尺寸
                    $width = (int) ($srcWidth * $scale);
                    $height = (int) ($srcHeight * $scale);
                }
            }

            $createFun = 'ImageCreateFrom'.('jpg' === $type ? 'jpeg' : $type); // 载入原图
            $srcImg = $createFun($image);

            // 创建缩略图
            if ('gif' !== $type && function_exists('imagecreatetruecolor')) {
                $thumbImg = imagecreatetruecolor($width, $height);
            } else {
                $thumbImg = imagecreate($width, $height);
            }

            // 复制图片
            if (function_exists('ImageCopyResampled')) {
                imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
            } else {
                imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
            }

            if ('gif' === $type || 'png' === $type) {
                imagealphablending($thumbImg, false); // 取消默认的混色模式
                $backgroundColor = imagecolorallocate($thumbImg, 0, 255, 0); // 指派一个绿色
                imagecolortransparent($thumbImg, $backgroundColor); // 设置为透明色，若注释掉该行则输出绿色的图
            }

            // 对jpeg图形设置隔行扫描
            if ('jpg' === $type || 'jpeg' === $type) {
                imageinterlace($thumbImg, $interlace);
            }

            if ('png' === $type) {
                $quality = ceil($quality / 10) - 1;

                if ($quality < 0) {
                    $quality = 0;
                }
            }

            $imageFun = 'image'.('jpg' === $type ? 'jpeg' : $type); // 生成图片
            $imageFun($thumbImg, $thumbName, $quality);
            imagedestroy($thumbImg);
            imagedestroy($srcImg);

            return $thumbName;
        }

        return false;
    }

    /**
     * 预览缩略图.
     *
     * @param string $targetFile
     * @param number $thumbWidth
     * @param number $thumbHeight
     */
    public static function thumbPreview($targetFile, $thumbWidth, $thumbHeight)
    {
        $attachInfo = getimagesize($targetFile);

        list($imgW, $imgH) = $attachInfo;
        header('Content-type:'.$attachInfo['mime']);

        if ($imgW >= $thumbWidth || $imgH >= $thumbHeight) {
            if (function_exists('imagecreatetruecolor') &&
                function_exists('imagecopyresampled') &&
                function_exists('imagejpeg')) {
                switch ($attachInfo['mime']) {
                    case 'image/jpeg':
                        $imageCreateFromFunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
                        $imageFunc = function_exists('imagejpeg') ? 'imagejpeg' : '';

                        break;
                    case 'image/gif':
                        $imageCreateFromFunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
                        $imageFunc = function_exists('imagegif') ? 'imagegif' : '';

                        break;
                    case 'image/png':
                        $imageCreateFromFunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
                        $imageFunc = function_exists('imagepng') ? 'imagepng' : '';

                        break;
                }

                $attachPhoto = $imageCreateFromFunc($targetFile);

                $xRatio = $thumbWidth / $imgW;
                $yRatio = $thumbHeight / $imgH;

                if (($xRatio * $imgH) < $thumbHeight) {
                    $thumb['height'] = ceil($xRatio * $imgH);
                    $thumb['width'] = $thumbWidth;
                } else {
                    $thumb['width'] = ceil($yRatio * $imgW);
                    $thumb['height'] = $thumbHeight;
                }

                $thumbPhoto = imagecreatetruecolor($thumb['width'], $thumb['height']);

                if ('image/jpeg' !== $attachInfo['mime']) {
                    $alpha = imagecolorallocatealpha($thumbPhoto, 0, 0, 0, 127);
                    imagefill($thumbPhoto, 0, 0, $alpha);
                }

                imagecopyresampled($thumbPhoto, $attachPhoto, 0, 0, 0, 0, $thumb['width'], $thumb['height'], $imgW, $imgH);

                if ('image/jpeg' !== $attachInfo['mime']) {
                    imagesavealpha($thumbPhoto, true);
                }

                clearstatcache();

                if ('image/jpeg' === $attachInfo['mime']) {
                    $imageFunc($thumbPhoto, null, 90);
                } else {
                    $imageFunc($thumbPhoto);
                }
            }
        } else {
            readfile($targetFile);
            exit();
        }
    }

    /**
     * 图像加水印.
     *
     * @param string $backgroundPath
     * @param array  $waterArgs
     * @param number $waterPos
     * @param bool   $deleteBackgroupPath
     *
     * @return bool
     */
    public static function imageWaterMark($backgroundPath, array $waterArgs, int $waterPos = 0, $deleteBackgroupPath = true)
    {
        $isWaterImage = false;

        if (!empty($backgroundPath) && is_file($backgroundPath)) { // 读取背景图片
            $backgroundInfo = getimagesize($backgroundPath);
            $groundWidth = $backgroundInfo[0]; // 取得背景图片的宽
            $groundHeight = $backgroundInfo[1]; // 取得背景图片的高

            switch ($backgroundInfo[2]) { // 取得背景图片的格式
                case 1:
                    $backgroundIm = imagecreatefromgif($backgroundPath);

                    break;
                case 2:
                    $backgroundIm = imagecreatefromjpeg($backgroundPath);

                    break;
                case 3:
                    $backgroundIm = imagecreatefrompng($backgroundPath);

                    break;
                default:
                    throw new RuntimeException('Wrong image format.');
            }
        } else {
            throw new RuntimeException(
                sprintf(
                    'The image %s is empty or nonexistent.',
                    $backgroundPath
                )
            );
        }

        imagealphablending($backgroundIm, true); // 设定图像的混色模式

        if (!empty($backgroundPath) && is_file($backgroundPath)) {
            if ('img' === $waterArgs['type'] && !empty($waterArgs['path'])) {
                $isWaterImage = true;
                $set = 0;

                $offset = !empty($waterArgs['offset']) ? $waterArgs['offset'] : 0;

                if (0 === strpos($waterArgs, 'http://localhost/') ||
                    0 === strpos($waterArgs, 'https://localhost/')) { // localhost 转127.0.0.1,否则将会错误
                    $waterArgs['path'] = str_replace('localhost', '127.0.0.1', $waterArgs['path']);
                }

                $waterInfo = getimagesize($waterArgs['path']);
                $waterWidth = $waterInfo[0]; // 取得水印图片的宽
                $waterHeight = $waterInfo[1]; // 取得水印图片的高

                switch ($waterInfo[2]) { // 取得水印图片的格式
                    case 1:
                        $waterIm = imagecreatefromgif($waterArgs['path']);

                        break;
                    case 2:
                        $waterIm = imagecreatefromjpeg($waterArgs['path']);

                        break;
                    case 3:
                        $waterIm = imagecreatefrompng($waterArgs['path']);

                        break;
                    default:
                        throw new RuntimeException('Wrong image format.');
                }
            } elseif ('text' === $waterArgs['type'] && '' !== $waterArgs['content']) {
                $fontfileTemp = $fontfile = $waterArgs['textFile'] ?? 'Microsoft YaHei.ttf';

                $fontfile = (!empty($waterArgs['textPath']) ?
                    str_replace('\\', '/', $waterArgs['textPath']) :
                    'C:\WINDOWS\Fonts').'/'.$fontfile;

                if (!is_file($fontfile)) {
                    throw new RuntimeException(
                        sprintf('The font file %s cannot be found.', $fontfile)
                    );
                }

                $waterText = $waterArgs['content'];
                $set = 1;
                $offset = !empty($waterArgs['offset']) ? $waterArgs['offset'] : 5;
                $textColor = empty($waterArgs['textColor']) ? '#FF0000' : $waterArgs['textColor'];
                $textFont = $waterArgs['textFont'] ?? 20;

                $temp = imagettfbbox(ceil($textFont), 0, $fontfile, $waterText); // 取得使用 TrueType 字体的文本的范围
                $waterWidth = $temp[2] - $temp[6];
                $waterHeight = $temp[3] - $temp[7];
            } else {
                throw new RuntimeException(
                    'The watermark parameter type is not img or text.'
                );
            }
        } else {
            throw new RuntimeException(
                'The watermark parameter must be an array.'
            );
        }

        if (($groundWidth < ($waterWidth * 2)) ||
            ($groundHeight < ($waterHeight * 2))) { // 如果水印占了原图一半就不搞水印了.影响浏览.抵制影响正常浏览的广告
            return true;
        }

        switch ($waterPos) {
            case 1: // 1 为顶端居左
                $posX = $offset * $set;
                $posY = ($waterHeight + $offset) * $set;

                break;
            case 2: // 2 为顶端居中
                $posX = ($groundWidth - $waterWidth) / 2;
                $posY = ($waterHeight + $offset) * $set;

                break;
            case 3: // 3 为顶端居右
                $posX = $groundWidth - $waterWidth - $offset * $set;
                $posY = ($waterHeight + $offset) * $set;

                break;
            case 4: // 4 为中部居左
                $posX = $offset * $set;
                $posY = ($groundHeight - $waterHeight) / 2;

                break;
            case 5: // 5 为中部居中
                $posX = ($groundWidth - $waterWidth) / 2;
                $posY = ($groundHeight - $waterHeight) / 2;

                break;
            case 6: // 6 为中部居右
                $posX = $groundWidth - $waterWidth - $offset * $set;
                $posY = ($groundHeight - $waterHeight) / 2;

                break;
            case 7: // 7 为底端居左
                $posX = $offset * $set;
                $posY = $groundHeight - $waterHeight;

                break;
            case 8: // 8 为底端居中
                $posX = ($groundWidth - $waterWidth) / 2;
                $posY = $groundHeight - $waterHeight;

                break;
            case 9: // 9为底端居右
                $posX = $groundWidth - $waterWidth - $offset * $set;
                $posY = $groundHeight - $waterHeight;

                break;
            default: // 随机
                $posX = rand(0, ($groundWidth - $waterWidth));
                $posY = rand(0, ($groundHeight - $waterHeight));

                break;
        }

        if (true === $isWaterImage) { // 图片水印
            imagealphablending($waterIm, true);
            imagealphablending($backgroundIm, true);
            imagecopy($backgroundIm, $waterIm, $posX, $posY, 0, 0, $waterWidth, $waterHeight); // 拷贝水印到目标文件
        } else { // 文字水印
            if (!empty($textColor) && (7 === strlen($textColor))) {
                $r = hexdec(substr($textColor, 1, 2));
                $g = hexdec(substr($textColor, 3, 2));
                $b = hexdec(substr($textColor, 5));
            } else {
                throw new RuntimeException('Watermark text color error.');
            }

            imagettftext(
                $backgroundIm,
                $textFont,
                0,
                $posX,
                $posY,
                imagecolorallocate($backgroundIm, $r, $g, $b),
                $fontfile,
                $waterText
            );
        }

        if (true === $deleteBackgroupPath) { // 生成水印后的图片
            unlink($backgroundPath);
        }

        switch ($backgroundInfo[2]) { // 取得背景图片的格式
            case 1:
                imagegif($backgroundIm, $backgroundPath);

                break;
            case 2:
                imagejpeg($backgroundIm, $backgroundPath);

                break;
            case 3:
                imagepng($backgroundIm, $backgroundPath);

                break;
            default:
                throw new RuntimeException('Wrong image format.');
        }

        if (isset($waterIm)) {
            imagedestroy($waterIm);
        }

        imagedestroy($backgroundIm);

        return true;
    }

    /**
     * 浏览器输出图像.
     *
     * @param mixed  $image
     * @param string $type
     * @param string $filename
     */
    public static function outputImage($image, $type = 'png', $filename = '')
    {
        header('Content-type: image/'.$type);

        $imageFun = 'image'.$type;

        if (empty($filename)) {
            $imageFun($image);
        } else {
            $imageFun($image, $filename);
        }

        imagedestroy($image);
    }

    /**
     * 读取远程图片.
     *
     * @param string $url
     * @param string $filename
     */
    public static function outerImage($url, $filename)
    {
        if ('' === $url || '' === $filename) {
            return false;
        }

        // 创建文件
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        // 写入文件
        ob_start();

        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();

        $resFp = fopen($filename, 'a');
        fwrite($resFp, $img);
        fclose($resFp);
    }

    /**
     * 计算返回图片改变大小相对尺寸.
     *
     * @param string $imgPath
     * @param number $maxWidth
     * @param number $maxHeight
     *
     * @return array
     */
    public static function returnChangeSize($imgPath, $maxWidth, $maxHeight)
    {
        $size = getimagesize($imgPath);

        $w = $size[0];
        $h = $size[1];

        $wRatio = $maxWidth / $w; // 计算缩放比例
        $hRatio = $maxHeight / $h;

        $result = [];

        if (($w <= $maxWidth) && ($h <= $maxHeight)) { // 决定处理后的图片宽和高
            $result['w'] = $w;
            $result['h'] = $h;
        } elseif (($wRatio * $h) < $maxHeight) {
            $result['h'] = ceil($wRatio * $h);
            $result['w'] = $maxWidth;
        } else {
            $result['w'] = ceil($hRatio * $w);
            $result['h'] = $maxHeight;
        }

        $result['old_w'] = $w;
        $result['old_h'] = $h;

        return $result;
    }

    /**
     * 取得图像信息.
     *
     * @param string $imagesPath
     *
     * @return mixed
     */
    public static function getImageInfo($imagesPath)
    {
        $imageInfo = getimagesize($imagesPath);

        if (false !== $imageInfo) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageSize = filesize($imagesPath);

            return [
                'width'  => $imageInfo[0],
                'height' => $imageInfo[1],
                'type'   => $imageType,
                'size'   => $imageSize,
                'mime'   => $imageInfo['mime'],
            ];
        }

        return false;
    }
}

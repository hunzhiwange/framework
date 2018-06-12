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
     * @param string $sImage
     * @param string $sThumbName
     * @param string $sType
     * @param number $nMaxWidth
     * @param number $nMaxHeight
     * @param bool   $bInterlace
     * @param bool   $bFixed
     * @param number $nQuality
     *
     * @return mixed
     */
    public static function thumb($sImage, $sThumbName, $sType = '', $nMaxWidth = 200, $nMaxHeight = 50, $bInterlace = true, $bFixed = false, $nQuality = 100)
    {
        // 获取原图信息
        $arrInfo = static::getImageInfo($sImage);

        if (false !== $arrInfo) {
            $nSrcWidth = $arrInfo['width'];
            $nSrcHeight = $arrInfo['height'];
            $sType = empty($sType) ? $arrInfo['type'] : $sType;
            $sType = strtolower($sType);
            $bInterlace = $bInterlace ? 1 : 0;
            unset($arrInfo);
            $nScale = min($nMaxWidth / $nSrcWidth, $nMaxHeight / $nSrcHeight); // 计算缩放比例

            if (true === $bFixed) {
                $nWidth = $nMaxWidth;
                $nHeight = $nMaxHeight;
            } else {
                // 超过原图大小不再缩略
                if ($nScale >= 1) {
                    $nWidth = $nSrcWidth;
                    $nHeight = $nSrcHeight;
                } else { // 缩略图尺寸
                    $nWidth = (int) ($nSrcWidth * $nScale);
                    $nHeight = (int) ($nSrcHeight * $nScale);
                }
            }

            $sCreateFun = 'ImageCreateFrom'.('jpg' === $sType ? 'jpeg' : $sType); // 载入原图
            $oSrcImg = $sCreateFun($sImage);

            // 创建缩略图
            if ('gif' !== $sType && function_exists('imagecreatetruecolor')) {
                $oThumbImg = imagecreatetruecolor($nWidth, $nHeight);
            } else {
                $oThumbImg = imagecreate($nWidth, $nHeight);
            }

            // 复制图片
            if (function_exists('ImageCopyResampled')) {
                imagecopyresampled($oThumbImg, $oSrcImg, 0, 0, 0, 0, $nWidth, $nHeight, $nSrcWidth, $nSrcHeight);
            } else {
                imagecopyresized($oThumbImg, $oSrcImg, 0, 0, 0, 0, $nWidth, $nHeight, $nSrcWidth, $nSrcHeight);
            }

            if ('gif' === $sType || 'png' === $sType) {
                imagealphablending($oThumbImg, false); // 取消默认的混色模式
                $oBackgroundColor = imagecolorallocate($oThumbImg, 0, 255, 0); // 指派一个绿色
                imagecolortransparent($oThumbImg, $oBackgroundColor); // 设置为透明色，若注释掉该行则输出绿色的图
            }

            // 对jpeg图形设置隔行扫描
            if ('jpg' === $sType || 'jpeg' === $sType) {
                imageinterlace($oThumbImg, $bInterlace);
            }

            if ('png' === $sType) {
                $nQuality = ceil($nQuality / 10) - 1;
                if ($nQuality < 0) {
                    $nQuality = 0;
                }
            }

            $sImageFun = 'image'.('jpg' === $sType ? 'jpeg' : $sType); // 生成图片
            $sImageFun($oThumbImg, $sThumbName, $nQuality);
            imagedestroy($oThumbImg);
            imagedestroy($oSrcImg);

            return $sThumbName;
        }

        return false;
    }

    /**
     * 预览缩略图.
     *
     * @param string $sTargetFile
     * @param number $nThumbWidth
     * @param number $nThumbHeight
     */
    public static function thumbPreview($sTargetFile, $nThumbWidth, $nThumbHeight)
    {
        $arrAttachInfo = getimagesize($sTargetFile);

        list($nImgW, $nImgH) = $arrAttachInfo;
        header('Content-type:'.$arrAttachInfo['mime']);

        if ($nImgW >= $nThumbWidth || $nImgH >= $nThumbHeight) {
            if (function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled') && function_exists('imagejpeg')) {
                switch ($arrAttachInfo['mime']) {
                    case 'image/jpeg':
                        $sImageCreateFromFunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
                        $sImageFunc = function_exists('imagejpeg') ? 'imagejpeg' : '';

                        break;
                    case 'image/gif':
                        $sImageCreateFromFunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
                        $sImageFunc = function_exists('imagegif') ? 'imagegif' : '';

                        break;
                    case 'image/png':
                        $sImageCreateFromFunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
                        $sImageFunc = function_exists('imagepng') ? 'imagepng' : '';

                        break;
                }

                $oAttachPhoto = $sImageCreateFromFunc($sTargetFile);

                $nXRatio = $nThumbWidth / $nImgW;
                $nYRatio = $nThumbHeight / $nImgH;

                if (($nXRatio * $nImgH) < $nThumbHeight) {
                    $arrThumb['height'] = ceil($nXRatio * $nImgH);
                    $arrThumb['width'] = $nThumbWidth;
                } else {
                    $arrThumb['width'] = ceil($nYRatio * $nImgW);
                    $arrThumb['height'] = $nThumbHeight;
                }

                $oThumbPhoto = imagecreatetruecolor($arrThumb['width'], $arrThumb['height']);
                if ('image/jpeg' !== $arrAttachInfo['mime']) {
                    $oAlpha = imagecolorallocatealpha($oThumbPhoto, 0, 0, 0, 127);
                    imagefill($oThumbPhoto, 0, 0, $oAlpha);
                }

                imagecopyresampled($oThumbPhoto, $oAttachPhoto, 0, 0, 0, 0, $arrThumb['width'], $arrThumb['height'], $nImgW, $nImgH);
                if ('image/jpeg' !== $arrAttachInfo['mime']) {
                    imagesavealpha($oThumbPhoto, true);
                }
                clearstatcache();

                if ('image/jpeg' === $arrAttachInfo['mime']) {
                    $sImageFunc($oThumbPhoto, null, 90);
                } else {
                    $sImageFunc($oThumbPhoto);
                }
            }
        } else {
            readfile($sTargetFile);
            exit();
        }
    }

    /**
     * 图像加水印.
     *
     * @param string $sBackgroundPath
     * @param array  $arrWaterArgs
     * @param number $nWaterPos
     * @param bool   $bDeleteBackgroupPath
     *
     * @return bool
     */
    public static function imageWaterMark($sBackgroundPath, $arrWaterArgs, $nWaterPos = 0, $bDeleteBackgroupPath = true)
    {
        $bIsWaterImage = false;

        if (!empty($sBackgroundPath) && is_file($sBackgroundPath)) { // 读取背景图片
            $arrBackgroundInfo = getimagesize($sBackgroundPath);
            $nGroundWidth = $arrBackgroundInfo[0]; // 取得背景图片的宽
            $nGroundHeight = $arrBackgroundInfo[1]; // 取得背景图片的高
            switch ($arrBackgroundInfo[2]) { // 取得背景图片的格式
                case 1:
                    $oBackgroundIm = imagecreatefromgif($sBackgroundPath);

                    break;
                case 2:
                    $oBackgroundIm = imagecreatefromjpeg($sBackgroundPath);

                    break;
                case 3:
                    $oBackgroundIm = imagecreatefrompng($sBackgroundPath);

                    break;
                default:
                    throw new RuntimeException('Wrong image format.');
            }
        } else {
            throw new RuntimeException(sprintf('The image %s is empty or nonexistent.', $sBackgroundPath));
        }

        imagealphablending($oBackgroundIm, true); // 设定图像的混色模式
        if (!empty($sBackgroundPath) && is_file($sBackgroundPath)) {
            if ('img' === $arrWaterArgs['type'] && !empty($arrWaterArgs['path'])) {
                $bIsWaterImage = true;
                $nSet = 0;

                $nOffset = !empty($arrWaterArgs['offset']) ? $arrWaterArgs['offset'] : 0;
                if (0 === strpos($arrWaterArgs, 'http://localhost/') || 0 === strpos($arrWaterArgs, 'https://localhost/')) { // localhost 转127.0.0.1,否则将会错误
                    $arrWaterArgs['path'] = str_replace('localhost', '127.0.0.1', $arrWaterArgs['path']);
                }

                $arrWaterInfo = getimagesize($arrWaterArgs['path']);
                $nWaterWidth = $arrWaterInfo[0]; // 取得水印图片的宽
                $nWaterHeight = $arrWaterInfo[1]; // 取得水印图片的高
                switch ($arrWaterInfo[2]) { // 取得水印图片的格式
                    case 1:
                        $oWaterIm = imagecreatefromgif($arrWaterArgs['path']);

                        break;
                    case 2:
                        $oWaterIm = imagecreatefromjpeg($arrWaterArgs['path']);

                        break;
                    case 3:
                        $oWaterIm = imagecreatefrompng($arrWaterArgs['path']);

                        break;
                    default:
                        throw new RuntimeException('Wrong image format.');
                }
            } elseif ('text' === $arrWaterArgs['type'] && '' !== $arrWaterArgs['content']) {
                $sFontfileTemp = $sFontfile = $arrWaterArgs['textFile'] ?? 'Microsoft YaHei.ttf';
                $sFontfile = (!empty($arrWaterArgs['textPath']) ? str_replace('\\', '/', $arrWaterArgs['textPath']) : 'C:\WINDOWS\Fonts').'/'.$sFontfile;
                if (!is_file($sFontfile)) {
                    throw new RuntimeException(sprintf('The font file %s cannot be found.', $sFontfile));
                }

                $sWaterText = $arrWaterArgs['content'];
                $nSet = 1;
                $nOffset = !empty($arrWaterArgs['offset']) ? $arrWaterArgs['offset'] : 5;
                $sTextColor = empty($arrWaterArgs['textColor']) ? '#FF0000' : $arrWaterArgs['textColor'];
                $nTextFont = $arrWaterArgs['textFont'] ?? 20;
                $arrTemp = imagettfbbox(ceil($nTextFont), 0, $sFontfile, $sWaterText); // 取得使用 TrueType 字体的文本的范围
                $nWaterWidth = $arrTemp[2] - $arrTemp[6];
                $nWaterHeight = $arrTemp[3] - $arrTemp[7];
                unset($arrTemp);
            } else {
                throw new RuntimeException('The watermark parameter type is not img or text.');
            }
        } else {
            throw new RuntimeException('The watermark parameter must be an array.');
        }

        if (($nGroundWidth < ($nWaterWidth * 2)) || ($nGroundHeight < ($nWaterHeight * 2))) { // 如果水印占了原图一半就不搞水印了.影响浏览.抵制影响正常浏览的广告
            return true;
        }

        switch ($nWaterPos) {
            case 1: // 1 为顶端居左
                $nPosX = $nOffset * $nSet;
                $nPosY = ($nWaterHeight + $nOffset) * $nSet;

                break;
            case 2: // 2 为顶端居中
                $nPosX = ($nGroundWidth - $nWaterWidth) / 2;
                $nPosY = ($nWaterHeight + $nOffset) * $nSet;

                break;
            case 3: // 3 为顶端居右
                $nPosX = $nGroundWidth - $nWaterWidth - $nOffset * $nSet;
                $nPosY = ($nWaterHeight + $nOffset) * $nSet;

                break;
            case 4: // 4 为中部居左
                $nPosX = $nOffset * $nSet;
                $nPosY = ($nGroundHeight - $nWaterHeight) / 2;

                break;
            case 5: // 5 为中部居中
                $nPosX = ($nGroundWidth - $nWaterWidth) / 2;
                $nPosY = ($nGroundHeight - $nWaterHeight) / 2;

                break;
            case 6: // 6 为中部居右
                $nPosX = $nGroundWidth - $nWaterWidth - $nOffset * $nSet;
                $nPosY = ($nGroundHeight - $nWaterHeight) / 2;

                break;
            case 7: // 7 为底端居左
                $nPosX = $nOffset * $nSet;
                $nPosY = $nGroundHeight - $nWaterHeight;

                break;
            case 8: // 8 为底端居中
                $nPosX = ($nGroundWidth - $nWaterWidth) / 2;
                $nPosY = $nGroundHeight - $nWaterHeight;

                break;
            case 9: // 9为底端居右
                $nPosX = $nGroundWidth - $nWaterWidth - $nOffset * $nSet;
                $nPosY = $nGroundHeight - $nWaterHeight;

                break;
            default: // 随机
                $nPosX = rand(0, ($nGroundWidth - $nWaterWidth));
                $nPosY = rand(0, ($nGroundHeight - $nWaterHeight));

                break;
        }

        if (true === $bIsWaterImage) { // 图片水印
            imagealphablending($oWaterIm, true);
            imagealphablending($oBackgroundIm, true);
            imagecopy($oBackgroundIm, $oWaterIm, $nPosX, $nPosY, 0, 0, $nWaterWidth, $nWaterHeight); // 拷贝水印到目标文件
        } else { // 文字水印
            if (!empty($sTextColor) && (7 === strlen($sTextColor))) {
                $R = hexdec(substr($sTextColor, 1, 2));
                $G = hexdec(substr($sTextColor, 3, 2));
                $B = hexdec(substr($sTextColor, 5));
            } else {
                throw new RuntimeException('Watermark text color error.');
            }
            imagettftext($oBackgroundIm, $nTextFont, 0, $nPosX, $nPosY, imagecolorallocate($oBackgroundIm, $R, $G, $B), $sFontfile, $sWaterText);
        }

        if (true === $bDeleteBackgroupPath) { // 生成水印后的图片
            unlink($sBackgroundPath);
        }

        switch ($arrBackgroundInfo[2]) { // 取得背景图片的格式
            case 1:
                imagegif($oBackgroundIm, $sBackgroundPath);

                break;
            case 2:
                imagejpeg($oBackgroundIm, $sBackgroundPath);

                break;
            case 3:
                imagepng($oBackgroundIm, $sBackgroundPath);

                break;
            default:
                throw new RuntimeException('Wrong image format.');
        }

        if (isset($oWaterIm)) {
            imagedestroy($oWaterIm);
        }
        imagedestroy($oBackgroundIm);

        return true;
    }

    /**
     * 浏览器输出图像.
     *
     * @param unknown $oImage
     * @param string  $sType
     * @param string  $sFilename
     */
    public static function outputImage($oImage, $sType = 'png', $sFilename = '')
    {
        header('Content-type: image/'.$sType);

        $sImageFun = 'image'.$sType;
        if (empty($sFilename)) {
            $sImageFun($oImage);
        } else {
            $sImageFun($oImage, $sFilename);
        }

        imagedestroy($oImage);
    }

    /**
     * 读取远程图片.
     *
     * @param string $sUrl
     * @param string $sFilename
     */
    public static function outerImage($sUrl, $sFilename)
    {
        if ('' === $sUrl || '' === $sFilename) {
            return false;
        }

        // 创建文件
        if (!is_dir(dirname($sFilename))) {
            mkdir(dirname($sFilename), 0777, true);
        }

        // 写入文件
        ob_start();
        readfile($sUrl);
        $sImg = ob_get_contents();
        ob_end_clean();
        $resFp = fopen($sFilename, 'a');
        fwrite($resFp, $sImg);
        fclose($resFp);
    }

    /**
     * 计算返回图片改变大小相对尺寸.
     *
     * @param string $sImgPath
     * @param number $nMaxWidth
     * @param number $nMaxHeight
     *
     * @return array
     */
    public static function returnChangeSize($sImgPath, $nMaxWidth, $nMaxHeight)
    {
        $arrSize = getimagesize($sImgPath);

        $nW = $arrSize[0];
        $nH = $arrSize[1];

        $nWRatio = $nMaxWidth / $nW; // 计算缩放比例
        $nHRatio = $nMaxHeight / $nH;

        $arrReturn = [];

        if (($nW <= $nMaxWidth) && ($nH <= $nMaxHeight)) { // 决定处理后的图片宽和高
            $arrReturn['w'] = $nW;
            $arrReturn['h'] = $nH;
        } elseif (($nWRatio * $nH) < $nMaxHeight) {
            $arrReturn['h'] = ceil($nWRatio * $nH);
            $arrReturn['w'] = $nMaxWidth;
        } else {
            $arrReturn['w'] = ceil($nHRatio * $nW);
            $arrReturn['h'] = $nMaxHeight;
        }

        $arrReturn['old_w'] = $nW;
        $arrReturn['old_h'] = $nH;

        return $arrReturn;
    }

    /**
     * 取得图像信息.
     *
     * @param string $sImagesPath
     *
     * @return mixed
     */
    public static function getImageInfo($sImagesPath)
    {
        $arrImageInfo = getimagesize($sImagesPath);

        if (false !== $arrImageInfo) {
            $sImageType = strtolower(substr(image_type_to_extension($arrImageInfo[2]), 1));
            $nImageSize = filesize($sImagesPath);

            return [
                'width'  => $arrImageInfo[0],
                'height' => $arrImageInfo[1],
                'type'   => $sImageType,
                'size'   => $nImageSize,
                'mime'   => $arrImageInfo['mime'],
            ];
        }

        return false;
    }
}

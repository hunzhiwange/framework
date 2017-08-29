<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\seccode;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use Exception;
use queryyetsimple\classs\option;
use queryyetsimple\string\string;
use queryyetsimple\filesystem\fso;
use queryyetsimple\seccode\interfaces\seccode as interfaces_seccode;

/**
 * 验证码
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.27
 * @version 1.0
 */
class seccode implements interfaces_seccode {
    
    use option;
    
    /**
     * 验证码
     *
     * @var str
     */
    protected $strCode;
    
    /**
     * 宽度
     *
     * @var int
     */
    protected $intResolvedWidth;
    
    /**
     * 高度
     *
     * @var int
     */
    protected $intResolvedHeight;
    
    /**
     * 字体路径
     *
     * @var string
     */
    protected $strResolvedFontPath;
    
    /**
     * 中文字体路径
     *
     * @var string
     */
    protected $strResolvedChineseFontPath;
    
    /**
     * 背景路径
     *
     * @var string
     */
    protected $strResolvedBackgroundPath;
    
    /**
     * 字体颜色
     *
     * @var array
     */
    protected $arrFontColor = [ ];
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
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
            'background' => true 
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct(array $arrOption = []) {
        $this->options ( $arrOption );
    }
    
    /**
     * 设置验证码
     *
     * @param mixed $mixCode            
     * @param string $strAutoType            
     * @param boolean $booAutoCode            
     * @return $this
     */
    public function display($mixCode = null, $strAutoType = self::ALPHA_UPPERCASE, $booAutoCode = true) {
        if (is_int ( $mixCode ) && $booAutoCode) {
            $this->autoCode ( $mixCode, $strAutoType );
        } else {
            $strCode && $this->code ( $strCode );
        }
        
        $resFoo = imagecreatefromstring ( $this->makeBackground () );
        
        if ($this->getOption ( 'adulterate' )) {
            $this->makeAdulterate ( $resFoo );
        }
        
        $this->makeTtfFont ( $resFoo );
        
        if (function_exists ( 'imagepng' )) {
            header ( 'Content-type: image/png' );
            imagepng ( $resFoo );
        } else {
            header ( 'Content-type: image/jpeg' );
            imagejpeg ( $resFoo, '', 100 );
        }
        
        imagedestroy ( $resFoo );
    }
    
    /**
     * 设置验证码
     *
     * @param string $strCode            
     * @return $this
     */
    public function code($strCode) {
        $this->strCode = $strCode;
        return $this;
    }
    
    /**
     * 返回验证码
     *
     * @return $this
     */
    public function getCode() {
        return $this->strCode;
    }
    
    /**
     * 返回宽度
     *
     * @return int
     */
    public function getWidth() {
        if (! is_null ( $this->intResolvedWidth ))
            return $this->intResolvedWidth;
        
        if ($this->getOption ( 'width' ) < static::MIN_WIDTH) {
            $this->option ( 'width', static::MIN_WIDTH );
        } elseif ($this->getOption ( 'width' ) > static::MAX_WIDTH) {
            $this->option ( 'width', static::MAX_WIDTH );
        }
        
        return $this->intResolvedWidth = $this->getOption ( 'width' );
    }
    
    /**
     * 返回高度
     *
     * @return int
     */
    public function getHeight() {
        if (! is_null ( $this->intResolvedHeight ))
            return $this->intResolvedHeight;
        
        if ($this->getOption ( 'height' ) < static::MIN_HEIGHT) {
            $this->option ( 'height', static::MIN_HEIGHT );
        } elseif ($this->getOption ( 'height' ) > static::MAX_HEIGHT) {
            $this->option ( 'height', static::MAX_HEIGHT );
        }
        
        return $this->intResolvedHeight = $this->getOption ( 'height' );
    }
    
    /**
     * 返回英文字体路径
     *
     * @return string
     */
    public function getFontPath() {
        if (! is_null ( $this->strResolvedFontPath ))
            return $this->strResolvedFontPath;
        
        return $this->strResolvedFontPath = $this->getOption ( 'font_path' ) ?  : $this->getDefaultFontPath ();
    }
    
    /**
     * 返回中文字体路径
     *
     * @return string
     */
    public function getChineseFontPath() {
        if (! is_null ( $this->strResolvedChineseFontPath ))
            return $this->strResolvedChineseFontPath;
        
        return $this->strResolvedChineseFontPath = $this->getOption ( 'chinese_font_path' ) ?  : $this->getDefaultChineseFontPath ();
    }
    
    /**
     * 返回背景图路径
     *
     * @return string
     */
    public function getBackgroundPath() {
        if (! is_null ( $this->strResolvedBackgroundPath ))
            return $this->strResolvedBackgroundPath;
        
        return $this->strResolvedBackgroundPath = $this->getOption ( 'background_path' ) ?  : $this->getDefaultBackgroundPath ();
    }
    
    /**
     * 创建背景图像
     *
     * @return string
     */
    protected function makeBackground() {
        $resFoo = imagecreatetruecolor ( $this->getWidth (), $this->getHeight () );
        $resColor = imagecolorallocate ( $resFoo, 255, 255, 255 );
        
        if ($this->makeBackgroundWithImage ( $resFoo ) === false) {
            $this->makeBackgroundDefault ( $resFoo );
        }
        
        ob_start ();
        if (function_exists ( 'imagepng' )) {
            imagepng ( $resFoo );
        } else {
            imagejpeg ( $resFoo, '', 100 );
        }
        
        imagedestroy ( $resFoo );
        $strBackground = ob_get_contents ();
        ob_end_clean ();
        
        return $strBackground;
    }
    
    /**
     * 创建随机背景图形
     *
     * @param resource $resFoo            
     * @return void
     */
    protected function makeAdulterate(&$resFoo) {
        $intLineNum = $this->getHeight () / 10;
        if ($intLineNum < 1) {
            return;
        }
        
        for($int = 0; $int <= $intLineNum; $int ++) {
            $resColor = $this->getOption ( 'color' ) ? imagecolorallocate ( $resFoo, mt_rand ( 0, 255 ), mt_rand ( 0, 255 ), mt_rand ( 0, 255 ) ) : imagecolorallocate ( $resFoo, $this->arrFontColor [0], $this->arrFontColor [1], $this->arrFontColor [2] );
            
            $intX = $this->mtRand ( 0, $this->getWidth () );
            $intY = $this->mtRand ( 0, $this->getHeight () );
            if (mt_rand ( 0, 1 )) {
                imagearc ( $resFoo, $intX, $intY, $this->mtRand ( 0, $this->getWidth () ), $this->mtRand ( 0, $this->getHeight () ), mt_rand ( 0, 360 ), mt_rand ( 0, 360 ), $resColor );
            } else {
                imageline ( $resFoo, $intX, $intY, 0 + mt_rand ( 0, 0 ), 0 + $this->mtRand ( 0, $this->mtRand ( $this->getHeight (), $this->getWidth () ) ), $resColor );
            }
        }
    }
    
    /**
     * 创建字体信息
     *
     * @param resource $resFoo            
     * @return void
     */
    protected function makeTtfFont(&$resFoo) {
        if (! function_exists ( 'imagettftext' )) {
            throw new Exception ( 'Function imagettftext is not exits' );
        }
        
        list ( $arrFont, $strCode, $intWidthTotal ) = $this->getFontOption ();
        
        // deg2rad() 函数将角度转换为弧度 cos 是 cosine 的简写
        // 表示余弦函数
        $intX = $this->mtRand ( $arrFont [0] ['tilt'] > 0 ? cos ( deg2rad ( 90 - $arrFont [0] ['tilt'] ) ) * $arrFont [0] ['zheight'] : 1, $this->getWidth () - $intWidthTotal );
        
        // 是否启用随机颜色
        ! $this->getOption ( 'color' ) && $resTextColor = imagecolorallocate ( $resFoo, $this->arrFontColor [0], $this->arrFontColor [1], $this->arrFontColor [2] );
        
        for($int = 0; $int < count ( $arrFont ); $int ++) {
            if ($this->getOption ( 'color' )) {
                $this->arrFontColor = [ 
                        mt_rand ( 0, 255 ),
                        mt_rand ( 0, 255 ),
                        mt_rand ( 0, 255 ) 
                ];
                $this->getOption ( 'shadow' ) && $resTextShadowColor = imagecolorallocate ( $resFoo, 255 - $this->arrFontColor [0], 255 - $this->arrFontColor [1], 255 - $this->arrFontColor [2] );
                $resTextColor = imagecolorallocate ( $resFoo, $this->arrFontColor [0], $this->arrFontColor [1], $this->arrFontColor [2] );
            } elseif ($this->getOption ( 'shadow' )) {
                $resTextShadowColor = imagecolorallocate ( $resFoo, 255 - $this->arrFontColor [0], 255 - $this->arrFontColor [1], 255 - $this->arrFontColor [2] );
            }
            
            $intY = $arrFont [0] ['tilt'] > 0 ? $this->mtRand ( $arrFont [$int] ['height'], $this->getHeight () ) : $this->mtRand ( $arrFont [$int] ['height'] - $arrFont [$int] ['hd'], $this->getHeight () - $arrFont [$int] ['hd'] );
            
            $this->getOption ( 'shadow' ) && imagettftext ( $resFoo, $arrFont [$int] ['size'], $arrFont [$int] ['tilt'], $intX + 1, $intY + 1, $resTextShadowColor, $arrFont [$int] ['font'], $strCode {$int} );
            imagettftext ( $resFoo, $arrFont [$int] ['size'], $arrFont [$int] ['tilt'], $intX, $intY, $resTextColor, $arrFont [$int] ['font'], $strCode {$int} );
            $intX += $arrFont [$int] ['width'];
        }
    }
    
    /**
     * 返回字体参数
     *
     * @return array
     */
    protected function getFontOption() {
        $strCode = $this->getCode ();
        $arrTtf = $this->getTtf ();
        
        if ($this->isChinese ( $strCode )) {
            $strCode = str_split ( $strCode, 3 );
            $intCodeLength = count ( $strCode );
        } else {
            $intCodeLength = strlen ( $strCode );
        }
        
        $arrFont = [ ];
        $intWidthTotal = 0;
        for($int = 0; $int < $intCodeLength; $int ++) {
            if (! isset ( $arrFont [$int] )) {
                $arrFont [$int] = [ ];
            }
            
            $arrFont [$int] ['font'] = $arrTtf [array_rand ( $arrTtf )];
            $arrFont [$int] ['tilt'] = $this->getOption ( 'tilt' ) ? mt_rand ( - 30, 30 ) : 0;
            $arrFont [$int] ['size'] = $this->getWidth () / 6;
            
            $this->getOption ( 'size' ) and $arrFont [$int] ['size'] = $this->mtRand ( $arrFont [$int] ['size'] - $this->getWidth () / 40, $arrFont [$int] ['size'] + $this->getWidth () / 20 );
            
            $resBox = imagettfbbox ( $arrFont [$int] ['size'], 0, $arrFont [$int] ['font'], $strCode {$int} );
            
            $arrFont [$int] ['zheight'] = max ( $resBox [1], $resBox [3] ) - min ( $resBox [5], $resBox [7] );
            
            $resBox = imagettfbbox ( $arrFont [$int] ['size'], $arrFont [$int] ['tilt'], $arrFont [$int] ['font'], $strCode {$int} );
            
            $arrFont [$int] ['height'] = max ( $resBox [1], $resBox [3] ) - min ( $resBox [5], $resBox [7] );
            
            $arrFont [$int] ['hd'] = $arrFont [$int] ['height'] - $arrFont [$int] ['zheight'];
            
            $arrFont [$int] ['width'] = (max ( $resBox [2], $resBox [4] ) - min ( $resBox [0], $resBox [6] )) + mt_rand ( 0, $this->getWidth () / 8 );
            $arrFont [$int] ['width'] = $arrFont [$int] ['width'] > $this->getWidth () / $intCodeLength ? $this->getWidth () / $intCodeLength : $arrFont [$int] ['width'];
            $intWidthTotal += $arrFont [$int] ['width'];
        }
        
        return [ 
                $arrFont,
                $strCode,
                $intWidthTotal 
        ];
    }
    
    /**
     * 创建图片背景图像
     *
     * @param resource $resFoo            
     * @return boolean
     */
    protected function makeBackgroundWithImage(&$resFoo) {
        $booBackground = false;
        if ($this->getOption ( 'background' ) && function_exists ( 'imagecreatefromjpeg' ) && function_exists ( 'imagecolorat' ) && function_exists ( 'imagecopymerge' ) && function_exists ( 'imagesetpixel' ) && function_exists ( 'imageSX' ) && function_exists ( 'imageSY' )) {
            if (! is_dir ( $this->getBackgroundPath () )) {
                throw new Exception ( sprintf ( 'Background path %s is not exists.', $this->getBackgroundPath () ) );
            }
            
            $arrBackground = fso::lists ( $this->getBackgroundPath (), 'file', true );
            
            if ($arrBackground) {
                $resBackground = imagecreatefromjpeg ( $arrBackground [array_rand ( $arrBackground )] );
                $resColorIndex = imagecolorat ( $resBackground, 0, 0 );
                $arrColor = imagecolorsforindex ( $resBackground, $resColorIndex );
                $resColorIndex = imagecolorat ( $resBackground, 1, 0 );
                imagesetpixel ( $resBackground, 0, 0, $resColorIndex );
                
                $arrColor [0] = $arrColor ['red'];
                $arrColor [1] = $arrColor ['green'];
                $arrColor [2] = $arrColor ['blue'];
                
                imagecopymerge ( $resFoo, $resBackground, 0, 0, $this->mtRand ( 0, 200 - $this->getWidth () ), $this->mtRand ( 0, 80 - $this->getHeight () ), imageSX ( $resBackground ), imageSY ( $resBackground ), 100 );
                imagedestroy ( $resBackground );
                
                $booBackground = true;
                $this->arrFontColor = $arrColor;
            }
        }
        
        return $booBackground;
    }
    
    /**
     * 创建默认背景图像
     *
     * @param resource $resFoo            
     * @return void
     */
    protected function makeBackgroundDefault(&$resFoo) {
        for($int = 0; $int < 3; $int ++) {
            $arrStart [$int] = mt_rand ( 200, 255 );
            $arrEnd [$int] = mt_rand ( 100, 150 );
            $arrStep [$int] = ($arrEnd [$int] - $arrStart [$int]) / $this->getWidth ();
            $arrColor [$int] = $arrStart [$int];
        }
        
        for($int = 0; $int < $this->getWidth (); $int ++) {
            $resColor = imagecolorallocate ( $resFoo, $arrColor [0], $arrColor [1], $arrColor [2] );
            imageline ( $resFoo, $int, 0, $int - ($this->getOption ( 'tilt' ) ? mt_rand ( - 30, 30 ) : 0), $this->getHeight (), $resColor );
            $arrColor [0] += $arrStep [0];
            $arrColor [1] += $arrStep [1];
            $arrColor [2] += $arrStep [2];
        }
        
        $arrColor [0] -= 20;
        $arrColor [1] -= 20;
        $arrColor [2] -= 20;
        $this->arrFontColor = $arrColor;
        unset ( $arrColor );
    }
    
    /**
     * 返回验证字体
     *
     * @return array
     */
    protected function getTtf() {
        $strFontPath = $this->isChinese ( $this->getCode () ) ? $this->getChineseFontPath () : $this->getFontPath ();
        if (! is_dir ( $strFontPath )) {
            throw new Exception ( sprintf ( 'Font path %s is not exits', $strFontPath ) );
        }
        
        $arrTtf = fso::lists ( $strFontPath, 'file', true );
        if (empty ( $arrTtf )) {
            throw new Exception ( 'Font not found' );
        }
        
        return $arrTtf;
    }
    
    /**
     * 自动产生验证码
     *
     * @param int $intSize            
     * @param string $strAutoType            
     * @return boolean
     */
    protected function autoCode($intSize, $strAutoType = self::ALPHA_UPPERCASE) {
        if ($intSize < 1)
            throw new Exception ( sprintf ( 'Code must be greater than %d', 0 ) );
        
        if (! in_array ( $strAutoType, $this->getAllowedAutoType () ))
            throw new Exception ( sprintf ( 'Code type must be these %s', implode ( ',', $this->getAllowedAutoType () ) ) );
        $this->code ( string::{'rand' . ucwords ( string::camelize ( $strAutoType ) )} ( $intSize ) );
    }
    
    /**
     * 返回允许自动验证码类型
     *
     * @return array
     */
    protected function getAllowedAutoType() {
        return [ 
                static::ALPHA_NUM,
                static::ALPHA_NUM_LOWERCASE,
                static::ALPHA_NUM_UPPERCASE,
                static::ALPHA,
                static::ALPHA_LOWERCASE,
                static::ALPHA_UPPERCASE,
                static::NUM,
                static::CHINESE 
        ];
    }
    
    /**
     * 是否为中文
     *
     * @param string $strCode            
     * @return boolean
     */
    protected function isChinese($strCode) {
        return preg_match ( '/^[\x{4e00}-\x{9fa5}]+$/u', $strCode );
    }
    
    /**
     * 文件后缀
     *
     * @param string $sFileName            
     * @return string
     */
    protected function ext($sFileName) {
        return trim ( substr ( strrchr ( $sFileName, '.' ), 1, 10 ) );
    }
    
    /**
     * 返回英文字体路径
     *
     * @return string
     */
    protected function getDefaultFontPath() {
        return __DIR__ . '/font';
    }
    
    /**
     * 返回中文字体路径
     *
     * @return string
     */
    protected function getDefaultChineseFontPath() {
        return '';
    }
    
    /**
     * 返回背景图路径
     *
     * @return string
     */
    protected function getDefaultBackgroundPath() {
        return __DIR__ . '/background';
    }
    
    /**
     * 生成随机数
     *
     * @param int $numFoo            
     * @param int $numBar            
     * @return number
     */
    protected function mtRand($numFoo, $numBar) {
        if ($numFoo > $numBar) {
            $intTemp = $numBar;
            $numBar = $numFoo;
            $numFoo = $intTemp;
            unset ( $intTemp );
        }
        return mt_rand ( $numFoo, $numBar );
    }
}

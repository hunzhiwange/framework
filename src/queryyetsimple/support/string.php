<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\string;

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

use queryyetsimple\classs\infinity as infinity;

/**
 * 字符串
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.05
 * @version 1.0
 */
class string {
    
    use infinity;
    
    /**
     * 随机字母数字
     *
     * @param int $nLength            
     * @param string $sCharBox            
     * @return string
     */
    public static function randAlphaNum($nLength, $sCharBox = null) {
        if (! $nLength)
            return false;
        if (is_null ( $sCharBox )) {
            $sCharBox = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        }
        return static::randSting ( $nLength, $sCharBox );
    }
    
    /**
     * 随机小写字母数字
     *
     * @param int $nLength            
     * @param string $sCharBox            
     * @return string
     */
    public static function randAlphaNumLowercase($nLength, $sCharBox = null) {
        if (! $nLength)
            return false;
        if (is_null ( $sCharBox )) {
            $sCharBox = 'abcdefghijklmnopqrstuvwxyz1234567890';
        }
        return static::randSting ( $nLength, $sCharBox );
    }
    
    /**
     * 随机大写字母数字
     *
     * @param int $nLength            
     * @param string $sCharBox            
     * @return string
     */
    public static function randAlphaNumUppercase($nLength, $sCharBox = null) {
        if (! $nLength)
            return false;
        if (is_null ( $sCharBox )) {
            $sCharBox = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        }
        return static::randSting ( $nLength, $sCharBox );
    }
    
    /**
     * 随机字母
     *
     * @param int $nLength            
     * @param string $sCharBox            
     * @return string
     */
    public static function randAlpha($nLength, $sCharBox = null) {
        if (! $nLength)
            return false;
        if (is_null ( $sCharBox )) {
            $sCharBox = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        return static::randSting ( $nLength, $sCharBox );
    }
    
    /**
     * 随机小写字母
     *
     * @param int $nLength            
     * @param string $sCharBox            
     * @return string
     */
    public static function randAlphaLowercase($nLength, $sCharBox = null) {
        if (! $nLength)
            return false;
        if (is_null ( $sCharBox )) {
            $sCharBox = 'abcdefghijklmnopqrstuvwxyz';
        } else {
            $sCharBox = strtolower ( $sCharBox );
        }
        return static::randSting ( $nLength, $sCharBox );
    }
    
    /**
     * 随机大写字母
     *
     * @param int $nLength            
     * @param string $sCharBox            
     * @return string
     */
    public static function randAlphaUppercase($nLength, $sCharBox = null) {
        if (! $nLength)
            return false;
        if (is_null ( $sCharBox )) {
            $sCharBox = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else {
            $sCharBox = strtoupper ( $sCharBox );
        }
        return static::randSting ( $nLength, $sCharBox );
    }
    
    /**
     * 随机数字
     *
     * @param int $nLength            
     * @param string $sCharBox            
     * @return string
     */
    public static function randNum($nLength, $sCharBox = null) {
        if (! $nLength)
            return false;
        if (is_null ( $sCharBox )) {
            $sCharBox = '0123456789';
        }
        return static::randSting ( $nLength, $sCharBox );
    }
    
    /**
     * 随机字中文
     *
     * @param int $nLength            
     * @param string $sCharBox            
     * @return string
     */
    public static function randChinese($nLength, $sCharBox = null) {
        if (! $nLength)
            return false;
        
        if (is_null ( $sCharBox )) {
            $sCharBox = '在了不和有大这主中人上为们地个用工时要动国产以我到他会作来分生对于学下级就年阶义发成部民可出能方进同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批如应形想制心样干都向变关点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九您取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培着河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑的一是';
        }
        
        $sRet = '';
        for($int = 0; $int < $nLength; $int ++) {
            $sRet .= static::subString ( $sCharBox, floor ( mt_rand ( 0, mb_strlen ( $sCharBox, 'utf-8' ) - 1 ) ), 1 );
        }
        unset ( $sCharBox );
        return $sRet;
    }
    
    /**
     * 随机字符串
     *
     * @param int $nLength            
     * @param string $sCharBox            
     * @return string
     */
    public static function randSting($nLength, $sCharBox) {
        if (! $nLength || ! $sCharBox)
            return false;
        return substr ( str_shuffle ( str_repeat ( $sCharBox, round ( $nLength / count ( $sCharBox ) ) ) ), 0, $nLength );
    }
    
    /**
     * 字符串编码转换
     *
     * @param mixed $mixContents            
     * @param string $sFromChar            
     * @param string $sToChar            
     * @return mixed
     */
    public static function stringEncoding($mixContents, $sFromChar, $sToChar = 'utf-8') {
        if (empty ( $mixContents )) {
            return $mixContents;
        }
        
        $sFromChar = strtolower ( $sFromChar ) == 'utf8' ? 'utf-8' : strtolower ( $sFromChar );
        $sToChar = strtolower ( $sToChar ) == 'utf8' ? 'utf-8' : strtolower ( $sToChar );
        if ($sFromChar == $sToChar || (is_scalar ( $mixContents ) && ! is_string ( $mixContents ))) {
            return $mixContents;
        }
        
        if (is_string ( $mixContents )) {
            if (function_exists ( 'iconv' )) {
                return iconv ( $sFromChar, $sToChar . '//IGNORE', $mixContents );
            } elseif (function_exists ( 'mb_convert_encoding' )) {
                return mb_convert_encoding ( $mixContents, $sToChar, $sFromChar );
            } else {
                return $mixContents;
            }
        } elseif (is_array ( $mixContents )) {
            foreach ( $mixContents as $sKey => $sVal ) {
                $sKeyTwo = static::gbkToUtf8 ( $sKey, $sFromChar, $sToChar );
                $mixContents [$sKeyTwo] = static::stringEncoding ( $sVal, $sFromChar, $sToChar );
                if ($sKey != $sKeyTwo) {
                    unset ( $mixContents [$sKeyTwo] );
                }
            }
            return $mixContents;
        } else {
            return $mixContents;
        }
    }
    
    /**
     * 判断字符串是否为 UTF8
     *
     * @param string $sString            
     * @return boolean
     */
    public static function isUtf8($sString) {
        $nLength = strlen ( $sString );
        
        for($nI = 0; $nI < $nLength; $nI ++) {
            if (ord ( $sString [$nI] ) < 0x80) {
                $nN = 0;
            } elseif ((ord ( $sString [$nI] ) & 0xE0) == 0xC0) {
                $nN = 1;
            } elseif ((ord ( $sString [$nI] ) & 0xF0) == 0xE0) {
                $nN = 2;
            } elseif ((ord ( $sString [$nI] ) & 0xF0) == 0xF0) {
                $nN = 3;
            } else {
                return false;
            }
            
            for($nJ = 0; $nJ < $nN; $nJ ++) {
                if ((++ $nI == $nLength) || ((ord ( $sString [$nI] ) & 0xC0) != 0x80)) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * 字符串截取
     *
     * @param string $sStr            
     * @param number $nStart            
     * @param number $nLength            
     * @param string $sCharset            
     * @param boolean $bSuffix            
     * @return string
     */
    public static function subString($sStr, $nStart = 0, $nLength = 255, $sCharset = "utf-8", $bSuffix = true) {
        // 对系统的字符串函数进行判断
        if (function_exists ( "mb_substr" )) {
            return mb_substr ( $sStr, $nStart, $nLength, $sCharset );
        } elseif (function_exists ( 'iconv_substr' )) {
            return iconv_substr ( $sStr, $nStart, $nLength, $sCharset );
        }
        
        // 常用几种字符串正则表达式
        $arrRe ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $arrRe ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $arrRe ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $arrRe ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        
        // 匹配
        preg_match_all ( $arrRe [$sCharset], $sStr, $arrMatch );
        $sSlice = join ( "", array_slice ( $arrMatch [0], $nStart, $nLength ) );
        
        if ($bSuffix) {
            return $sSlice . "…";
        }
        
        return $sSlice;
    }
    
    /**
     * 日期格式化
     *
     * @param int $nDateTemp            
     * @param string $sDateFormat            
     * @return string
     */
    public static function formatDate($nDateTemp, $sDateFormat = 'Y-m-d H:i') {
        $sReturn = '';
        
        $nSec = time () - $nDateTemp;
        $nHover = floor ( $nSec / 3600 );
        if ($nHover == 0) {
            $nMin = floor ( $nSec / 60 );
            if ($nMin == 0) {
                $sReturn = $nSec . ' ' . __ ( "秒前" );
            } else {
                $sReturn = $nMin . ' ' . __ ( "分钟前" );
            }
        } elseif ($nHover < 24) {
            $sReturn = __ ( "大约 %d 小时前", $nHover );
        } else {
            $sReturn = date ( $sDateFormat, $nDateTemp );
        }
        
        return $sReturn;
    }
    
    /**
     * 文件大小格式化
     *
     * @param int $nFileSize            
     * @param boolean $booUnit            
     * @return string
     */
    public static function formatBytes($nFileSize, $booUnit = true) {
        if ($nFileSize >= 1073741824) {
            $nFileSize = round ( $nFileSize / 1073741824, 2 ) . ($booUnit ? 'GB' : '');
        } elseif ($nFileSize >= 1048576) {
            $nFileSize = round ( $nFileSize / 1048576, 2 ) . ($booUnit ? 'MB' : '');
        } elseif ($nFileSize >= 1024) {
            $nFileSize = round ( $nFileSize / 1024, 2 ) . ($booUnit ? 'KB' : '');
        } else {
            $nFileSize = $nFileSize . ($booUnit ? __ ( '字节' ) : '');
        }
        
        return $nFileSize;
    }
    
    /**
     * 下划线转驼峰
     *
     * @param string $strValue            
     * @param string $strSeparator            
     * @return string
     */
    public static function camelize($strValue, $strSeparator = '_') {
        $strValue = $strSeparator . str_replace ( $strSeparator, " ", strtolower ( $strValue ) );
        return ltrim ( str_replace ( " ", "", ucwords ( $strValue ) ), $strSeparator );
    }
    
    /**
     * 驼峰转下划线
     *
     * @param string $strValue            
     * @param string $strSeparator            
     * @return string
     */
    public static function unCamelize($strValue, $strSeparator = '_') {
        return strtolower ( preg_replace ( '/([a-z])([A-Z])/', "$1" . $strSeparator . "$2", $strValue ) );
    }
    
    /**
     * 判断字符串中是否包含给定的字符开始
     *
     * @param string $strToSearched            
     * @param string $strSearch            
     * @return bool
     */
    public static function startsWith($strToSearched, $strSearch) {
        if ($strSearch != '' && strpos ( $strToSearched, $strSearch ) === 0) {
            return true;
        }
        return false;
    }
    
    /**
     * 判断字符串中是否包含给定的字符结尾
     *
     * @param string $strToSearched            
     * @param string $strSearch            
     * @return bool
     */
    public static function endsWith($strToSearched, $strSearch) {
        if (( string ) $strSearch === substr ( $strToSearched, - strlen ( $strSearch ) )) {
            return true;
        }
        return false;
    }
    
    /**
     * 判断字符串中是否包含给定的字符串集合
     *
     * @param string $strToSearched            
     * @param string $strSearch            
     * @return bool
     */
    public static function contains($strToSearched, $strSearch) {
        if ($strSearch != '' && strpos ( $strToSearched, $strSearch ) !== false) {
            return true;
        }
        return false;
    }
}

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

namespace Leevel\Support;

/**
 * 字符串.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.05
 *
 * @version 1.0
 */
class Str
{
    use TMacro;

    /**
     * 随机字母数字.
     *
     * @param int    $length
     * @param string $charBox
     *
     * @return string
     */
    public static function randAlphaNum(int $length, ?string $charBox = null)
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        }

        return static::randStr($length, $charBox);
    }

    /**
     * 随机小写字母数字.
     *
     * @param int    $length
     * @param string $charBox
     *
     * @return string
     */
    public static function randAlphaNumLowercase(int $length, ?string $charBox = null)
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'abcdefghijklmnopqrstuvwxyz1234567890';
        } else {
            $charBox = strtolower($charBox);
        }

        return static::randStr($length, $charBox);
    }

    /**
     * 随机大写字母数字.
     *
     * @param int    $length
     * @param string $charBox
     *
     * @return string
     */
    public static function randAlphaNumUppercase(int $length, ?string $charBox = null)
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        } else {
            $charBox = strtoupper($charBox);
        }

        return static::randStr($length, $charBox);
    }

    /**
     * 随机字母.
     *
     * @param int    $length
     * @param string $charBox
     *
     * @return string
     */
    public static function randAlpha(int $length, ?string $charBox = null)
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        return static::randStr($length, $charBox);
    }

    /**
     * 随机小写字母.
     *
     * @param int    $length
     * @param string $charBox
     *
     * @return string
     */
    public static function randAlphaLowercase(int $length, ?string $charBox = null)
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'abcdefghijklmnopqrstuvwxyz';
        } else {
            $charBox = strtolower($charBox);
        }

        return static::randStr($length, $charBox);
    }

    /**
     * 随机大写字母.
     *
     * @param int    $length
     * @param string $charBox
     *
     * @return string
     */
    public static function randAlphaUppercase(int $length, ?string $charBox = null)
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        } else {
            $charBox = strtoupper($charBox);
        }

        return static::randStr($length, $charBox);
    }

    /**
     * 随机数字.
     *
     * @param int    $length
     * @param string $charBox
     *
     * @return string
     */
    public static function randNum(int $length, ?string $charBox = null)
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = '0123456789';
        }

        return static::randStr($length, $charBox);
    }

    /**
     * 随机字中文.
     *
     * @param int    $length
     * @param string $charBox
     *
     * @return string
     */
    public static function randChinese(int $length, ?string $charBox = null)
    {
        if (!$length) {
            return '';
        }

        if (null === $charBox) {
            $charBox = '在了不和有大这主中人上为们地个用工时要动国产以我到他会'.
                '作来分生对于学下级就年阶义发成部民可出能方进同行面说种过命度革而'.
                '多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所'.
                '二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两'.
                '些还天资事队批如应形想制心样干都向变关点育重其思与间内去因件日利'.
                '相由压员气业代全组数果期导平各基或月毛然问比展那它最及外没看治提'.
                '五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位'.
                '情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直'.
                '团统式转别造切九您取西持总料连任志观调七么山程百报更见必真保热委'.
                '手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南'.
                '广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据'.
                '速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确'.
                '究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走'.
                '议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半'.
                '敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破'.
                '述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄'.
                '易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页'.
                '抗苏显苦英快称坏移约巴材省黑武培着河帝仅针怎植京助升王眼她抓含苗'.
                '副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻'.
                '靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江'.
                '析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功'.
                '套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟'.
                '裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散'.
                '商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩'.
                '益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃'.
                '执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵'.
                '箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽'.
                '倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰编印蜂急拿扩伤飞露核缘游振'.
                '操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃'.
                '欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜'.
                '笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣'.
                '铜沿替滚客召旱悟刺脑的一是';
        }

        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= static::substr($charBox,
                (int) (floor(mt_rand(0, mb_strlen($charBox, 'utf-8') - 1))),
                1
            );
        }

        unset($charBox);

        return $result;
    }

    /**
     * 随机字符串.
     *
     * @param int    $length
     * @param string $charBox
     *
     * @return string
     */
    public static function randStr(int $length, string $charBox)
    {
        if (!$length || !$charBox) {
            return '';
        }

        return substr(
            str_shuffle($charBox),
            0,
            $length
        );
    }

    /**
     * 字符串编码转换.
     *
     * @param mixed  $contents
     * @param string $fromChar
     * @param string $toChar
     *
     * @return mixed
     */
    public static function strEncoding($contents, string $fromChar, string $toChar = 'utf-8')
    {
        if (empty($contents) ||
            (!is_array($contents) && !is_string($contents)) ||
            strtolower($fromChar) === strtolower($toChar)) {
            return $contents;
        }

        if (is_string($contents)) {
            return mb_convert_encoding($contents, $toChar, $fromChar);
        }

        foreach ($contents as $key => $val) {
            $tmp = static::strEncoding($key, $fromChar, $toChar);
            $contents[$tmp] = static::strEncoding($val, $fromChar, $toChar);

            if ($key !== $tmp) {
                unset($contents[$tmp]);
            }
        }

        return $contents;
    }

    /**
     * 字符串截取.
     *
     * @param string $strings
     * @param int    $start
     * @param int    $length
     * @param string $charset
     *
     * @return string
     */
    public static function substr(string $strings, int $start = 0, int $length = 255, string $charset = 'utf-8')
    {
        return mb_substr($strings, $start, $length, $charset);
    }

    /**
     * 日期格式化.
     *
     * @param int    $dateTemp
     * @param array  $lang
     * @param string $dateFormat
     *
     * @return string
     */
    public static function formatDate(int $dateTemp, array $lang = [], string $dateFormat = 'Y-m-d H:i')
    {
        $sec = time() - $dateTemp;

        if ($sec < 0) {
            return date($dateFormat, $dateTemp);
        }

        $hover = (int) (floor($sec / 3600));

        if (0 === $hover) {
            if (0 === ($min = (int) (floor($sec / 60)))) {
                return $sec.' '.($lang['seconds'] ?? 'seconds ago');
            }

            return $min.' '.($lang['minutes'] ?? 'minutes ago');
        }
        if ($hover < 24) {
            return $hover.' '.($lang['hours'] ?? 'hours ago');
        }

        return date($dateFormat, $dateTemp);
    }

    /**
     * 文件大小格式化.
     *
     * @param int  $fileSize
     * @param bool $withUnit
     *
     * @return string
     */
    public static function formatBytes(int $fileSize, bool $withUnit = true)
    {
        if ($fileSize >= 1073741824) {
            $fileSize = round($fileSize / 1073741824, 2).($withUnit ? 'G' : '');
        } elseif ($fileSize >= 1048576) {
            $fileSize = round($fileSize / 1048576, 2).($withUnit ? 'M' : '');
        } elseif ($fileSize >= 1024) {
            $fileSize = round($fileSize / 1024, 2).($withUnit ? 'K' : '');
        } else {
            $fileSize = $fileSize.($withUnit ? 'B' : '');
        }

        return $fileSize;
    }

    /**
     * 下划线转驼峰.
     *
     * @param string $value
     * @param string $separator
     *
     * @return string
     */
    public static function camelize(string $value, string $separator = '_')
    {
        $value = $separator.str_replace($separator, ' ', strtolower($value));

        return ltrim(
            str_replace(
                ' ',
                '',
                ucwords($value)
            ),
            $separator
        );
    }

    /**
     * 驼峰转下划线
     *
     * @param string $value
     * @param string $separator
     *
     * @return string
     */
    public static function unCamelize(string $value, string $separator = '_')
    {
        return strtolower(
            preg_replace(
                '/([a-z])([A-Z])/',
                '$1'.$separator.'$2',
                $value
            )
        );
    }

    /**
     * 判断字符串中是否包含给定的字符开始.
     *
     * @param string $toSearched
     * @param string $search
     *
     * @return bool
     */
    public static function startsWith(string $toSearched, string $search): bool
    {
        if ('' !== $search &&
            0 === strpos($toSearched, $search)) {
            return true;
        }

        return false;
    }

    /**
     * 判断字符串中是否包含给定的字符结尾.
     *
     * @param string $toSearched
     * @param string $search
     *
     * @return bool
     */
    public static function endsWith(string $toSearched, string $search): bool
    {
        if ((string) $search === substr($toSearched, -strlen($search))) {
            return true;
        }

        return false;
    }

    /**
     * 判断字符串中是否包含给定的字符串集合.
     *
     * @param string $toSearched
     * @param string $search
     *
     * @return bool
     */
    public static function contains(string $toSearched, string $search): bool
    {
        if ('' !== $search &&
            false !== strpos($toSearched, $search)) {
            return true;
        }

        return false;
    }
}

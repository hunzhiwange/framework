<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\i18n;

use InvalidArgumentException;
use queryyetsimple\support\option;
use queryyetsimple\cookie\icookie;

/**
 * 国际化组件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
class i18n implements ii18n
{
    use option;

    /**
     * cookie
     *
     * @var \queryyetsimple\cookie\icookie
     */
    protected $objCookie;

    /**
     * 当前语言上下文
     *
     * @var string
     */
    protected $sI18nName;

    /**
     * 默认语言上下文
     *
     * @var string
     */
    protected $sDefaultI18nName = 'zh-cn';

    /**
     * 语言数据
     *
     * @var array
     */
    protected $arrText = [];

    /**
     * 语言 cookie
     *
     * @var string
     */
    protected $sCookieName = 'i18n';

    /**
     * 国际化参数名
     *
     * @var string
     */
    const ARGS = '~@i18n';

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'on' => true,
        'switch' => true,
        'cookie_app' => false,
        'default' => 'zh-cn',
        'auto_accept' => true,
        'app_name' => 'home'
    ];

    /**
     * 构造函数
     *
     * @param \queryyetsimple\cookie\icookie $objCookie
     * @param array $arrOption
     * @return void
     */
    public function __construct(icookie $objCookie, array $arrOption = [])
    {
        $this->objCookie = $objCookie;
        $this->options($arrOption);
    }

    /**
     * 获取语言text
     *
     * @param string $sValue 当前的语言
     * @return string
     */
    public function getText($sValue)
    {
        // 未开启直接返回
        if (! $this->getOption('on')) {
            return func_num_args() > 1 ? call_user_func_array('sprintf', func_get_args()) : $sValue;
        }

        // 开启读取语言包
        $sContext = $this->getContext();
        $sValue = $sContext && isset($this->arrText[$sContext][$sValue]) ? $this->arrText[$sContext][$sValue] : $sValue;
        if (func_num_args() > 1) {
            $arrArgs = func_get_args();
            $arrArgs[0] = $sValue;
            $sValue = call_user_func_array('sprintf', $arrArgs);
            unset($arrArgs);
        }
        return $sValue;
    }

    /**
     * 获取语言text
     *
     * @param string $sValue 当前的语言
     * @return string
     */
    public function __($sValue)
    {
        return call_user_func_array([
            $this,
            'getText'
        ], func_get_args());
    }

    /**
     * 添加语言包
     *
     * @param string $sI18nName 语言名字
     * @param array $arrData 语言包数据
     * @return void
     */
    public function addI18n($sI18nName, $arrData = [])
    {
        if (! $sI18nName || ! is_string($sI18nName)) {
            throw new InvalidArgumentException('I18n name not allowed empty.');
        }

        if (array_key_exists($sI18nName, $this->arrText)) {
            $this->arrText[$sI18nName] = array_merge($this->arrText[$sI18nName], $arrData);
        } else {
            $this->arrText[$sI18nName] = $arrData;
        }
    }

    /**
     * 自动分析语言上下文环境
     *
     * @return string
     */
    public function parseContext()
    {
        if (! $this->getOption('switch')) {
            $sI18nSet = $this->getOption('default');
        } else {
            if ($this->getOption('cookie_app') === true) {
                $sCookieName = $this->getOption('app_name') . '_i18n';
            } else {
                $sCookieName = 'i18n';
            }
            $this->setCookieName($sCookieName);

            if (isset($_GET[static::ARGS])) {
                $sI18nSet = $_GET[static::ARGS];
                $this->objCookie->set($sCookieName, $sI18nSet);
            } elseif ($sCookieName) {
                $sI18nSet = $this->objCookie->get($sCookieName);
                if (empty($sI18nSet)) {
                    $sI18nSet = $this->getOption('default');
                }
            } elseif ($this->getOption('auto_accept') && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $arrMatches);
                $sI18nSet = $arrMatches[1];
            } else {
                $sI18nSet = $this->getOption('default');
            }
        }
        $this->setDefaultContext($this->getOption('default'));
        $this->setContext($sI18nSet);

        return $sI18nSet;
    }

    /**
     * 设置当前语言包上下文环境
     *
     * @param string $sI18nName
     * @return void
     */
    public function setContext($sI18nName)
    {
        $this->sI18nName = $sI18nName;
    }

    /**
     * 设置当前语言包默认上下文环境
     *
     * @param string $sI18nName
     * @return void
     */
    public function setDefaultContext($sI18nName)
    {
        $this->sDefaultI18nName = $sI18nName;
    }

    /**
     * 设置 cookie 名字
     *
     * @param string $sCookieName cookie名字
     * @return void
     */
    public function setCookieName($sCookieName)
    {
        return $this->sCookieName == $sCookieName;
    }

    /**
     * 获取当前语言包默认上下文环境
     *
     * @return string
     */
    public function getDefaultContext()
    {
        return $this->sDefaultI18nName;
    }

    /**
     * 获取当前语言包 cookie 名字
     *
     * @return string
     */
    public function getCookieName()
    {
        return $this->sCookieName;
    }

    /**
     * 获取当前语言包上下文环境
     *
     * @return string
     */
    public function getContext()
    {
        return $this->sI18nName;
    }
}

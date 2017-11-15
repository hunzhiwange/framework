<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mail;

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

/**
 * imail 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
 * @version 1.0
 */
interface imail
{

    /**
     * 设置邮件发送来源
     *
     * @param string $strAddress
     * @param string|null $mixName
     * @return $this
     */
    public function globalFrom($strAddress, $mixName = null);

    /**
     * 设置邮件发送地址
     *
     * @param string $strAddress
     * @param string|null $mixName
     * @return $this
     */
    public function globalTo($strAddress, $mixName = null);

    /**
     * 视图 html 邮件内容
     *
     * @param string $sFile
     * @param array $arrData
     * @return $this
     */
    public function view($sFile, array $arrData = []);

    /**
     * html 邮件内容
     *
     * @param string $strContent
     * @return $this
     */
    public function html($strContent);

    /**
     * 纯文本邮件内容
     *
     * @param string $strContent
     * @return $this
     */
    public function plain($strContent);

    /**
     * 视图纯文本邮件内容
     *
     * @param string $sFile
     * @param array $arrData
     * @return $this
     */
    public function viewPlain($sFile, array $arrData = []);

    /**
     * 消息回调处理
     *
     * @param callable|string $mixCallback
     * @return $this
     */
    public function message($mixCallback);

    /**
     * 添加附件
     *
     * @param string $strFile
     * @param callable|null $mixCallback
     * @return $this
     */
    public function attach($strFile, $mixCallback = null);

    /**
     * 添加内存内容附件
     * file_get_content( path )
     *
     * @param string $strData
     * @param string $strName
     * @param callable|null $mixCallback
     * @return $this
     */
    public function attachData($strData, $strName, $mixCallback = null);

    /**
     * 图片嵌入邮件
     *
     * @param string $file
     * @return string
     */
    public function attachView($strFile);

    /**
     * 内存内容图片嵌入邮件
     *
     * @param string $strData
     * @param string $strName
     * @param string|null $contentType
     * @return string
     */
    public function attachDataView($strData, $strName, $strContentType = null);

    /**
     * 格式化中文附件名字
     *
     * @param string $strFile
     * @return string
     */
    public function attachChinese($strFile);

    /**
     * 发送邮件
     *
     * @param callable|string $mixCallback
     * @param boolean $booHtmlPriority
     * @return int
     */
    public function send($mixCallback = null, $booHtmlPriority = true);

    /**
     * 错误消息
     *
     * @return array
     */
    public function failedRecipients();
}

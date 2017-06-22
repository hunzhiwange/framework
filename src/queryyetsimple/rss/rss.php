<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\rss;

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

use queryyetsimple\rss\interfaces\rss as interfaces_rss;

/**
 * rss 2.0
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.08
 * @version 1.0
 */
class rss implements interfaces_rss {
    
    /**
     * 频道信息
     *
     * @var array
     */
    protected $arrOption = [ 
            'title' => '',
            'atom_link' => '',
            'link' => '',
            'description' => '',
            'language' => 'zh-cn',
            'pub_date' => '',
            'last_build_date' => '',
            'generator' => 'rss 2.0',
            'img_url' => '' 
    ];
    
    /**
     * rss 内容项
     *
     * @var array
     */
    protected $arrItems = [ ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @return void
     */
    public function __construct($arrOption = []) {
        if ($arrOption)
            $this->setOption ( $arrOption );
        if (! $this->getOption ( 'pub_date' )) {
            $this->setOption ( 'pub_date', date ( 'Y-m-d H:i:s' ) );
        }
        if (! $this->getOption ( 'last_build_date' )) {
            $this->setOption ( 'last_build_date', date ( 'Y-m-d H:i:s' ) );
        }
    }
    
    /**
     * 生成 rss
     *
     * @return void
     */
    public function run($booDisplay = true) {
        if (! headers_sent ())
            header ( "Content-Type: text/xml; charset=utf-8" );
        $sRss = $this->header ();
        $sRss .= $this->body ();
        $sRss .= $this->footer ();
        if ($booDisplay === true) {
            exit ( $sRss );
        } else {
            return $sRss;
        }
    }
    
    /**
     * 添加一个 rss 项数据
     *
     * @param array $arrItem            
     * @return void
     */
    public function addItem($arrItem) {
        if (count ( $arrItem ) != count ( $arrItem, COUNT_RECURSIVE )) {
            foreach ( $arrItem as $arrTemp )
                $this->addItem ( $arrTemp );
        } else {
            $this->arrItems [] = array_merge ( [ 
                    'title' => '',
                    'link' => '',
                    'comments' => '',
                    'pubdata' => '',
                    'creator' => '',
                    'category' => '',
                    'guid' => '',
                    'description' => '',
                    'content_encoded' => '',
                    'comment_rss' => '',
                    'comment_num' => '' 
            ], $arrItem );
        }
    }
    
    /**
     * 更改配置
     *
     * @param string|array $mixKey            
     * @param string|null $sValue            
     * @return void
     */
    public function setOption($mixKey, $sValue = null) {
        if (is_array ( $mixKey )) {
            foreach ( $mixKey as $strKey => $mixValue )
                $this->setOption ( $strKey, $mixValue );
        } else {
            $this->arrOption [$mixKey] = $sValue;
        }
    }
    
    /**
     * 返回配置
     *
     * @param string $sKey            
     * @return string
     */
    public function getOption($sKey) {
        return isset ( $this->arrOption [$sKey] ) ? $this->arrOption [$sKey] : null;
    }
    
    /**
     * rss 头部
     *
     * @return string
     */
    protected function header() {
        $arrRss = [ ];
        $arrRss [] = '<?xml version="1.0" encoding="UTF-8"?>';
        $arrRss [] = '<rss version="2.0" ';
        $arrRss [] = 'xmlns:content="http://purl.org/rss/1.0/modules/content/"';
        $arrRss [] = 'xmlns:wfw="http://wellformedweb.org/CommentAPI/"';
        $arrRss [] = 'xmlns:dc="http://purl.org/dc/elements/1.1/"';
        $arrRss [] = 'xmlns:atom="http://www.w3.org/2005/Atom"';
        $arrRss [] = 'xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"';
        $arrRss [] = 'xmlns:slash="http://purl.org/rss/1.0/modules/slash/">';
        $arrRss [] = '<channel>';
        $arrRss [] = '<title><![CDATA[' . $this->getOption ( 'title' ) . ']]></title>';
        $arrRss [] = '<atom:link href="' . $this->getOption ( 'atom_link' ) . '" rel="self" type="application/rss+xml" />';
        $arrRss [] = '<link>' . $this->getOption ( 'link' ) . '</link>';
        $arrRss [] = '<description><![CDATA[' . $this->getOption ( 'description' ) . ']]></description>';
        $arrRss [] = '<sy:updatePeriod>hourly</sy:updatePeriod>';
        $arrRss [] = '<sy:updateFrequency>1</sy:updateFrequency>';
        $arrRss [] = '<language>' . $this->getOption ( 'language' ) . '</language>';
        $arrRss [] = '<pubDate>' . $this->getOption ( 'pub_date' ) . '</pubDate>';
        $arrRss [] = '<lastBuildDate>' . $this->getOption ( 'last_build_date' ) . '</lastBuildDate>';
        
        if (! empty ( $this->getOption ( 'generator' ) )) {
            $arrRss [] = '<generator>' . $this->getOption ( 'generator' ) . '</generator>';
        }
        if ($this->getOption ( 'img_url' )) {
            $arrRss [] = '<image>';
            $arrRss [] = '<title><![CDATA[' . $this->getOption ( 'title' ) . ']]></title>';
            $arrRss [] = '<link>' . $this->getOption ( 'link' ) . '</link>';
            $arrRss [] = '<url>' . $this->getOption ( 'img_url' ) . '</url>';
            $arrRss [] = '</image>';
        }
        
        return implode ( PHP_EOL, $arrRss );
    }
    
    /**
     * rss 内容
     *
     * @return string
     */
    protected function body() {
        $arrResult = [ ];
        
        foreach ( $this->arrItems as $arrItem ) {
            $arrTemp = [ ];
            $arrTemp [] = '<item>';
            $arrTemp [] = '<title><![CDATA[' . $arrItem ['title'] . ']]></title>';
            $arrTemp [] = '<link>' . $arrItem ['link'] . '</link>';
            $arrTemp [] = '<comments>' . $arrItem ['comments'] . '</comments>';
            $arrTemp [] = '<pubDate>' . $arrItem ['pubdata'] . '</pubDate>';
            $arrTemp [] = '<dc:creator>' . $arrItem ['creator'] . '</dc:creator>';
            $arrTemp [] = '<category><![CDATA[' . $arrItem ['category'] . ']]></category>';
            $arrTemp [] = '<guid isPermaLink="false">' . $arrItem ['guid'] . '</guid>';
            $arrTemp [] = '<description><![CDATA[' . $arrItem ['description'] . ']]></description>';
            $arrTemp [] = '<content:encoded><![CDATA[' . $arrItem ['content_encoded'] . ']]></content:encoded>';
            $arrTemp [] = '<wfw:commentRss>' . $arrItem ['comment_rss'] . '</wfw:commentRss>';
            $arrTemp [] = '<slash:comments>' . $arrItem ['comment_num'] . '</slash:comments>';
            $arrTemp [] = '</item>';
            $arrResult [] = implode ( PHP_EOL, $arrTemp );
        }
        
        return implode ( PHP_EOL, $arrResult );
    }
    
    /**
     * rss 底部
     *
     * @return string
     */
    protected function footer() {
        return '</channel>' . PHP_EOL . '</rss>';
    }
}

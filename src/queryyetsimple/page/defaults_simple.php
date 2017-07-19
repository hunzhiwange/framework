<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\page;

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
 * 默认简单分页渲染
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.14
 * @version 1.0
 */
class defaults_simple extends defaults {
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\page\page $objPage            
     * @param array $arrOption            
     * @return void
     */
    public function __construct(page $objPage, array $arrOption = []) {
        parent::__construct ( $objPage, $arrOption );
        $this->option ( 'template', '{header} {prev} {ul} {first} {main} {last} {endul} {next} {footer}' );
    }
}

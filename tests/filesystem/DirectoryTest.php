<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace tests\filesystem;

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

use queryyetsimple\filesystem\directory;
use queryyetsimple\filesystem\file;
use tests\testcase;

/**
 * filesystem.directory 组件测试
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.06.01
 * @version 1.0
 */
class Directory_test extends testcase {
    
    /**
     * 目录
     *
     * @var string
     */
    private $strDir;
    
    /**
     * 初始化
     *
     * @return void
     */
    protected function setUp() {
        $this->strDir = project ()->pathRuntime () . '/test_create_dir';
    }
    
    /**
     * 测试打散目录
     *
     * @return void
     */
    public function testDistributed() {
        $this->assertTrue ( directory::distributed ( 1 ) === [ 
                '000/00/00/',
                '01' 
        ] );
        $this->assertTrue ( directory::distributed ( 1000 ) === [ 
                '000/00/10/',
                '00' 
        ] );
    }
    
    /**
     * 测试创建目录和文件
     *
     * @return void
     */
    public function testCreate() {
        directory::create ( $this->strDir );
        directory::create ( $this->strDir . '/test' );
        
        file::create ( $this->strDir . '/test.txt' );
        file::create ( $this->strDir . '/test/test.txt' );
        
        $this->assertEquals ( true, is_dir ( $this->strDir ) );
        $this->assertEquals ( true, is_dir ( $this->strDir . '/test' ) );
        
        $this->assertEquals ( true, is_file ( $this->strDir . '/test.txt' ) );
        $this->assertEquals ( true, is_file ( $this->strDir . '/test/test.txt' ) );
    }
    
    /**
     * 测试复制目录
     *
     * @return void
     */
    public function testCopy() {
        directory::copy ( $this->strDir, dirname ( $this->strDir ) . '/test_copy2' );
        $this->assertEquals ( true, is_file ( dirname ( $this->strDir ) . '/test_copy2/test/test.txt' ) );
    }
    
    /**
     * 测试读取目录
     *
     * @return void
     */
    public function testList() {
        $this->assertEquals ( [ 
                'file' => [ 
                        'NewFile.html' 
                ],
                'dir' => [ 
                        'test' 
                ] 
        ], directory::lists ( dirname ( $this->strDir ) . '/test_copy2', 'both' ) );
    }
    
    /**
     * 测试删除目录
     *
     * @return void
     */
    public function testDelete() {
        $this->assertEquals ( true, is_dir ( dirname ( $this->strDir ) . '/test_copy2' ) );
        directory::delete ( dirname ( $this->strDir ) . '/test_copy2', true );
        $this->assertEquals ( true, ! is_dir ( dirname ( $this->strDir ) . '/test_copy2' ) );
    }
}

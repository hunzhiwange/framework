<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc;

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

use RuntimeException;
use queryyetsimple\http\request;
use queryyetsimple\helper\helper;
use queryyetsimple\http\response;
use queryyetsimple\mvc\interfaces\view;

/**
 * 基类控制器
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class controller {
    
    /**
     * 项目容器
     *
     * @var \queryyetsimple\bootstrap\project
     */
    protected $objProject = null;
    
    /**
     * 视图
     *
     * @var \queryyetsimple\mvc\interfaces\view
     */
    protected $objView = null;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\mvc\interfaces\view $objView            
     * @return void
     */
    public function __construct(view $objView) {
        $this->objView = $objView;
    }
    
    /**
     * 执行子方法器
     *
     * @param string $sActionName
     *            方法名
     * @return void
     */
    public function action($sActionName) {
        // 判断是否已经注册过
        // if (($objAction = $this->project ()->make ( 'app' )->getAction ( $this->project ()->controller_name, $sActionName )) && ! (is_array ( $objAction ) && isset ( $objAction [1] ) && helper::isKindOf ( $objAction [0], 'queryyetsimple\mvc\controller' ))) {
        // return $this->project ()->make ( 'app' )->action ( $this->project ()->controller_name, $sActionName );
        // }
        //
        //
        // echo 'sdfsdf';
        // exit();
        
        // 读取默认方法器
        $sActionName = get_class ( $this ) . '\\' . $sActionName;
        var_dump ( $sActionName );
        
        if (class_exists ( $sActionName )) {
            // 注册方法器
            // $this->project ()->make ( 'app' )->registerAction ( $this->project ()->controller_name, $sActionName, [
            // $sActionName,
            // 'run'
            // ] );
            
            // dump($xx);
            
            // 运行方法器
            // return $this->project ()->make ( 'app' )->action ( $this->project ()->controller_name, $sActionName );
        } else {
            throw new RuntimeException ( __ ( '控制器 %s 的方法 %s 不存在', get_class ( $this ), $sActionName ) );
        }
    }
    
    /**
     * 赋值
     *
     * @param 变量或变量数组集合 $Name            
     * @param string $Value            
     * @return $this
     */
    public function assign($Name, $Value = '') {
        $this->objView->assign ( $Name, $Value );
        return $this;
    }
    
    /**
     * 取回赋值
     *
     * @param 变量名字 $sName            
     * @return mixed
     */
    public function getAssign($sName) {
        return $this->objView->getVar ( $sName );
    }
    
    /**
     * 加载视图文件
     *
     * @param string $sThemeFile            
     * @param array $in
     *            charset 编码
     *            content_type 类型
     *            return 是否返回 html 返回而不直接输出
     * @return mixed
     */
    public function display($sThemeFile = '', $arrOption = []) {
        $arrOption = array_merge ( [ 
                'charset' => 'utf-8',
                'content_type' => 'text/html',
                'return' => false 
        ], $arrOption );
        
        return $this->objView->display ( $sThemeFile, $arrOption );
    }
    
    /**
     * 设置或者返回服务容器
     *
     * @param \queryyetsimple\bootstrap\project $objProject            
     * @return void
     */
    public function project($objProject = null) {
        if (is_null ( $objProject )) {
            return $this->objProject;
        } else {
            $this->objProject = $objProject;
            return $this;
        }
    }
    
    /**
     * 赋值
     *
     * @param mixed $mixName            
     * @param mixed $Value            
     * @return void
     */
    public function __set($mixName, $mixValue) {
        $this->assign ( $mixName, $mixValue );
    }
    
    /**
     * 获取值
     *
     * @param string $sName            
     * @return mixed
     */
    public function __get($sName) {
        return $this->getAssign ( $sName );
    }
    
    /**
     * 实现 isPost,isGet等
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return boolean
     */
    public function __call($sMethod = '', $arrArgs = []) {
        echo $sMethod;
        switch ($sMethod) {
            case 'isPost' :
                return request::isPosts ();
            case 'isGet' :
                return request::isGets ();
            case 'in' :
                if (! empty ( $arrArgs [0] )) {
                    return request::ins ( $arrArgs [0], isset ( $arrArgs [1] ) ? $arrArgs [1] : 'R' );
                } else {
                    throw new RuntimeException ( 'Can not find method.' );
                }
            default :
                throw new RuntimeException ( __ ( '控制器 %s 的方法 %s 不存在', get_class ( $this ), $sMethod ) );
        }
    }
}

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

use queryyetsimple\database\database;

/**
 * 数据库元对象
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.27
 * @version 1.0
 */
class meta {
    
    /**
     * meta 对象实例
     *
     * @var array
     */
    protected static $arrInstances = array ();
    protected $objConnect = null;
    
    /**
     * 数据库查询的原生字段
     *
     * @var array
     */
    public $arrFields = [ ];
    
    /**
     * 主键
     *
     * @var array
     */
    public $arrPrimaryKey = [ ];
    
    /**
     * 自动增加 ID
     *
     * @var string
     */
    public $strAutoIncrement = null;
    
    /**
     * 属性映射字段
     *
     * @var array
     */
    public $arrPropsFields = [ ];
    
    /**
     * 字段映射属性
     *
     * @var array
     */
    public $arrFieldsProps = [ ];
    
    /**
     * 是否使用复合主键
     *
     * @var bool
     */
    public $booCompositeId = false;
    
    /**
     * 字段格式化类型
     *
     * @var array
     */
    protected static $arrFieldType = [ 
            'int' => [ 
                    'int',
                    'integer',
                    'smallint',
                    'serial' 
            ],
            'float' => [ 
                    'float',
                    'number' 
            ],
            'boolean' => [ 
                    'bool',
                    'boolean' 
            ] 
    ];
    protected $strTable;
    protected $strConnect;
    
    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct($strTable, $strConnect = '') {
        // $this->_bInitClass=$bInitClass;
        // if($bInitClass===false){
        // $sModelClass = explode ( '\\', $sModelClass );
        // $sModelClass = array_pop ( $sModelClass );
        $this->strTable = $strTable;
        $this->strConnect = $strConnect;
        
        $this->init ( $strTable );
        // }else{
        // $this->initSimple($sClass);
        // }
    }
    
    /**
     * 字段转属性
     *
     * @param array $arrData            
     * @return array
     */
    public function fieldsProps(array $arrData) {
        $arrResult = [ ];
        
        foreach ( $arrData as $strField => $mixValue ) {
            if (! isset ( $this->arrFieldsProps [$strField] ))
                continue;
            $strField = $this->arrFieldsProps [$strField];
            
            switch (true) {
                case in_array ( $this->arrFields [$strField] ['type'], static::$arrFieldType ['int'] ) :
                    $mixValue = intval ( $mixValue );
                    break;
                
                case in_array ( $this->arrFields [$strField] ['type'], static::$arrFieldType ['float'] ) :
                    $mixValue = floatval ( $mixValue );
                    break;
                
                case in_array ( $this->arrFields [$strField] ['type'], static::$arrFieldType ['boolean'] ) :
                    $mixValue = $mixValue ? true : false;
                    break;
                
                default :
                    $mixValue = ( string ) $mixValue;
            }
            $arrResult [$strField] = $mixValue;
        }
        return $arrResult;
    }
    
    /**
     * 返回数据库元对象
     *
     * @param string $sModelClass            
     * @return $this
     */
    public static function instance($sModelClass) {
        if (! isset ( static::$arrInstances [$sModelClass] )) {
            return static::$arrInstances [$sModelClass] = new self ( $sModelClass );
        } else {
            return static::$arrInstances [$sModelClass];
        }
    }
    protected function initConnect() {
        // $objConnect
        // if(is_null($this->objConnect)){
        $this->objConnect = database::connects ( $this->strConnect );
        // }
    }
    
    /**
     * 新增返回数据
     *
     * @param array $arrSaveData            
     * @return array
     */
    public function insert($arrSaveData) {
        return [ 
                reset ( $this->arrPropsFields ) => $this->objConnect->table ( $this->strTable )->insert ( $arrSaveData ) 
        ];
    }
    public function update($arrCondition, $arrSaveData) {
        // database::table( 'test')
        // ->where('id',503)
        // ->update(['name' => '小猪'])
        //print_r ( $arrCondition );
        //print_r ( $arrSaveData );
        
        // exit();
        // echo 'yyyyyyyyy';
        //print_r ( $this->objConnect->table ( $this->strTable )->where ( $arrCondition )->update ( $arrSaveData ) );
    }
    protected function init($sModelClass) {
        // echo $sModelClass;
        $this->initConnect ();
        
        $arrColumnInfo = $this->objConnect->getTableColumns ( $sModelClass );
        $this->arrFields = $arrColumnInfo ['list'];
        $this->arrPrimaryKey = $arrColumnInfo ['primary_key'];
        $this->strAutoIncrement = $arrColumnInfo ['auto_increment'];
        
        if (count ( $this->arrPrimaryKey ) > 1) {
            $this->booCompositeId = true;
        }
        
        foreach ( $arrColumnInfo ['list'] as $strField => $arrField ) {
            $this->arrPropsFields [$strField] = $strField;
            $this->arrFieldsProps [$strField] = $strField;
        }
        
        // print_r($arrColumnInfo) ;
        // print_r($this->arrFields);
        // $this->_sClassName=$sClass;
        // $arrRef=(array)call_user_func(array($sClass,'init__'));
        
        // $arrTableConfig=!empty($arrRef['table_config'])?(array)$arrRef['table_config']:array();// 设置表数据入口对象
        // $this->_oTable=$this->tableByName($arrRef['table_name'],$arrTableConfig);
        // $this->_arrTableMeta=$this->_oTable->columns();
        
        // if(!empty($arrRef['autofill']) && is_array($arrRef['autofill'])){
        // $this->_arrAutofill=$arrRef['autofill'];
        // }
        
        // 准备验证规则
        // if(empty($arrRef['check']) || ! is_array($arrRef['check'])){
        // $arrRef['check']=array();
        // }
        // $this->_arrCheck=$this->prepareCheckRules($arrRef['check']);
    }
    public function getPrimaryKey() {
        return $this->arrPrimaryKey;
    }
    
    public function get(){
        
    }
}

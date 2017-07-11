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

use queryyetsimple\database;

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
    protected static $arrInstances = [ ];
    
    /**
     * 数据库连接
     *
     * @var \queryyetsimple\database\interfaces\connect
     */
    protected $objConnect = null;
    
    /**
     * 数据库查询的原生字段
     *
     * @var array
     */
    protected $arrFields = [ ];
    
    /**
     * 主键
     *
     * @var array
     */
    protected $arrPrimaryKey = [ ];
    
    /**
     * 自动增加 ID
     *
     * @var string
     */
    protected $strAutoIncrement = null;
    
    /**
     * 属性映射字段
     *
     * @var array
     */
    protected $arrPropField = [ ];
    
    /**
     * 字段映射属性
     *
     * @var array
     */
    protected $arrFieldProp = [ ];
    
    /**
     * 是否使用复合主键
     *
     * @var bool
     */
    protected $booCompositeId = false;
    
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
    
    /**
     * 元对象表
     *
     * @var string
     */
    protected $strTable;
    
    /**
     * 表连接
     *
     * @var mixed
     */
    protected $mixConnect;
    
    /**
     * 构造函数
     * 禁止直接访问构造函数，只能通过 instance 生成对象
     *
     * @param string $strTabe            
     * @param mixed $mixConnect            
     * @return void
     */
    protected function __construct($strTable, $mixConnect = null) {
        $this->strTable = $strTable;
        $this->mixConnect = $mixConnect;
        $this->initialization ( $strTable );
    }
    
    /**
     * 返回数据库元对象
     *
     * @param string $strTable            
     * @param mixed $mixConnect            
     * @return $this
     */
    public static function instance($strTable, $mixConnect = null) {
        if (! isset ( static::$arrInstances [$strTable] )) {
            return static::$arrInstances [$strTable] = new self ( $strTable );
        } else {
            return static::$arrInstances [$strTable];
        }
    }
    
    /**
     * 字段转属性
     *
     * @param string $strField            
     * @param mixed $mixValue            
     * @return array
     */
    public function fieldsProp($strField, $mixValue) {
        if (! isset ( $this->arrFieldProp [$strField] ))
            return null;
        $strField = $this->arrFieldProp [$strField];
        
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
                if (! is_null ( $mixValue ) && is_scalar ( $mixValue ))
                    $mixValue = ( string ) $mixValue;
        }
        return $mixValue;
    }
    
    /**
     * 批量字段转属性
     *
     * @param array $arrData            
     * @return array
     */
    public function fieldsProps(array $arrData) {
        $arrResult = [ ];
        foreach ( $arrData as $strField => $mixValue ) {
            if (! is_null ( ($mixValue = $this->fieldsProp ( $strField, $mixValue )) )) {
                $arrResult [$strField] = $mixValue;
            }
        }
        return $arrResult;
    }
    
    /**
     * 新增并返回数据
     *
     * @param array $arrSaveData            
     * @return array
     */
    public function insert(array $arrSaveData) {
        return [ 
                $this->getAutoIncrement () => $this->objConnect->table ( $this->strTable )->insert ( $arrSaveData ) 
        ];
    }
    
    /**
     * 更新并返回数据
     *
     * @param array $arrCondition            
     * @param array $arrSaveData            
     * @return int
     */
    public function update(array $arrCondition, array $arrSaveData) {
        return $this->objConnect->table ( $this->strTable )->where ( $arrCondition )->update ( $arrSaveData );
    }
    
    /**
     * 返回主键
     *
     * @return array
     */
    public function getPrimaryKey() {
        return $this->arrPrimaryKey;
    }
    
    /**
     * 是否为符合主键
     *
     * @return boolean
     */
    public function getCompositeId() {
        return $this->booCompositeId;
    }
    
    /**
     * 返回自增 ID
     *
     * @return string|null
     */
    public function getAutoIncrement() {
        return $this->strAutoIncrement;
    }

     /**
     * 返回数据库查询的原生字段
     *
     * @return array
     */
    public function getFields() {
        return $this->arrFields;
    }

    /**
     * 返回属性映射字段
     *
     * @return array
     */
    public function getPropField() {
        return $this->arrPropField;
    }
    
    /**
     * 返回字段映射属性
     *
     * @return array
     */
    public function getFieldProp() {
        return $this->arrFieldProp;
    }

    /**
     * 返回连接
     *
     * @return \queryyetsimple\database\interfaces\connect
     */
    public function getConnect() {
        return $this->objConnect;
    }
    
    /**
     * 返回查询
     *
     * @var \queryyetsimple\database\interfaces\connect
     */
    public function getSelect() {
        return $this->objConnect->table ( $this->strTable );
    }
    
    /**
     * 初始化元对象
     *
     * @param string $strTable            
     * @return
     *
     */
    protected function initialization($strTable) {
        $this->initConnect ();
        
        $arrColumnInfo = $this->objConnect->getTableColumns ( $strTable );
        $this->arrFields = $arrColumnInfo ['list'];
        $this->arrPrimaryKey = $arrColumnInfo ['primary_key'];
        $this->strAutoIncrement = $arrColumnInfo ['auto_increment'];
        
        if (count ( $this->arrPrimaryKey ) > 1) {
            $this->booCompositeId = true;
        }
        
        foreach ( $arrColumnInfo ['list'] as $strField => $arrField ) {
            $this->arrPropField [$strField] = $strField;
            $this->arrFieldProp [$strField] = $strField;
        }
        
        unset ( $arrColumnInfo );
    }
    
    /**
     * 连接数据库
     *
     * @return \queryyetsimple\database\interfaces\connect
     */
    protected function initConnect() {
        $this->objConnect = database::connect ( $this->mixConnect );
    }
}

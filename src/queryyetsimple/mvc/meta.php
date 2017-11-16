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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\mvc;

use queryyetsimple\database\manager as database_manager;

/**
 * 数据库元对象
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.27
 * @version 1.0
 */
class meta
{
    
    /**
     * Database 管理
     *
     * @var array
     */
    protected static $objDatabaseManager;
    
    /**
     * meta 对象实例
     *
     * @var array
     */
    protected static $arrInstances = [];
    
    /**
     * 数据库仓储
     *
     * @var \queryyetsimple\database\idatabase
     */
    protected $objConnect;
    
    /**
     * 数据库查询的原生字段
     *
     * @var array
     */
    protected $arrFields = [];
    
    /**
     * 数据库字段名字
     *
     * @var array
     */
    protected $arrField = [];
    
    /**
     * 主键
     *
     * @var array
     */
    protected $arrPrimaryKey = [];
    
    /**
     * 自动增加 ID
     *
     * @var string
     */
    protected $strAutoIncrement;
    
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
    protected function __construct($strTable, $mixConnect = null)
    {
        $this->strTable = $strTable;
        $this->mixConnect = $mixConnect;
        $this->initialization($strTable);
    }
    
    /**
     * 返回数据库元对象
     *
     * @param string $strTable
     * @param mixed $mixConnect
     * @return $this
     */
    public static function instance($strTable, $mixConnect = null)
    {
        $strUnique = static::getUnique($strTable, $mixConnect);
        if (! isset(static::$arrInstances[$strUnique])) {
            return static::$arrInstances[$strUnique] = new static($strTable, $mixConnect);
        } else {
            return static::$arrInstances[$strUnique];
        }
    }
    
    /**
     * 设置数据库管理对象
     *
     * @param \queryyetsimple\database\manager $objDatabaseManager
     * @return void
     */
    public static function setDatabaseManager(database_manager $objDatabaseManager)
    {
        static::$objDatabaseManager = $objDatabaseManager;
    }
    
    /**
     * 字段强制过滤
     *
     * @param string $strField
     * @param mixed $mixValue
     * @return array
     */
    public function fieldsProp($strField, $mixValue)
    {
        if (! in_array($strField, $this->arrField)) {
            return $mixValue;
        }
        
        $strType = $this->arrFields[$strField]['type'];
        
        switch (true) {
            case in_array($strType, static::$arrFieldType['int']):
                $mixValue = intval($mixValue);
                break;
            
            case in_array($strType, static::$arrFieldType['float']):
                $mixValue = floatval($mixValue);
                break;
            
            case in_array($strType, static::$arrFieldType['boolean']):
                $mixValue = $mixValue ? true : false;
                break;
            
            default:
                if (! is_null($mixValue) && is_scalar($mixValue)) {
                    $mixValue = ( string ) $mixValue;
                }
        }
        return $mixValue;
    }
    
    /**
     * 批量字段转属性
     *
     * @param array $arrData
     * @return array
     */
    public function fieldsProps(array $arrData)
    {
        $arrResult = [];
        foreach ($arrData as $strField => $mixValue) {
            if (! is_null(($mixValue = $this->fieldsProp($strField, $mixValue)))) {
                $arrResult[$strField] = $mixValue;
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
    public function insert(array $arrSaveData)
    {
        return [
            $this->getAutoIncrement() ?  : 0 => $this->objConnect->table($this->strTable)->insert($arrSaveData)
        ];
    }
    
    /**
     * 更新并返回数据
     *
     * @param array $arrCondition
     * @param array $arrSaveData
     * @return int
     */
    public function update(array $arrCondition, array $arrSaveData)
    {
        return $this->objConnect->table($this->strTable)->where($arrCondition)->update($arrSaveData);
    }
    
    /**
     * 返回主键
     *
     * @return array
     */
    public function getPrimaryKey()
    {
        return $this->arrPrimaryKey;
    }
    
    /**
     * 是否为符合主键
     *
     * @return boolean
     */
    public function getCompositeId()
    {
        return $this->booCompositeId;
    }
    
    /**
     * 返回自增 ID
     *
     * @return string|null
     */
    public function getAutoIncrement()
    {
        return $this->strAutoIncrement;
    }
    
    /**
     * 返回数据库查询的原生字段
     *
     * @return array
     */
    public function getFields()
    {
        return $this->arrFields;
    }
    
    /**
     * 返回字段名字
     *
     * @return array
     */
    public function getField()
    {
        return $this->arrField;
    }
    
    /**
     * 返回数据库仓储
     *
     * @return \queryyetsimple\database\idatabase
     */
    public function getConnect()
    {
        return $this->objConnect;
    }
    
    /**
     * 返回查询
     *
     * @var \queryyetsimple\database\iconnect
     */
    public function getSelect()
    {
        return $this->objConnect->table($this->strTable);
    }
    
    /**
     * 初始化元对象
     *
     * @param string $strTable
     * @return void
     */
    protected function initialization($strTable)
    {
        $this->initConnect();
        
        $arrColumnInfo = $this->objConnect->getTableColumnsCache($strTable);
        $this->arrFields = $arrColumnInfo['list'];
        $this->arrPrimaryKey = $arrColumnInfo['primary_key'];
        $this->strAutoIncrement = $arrColumnInfo['auto_increment'];
        
        if (count($this->arrPrimaryKey) > 1) {
            $this->booCompositeId = true;
        }
        
        $this->arrField = array_keys($arrColumnInfo['list']);
    }
    
    /**
     * 连接数据库仓储
     *
     * @return \queryyetsimple\database\idatabase
     */
    protected function initConnect()
    {
        $this->objConnect = static::$objDatabaseManager->connect($this->mixConnect);
    }
    
    /**
     * 取得唯一值
     *
     * @param string $strTabe
     * @param mixed $mixConnect
     * @return string
     */
    protected static function getUnique($strTable, $mixConnect = null)
    {
        return $strTable . '.' . md5(serialize($mixConnect));
    }
}

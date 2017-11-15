<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\bootstrap\console;

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
use queryyetsimple\support\helper;
use queryyetsimple\filesystem\fso;

/**
 * 命令行工具类导入类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.11.06
 * @version 1.0
 */
class load
{

    /**
     * 缓存路径
     *
     * @var string
     */
    protected $strCachePath;

    /**
     * 载入命名空间
     *
     * @var array
     */
    protected $arrNamespace = [ ];

    /**
     * 已经载入数据
     *
     * @var array
     */
    protected $arrLoad;

    /**
     * 构造函数
     *
     * @param array $arrNamespace
     * @return void
     */
    public function __construct(array $arrNamespace = [])
    {
        $this->arrNamespace = $arrNamespace;
    }

    /**
     * 设置缓存路径
     *
     * @param string $strCachePath
     * @return $this
     */
    public function setCachePath($strCachePath)
    {
        $this->strCachePath = $strCachePath;
        return $this;
    }

    /**
     * 添加命名空间
     *
     * @param array $arrNamespace
     * @return $this
     */
    public function addNamespace(array $arrNamespace)
    {
        $this->arrNamespace = array_merge($this->arrNamespace, $arrNamespace);
        return $this;
    }

    /**
     * 设置载入数据
     *
     * @param array $arrLoad
     * @return $this
     */
    public function setData(array $arrLoad)
    {
        $this->arrLoad = $arrLoad;
        return $this;
    }

    /**
     * 载入命令行数据
     *
     * @return array
     */
    public function loadData()
    {
        if (! is_null($this->arrLoad)) {
            return $this->arrLoad;
        }

        $arrFiles = $this->findConsoleFile($this->arrNamespace);

        $sCacheFile = $this->strCachePath;
        $sDir = dirname($sCacheFile);
        if (! is_dir($sDir)) {
            fso::createDirectory($sDir);
        }

        if (! file_put_contents($sCacheFile, '<?php return ' . var_export($arrFiles, true) . '; ?>')) {
            throw new RuntimeException(sprintf('Dir %s do not have permission.', $sDir));
        }
        file_put_contents($sCacheFile, '<?php /* ' . date('Y-m-d H:i:s') . ' */ ?>' . PHP_EOL . php_strip_whitespace($sCacheFile));

        return $this->arrLoad = $arrFiles;
    }

    /**
     * 分析目录中的 PHP 命令包包含的文件
     *
     * @param array $arrNamespace
     * @return array
     */
    public function findConsoleFile(array $arrNamespace)
    {
        $arrFiles = [ ];
        foreach ($arrNamespace as $sNamespace => $sDir) {
            if (! is_dir($sDir)) {
                continue;
            }

            $arrFiles = array_merge($arrFiles, array_map(function ($strItem) use ($sNamespace) {
                return $sNamespace . '\\' . basename($strItem, '.php');
            }, fso::lists($sDir, 'file', false, [ ], [
                    'php'
            ])));
        }

        return $arrFiles;
    }
}

<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\filesystem;

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

use DirectoryIterator;

/**
 * 文件夹
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.04.05
 * @version 1.0
 */
class directory {
    
    /**
     * 创建目录
     *
     * @param string $sDir            
     * @param number $nMode            
     * @return void|true
     */
    public static function create($sDir, $nMode = 0777) {
        if (is_dir ( $sDir )) {
            return;
        }
        
        if (is_string ( $sDir )) {
            $sDir = explode ( '/', str_replace ( '\\', '/', trim ( $sDir, '/' ) ) );
        }
        
        $sCurDir = DIRECTORY_SEPARATOR == '\\' ? '' : '/';
        
        foreach ( $sDir as $nKey => $sTemp ) {
            $sCurDir .= $sTemp . '/';
            if (! is_dir ( $sCurDir )) {
                if (isset ( $sDir [$nKey + 1] ) && is_dir ( $sCurDir . $sDir [$nKey + 1] )) {
                    continue;
                }
                @mkdir ( $sCurDir, $nMode );
            }
        }
        
        return true;
    }
    
    /**
     * 删除目录
     *
     * @param string $sDir            
     * @param boolean $bRecursive            
     * @return void
     */
    public static function delete($sDir, $bRecursive = false) {
        if (! file_exists ( $sDir ) || ! is_dir ( $sDir ))
            return;
        
        if (! $bRecursive) {
            rmdir ( $sDir );
        } else {
            $objDir = new DirectoryIterator ( $sDir );
            foreach ( $objDir as $objFile ) {
                if ($objFile->isDot ())
                    continue;
                
                if ($objFile->isFile ()) {
                    if (! unlink ( $objFile->getRealPath () ))
                        return;
                } 

                elseif ($objFile->isDir ()) {
                    static::delete ( $objFile->getRealPath (), $bRecursive );
                }
            }
            rmdir ( $sDir );
        }
    }
    
    /**
     * 复制目录
     *
     * @param string $sSourcePath            
     * @param string $sTargetPath            
     * @param array $arrFilter            
     * @return void
     */
    public static function copy($sSourcePath, $sTargetPath, $arrFilter = []) {
        $arrFilter = array_merge ( [ 
                '.svn',
                '.git',
                'node_modules',
                '.gitkeep' 
        ], $arrFilter );
        
        if (! is_dir ( $sSourcePath )) {
            return;
        }
        if (file_exists ( $sTargetPath )) {
            return;
        }
        
        $objDir = new DirectoryIterator ( $sSourcePath );
        foreach ( $objDir as $objFile ) {
            if ($objFile->isDot () || in_array ( $objFile->getFilename (), $arrFilter ))
                continue;
            
            $sNewPath = $sTargetPath . '/' . $objFile->getFilename ();
            
            if ($objFile->isFile ()) {
                if (! is_dir ( $sNewPath )) {
                    static::create ( dirname ( $sNewPath ) );
                }
                if (! copy ( $objFile->getRealPath (), $sNewPath )) {
                    return;
                }
            } 

            elseif ($objFile->isDir ()) {
                if (! static::copy ( $objFile->getRealPath (), $sNewPath )) {
                    return;
                }
            }
        }
    }
    
    /**
     * 只读取一级目录
     *
     * @param string $sDir            
     * @param
     *            string strReturnType
     * @param boolean $booFullpath            
     * @param array $arrFilter            
     * @return array
     */
    public static function lists($sDir, $strReturnType = 'dir', $booFullpath = false, $arrFilter = [], $arrFilterExt = []) {
        $arrFilter = array_merge ( [ 
                '.svn',
                '.git',
                'node_modules',
                '.gitkeep' 
        ], $arrFilter );
        
        $arrReturnData = [ 
                'file' => [ ],
                'dir' => [ ] 
        ];
        
        if (is_dir ( $sDir )) {
            $arrFiles = [ ];
            
            $objDir = new DirectoryIterator ( $sDir );
            foreach ( $objDir as $objFile ) {
                if ($objFile->isDot () || in_array ( $objFile->getFilename (), $arrFilter ))
                    continue;
                
                if ($objFile->isDir () && in_array ( $strReturnType, [ 
                        'dir',
                        'both' 
                ] )) {
                    $arrReturnData ['dir'] [] = $booFullpath ? $objFile->getRealPath () : $objFile->getFilename ();
                }
                
                if ($objFile->isFile () && in_array ( $strReturnType, [ 
                        'file',
                        'both' 
                ] ) && (! $arrFilterExt || ! in_array ( file::getExtName ( $objFile->getFilename (), 2 ), $arrFilterExt ))) {
                    $arrReturnData ['file'] [] = $booFullpath ? $objFile->getRealPath () : $objFile->getFilename ();
                }
            }
            
            if ($strReturnType == 'file') {
                return $arrReturnData ['file'];
            } elseif ($strReturnType == 'dir') {
                return $arrReturnData ['dir'];
            } else {
                return $arrReturnData;
            }
        } else {
            return [ ];
        }
    }
    
    /**
     * 整理目录斜线风格
     *
     * @param string $sPath            
     * @param boolean $bUnix            
     * @return string
     */
    public static function tidyPath($sPath, $bUnix = true) {
        $sRetPath = str_replace ( '\\', '/', $sPath ); // 统一 斜线方向
        $sRetPath = preg_replace ( '|/+|', '/', $sRetPath ); // 归并连续斜线
        
        $arrDirs = explode ( '/', $sRetPath ); // 削除 .. 和 .
        $arrDirsTemp = [ ];
        while ( ($sDirName = array_shift ( $arrDirs )) !== null ) {
            if ($sDirName == '.') {
                continue;
            }
            
            if ($sDirName == '..') {
                if (count ( $arrDirsTemp )) {
                    array_pop ( $arrDirsTemp );
                    continue;
                }
            }
            
            array_push ( $arrDirsTemp, $sDirName );
        }
        
        $sRetPath = implode ( '/', $arrDirsTemp ); // 目录 以 '/' 结尾
        if (@is_dir ( $sRetPath )) { // 存在的目录
            if (! preg_match ( '|/$|', $sRetPath )) {
                $sRetPath .= '/';
            }
        } else if (preg_match ( "|\.$|", $sPath )) { // 不存在，但是符合目录的格式
            if (! preg_match ( '|/$|', $sRetPath )) {
                $sRetPath .= '/';
            }
        }
        
        $sRetPath = str_replace ( ':/', ':\\', $sRetPath ); // 还原 驱动器符号
        if (! $bUnix) { // 转换到 Windows 斜线风格
            $sRetPath = str_replace ( '/', '\\', $sRetPath );
        }
        
        $sRetPath = rtrim ( $sRetPath, '\\/' ); // 删除结尾的“/”或者“\”
        
        return $sRetPath;
    }
    
    /**
     * 判断是否为绝对路径
     *
     * @param string $sPath            
     * @return boolean
     */
    public static function isAbsolute($sPath) {
        return preg_match ( '/^(\/|[a-z]:)/i', $sPath );
    }
    
    /**
     * 格式化文件或者目录为 Linux 风格
     *
     * @param string $strPath            
     * @param boolean $booWindowsWithLetter            
     * @return string
     */
    public static function tidyPathLinux($strPath, $booWindowsWithLetter = false) {
        $strPath = ltrim ( static::tidyPath ( $strPath, true ), '//' );
        if (strpos ( $strPath, ':\\' ) !== false) {
            $arrTemp = explode ( ':\\', $strPath );
            $strPath = ($booWindowsWithLetter === true ? strtolower ( $arrTemp [0] ) . '/' : '') . $arrTemp [1];
        }
        return '/' . $strPath;
    }
    
    /**
     * 根据 ID 获取打散目录
     *
     * @param int $intDataId            
     * @return array
     */
    public static function distributed($intDataId) {
        $intDataId = abs ( intval ( $intDataId ) );
        $intDataId = sprintf ( "%09d", $intDataId ); // 格式化为 9 位数，前面不够填充 0
        return [ 
                substr ( $intDataId, 0, 3 ) . '/' . substr ( $intDataId, 3, 2 ) . '/' . substr ( $intDataId, 5, 2 ) . '/',
                substr ( $intDataId, - 2 ) 
        ];
    }
}

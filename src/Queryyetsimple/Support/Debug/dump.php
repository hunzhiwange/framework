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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\support\debug;

use Symfony\Component\VarDumper\{
    Dumper\CliDumper,
    Cloner\VarCloner,
    Dumper\HtmlDumper
};

/**
 * 调试一个变量
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.05
 * @version 1.0
 */
class dump
{

    /**
     * 调试一个变量
     *
     * @param mixed $mixValue
     * @param boolean $booSimple
     * @return void|string
     */
    public static function dump($mixValue, $booSimple = false)
    {
        static $objDump, $objVarCloner;
        if ($booSimple === false && class_exists(CliDumper::class)) {
            if (! $objDump) {
                $objDump = ('cli' === PHP_SAPI ? new CliDumper() : new HtmlDumper());
                $objVarCloner = new VarCloner();
            }
            $objDump->dump($objVarCloner->cloneVar($mixValue));
        } else {
            $arrArgs = func_get_args();
            array_shift($arrArgs);
            array_shift($arrArgs);
            array_unshift($arrArgs, $mixValue);
            return call_user_func_array([
                'queryyetsimple\support\debug\dump',
                'varDump'
            ], $arrArgs);
        }
    }

    /**
     * 调试变量
     *
     * @param mixed $Var
     * @param boolean $bEcho
     * @return mixed
     */
    public static function varDump($mixVar, $bEcho = true)
    {
        ob_start();
        var_dump($mixVar);
        $sOutput = ob_get_clean();
        if (! extension_loaded('xdebug')) {
            $sOutput = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $sOutput);
            $sOutput = 'cli' === PHP_SAPI ? $sOutput : '<pre>' . htmlspecialchars($sOutput, ENT_COMPAT, 'UTF-8') . '</pre>';
        }

        if ($bEcho) {
            echo $sOutput;
        } else {
            return $sOutput;
        }
    }
}

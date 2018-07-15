<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\View;

/**
 * phpui 模板处理类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.11.21
 *
 * @version 1.0
 */
class Phpui extends Connect implements IConnect
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'controller_name'       => 'index',
        'action_name'           => 'index',
        'controlleraction_depr' => '_',
        'theme_name'            => 'default',
        'theme_path'            => '',
        'theme_path_default'    => '',
        'suffix'                => '.php',
    ];

    /**
     * 加载视图文件.
     *
     * @param string $file    视图文件地址
     * @param array  $vars
     * @param string $ext     后缀
     * @param bool   $display 是否显示
     *
     * @return string
     */
    public function display(?string $file = null, array $vars = [], ?string $ext = null, bool $display = true)
    {
        // 加载视图文件
        $file = $this->parseDisplayFile($file, $ext);

        // 变量赋值
        if ($vars) {
            $this->setVar($vars);
        }

        if (is_array($this->vars) && !empty($this->vars)) {
            extract($this->vars, EXTR_PREFIX_SAME, 'q_');
        }

        // 返回类型
        if (false === $display) {
            ob_start();
            include $file;
            $result = ob_get_contents();
            ob_end_clean();

            return $result;
        }

        include $file;
    }
}

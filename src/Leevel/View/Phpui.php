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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\View;

/**
 * phpui 模板处理类.
 */
class Phpui extends View implements IView
{
    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'theme_path'            => '',
        'suffix'                => '.php',
    ];

    /**
     * 加载视图文件.
     */
    public function display(string $file, array $vars = [], ?string $ext = null): string
    {
        $file = $this->parseDisplayFile($file, $ext);

        if ($vars) {
            $this->setVar($vars);
        }
        if (is_array($this->vars) && !empty($this->vars)) {
            extract($this->vars, EXTR_PREFIX_SAME, 'q_');
        }

        ob_start();
        include $file;
        $result = ob_get_contents() ?: '';
        ob_end_clean();

        return $result;
    }
}

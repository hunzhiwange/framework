<?php declare(strict_types=1);
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
namespace Leevel\Bootstrap\Bootstrap;

use Leevel\I18n\Load;
use Leevel\I18n\I18n;
use Leevel\Support\Facade;
use Leevel\Bootstrap\Project;

/**
 * 读取语言包
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.05.03
 * @version 1.0
 */
class LoadI18n
{

    /**
     * 响应
     * 
     * @param \Leevel\Bootstrap\IProject $project
     * @return void
     */
    public function handle(Project $project)
    {
        $i18nDefault = $project['option']['i18n\default'];

        if ($project->isCachedI18n($i18nDefault)) {
            $data = (array) include $project->pathCacheI18nFile($i18nDefault);
        } else {
            $load = (new Load([$project->pathI18n()]))->setI18n($i18nDefault);

            $data = $load->loadData();
        }

        
        $project->instance('i18n', $i18n = new I18n($i18nDefault));

        $i18n->addText($i18nDefault, $data);
    }
}

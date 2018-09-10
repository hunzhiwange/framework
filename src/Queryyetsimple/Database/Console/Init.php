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

namespace Leevel\Database\Console;

use InvalidArgumentException;
use Phinx\Console\Command\Init as PhinxInit;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 数据库迁移初始化.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.09
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Init extends PhinxInit
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('migrate:init');
    }

    /**
     * Initializes the application.
     * 重写读取配置文件，个性化配置，例外默认配置文件有一个解析 BUG.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get the migration path from the config
        $path = $input->getArgument('path');

        if (null === $path) {
            $path = getcwd();
        }

        $path = realpath($path);

        if (!is_writable($path)) {
            throw new InvalidArgumentException(
                sprintf('The directory "%s" is not writable', $path)
            );
        }

        // Compute the file path
        // TODO - maybe in the future we allow custom config names.
        $fileName = 'phinx.yml';
        $filePath = $path.DIRECTORY_SEPARATOR.$fileName;

        if (file_exists($filePath)) {
            throw new InvalidArgumentException(
                sprintf('The file "%s" already exists', $filePath)
            );
        }

        // load the config template
        // 自定义 migrate.yml 文件
        $contents = file_get_contents(__DIR__.'/yml/migrate.yml');

        if (!is_writable(dirname($filePath)) ||
            file_put_contents($filePath, $contents)) {
            throw new RuntimeException(
                sprintf('The file "%s" could not be written to', $path)
            );
        }

        $output->writeln('<info>created</info> .'.str_replace(getcwd(), '', $filePath));
    }
}

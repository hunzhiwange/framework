<?php

declare(strict_types=1);

namespace Leevel\I18n\Console;

use Leevel\Config\IConfig;
use Leevel\Console\Command;
use Leevel\I18n\GettextGenerator;
use Leevel\Kernel\IApp;

/**
 * 语言包生成.
 */
class Generate extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'i18n:generate';

    /**
     * 命令行描述.
     */
    protected string $description = 'Generate i18n file';

    /**
     * 响应命令.
     */
    public function handle(IApp $app, IConfig $config): int
    {
        $this->line('Start to generate i18n file.');

        $gettextGenerator = new GettextGenerator();
        $generatedLanguageFiles = $gettextGenerator->generatorPoFiles(
            $config->get(':composer.i18n-paths', []),
            // @phpstan-ignore-next-line
            explode(',', $config->get('console\\app_i18n')),
            $app->path('assets/i18n')
        );

        foreach ($generatedLanguageFiles as $namespace => $files) {
            foreach ($files as $file) {
                $this->info(sprintf('I18n file %s generate succeed at %s.', $namespace, $file));
            }
        }

        $this->info('I18n file generate succeed.');

        return self::SUCCESS;
    }
}

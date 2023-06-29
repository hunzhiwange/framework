<?php

declare(strict_types=1);

namespace Leevel\I18n\Console;

use Leevel\Console\Command;
use Leevel\I18n\GettextGenerator;
use Leevel\Kernel\IApp;
use Leevel\Option\IOption;

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
    public function handle(IApp $app, IOption $option): int
    {
        $this->line('Start to generate i18n file.');

        $gettextGenerator = new GettextGenerator();
        $generatedLanguageFiles = $gettextGenerator->generatorPoFiles(
            $option->get(':composer.i18n-paths', []),
            // @phpstan-ignore-next-line
            explode(',', $option->get('console\\app_i18n')),
            $app->path('assets/i18n')
        );

        foreach ($generatedLanguageFiles as $namespace => $files) {
            foreach ($files as $file) {
                $this->info(sprintf('I18n file %s generate successed at %s.', $namespace, $file));
            }
        }

        $this->info('I18n file generate successed.');

        return self::SUCCESS;
    }
}

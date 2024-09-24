<?php

declare(strict_types=1);

namespace Leevel\Database\Migrations;

use Leevel\Database\Console\Migrate;
use Leevel\Database\Console\SeedRun;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('Migrations by QueryPHP');

        $this->addCommands([
            new Migrate(),
            new SeedRun(),
        ]);
    }
}

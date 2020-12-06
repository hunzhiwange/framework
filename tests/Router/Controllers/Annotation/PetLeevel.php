<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Annotation;

use Leevel\Di\IContainer;

/**
 * petLeevel.
 */
class PetLeevel
{
    public function handle(IContainer $container): string
    {
        return 'hello plus for petLeevel, attributes petId is '.
            $container
                ->make('request')
                ->attributes
                ->get('petId');
    }
}

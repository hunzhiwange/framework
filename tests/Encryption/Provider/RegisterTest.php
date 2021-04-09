<?php

declare(strict_types=1);

namespace Tests\Encryption\Provider;

use Leevel\Di\Container;
use Leevel\Encryption\IEncryption;
use Leevel\Encryption\Provider\Register;
use Leevel\Option\Option;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());

        $test->register();

        $container->alias($test->providers());

        $encryption = $container->make('encryption');

        $this->assertInstanceof(IEncryption::class, $encryption);

        $sourceMessage = '123456';

        $encodeMessage = $encryption->encrypt($sourceMessage);

        $this->assertFalse($sourceMessage === $encodeMessage);

        $this->assertSame(
            $encryption->decrypt($encodeMessage),
            $sourceMessage
        );

        $this->assertSame(
            $encryption->decrypt($encodeMessage.'foo'),
            false
        );
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'app' => [
                'auth_key'         => '7becb888f518b20224a988906df51e05',
                'auth_cipher'      => 'AES-256-CBC',
                'auth_rsa_private' => '',
                'auth_rsa_public'  => '',
            ],
        ]);

        $container->singleton('option', $option);

        return $container;
    }
}

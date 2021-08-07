<?php

namespace Wmandai\Mpesa\Tests;

use Dotenv\Dotenv;
use Wmandai\Mpesa\DarajaServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        if (phpversion() > 7.2) {
            $this->loadEnvironmentVariables();
        }
    }
    protected function getPackageProviders($app)
    {
        return [DarajaServiceProvider::class];
    }

    protected function loadEnvironmentVariables()
    {
        if (!file_exists(__DIR__ . '/../.env')) {
            return;
        }

        $dotEnv = Dotenv::createImmutable(__DIR__ . '/..');

        $dotEnv->load();
    }
}

<?php

namespace Wmandai\Mpesa\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Wmandai\Mpesa\DarajaServiceProvider;

class InstallMpesaPackage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpesa:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Daraja MPESA package.';

    public function __construct()
    {
        parent::__construct();

        if (file_exists(config_path('mpesa.php'))) {
            $this->setHidden(true);
        }
    }

    public function handle()
    {
        $this->info('Installing Daraja MPESA API...');

        $this->info('Publishing configuration...');

        if (!$this->configExists('mpesa.php')) {
            $this->publishConfiguration();
            $this->info('Published configuration');
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->info('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->error('Existing configuration was not overwritten');
            }
        }

        $this->info('Installed Daraja MPESA API Package 👏');
    }

    private function configExists($fileName)
    {
        return File::exists(config_path($fileName));
    }

    private function shouldOverwriteConfig()
    {
        return $this->confirm('MPESA Config file already exists. Do you want to overwrite it?');
    }

    private function publishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => DarajaServiceProvider::class,
            '--tag' => "config",
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }
}

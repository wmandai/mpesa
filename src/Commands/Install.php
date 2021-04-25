<?php
/**
 * @author William Mandai <wm@gitbench.com>
 */

namespace Wmandai\Mpesa\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Wmandai\Mpesa\LaravelMpesaServiceProvider;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'mpesa:install';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Install the MPESA config file into your app.';

    public function __construct()
    {
        parent::__construct();

        if (file_exists(config_path('mpesa.php'))) {
            $this->setHidden(true);
        }
    }

    /**
     * @throws Exception
     */
    public function handle()
    {
        Artisan::call("vendor:publish", [
            '--provider' => LaravelMpesaServiceProvider::class,
        ]);

        $this->info('Config file published!');
    }

}

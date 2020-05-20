<?php
namespace Wmandai\Mpesa\Commands;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Wmandai\Mpesa\Library\RegisterUrl;

class RegisterUrlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpesa:register_url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register mpesa validation and confirmation URL';

    protected $registerUrl;

    /**
     * Create a new command instance.
     *
     * @param mixed $registerUrl \Wmandai\Mpesa\Library\RegisterUrl
     */
    public function __construct(RegisterUrl $registerUrl)
    {
        parent::__construct();
        $this->registerUrl = $registerUrl;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws GuzzleException
     * @throws \Exception
     */
    public function handle()
    {
        $this->registerUrl->register($this->askShortcode())
            ->onConfirmation($this->askConfirmationUrl())
            ->onValidation($this->askValidationUrl())
            ->submit();
        $this->info('URLs registered succesfully');
    }

    protected function askShortcode(): string
    {
        return $this->ask('What is your shortcode', config('mpesa.c2b.shortcode'));
    }

    protected function askConfirmationUrl(): string
    {
        return $this->ask('Confirmation Url', config('mpesa.c2b.confirmation_url'));
    }

    protected function askValidationUrl(): string
    {
        return $this->ask('Validation Url', config('mpesa.c2b.validation_url'));
    }
}

<?php

namespace Wmandai\Mpesa\Console;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Wmandai\Mpesa\Daraja;

class DarajaRegisterUrl extends Command
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

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mpesa = new Daraja();
        $mpesa->shortCode = $this->askShortCode();
        $mpesa->confirmationUrl = $this->askConfirmationUrl();
        $mpesa->validationUrl = $this->askValidationUrl();
        $mpesa->register();
        $this->info('URLs registered succesfully');
    }

    protected function askShortcode(): string
    {
        return $this->ask('What is your shortcode', config('mpesa.c2b.shortcode'));
    }

    protected function askConfirmationUrl(): string
    {
        return $this->ask('Confirmation Url', config('mpesa.confirmation_url'));
    }

    protected function askValidationUrl(): string
    {
        return $this->ask('Validation Url', config('mpesa.validation_url'));
    }
}

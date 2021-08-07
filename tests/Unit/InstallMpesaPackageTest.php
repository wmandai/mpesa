<?php

namespace Wmandai\Mpesa\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Wmandai\Mpesa\Tests\TestCase;

class InstallMpesaPackageTest extends TestCase
{
    /** @test */
    public function the_install_command_copies_the_configuration()
    {
        // make sure we're starting from a clean state
        if (File::exists(config_path('mpesa.php'))) {
            unlink(config_path('mpesa.php'));
        }

        $this->assertFalse(File::exists(config_path('mpesa.php')));

        Artisan::call('mpesa:install');

        $this->assertTrue(File::exists(config_path('mpesa.php')));
    }
    /** @test */
    public function when_a_config_file_is_present_users_can_choose_to_not_overwrite_it()
    {
        // Given we already have an existing config file
        File::put(config_path('mpesa.php'), '<?php return []; ?>');
        $this->assertTrue(File::exists(config_path('mpesa.php')));

        // We expect a warning that our configuration file exists
        if (app()->version() >= 7) {
            // We should see a message that our file was not overwritten
            $this->artisan('mpesa:install')
                ->expectsConfirmation(
                    'MPESA Config file already exists. Do you want to overwrite it?',
                    // When answered with "no"
                    'no'
                )
                ->expectsOutput('Existing configuration was not overwritten')
                ->execute();
        } else {
            $this->artisan('mpesa:install')
                ->expectsQuestion(
                    'MPESA Config file already exists. Do you want to overwrite it?',
                    false
                )
                ->expectsOutput('Existing configuration was not overwritten')
                ->execute();
        }

        // Assert that the original contents of the config file remain
        $this->assertEquals('<?php return []; ?>', file_get_contents(config_path('mpesa.php')));

        // Clean up
        unlink(config_path('mpesa.php'));
    }

    /**
     * @test
     * */
    public function when_a_config_file_is_present_users_can_choose_to_do_overwrite_it()
    {
        // Given we have already have an existing config file
        File::put(config_path('mpesa.php'), '<?php return []; ?>');
        $this->assertTrue(File::exists(config_path('mpesa.php')));

        if (app()->version() >= 7) {
            $this->artisan('mpesa:install')
                ->expectsConfirmation(
                    'MPESA Config file already exists. Do you want to overwrite it?',
                    // When answered with "yes"
                    'yes'
                )
                ->expectsOutput('Overwriting configuration file...')
                ->execute();
        } else {
            $this->artisan('mpesa:install')
                ->expectsQuestion(
                    'MPESA Config file already exists. Do you want to overwrite it?',
                    'yes'
                )
                ->expectsOutput('Overwriting configuration file...')
                ->execute();
        }

        // Assert that the original contents are overwritten
        $this->assertEquals(
            file_get_contents(__DIR__ . '/../../config/config.php'),
            file_get_contents(config_path('mpesa.php'))
        );

        // Clean up
        unlink(config_path('mpesa.php'));
    }
}

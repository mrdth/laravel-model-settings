<?php

namespace Mrdth\LaravelModelSettings\Commands;

use Illuminate\Console\Command;

class LaravelModelSettingsCommand extends Command
{
    public $signature = 'laravel-model-settings';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

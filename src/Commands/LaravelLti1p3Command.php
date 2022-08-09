<?php

namespace Wien\LaravelLti1p3\Commands;

use Illuminate\Console\Command;

class LaravelLti1p3Command extends Command
{
    public $signature = 'laravel-lti1p3';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

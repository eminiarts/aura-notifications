<?php

namespace Aura\Notifications\Commands;

use Illuminate\Console\Command;

class NotificationsCommand extends Command
{
    public $description = 'My command';

    public $signature = 'notifications';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

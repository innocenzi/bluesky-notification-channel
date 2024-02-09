<?php

namespace Innocenzi\BlueskyNotificationChannel\Commands;

use Illuminate\Console\Command;

class BlueskyNotificationChannelCommand extends Command
{
    public $signature = 'bluesky-notification-channel';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

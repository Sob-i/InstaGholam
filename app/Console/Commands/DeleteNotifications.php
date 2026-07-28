<?php

namespace App\Console\Commands;

use App\Models\notifications\notificationModel;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffTime = Carbon::now()->subDays(7);

        $deleteNotifs = notificationModel::where('created_at', '<', $cutoffTime)->delete();

        $message = "{$deleteNotifs} notifications deleted that were older than 7 days.";

        $this->info($message);

        \Illuminate\Log\log($message);

        return Command::SUCCESS;
    }

}

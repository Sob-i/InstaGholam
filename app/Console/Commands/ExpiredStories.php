<?php

namespace App\Console\Commands;

use App\Models\story\storyModel;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpiredStories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-stories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Closes stories that are expired after 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffTime = Carbon::now();

        $expiredCount = storyModel::where('expires_at', '<' , $cutoffTime)->update([
            'status' => 'archived'
        ]);

        $this->info("{$expiredCount} stories expired that were there for 24+ hours.");

        return Command::SUCCESS;
    }
}

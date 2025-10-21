<?php

namespace App\Console\Commands;

use App\Http\Controllers\ProcessDealerFeedController;
use App\Models\User;
use Illuminate\Console\Command;

class ProcessDealerFeedCommand extends Command
{
    protected $signature = 'dealer:process-feed';
    protected $description = 'Process data feeds for all dealers';

    public function handle()
    {
         $dealers = User::whereNotNull('dealer_id')
                       ->where('source_type', 'feed')
                       ->get();
        $controller = new ProcessDealerFeedController();

        foreach ($dealers as $dealer) {
            $this->info("Processing feed for DealerID: {$dealer->dealer_id}");
            $response = $controller->process($dealer->dealer_id);
            if ($response->getStatusCode() === 200) {
                $this->info("Successfully processed feed for DealerID: {$dealer->dealer_id}");
            } else {
                $this->error("Failed to process feed for DealerID: {$dealer->dealer_id}");
            }
        }
    }
}
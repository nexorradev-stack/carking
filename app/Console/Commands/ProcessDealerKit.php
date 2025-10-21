<?php

namespace App\Console\Commands;

use App\Http\Controllers\DealerKitController;
use App\Models\User;
use Illuminate\Console\Command;

class ProcessDealerKit  extends Command
{
    protected $signature = 'dealer:process-api';
    protected $description = 'Process data feeds for all dealers';

    public function handle()
    {
        $dealers = User::whereNotNull('dealer_id')
                       ->where('source_type', 'api')
                       ->get();
        $controller = new DealerKitController();

        foreach ($dealers as $dealer) {
            $this->info("Processing feed for DealerID: {$dealer->dealer_id}");
            $response = $controller->fetchVehicles($dealer->dealer_id);
            if ($response->getStatusCode() === 200) {
                $this->info("Successfully processed cars for DealerID: {$dealer->dealer_id}");
            } else {
                $this->error("Failed to process cars for DealerID: {$dealer->dealer_id}");
            }
        }
    }
}
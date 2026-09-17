<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PaymentService;

class ReleaseExpiredHolds extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:release-holds';

    /**
     * The console command description.
     */
    protected $description = 'Release court slots that have passed their 10-minute payment hold window';

    /**
     * Execute the console command.
     */
    public function handle(PaymentService $paymentService)
    {
        $releasedCount = $paymentService->releaseExpiredHolds();

        if ($releasedCount > 0) {
            $this->info("Successfully released {$releasedCount} expired court holds.");
        } else {
            $this->info("No expired holds to release.");
        }
    }
}
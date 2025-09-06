<?php

namespace App\Console\Commands;

use App\Services\ReservationService;
use Illuminate\Console\Command;

class CleanupExpiredReservations extends Command
{
    protected $signature = 'reservations:cleanup-expired';
    protected $description = 'Mark expired stock reservations as expired';

    public function handle(ReservationService $service): int
    {
        $count = $service->cleanupExpired();
        $this->info("Expired reservations marked: {$count}");
        return self::SUCCESS;
    }
}

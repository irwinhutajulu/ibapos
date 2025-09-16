<?php

namespace App\Services;

use App\Models\InvoiceCounter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceGenerator
{
    /**
     * Generate next invoice number for given type and location.
     * Format: IBA-{location_id}-{YYYYMMDD}-{000001}
     */
    public static function next(string $type, ?int $locationId = null): string
    {
        $today = Carbon::now()->toDateString(); // YYYY-MM-DD

        return DB::transaction(function () use ($type, $locationId, $today) {
            $counter = InvoiceCounter::where('type', $type)
                ->where('location_id', $locationId)
                ->where('date', $today)
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = InvoiceCounter::create([
                    'type' => $type,
                    'location_id' => $locationId,
                    'last_number' => 0,
                    'date' => $today,
                ]);
            }

            $counter->last_number = $counter->last_number + 1;
            $counter->save();

            $seq = str_pad((string)$counter->last_number, 6, '0', STR_PAD_LEFT);
            $loc = $locationId ?? 0;
            $ymd = Carbon::parse($today)->format('Ymd');

            return "IBA-{$loc}-{$ymd}-{$seq}";
        });
    }
}

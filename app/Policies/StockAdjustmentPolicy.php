<?php

namespace App\Policies;

use App\Models\StockAdjustment;
use App\Models\User;

class StockAdjustmentPolicy
{
    /**
     * Only allow posting if status is draft.
     */
    public function post(User $user, StockAdjustment $adjustment)
    {
        return $adjustment->status === 'draft';
    }

    /**
     * Only allow voiding if status is posted.
     */
    public function void(User $user, StockAdjustment $adjustment)
    {
        return $adjustment->status === 'posted';
    }
}

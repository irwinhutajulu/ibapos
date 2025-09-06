<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    /**
     * Only allow posting if status is draft.
     */
    public function post(User $user, Sale $sale)
    {
        return $sale->status === 'draft';
    }

    /**
     * Only allow voiding if status is posted.
     */
    public function void(User $user, Sale $sale)
    {
        return $sale->status === 'posted';
    }
}

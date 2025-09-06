<?php

namespace App\Policies;

use App\Models\Purchase;
use App\Models\User;

class PurchasePolicy
{
    /**
     * Only allow editing if status is draft.
     */
    public function update(User $user, Purchase $purchase)
    {
        return $purchase->status === 'draft';
    }

    /**
     * Only allow posting if status is received.
     */
    public function post(User $user, Purchase $purchase)
    {
        return $purchase->status === 'received';
    }

    /**
     * Only allow voiding if status is posted.
     */
    public function void(User $user, Purchase $purchase)
    {
        return $purchase->status === 'posted';
    }

    /**
     * Only allow receiving if status is draft.
     */
    public function receive(User $user, Purchase $purchase)
    {
        return $purchase->status === 'draft';
    }
}

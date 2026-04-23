<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class ReleasePayouts extends Command
{
    protected $signature   = 'orders:release-payouts';
    protected $description = 'Release held payouts where the 48-hour dispute window has passed';

    public function handle(): void
    {
        $released = Order::where('status', 'delivered')
            ->where('payout_status', 'held')
            ->where('payout_due_at', '<=', now())
            ->update([
                'payout_status'      => 'released',
                'payout_released_at' => now(),
            ]);

        $this->info("Released {$released} payout(s).");
    }
}

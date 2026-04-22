<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderPlacedNotification implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->load(['buyer', 'seller', 'items']);

        // Notify buyer
        Mail::to($order->buyer->email)->send(new \App\Mail\OrderPlacedBuyer($order));

        // Notify seller
        Mail::to($order->seller->email)->send(new \App\Mail\OrderPlacedSeller($order));
    }
}

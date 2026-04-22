<?php

namespace App\Listeners;

use App\Events\SellerApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendSellerApprovedNotification implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(SellerApproved $event): void
    {
        Mail::to($event->seller->email)->send(new \App\Mail\SellerApprovedMail($event->seller));
    }
}

<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\GenerateAnalyticsJob;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\SendEmailJob;
use App\Jobs\SyncERPJob;

class DispatchOrderFulfillmentJobs
{
    public function handle(OrderPlaced $event): void
    {
        $orderId = $event->order->id;

        GenerateInvoiceJob::dispatch($orderId)->onQueue('invoices');
        SyncERPJob::dispatch($orderId)->onQueue('erp');
        GenerateAnalyticsJob::dispatch($orderId)->onQueue('analytics');
        SendEmailJob::dispatch($orderId)->onQueue('emails');
    }
}

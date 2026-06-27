<?php

namespace App\Services\Demo;

use App\Jobs\GenerateAnalyticsJob;
use App\Jobs\GenerateInvoiceJob;
use App\Jobs\SendEmailJob;
use App\Jobs\SyncERPJob;
use App\Models\Order;

class JobsDemoService
{
    /**
     * @return array<string, mixed>
     */
    public function dispatchFulfillmentJobs(Order $order): array
    {
        GenerateInvoiceJob::dispatch($order->id)->onQueue('invoices');
        SyncERPJob::dispatch($order->id)->onQueue('erp');
        GenerateAnalyticsJob::dispatch($order->id)->onQueue('analytics');
        SendEmailJob::dispatch($order->id)->onQueue('emails');

        return [
            'order_id' => $order->id,
            'jobs' => [
                'GenerateInvoiceJob',
                'SyncERPJob',
                'GenerateAnalyticsJob',
                'SendEmailJob',
            ],
            'queues' => ['invoices', 'erp', 'analytics', 'emails'],
        ];
    }
}

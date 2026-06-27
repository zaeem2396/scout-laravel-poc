<?php

namespace App\Listeners;

use App\Events\DashboardProbeEvent;
use App\Support\ObservabilityMetricsCollector;

class RecordDashboardProbeListener
{
    public function __construct(
        private readonly ObservabilityMetricsCollector $metrics,
    ) {}

    public function handle(DashboardProbeEvent $event): void
    {
        $this->metrics->incrementListenerInvocations();
    }
}

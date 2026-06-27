<?php

namespace App\Services\Demo;

use App\Repositories\DemoRepository;

class RequestLifecycleService
{
    public function __construct(
        private readonly DemoRepository $demoRepository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $productCount = $this->demoRepository->countActiveProducts();
        $summaries = $this->demoRepository->latestActiveProductSummaries();

        return [
            'active_products' => $productCount,
            'sample_products' => $summaries->toArray(),
        ];
    }
}

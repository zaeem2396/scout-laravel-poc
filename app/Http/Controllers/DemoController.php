<?php

namespace App\Http\Controllers;

use App\Exceptions\ObservabilityDemoException;
use App\Http\Requests\FullFlowDemoRequest;
use App\Models\Product;
use App\Services\Demo\CacheDemoService;
use App\Services\Demo\DemoOrderResolver;
use App\Services\Demo\EventsDemoService;
use App\Services\Demo\FullFlowDemoService;
use App\Services\Demo\JobsDemoService;
use App\Services\Demo\NPlusOneDemoService;
use App\Services\Demo\RequestLifecycleService;
use App\Services\Demo\SlowQueryDemoService;
use App\Services\RecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DemoController extends Controller
{
    public function __construct(
        private readonly RequestLifecycleService $requestLifecycle,
        private readonly NPlusOneDemoService $nPlusOne,
        private readonly SlowQueryDemoService $slowQuery,
        private readonly RecommendationService $recommendations,
        private readonly CacheDemoService $cacheDemo,
        private readonly EventsDemoService $eventsDemo,
        private readonly JobsDemoService $jobsDemo,
        private readonly FullFlowDemoService $fullFlow,
        private readonly DemoOrderResolver $orderResolver,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json([
            'demos' => [
                'request' => route('demo.request'),
                'n_plus_one' => route('demo.n-plus-one'),
                'slow_query' => route('demo.slow-query'),
                'slow_method' => route('demo.slow-method'),
                'cache' => route('demo.cache'),
                'events' => route('demo.events'),
                'jobs' => route('demo.jobs'),
                'memory' => route('demo.memory'),
                'exception' => route('demo.exception'),
                'dashboard' => route('demo.dashboard'),
                'full_flow' => route('demo.full-flow'),
            ],
        ]);
    }

    public function request(Request $request): JsonResponse
    {
        return response()->json([
            'tenant_id' => $request->attributes->get('tenant_id'),
            'feature_flags' => $request->attributes->get('feature_flags'),
            'result' => $this->requestLifecycle->execute(),
        ]);
    }

    public function nPlusOne(Request $request): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 100), 1), 200);

        return response()->json([
            'limit' => $limit,
            'products' => $this->nPlusOne->loadProductsWithRelations($limit),
        ]);
    }

    public function slowQuery(): JsonResponse
    {
        return response()->json($this->slowQuery->execute());
    }

    public function slowMethod(): JsonResponse
    {
        $productIds = Product::query()
            ->where('is_active', true)
            ->limit(5)
            ->pluck('id')
            ->all();

        $scores = $this->recommendations->scoreProducts($productIds);

        return response()->json([
            'product_ids' => $productIds,
            'scores' => $scores,
        ]);
    }

    public function cache(Request $request): JsonResponse
    {
        if ($request->boolean('reset')) {
            $this->cacheDemo->reset();
        }

        return response()->json($this->cacheDemo->resolveCategoryCounts());
    }

    public function events(Request $request): JsonResponse
    {
        $user = $request->user();
        $order = $user !== null
            ? $this->orderResolver->resolveForUser($user)
            : $this->orderResolver->resolveAny();

        return response()->json($this->eventsDemo->dispatchOrderPlacedListeners($order));
    }

    public function jobs(Request $request): JsonResponse
    {
        $user = $request->user();
        $order = $user !== null
            ? $this->orderResolver->resolveForUser($user)
            : $this->orderResolver->resolveAny();

        return response()->json($this->jobsDemo->dispatchFulfillmentJobs($order));
    }

    public function memory(): JsonResponse
    {
        $collection = collect(range(1, 10_000))->map(function (int $index) {
            return [
                'index' => $index,
                'payload' => str_repeat('x', 100),
                'nested' => [
                    'hash' => md5((string) $index),
                ],
            ];
        });

        return response()->json([
            'items' => $collection->count(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ]);
    }

    public function exception(): JsonResponse
    {
        throw new ObservabilityDemoException('Scout should capture this intentional demo exception.');
    }

    public function fullFlow(FullFlowDemoRequest $request): JsonResponse
    {
        return response()->json([
            'note' => $request->validated('note'),
            'result' => $this->fullFlow->execute($request->user()),
        ]);
    }
}

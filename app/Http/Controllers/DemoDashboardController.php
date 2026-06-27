<?php

namespace App\Http\Controllers;

use App\Services\Demo\DashboardMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DemoDashboardController extends Controller
{
    public function __construct(
        private readonly DashboardMetricsService $dashboard,
    ) {}

    public function __invoke(Request $request): View|JsonResponse
    {
        $metrics = $this->dashboard->gather();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json($metrics);
        }

        return view('demo.dashboard', [
            'metrics' => $metrics,
        ]);
    }
}

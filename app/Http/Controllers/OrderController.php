<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
    ) {}

    public function index(Request $request): View
    {
        return view('orders.index', [
            'orders' => $this->orders->listForUser($request->user()),
        ]);
    }

    public function show(Request $request, int $order): View
    {
        $order = $this->orders->showForUser($request->user(), $order);

        abort_if($order === null, 404);

        return view('orders.show', [
            'order' => $order,
        ]);
    }
}

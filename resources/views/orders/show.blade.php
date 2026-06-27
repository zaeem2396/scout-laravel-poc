<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Order #{{ $order->id }}</h1>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">Back to orders</a>
        </div>
    </x-slot>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">Items</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Vendor</dt>
                        <dd class="col-7">{{ $order->vendor->name }}</dd>
                        <dt class="col-5">Status</dt>
                        <dd class="col-7">{{ $order->status->value }}</dd>
                        <dt class="col-5">Subtotal</dt>
                        <dd class="col-7">${{ number_format($order->subtotal, 2) }}</dd>
                        <dt class="col-5">Discount</dt>
                        <dd class="col-7">${{ number_format($order->discount, 2) }}</dd>
                        <dt class="col-5">Tax</dt>
                        <dd class="col-7">${{ number_format($order->tax, 2) }}</dd>
                        <dt class="col-5">Total</dt>
                        <dd class="col-7 fw-semibold">${{ number_format($order->total, 2) }}</dd>
                        @if ($order->coupon)
                            <dt class="col-5">Coupon</dt>
                            <dd class="col-7">{{ $order->coupon->code }}</dd>
                        @endif
                        @if ($order->payment)
                            <dt class="col-5">Payment</dt>
                            <dd class="col-7">{{ $order->payment->method->value }} ({{ $order->payment->status->value }})</dd>
                        @endif
                        <dt class="col-5">Placed</dt>
                        <dd class="col-7">{{ $order->placed_at?->format('M j, Y g:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

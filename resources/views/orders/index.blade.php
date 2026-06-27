<x-app-layout>
    <x-slot name="header">
        <h1 class="h3 mb-0">My Orders</h1>
    </x-slot>

    @if (session('status'))
        <x-alert>{{ session('status') }}</x-alert>
    @endif

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Vendor</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Placed</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->vendor->name }}</td>
                        <td><span class="badge text-bg-secondary">{{ $order->status->value }}</span></td>
                        <td>${{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->placed_at?->format('M j, Y g:i A') }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $orders->links() }}
</x-app-layout>

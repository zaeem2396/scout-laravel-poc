<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Shopping Cart</h1>
            @if ($lines->isNotEmpty())
                <a href="{{ route('checkout.create') }}" class="btn btn-primary btn-sm">Proceed to checkout</a>
            @endif
        </div>
    </x-slot>

    @if (session('status'))
        <x-alert>{{ session('status') }}</x-alert>
    @endif

    @if (session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    @if ($lines->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <p class="mb-3">Your cart is empty.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Browse products</a>
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Vendor</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Line total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lines as $line)
                        @php($product = $line['product'])
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->vendor->name }}</td>
                            <td>${{ number_format($product->price, 2) }}</td>
                            <td>
                                <form method="POST" action="{{ route('cart.update', $product) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $line['quantity'] }}" min="0" max="99" class="form-control form-control-sm" style="width: 5rem">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                                </form>
                            </td>
                            <td>${{ number_format($product->price * $line['quantity'], 2) }}</td>
                            <td>
                                <form method="POST" action="{{ route('cart.destroy', $product) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-app-layout>

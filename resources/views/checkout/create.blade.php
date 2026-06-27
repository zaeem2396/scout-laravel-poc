<x-app-layout>
    <x-slot name="header">
        <h1 class="h3 mb-0">Checkout</h1>
    </x-slot>

    @if (session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">Order summary</div>
                <ul class="list-group list-group-flush">
                    @foreach ($lines as $line)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $line['product']->name }} × {{ $line['quantity'] }}</span>
                            <span>${{ number_format($line['product']->price * $line['quantity'], 2) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('checkout.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="coupon_code" class="form-label">Coupon code</label>
                            <input type="text" name="coupon_code" id="coupon_code" value="{{ old('coupon_code') }}" class="form-control" placeholder="Optional">
                            @error('coupon_code')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment method</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->value }}" @selected(old('payment_method') === $method->value)>
                                        {{ ucfirst(str_replace('_', ' ', $method->value)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_method')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <p class="small text-muted">Orders are grouped by vendor. Checkout runs inside a database transaction.</p>

                        <button type="submit" class="btn btn-primary w-100">Place order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

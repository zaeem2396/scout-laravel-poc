<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">{{ $product->name }}</h1>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">Back to products</a>
        </div>
    </x-slot>

    @if (session('status'))
        <x-alert>{{ session('status') }}</x-alert>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <p class="text-muted mb-2">SKU: {{ $product->sku }}</p>
                    <p>{{ $product->description }}</p>
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Vendor</dt>
                        <dd class="col-sm-9">{{ $product->vendor->name }}</dd>
                        <dt class="col-sm-3">Category</dt>
                        <dd class="col-sm-9">{{ $product->category->name }}</dd>
                        <dt class="col-sm-3">Reviews</dt>
                        <dd class="col-sm-9">{{ $product->reviews->count() }}</dd>
                        <dt class="col-sm-3">Images</dt>
                        <dd class="col-sm-9">{{ $product->images->count() }}</dd>
                    </dl>
                </div>
            </div>

            @if ($product->images->isNotEmpty())
                <div class="card">
                    <div class="card-header">Images</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($product->images as $image)
                            <li class="list-group-item small text-muted">{{ $image->path }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <p class="display-6">${{ number_format($product->price, 2) }}</p>
                    <p class="text-muted">In stock: {{ $product->inventory?->quantity ?? 0 }}</p>

                    @auth
                        <form method="POST" action="{{ route('cart.store', $product) }}" class="row g-2">
                            @csrf
                            <div class="col-4">
                                <input type="number" name="quantity" value="1" min="1" max="99" class="form-control" required>
                            </div>
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary w-100">Add to cart</button>
                            </div>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">Log in to purchase</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

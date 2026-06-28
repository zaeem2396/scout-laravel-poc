<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-5 fw-semibold mb-3">{{ config('app.name') }}</h1>
                    <p class="lead text-muted mb-4">
                        Laravel observability playground for Scout APM instrumentation demos.
                    </p>

                    @if (Route::has('login'))
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a href="{{ route('products.index') }}" class="btn btn-primary">Browse products</a>
                            <a href="{{ route('demo.n-plus-one', ['limit' => 100]) }}" class="btn btn-warning">Trigger N+1 demo</a>
                            <a href="{{ route('demo.exception') }}" class="btn btn-danger">Trigger error demo</a>
                            <a href="{{ route('demo.sql-error') }}" class="btn btn-outline-danger">Trigger SQL error</a>
                            <a href="{{ route('demo.human-error') }}" class="btn btn-outline-danger">Trigger human error</a>
                            <a href="{{ route('demo.index') }}" class="btn btn-outline-primary">Observability demos</a>
                            <a href="{{ route('demo.dashboard') }}" class="btn btn-outline-primary">Dashboard</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-secondary">Register</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </body>
</html>

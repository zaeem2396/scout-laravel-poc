<?php

namespace App\Services\Demo;

use Illuminate\Support\Facades\Http;

class ExternalApiDemoService
{
    /**
     * @return array<string, mixed>
     */
    public function fetchSampleData(): array
    {
        $jsonPlaceholder = Http::timeout(5)->get('https://jsonplaceholder.typicode.com/posts/1');
        $dummyJson = Http::timeout(5)->get('https://dummyjson.com/products/1');
        $github = Http::timeout(5)->get('https://api.github.com/repos/laravel/framework');

        return [
            'jsonplaceholder' => $jsonPlaceholder->json(),
            'dummyjson' => $dummyJson->json(),
            'github' => $github->json('full_name'),
        ];
    }
}

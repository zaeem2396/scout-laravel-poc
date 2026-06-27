@props(['type' => 'success'])

@php
    $class = $type === 'error' ? 'alert-danger' : 'alert-success';
@endphp

@if ($slot->isNotEmpty())
    <div {{ $attributes->merge(['class' => "alert {$class}"]) }}>
        {{ $slot }}
    </div>
@endif

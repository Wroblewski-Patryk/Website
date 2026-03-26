@props([
    'type' => 'success',
    'message' => '',
])

@php
    $class = match ($type) {
        'error' => 'error',
        default => 'success',
    };
@endphp

@if (!empty($message))
    <div class="alert {{ $class }}">{{ $message }}</div>
@endif

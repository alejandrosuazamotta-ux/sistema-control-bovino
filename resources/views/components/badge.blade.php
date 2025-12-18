@props(['variant' => 'default', 'size' => 'md', 'animated' => false])

@php
    $variants = [
        'default' => 'bg-spg-soft text-spg-primary',
        'primary' => 'bg-spg-primary text-white',
        'secondary' => 'bg-spg-secondary text-white',
        'success' => 'bg-green-100 text-green-800 border border-green-200',
        'warning' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
        'danger' => 'bg-red-100 text-red-800 border border-red-200',
        'info' => 'bg-blue-100 text-blue-800 border border-blue-200',
        'soft' => 'bg-spg-beige text-spg-deepblue',
    ];
    
    $sizes = [
        'sm' => 'text-xs px-2 py-1',
        'md' => 'text-sm px-3 py-1.5',
        'lg' => 'text-base px-4 py-2',
    ];
    
    $classes = $variants[$variant] ?? $variants['default'];
    $classes .= ' ' . ($sizes[$size] ?? $sizes['md']);
    $classes .= ' inline-flex items-center font-medium rounded-full';
    $classes .= $animated ? ' badge-animated' : '';
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>


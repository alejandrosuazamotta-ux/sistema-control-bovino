@props(['class' => ''])

<tr {{ $attributes->merge(['class' => 'table-row-hover ' . $class]) }}>
    {{ $slot }}
</tr>


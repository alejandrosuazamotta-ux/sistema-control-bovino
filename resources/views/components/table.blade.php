@props(['class' => ''])

<div class="overflow-x-auto bg-white rounded-xl shadow-saas animate-fade-in-up">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 ' . $class]) }}>
        {{ $slot }}
    </table>
</div>


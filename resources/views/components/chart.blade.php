@props(['id', 'type' => 'line', 'height' => 300, 'title' => null, 'class' => ''])

<div class="bg-white rounded-card shadow-saas p-4 md:p-6 {{ $class }} animate-fade-in-up">
    @if($title)
        <div class="mb-4 pb-3 border-b border-gray-100">
            <h4 class="text-lg font-semibold text-gray-900 font-display">{{ $title }}</h4>
        </div>
    @endif
    <div id="{{ $id }}" style="height: {{ $height }}px;" class="animate-fade-in"></div>
</div>

